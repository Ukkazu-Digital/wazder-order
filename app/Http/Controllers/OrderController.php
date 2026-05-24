<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\v2\Product; 
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\LinkOrder;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    private $token;
    private $phoneId;
    private $apiUrl;

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
        $this->apiUrl = "https://graph.facebook.com/v25.0/{$this->phoneId}/messages";
    }

    /**
     * Menampilkan Halaman Katalog & Order
     */
    public function index($encoded_trx = null)
    {
        $transaction_id = $encoded_trx ? base64_decode($encoded_trx) : 'TRX-' . strtoupper(Str::random(6));

        $getLinkData = LinkOrder::leftJoin('contacts', 'contacts.wa_id', '=', 'link_order.wa_id')
            ->leftJoin('customers', 'customers.id', '=', 'contacts.customer_id')
            ->where('kode_pesanan', $transaction_id)
            // ->where('link_order.expired_at', '>', Carbon::now())
            ->firstOrFail();

        if (Carbon::now()->greaterThan($getLinkData->expired_at)) {
            return redirect()->route('order.failed', [
                'msg' => 'Maaf, tautan katalog ini sudah kedaluwarsa dan tidak dapat digunakan lagi.'
            ]);
        }

        if ($getLinkData) {
            if ($getLinkData->customer_id == null) {
                $customers = [
                    'nama' => $getLinkData->name,
                    'wa' => $getLinkData->wa_id,
                    'alamat' => $getLinkData->address
                ];
            } else {
                $customers = [
                    'nama' => $getLinkData->customers_name,
                    'wa' => $getLinkData->wa_id,
                    'alamat' => $getLinkData->address
                ];
            }
        } else {
            return redirect()->route('order.failed', [
                'msg' => 'Tautan pesanan tidak valid atau tidak ditemukan.'
            ]);
        }

        // OPTIMASI v2: Filter langsung di level DB via whereHas agar hemat RAM server
        $products = Product::whereHas('stockEntries', function ($query) {
            $query->where('qty_remaining', '>', 0);
        })->orderBy('name', 'asc')->get();

        return view('order.index', compact('products', 'transaction_id', 'customers'));
    }

    /**
     * Menyimpan Pesanan Baru (Product v2 - FIFO Batch dengan Pessimistic Locking)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'wa' => 'required|string|max:20',
            'alamat' => 'string',
            'kode_pesanan' => 'required|string',
            'cart' => 'required|array', 
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
            $normalizedWa = preg_replace('/[^0-9]/', '', $request->wa);

            // 1. Cari atau Buat Kontak Terlebih Dahulu
            $contact = Contact::where('wa_id', $normalizedWa)->first();

            $customer = null;

            if ($contact && !empty($contact->customer_id)) {
                // Jika kontak ada dan sudah punya customer_id, ambil datanya
                $customer = Customer::find($contact->customer_id);
            }

            // 2. Jika customer belum ketemu dari tabel contact, cari berdasarkan nomor WA di tabel customers
            if (!$customer) {
                $customer = Customer::where('customers_wa_id', $normalizedWa)->first();
            }

            // 3. Update data jika customer ditemukan, atau Buat Baru jika benar-benar pelanggan baru
            if ($customer) {
                $customer->update([
                    'customers_name' => $request->nama,
                    'address'        => $request->alamat
                ]);
            } else {
                // MEMBUAT DATA CUSTOMER BARU SAAT ORDER PERTAMA
                $customer = Customer::create([
                    'customers_wa_id' => $normalizedWa,
                    'customers_name'  => $request->nama,
                    'address'         => $request->alamat
                ]);
            }

            // 4. Sinkronisasi kembali ke tabel contacts agar contact terikat dengan customer_id yang benar
            if ($contact) {
                if ($contact->customer_id !== $customer->id) {
                    $contact->customer_id = $customer->id;
                    $contact->save();
                }
            } else {
                Contact::create([
                    'wa_id'       => $normalizedWa,
                    'customer_id' => $customer->id
                ]);
            }

                // 1. Validasi total stok & Kunci baris data produk (Lock for Update)
                foreach ($request->cart as $id => $item) {
                    $product = Product::where('id', $id)->lockForUpdate()->findOrFail($id);
                    if ($product->totalStock() < $item['qty']) {
                        throw new \Exception("Stok produk '{$product->name}' tidak mencukupi. Sisa stok: " . $product->totalStock());
                    }
                }

                // 2. Buat Header Order
                $order = new Order();
                $order->order_code = $request->kode_pesanan;
                $order->customer_id = $customer->id;
                $order->total_price = 0;
                $order->status = 'pending';
                $order->source = 'whatsapp'; 
                $order->save();

                DB::table('order_histories')->insert([
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'note' => 'Pesanan dibuat oleh konsumen via tautan mandiri.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalBelanja = 0;

                // 3. Eksekusi Pengurangan Stok FIFO v2
                foreach ($request->cart as $id => $item) {
                    $product = Product::where('id', $id)->lockForUpdate()->findOrFail($id);
                    $qtyNeeded = $item['qty'];
                    
                    $sellingPrice = $product->selling_price;
                    $subtotal = $sellingPrice * $qtyNeeded;
                    $totalBelanja += $subtotal;

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'qty' => $qtyNeeded,
                        'buy_price' => $sellingPrice,
                        'subtotal' => $subtotal
                    ]);

                    // Ambil batch v2 dengan lockForUpdate() agar tidak direbut transaksi lain
                    $batches = $product->stockEntries()
                                       ->where('qty_remaining', '>', 0)
                                       ->orderBy('created_at', 'asc')
                                       ->lockForUpdate()
                                       ->get();

                    foreach ($batches as $batch) {
                        if ($qtyNeeded <= 0) break;

                        $takeQty = min($qtyNeeded, $batch->qty_remaining);
                        
                        $batch->qty_remaining -= $takeQty;
                        $batch->save();

                        // Catat ke tabel mutasi
                        DB::table('stock_mutations')->insert([
                            'product_id' => $product->id,
                            'stock_entry_id' => $batch->id,
                            'reference_id' => $order->order_code,
                            'category' => 'sale',
                            'type' => 'out',
                            'qty' => $takeQty,
                            'price' => $batch->purchase_price, 
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $qtyNeeded -= $takeQty;
                    }
                }

                $order->update(['total_price' => $totalBelanja]);

                ShippingAddress::insert([
                    'order_id' => $order->id,
                    'address' => $request->alamat
                ]);

                $items = OrderDetail::with('product')->where('order_id', $order->id)->get();
                return [
                    'order_code' => $order->order_code,
                    'phone' => $request->wa,
                    'total' => $totalBelanja,
                    'items' => $items
                ];
            });

            LinkOrder::where('kode_pesanan', $request->kode_pesanan)->update(['expired_at' => Carbon::now()]);
            $this->sendWhatsAppNotification($result);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disimpan dan notifikasi telah dikirim!',
                'order_id' => $result['order_code']
            ]);

        } catch (\Exception $e) {
            Log::error("Gagal membuat pesanan (Link Order v2): " . $e->getMessage(), [
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 422);
        }
    }

    private function sendWhatsAppNotification($data)
    {
        $itemsText = "";
        foreach ($data['items'] as $item) {
            $itemsText .= $item->product->name . " (x" . $item->qty . "), ";
        }
        $itemsText = rtrim($itemsText, ", ");

        $encodedCode = base64_encode($data['order_code']);
        $bodyContent = "Pesanan {$data['order_code']} berhasil. Total: Rp " . number_format($data['total'], 0, ',', '.');
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $data['phone'],
            'type' => 'template',
            'template' => [
                'name' => 'pesanan_sukses',
                'language' => ['code' => 'id'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $data['order_code']],
                            ['type' => 'text', 'text' => $itemsText],
                            ['type' => 'text', 'text' => 'Rp ' . number_format($data['total'], 0, ',', '.')],
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            ['type' => 'text', 'text' => $encodedCode]
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($this->token)->post($this->apiUrl, $payload);

        if ($response->successful()) {
            Log::info("API Meta SUCCESS mengirim template ke " . $payload['to']);
            
            DB::table('messages')->insert([
                'msg_id' => data_get($response->json(), 'messages.0.id'),
                'contact_wa_id' => $payload['to'],
                'direction' => 'outbound',
                'type' => 'template',
                'body' => $bodyContent,
                'status' => 'sent',
                'timestamp_unix' => time(),
                'created_at' => Carbon::now(),
            ]);
        } else {
            Log::error("API Meta FAILED mengirim template", [
                'response' => $response->json(),
                'payload' => $payload
            ]);
        }

        return $response->json();
    }

    public function track($encoded_trx = null)
    {
        $transaction_id = $encoded_trx ? base64_decode($encoded_trx) : 'TRX-' . strtoupper(Str::random(6));
        $order = Order::where('order_code', $transaction_id)
            ->with(['details.product', 'customer', 'shippingAddress', 'histories' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->firstOrFail();
        return view('order.track', compact('transaction_id', 'order'));
    }

    public function success($transaction_id)
    {
        return view('order.success', compact('transaction_id'));
    }

    public function failed($msg = null)
    {
        $msg = $msg ?? 'Terjadi kesalahan atau link sudah tidak valid.';
        return view('order.failed', compact('msg'));
    }
}