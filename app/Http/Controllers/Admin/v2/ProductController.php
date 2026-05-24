<?php

namespace App\Http\Controllers\Admin\v2;

use App\Http\Controllers\Controller;
use App\Models\v2\Product;
use App\Models\v2\StockEntry;
use App\Models\v2\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan tabel daftar produk (views/products/index.blade.php)
     */
    public function index()
    {
        $products = Product::latest()->get();
        return view('admin.v2.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan halaman form tambah produk (views/products/create.blade.php)
     */
    public function create()
    {
        return view('admin.v2.products.create');
    }

    /**
     * Store a newly created resource in storage.
     * Memproses submit data dari form create.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|integer|min:0',
            'qty_received' => 'nullable|integer|min:1',
            'purchase_price' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'name' => $request->name,
                'selling_price' => $request->selling_price,
            ]);

            if ($request->filled('qty_received') && $request->filled('purchase_price')) {
                $batch = StockEntry::create([
                    'product_id' => $product->id,
                    'qty_received' => $request->qty_received,
                    'qty_remaining' => $request->qty_received,
                    'purchase_price' => $request->purchase_price,
                ]);

                StockMutation::create([
                    'product_id' => $product->id,
                    'stock_entry_id' => $batch->id,
                    'type' => 'in',
                    'category' => 'purchase',
                    'qty' => $request->qty_received,
                    'price' => $request->purchase_price,
                    'reference_id' => 'INIT-' . strtoupper(uniqid()),
                ]);
            }
    });

        return redirect()->route('admin.v2.products.index')
            ->with('success', 'Produk dan stok awal berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * Menampilkan halaman detail produk dan KARTU STOK (views/products/show.blade.php)
     */
    public function show($id)
    {
        $product = Product::with([
            'stockEntries' => function($query) {
                $query->where('qty_remaining', '>', 0)
                    ->orderBy('created_at', 'asc'); // Urutan FIFO
            },
            'stockMutations' => function($query) {
                $query->orderBy('created_at', 'desc')->take(50); // Ambil 50 mutasi terakhir
            }
        ])->findOrFail($id);

        $stockCard = DB::select("
            SELECT 
                m.id,
                m.created_at AS tanggal,
                m.category AS kategori,
                m.reference_id AS referensi,
                CASE WHEN m.type = 'in' THEN m.qty ELSE NULL END AS qty_masuk,
                CASE WHEN m.type = 'in' THEN m.price ELSE NULL END AS harga_masuk,
                CASE WHEN m.type = 'out' THEN m.qty ELSE NULL END AS qty_keluar,
                CASE WHEN m.type = 'out' THEN m.price ELSE NULL END AS modal_keluar,
                SUM(CASE WHEN m.type = 'in' THEN m.qty ELSE -m.qty END) 
                    OVER (ORDER BY m.created_at ASC, m.id ASC) AS saldo_stok_akhir
            FROM stock_mutations m
            WHERE m.product_id = ? AND m.deleted_at IS NULL
            ORDER BY m.created_at DESC, m.id DESC
        ", [$product->id]);

        return view('admin.v2.products.show', compact('product', 'stockCard'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan halaman edit (views/products/edit.blade.php)
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.v2.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|integer|min:0',
        ]);

        $product->update($request->only(['name', 'selling_price']));

        return redirect()->route('admin.v2.products.index')
            ->with('success', 'Data produk berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.v2.products.index')
            ->with('success', 'Produk berhasil di-softdelete.');
    }

    /**
     * PROSES CHECKOUT ORDER (FIFO OUT)
     */
    public function checkoutOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty_ordered' => 'required|integer|min:1',
            'order_id' => 'required|string',
        ]);

        $productId = $request->product_id;
        $qtyOrdered = $request->qty_ordered;
        $orderId = $request->order_id;

        try {
            DB::transaction(function () use ($productId, $qtyOrdered, $orderId) {
                $batches = StockEntry::availableFifo($productId)->get();

                if ($batches->sum('qty_remaining') < $qtyOrdered) {
                    throw new \Exception("Stok gabungan gudang tidak mencukupi.");
                }

                $remainingToFulfill = $qtyOrdered;

                foreach ($batches as $batch) {
                    if ($remainingToFulfill <= 0) break;

                    if ($batch->qty_remaining >= $remainingToFulfill) {
                        $qtyTaken = $remainingToFulfill;
                        $batch->decrement('qty_remaining', $qtyTaken);
                        $remainingToFulfill = 0;
                    } else {
                        $qtyTaken = $batch->qty_remaining;
                        $batch->update(['qty_remaining' => 0]);
                        $remainingToFulfill -= $qtyTaken;
                    }

                    StockMutation::create([
                        'product_id' => $productId,
                        'stock_entry_id' => $batch->id,
                        'type' => 'out',
                        'category' => 'sale',
                        'qty' => $qtyTaken,
                        'price' => $batch->purchase_price,
                        'reference_id' => $orderId,
                    ]);
                }
        });

            return redirect()->back()->with('success', 'Checkout FIFO berhasil, stok terpotong!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * PEMBATALAN TRANSAKSI (RESTORE STOK)
     */
    public function cancelOrder($orderId)
    {
        try {
            DB::transaction(function () use ($orderId) {
                $mutations = StockMutation::where('reference_id', $orderId)
                    ->where('type', 'out')
                    ->get();

                if ($mutations->isEmpty()) {
                    throw new \Exception("Data transaksi tidak ditemukan.");
                }

                foreach ($mutations as $mutation) {
                    if ($mutation->stock_entry_id) {
                        StockEntry::where('id', $mutation->stock_entry_id)
                            ->increment('qty_remaining', $mutation->qty);
                    }
                    $mutation->delete();
                }
        });

            return redirect()->back()->with('success', 'Transaksi dibatalkan, stok dikembalikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}