<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
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
        // Dekode ID Transaksi dari URL (Base64)
        $transaction_id = $encoded_trx ? base64_decode($encoded_trx) : 'TRX-' . strtoupper(Str::random(6));

        // Ambil data konsumen
        $getLinkData = LinkOrder::leftJoin('contacts','contacts.wa_id','=','link_order.wa_id')->leftJoin('customers','customers.id','=','contacts.customer_id')->where('kode_pesanan', $transaction_id)->first();
        if($getLinkData->customer_id == null){
            $customers = [
                'nama' => $getLinkData->name,
                'wa' => $getLinkData->wa_id,
                'alamat' => $getLinkData->address
            ];
        }else{
            $customers = [
                'nama' => $getLinkData->customers_name,
                'wa' => $getLinkData->wa_id,
                'alamat' => $getLinkData->address
            ];
        }


        // Ambil produk yang aktif
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('order.index', compact('products', 'transaction_id', 'customers'));
    }

    /**
     * Menyimpan Pesanan Baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama' => 'required|string|max:255',
            'wa' => 'required|string|max:20',
            'alamat' => 'string',
            'kode_pesanan' => 'required|string',
            'cart' => 'required|array', // Data JSON dari frontend
        ]);

        try {
            // Gunakan Transaction agar jika satu gagal, semua batal (Rollback)
            return DB::transaction(function () use ($request) {
                
                // 2. Cek/Simpan Data Pelanggan (Gunakan nomor WA sebagai unik)
                $customer = Customer::updateOrCreate(
                    ['customers_wa_id' => $request->wa], // Sesuaikan logic unik Anda
                    [
                        'customers_name' => $request->nama,
                        'address' => $request->alamat
                    ]
                );

            //     $contact = Contact::where('wa_id', $request->wa)->first();

            // if ($contact) {
            //     // Update customer_id jika memang belum ada
            //     if ($contact->customer_id == null) {
            //         $contact->update(['customer_id' => $customer->id]);
            //     }
            // }

                // 3. Buat Header Order
                $order = new Order();
                $order->order_code = $request->kode_pesanan;
                $order->customer_id = $customer->id;
                $order->total_price = 0; // Akan diupdate setelah hitung detail
                $order->status = 'pending';
                $order->save();

                $totalBelanja = 0;

                // 4. Simpan Detail Order & Update Stok
                foreach ($request->cart as $id => $item) {
                    $product = Product::findOrFail($id);

                    // Validasi stok di sisi server (Security Check)
                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                    }

                    $subtotal = $product->price * $item['qty'];
                    $totalBelanja += $subtotal;

                    // Simpan ke OrderDetail
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'qty' => $item['qty'],
                        'buy_price' => $product->price,
                        'subtotal' => $subtotal
                    ]);

                    // Kurangi Stok Produk
                    $product->decrement('stock', $item['qty']);
                }

                // 5. Update Total Harga di Header Order
                $order->update(['total_price' => $totalBelanja]);

                // 6. Insert ke tabel alamat pengiriman
                ShippingAddress::insert([
                    'order_id' => $order->id,
                    'address' => $request->alamat
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil disimpan!',
                    'order_id' => $order->order_code
                ]);
            });

        } catch (\Exception $e) {
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
            $itemsText .= "- " . $item->product->name . " (x" . $item->qty . ")\n";
        }

        // Kode pesanan di-encode untuk tombol dinamis
        $encodedCode = base64_encode($data['order_code']);

        // Payload untuk WhatsApp Business API (Contoh menggunakan Cloud API)
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $data['phone'],
            'type' => 'template',
            'template' => [
                'name' => 'konfirmasi_pesanan_baru', // Nama template yang Anda daftarkan
                'language' => ['code' => 'id'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $data['order_code']], // {{1}}
                            ['type' => 'text', 'text' => $itemsText],         // {{2}}
                            ['type' => 'text', 'text' => 'Rp ' . number_format($data['total'], 0, ',', '.')], // {{3}}
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0', // Indeks tombol pertama
                        'parameters' => [
                            ['type' => 'text', 'text' => $encodedCode] // Ini akan menyambung ke Base URL status
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($this->token)->post($this->apiUrl, $payload);

        if ($response->successful()) {
            Log::info("API Meta SUCCESS mengirim template ke " . $payload['to']);
            
            // Simpan outbound ke DB
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
        $order = OrderDetail::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('order.track', compact('transaction_id', 'order'));
    }

    private function checkExistCustomer($waId)
    {

    }
}