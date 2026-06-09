<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\v2\Product; // Menggunakan model Product dari sub-namespace v2
use App\Models\Kurir;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class KasirController extends Controller
{
    /**
     * Display list of kasir orders (created via POS system)
     */
    public function index()
    {
        $orders = Order::where('source', 'kasir')->latest()->get();
        return view('admin.kasir.index', compact('orders'));
    }

    /**
     * Show form to create new order via kasir
     */
    public function create()
    {
        $customers = Customer::all();
        
        // OPTIMASI v2: Filter langsung di level DB via whereHas agar hemat RAM server kasir
        $products = Product::whereHas('stockEntries', function ($query) {
            $query->where('qty_remaining', '>', 0);
        })->orderBy('name', 'asc')->get();
        
        $kurirs = Kurir::where('status', 'Aktif')->get();
        $transaction_id = 'ORD-' . strtoupper(Str::random(6)) . '-' . date('YmdHis');
        
        return view('admin.kasir.create', compact('customers', 'products', 'kurirs', 'transaction_id'));
    }

    /**
     * Store new order from kasir
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id', 
            'products.*.qty' => 'required|integer|min:1',
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($request->customer_id === 'new') {
            $request->validate([
                'new_customer_name' => 'required|string|max:255',
                'new_customer_address' => 'nullable|string',
                'new_customer_phone' => 'nullable|string|max:20',
            ]);
        }

        // Menggunakan Database Transaction untuk menjamin konsistensi multi-tabel FIFO v2
        DB::beginTransaction();

        try {
            $orderCode = 'ORD-' . strtoupper(Str::random(6)) . '-' . date('YmdHis');
            $totalPrice = 0;
            $productsList = $request->products;

            // 1. Validasi awal kesediaan total stok v2 sebelum eksekusi pembayaran
            foreach ($productsList as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->findOrFail($item['product_id']);
                if ($product->totalStock() < $item['qty']) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi. Sisa stok: " . $product->totalStock());
                }
                // Kalkulasi subtotal berdasarkan selling_price v2
                $totalPrice += $product->selling_price * $item['qty'];
            }

            // 2. Tentukan atau buat customer baru
            if ($request->customer_id === 'new') {
                $customer = Customer::create([
                    'customers_name' => $request->new_customer_name,
                    'address' => $request->new_customer_address ?? null,
                    'customers_wa_id' => $request->new_customer_phone ?? null,
                ]);
                Contact::create([
                    'name' => $request->new_customer_name,
                    'wa_id' => $request->new_customer_phone ?? null,
                    'customer_id' => $customer->id,
                    'last_status' => 'awaiting_response_bot'
                ]);
            } else {
                $customer = Customer::findOrFail($request->customer_id);
            }

            // 3. Simpan data Order Utama
            $orderData = [
                'order_code' => $orderCode,
                'customer_id' => $customer->id,
                'total_price' => $totalPrice,
                'status' => $request->status,
                'source' => 'kasir',
                'notes' => $request->notes,
            ];

            if ($request->has('kurir_id') && $request->kurir_id) {
                $orderData['kurir_id'] = $request->kurir_id;
            }

            $order = Order::create($orderData);

            // 4. Proses Pengurangan Stok dengan Antrean Batch FIFO v2
            foreach ($productsList as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->findOrFail($item['product_id']);
                $qtyNeeded = $item['qty'];
                $sellingPrice = $product->selling_price;
                $subtotal = $sellingPrice * $qtyNeeded;

                // Tambah data Order Detail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $qtyNeeded,
                    'buy_price' => $sellingPrice,
                    'subtotal' => $subtotal,
                ]);

                // Mengambil relasi stockEntries dari model v2\Product yang paling lama masuk
                $batches = $product->stockEntries()
                                   ->where('qty_remaining', '>', 0)
                                   ->orderBy('created_at', 'asc')
                                   ->lockForUpdate()
                                   ->get();

                foreach ($batches as $batch) {
                    if ($qtyNeeded <= 0) break;

                    $takeQty = min($qtyNeeded, $batch->qty_remaining);
                    
                    // Potong kuota sisa di batch v2
                    $batch->qty_remaining -= $takeQty;
                    $batch->save();

                    // Masukkan ke log ledger stock_mutations
                    DB::table('stock_mutations')->insert([
                        'product_id' => $product->id,
                        'stock_entry_id' => $batch->id,
                        'reference_id' => $orderCode,
                        'category' => 'sale',
                        'type' => 'out',
                        'qty' => $takeQty,
                        'price' => $batch->purchase_price, // HPP modal dari batch v2 yang terpotong
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $qtyNeeded -= $takeQty;
                }
            }

            //insert ke term of payment jika status TOP
            if ($request->status === 'TOP') {
                $top = DB::table('term_of_payments')->insert([
                    'order_id' => $order->id,
                    'payment_due_date' => Carbon::parse('2026-06-30')->format('Y-m-d 23:59:59'), // Contoh: Jatuh tempo 7 hari setelah order
                    'amount_due' => $totalPrice,
                    'status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('order_histories')->insert([
                    'order_id' => $order->id,
                    'status' => 'TOP',
                    'note' => 'Pesanan dibuat dengan status TOP, jatuh tempo pada ' . $request->due_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }else{
                // 5. Catat riwayat order status
                DB::table('order_histories')->insert([
                    'order_id' => $order->id,
                    'status' => $request->status,
                    'note' => 'Pesanan dibuat melalui sistem kasir menggunakan manajemen batch FIFO v2.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();
            return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        if ($order->source !== 'kasir') {
            return redirect()->route('admin.orders.index')->with('error', 'Pesanan tidak ditemukan');
        }

        $kurirs = Kurir::where('status', 'Aktif')->get();
        return view('admin.kasir.show', compact('order', 'kurirs'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,completed',
            'kurir_id' => $request->status === 'shipped' ? 'required|exists:kurirs,id' : 'nullable',
        ]);

        $order->status = $request->status;

        if ($request->status === 'shipped' && $request->kurir_id) {
            $order->kurir_id = $request->kurir_id;
        }

        if ($order->save()) {
            $note = 'Status berubah menjadi ' . $request->status;
            if ($request->status === 'shipped' && $request->kurir_id) {
                $kurir = Kurir::find($request->kurir_id);
                $note .= ' - Kurir: ' . $kurir->name;
            }

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'status' => $request->status,
                'note' => $note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('kasir.show', $order)->with('success', 'Status berhasil diperbarui!');
    }

    /**
     * Delete / Cancel kasir order (Mengembalikan kuota FIFO v2 & hapus mutasi terkait)
     */
    public function destroy(Order $order)
    {
        if ($order->source !== 'kasir') {
            return redirect()->route('admin.orders.index')->with('error', 'Pesanan tidak ditemukan');
        }

        DB::beginTransaction();

        try {
            // Ambil data mutasi keluar untuk dikembalikan posisinya ke batch asal v2
            $mutations = DB::table('stock_mutations')
                            ->where('reference_id', $order->order_code)
                            ->where('type', 'out')
                            ->get();

            foreach ($mutations as $mutation) {
                // FIX: Kunci baris entry batch yang ingin di-increment menggunakan lockForUpdate()
                // agar aman dari pengguna link order yang checkout bersamaan saat restorasi stok berlangsung.
                DB::table('stock_entries')
                  ->where('id', $mutation->stock_entry_id)
                  ->lockForUpdate() 
                  ->increment('qty_remaining', $mutation->qty);
            }

            // Hapus log mutasi terkait dari ledger
            DB::table('stock_mutations')->where('reference_id', $order->order_code)->delete();

            // Hapus data order utama
            $order->delete();

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Pesanan dibatalkan dan stok batch FIFO v2 berhasil dipulihkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }

    public function searchCustomers(Request $request)
    {
        $search = $request->get('q');
        $customers = Customer::where('customers_name', 'LIKE', "%$search%")
                            ->leftJoin('contacts', 'customers.id', '=', 'contacts.customer_id')
                            ->orWhere('contacts.wa_id', 'LIKE', "%$search%")
                            ->limit(10)
                            ->get();

        $results = $customers->map(function($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->customers_name . ' - ' . $customer->wa_id
            ];
        });

        return response()->json($results);
    }
}