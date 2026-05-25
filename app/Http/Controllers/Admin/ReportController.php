<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Product;
use App\Models\v2\StockEntry;
use App\Models\v2\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

// IMPORT LIBRARY UNTUK EXPORT BERBASIS SERVER
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Helper internal untuk menarik data breakdown laba rugi produk (FIFO)
     * Digunakan bersama oleh halaman web, excel, dan PDF agar data konsisten.
     */
    private function getReportData($startDate, $endDate)
    {
        // 1. Subquery untuk Total Masuk (Pembelian/Restock) s/d End Date
        $totalIn = DB::table('stock_entries')
            ->select('product_id', DB::raw('SUM(qty_received) as total_in'))
            ->where('created_at', '<=', $endDate . ' 23:59:59')
            ->groupBy('product_id');

        // 2. Subquery untuk Total Keluar (Penjualan) s/d End Date
        $totalOut = DB::table('stock_mutations')
            ->select('product_id', DB::raw('SUM(qty) as total_out'))
            ->where('type', 'out')
            ->where('created_at', '<=', $endDate . ' 23:59:59')
            ->groupBy('product_id');

        return DB::table('products')
            // Join ke mutasi khusus periode yang dipilih (untuk tabel laporan)
            ->leftJoin('stock_mutations', function ($join) use ($startDate, $endDate) {
                $join->on('products.id', '=', 'stock_mutations.product_id')
                    ->where('stock_mutations.type', '=', 'out')
                    ->where('stock_mutations.category', '=', 'sale')
                    ->whereBetween('stock_mutations.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            // Join ke subquery stok
            ->leftJoinSub($totalIn, 'stock_in', 'products.id', '=', 'stock_in.product_id')
            ->leftJoinSub($totalOut, 'stock_out', 'products.id', '=', 'stock_out.product_id')
            ->select(
                'products.id',
                'products.name',
                // Data Penjualan Periode Terpilih
                DB::raw('COALESCE(SUM(stock_mutations.qty), 0) as total_qty_sold'),
                DB::raw('COALESCE(SUM(stock_mutations.qty * stock_mutations.price), 0) as total_hpp_sold'),
                DB::raw('COALESCE(SUM(stock_mutations.qty * products.selling_price), 0) as estimated_revenue'),
                
                // Perhitungan Stok Sisa per End Date
                DB::raw('COALESCE(stock_in.total_in, 0) - COALESCE(stock_out.total_out, 0) as total_qty_in_stock'),
                
                // Estimasi Nilai Aset (menggunakan harga rata-rata sederhana atau harga beli terakhir)
                // Catatan: Jika ingin akurasi FIFO 100%, gunakan logika valuation tabel stock_entries
                DB::raw('(COALESCE(stock_in.total_in, 0) - COALESCE(stock_out.total_out, 0)) * products.selling_price as total_hpp_in_stock')
            )
            ->groupBy('products.id', 'products.name', 'stock_in.total_in', 'stock_out.total_out', 'products.selling_price');
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $sortBy = $request->get('sort', 'desc');

        $query = $this->getReportData($startDate, $endDate);

        // Menerapkan Sorting
        $productPerformances = ($sortBy === 'asc') 
            ? $query->orderBy('total_qty_in_stock', 'asc')->get() 
            : $query->orderBy('total_qty_in_stock', 'desc')->get();

        // Hitung Ringkasan
        $revenue = $productPerformances->sum('estimated_revenue');
        $totalHpp = $productPerformances->sum('total_hpp_sold');
        $grossProfit = $revenue - $totalHpp;
        $totalAssetValue = $productPerformances->sum('total_hpp_in_stock');

        return view('admin.reports.profit', compact(
            'revenue', 'totalHpp', 'grossProfit', 'totalAssetValue', 
            'startDate', 'endDate', 'productPerformances', 'sortBy'
        ));
    }

    /**
     * Proses Download Berkas Excel via Server
     */
    public function exportProfitExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = $this->getReportData($startDate, $endDate)->get();

        return Excel::download(new ProfitReportExport($data, $startDate, $endDate), "Laporan_LabaRugi_FIFO_{$startDate}_to_{$endDate}.xlsx");
    }

    /**
     * Proses Stream/Download Berkas PDF via Server
     */
    public function exportProfitPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Ambil data (perlu ditambahkan ->get() jika di helper belum ada)
        $productPerformances = $this->getReportData($startDate, $endDate)->get();
        
        // Perhitungan ringkasan
        $revenue = $productPerformances->sum('estimated_revenue');
        $totalHpp = $productPerformances->sum('total_hpp_sold'); // Sesuaikan dengan nama alias di query
        $grossProfit = $revenue - $totalHpp;
        $totalAssetValue = $productPerformances->sum('total_hpp_in_stock'); // Tambahkan ini

        // Kirim semua variabel ke view PDF
        $pdf = Pdf::loadView('admin.reports.profit_pdf', compact(
            'revenue', 'totalHpp', 'grossProfit', 'totalAssetValue', 'startDate', 'endDate', 'productPerformances'
        ))
        ->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_Keuntungan_FIFO_{$startDate}_s_d_{$endDate}.pdf");
    }

    /**
     * Laporan Nilai Aset Gudang Saat Ini
     */
    public function inventoryValuation()
    {
        // Menghitung sisa uang yang ngendap di gudang berdasarkan sisa kuota batch FIFO
        $totalValuation = StockEntry::where('qty_remaining', '>', 0)
            ->select(DB::raw('SUM(qty_remaining * purchase_price) as total_asset'))
            ->value('total_asset') ?? 0;

        // Detail aset per produk
        $productAssets = Product::with(['stockEntries' => function($q) {
                $q->where('qty_remaining', '>', 0);
            }])
            ->get()
            ->map(function($product) {
                $stockLeft = $product->totalStock();
                $assetValue = $product->stockEntries->sum(function($batch) {
                    return $batch->qty_remaining * $batch->purchase_price;
                });

                return [
                    'name' => $product->name,
                    'current_stock' => $stockLeft,
                    'asset_valuation' => $assetValue
                ];
            })->filter(function($item) {
                return $item['current_stock'] > 0; // Hanya tampilkan yang punya stok
            });

        return view('admin.reports.inventory', compact('totalValuation', 'productAssets'));
    }
}