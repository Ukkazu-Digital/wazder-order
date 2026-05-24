<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Product;
use App\Models\v2\StockEntry;
use App\Models\v2\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockManagementController extends Controller
{
    // 1. Menampilkan Semua Riwayat Batch Stok Masuk
    public function index()
    {
        // Mengambil entri stok beserta produknya, diurutkan dari yang terbaru masuk
        $entries = StockEntry::with('product')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.stocks.index', compact('entries'));
    }

    // 2. Menampilkan Form Catat Stok Masuk
    public function create()
    {
        // Mengambil semua produk untuk opsi pilihan di form
        $products = Product::with(['stockEntries' => function($query) {
            $query->latest();
        }])->orderBy('name', 'asc')->get();
        return view('admin.stocks.create', compact('products'));
    }

    // 3. Memproses Penyimpanan Stok Masuk (Database Transaction)
    public function store(Request $request)
    {
        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'qty_received'      => 'required|integer|min:1',
            'purchase_price'    => 'required|integer|min:0',
            'reference_id'      => 'nullable|string|max:50', 
            'new_selling_price' => 'required_if:update_selling_price,on|nullable|integer|min:0', // Validasi bersyarat harga baru
        ]);

        DB::beginTransaction();

        try {
            // Langkah A: Amankan kuota batch di tabel stock_entries
            $entry = StockEntry::create([
                'product_id'     => $request->product_id,
                'qty_received'   => $request->qty_received,
                'qty_remaining'  => $request->qty_received, 
                'purchase_price' => $request->purchase_price,
            ]);

            // Langkah B: Catat buku besar mutasi sebagai dokumen audit trail 'in'
            StockMutation::create([
                'product_id'     => $request->product_id,
                'stock_entry_id' => $entry->id, 
                'type'           => 'in',
                'category'       => 'purchase', 
                'qty'            => $request->qty_received,
                'price'          => $request->purchase_price,
                'reference_id'   => $request->reference_id,
            ]);

            // Langkah C: UPDATE HARGA JUAL MASTER JIKA SWITCH DIAKTIFKAN
            if ($request->has('update_selling_price') && $request->update_selling_price === 'on') {
                $product = Product::findOrFail($request->product_id);
                $product->update([
                    'selling_price' => $request->new_selling_price
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stocks.index')
                             ->with('success', "Stok masuk berhasil dibukukan" . ($request->update_selling_price === 'on' ? " dan harga jual produk berhasil diperbarui." : "."));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['error' => 'Gagal mengamankan stok masuk: ' . $e->getMessage()]);
        }
    }

    // 4. Menampilkan Form Stok Keluar / Penyesuaian
    public function createAdjustment()
    {
        $products = Product::orderBy('name', 'asc')->get();
        return view('admin.stocks.adjustment', compact('products'));
    }

    // 5. Memproses Pengurangan Stok dengan Logika FIFO Loop
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'stock_entry_id' => 'nullable|exists:stock_entries,id', // Validasi pilihan batch opsional
            'qty'            => 'required|integer|min:1',
            'category'       => 'required|in:adjustment,damaged,return',
            'reference_id'   => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qtyToDeduct = $request->qty;

        // 1. JIKA USER MEMILIH BATCH SPESIFIK (Spesifik Batch / Kasus Rusak di Batch Tertentu)
        if ($request->filled('stock_entry_id')) {
            $batch = StockEntry::where('product_id', $request->product_id)
                               ->findOrFail($request->stock_entry_id);

            if ($batch->qty_remaining < $qtyToDeduct) {
                return redirect()->back()->withInput()->withErrors([
                    'qty' => "Stok di BATCH-#{$batch->id} tidak mencukupi. Sisa kuota batch ini hanya {$batch->qty_remaining} pcs."
                ]);
            }

            DB::beginTransaction();
            try {
                $batch->decrement('qty_remaining', $qtyToDeduct);

                StockMutation::create([
                    'product_id'     => $request->product_id,
                    'stock_entry_id' => $batch->id,
                    'type'           => 'out',
                    'category'       => $request->category,
                    'qty'            => $qtyToDeduct,
                    'price'          => $batch->purchase_price, // Menggunakan harga modal batch spesifik tersebut!
                    'reference_id'   => $request->reference_id,
                ]);

                DB::commit();
                return redirect()->route('admin.stocks.index')
                                 ->with('success', "Penyesuaian stok keluar khusus BATCH-#{$batch->id} berhasil diproses.");
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
            }
        }

        // 2. JIKA BATCH DIKOSONGKAN (Gunakan Logika Otomatis FIFO yang Sebelumnya)
        if ($product->totalStock() < $qtyToDeduct) {
            return redirect()->back()->withInput()->withErrors(['qty' => "Total stok tidak mencukupi."]);
        }

        DB::beginTransaction();
        try {
            $activeBatches = StockEntry::availableFifo($request->product_id)->get();

            foreach ($activeBatches as $batch) {
                if ($qtyToDeduct <= 0) break;

                $deduction = min($batch->qty_remaining, $qtyToDeduct);
                $batch->decrement('qty_remaining', $deduction);

                StockMutation::create([
                    'product_id'     => $request->product_id,
                    'stock_entry_id' => $batch->id,
                    'type'           => 'out',
                    'category'       => $request->category,
                    'qty'            => $deduction,
                    'price'          => $batch->purchase_price,
                    'reference_id'   => $request->reference_id,
                ]);

                $qtyToDeduct -= $deduction;
            }

            DB::commit();
            return redirect()->route('admin.stocks.index')->with('success', "Penyesuaian stok (FIFO) berhasil diproses.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Method baru untuk melayani request AJAX dari form
    public function getActiveBatches($productId)
    {
        // Ambil batch yang masih ada sisa stoknya
        $batches = StockEntry::where('product_id', $productId)
                             ->where('qty_remaining', '>', 0)
                             ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru agar mudah dicari
                             ->get(['id', 'qty_remaining', 'purchase_price', 'created_at']);

        return response()->json($batches);
    }
}