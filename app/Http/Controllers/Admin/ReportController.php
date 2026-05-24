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
        return DB::table('stock_mutations')
            ->join('products', 'products.id', '=', 'stock_mutations.product_id')
            ->where('stock_mutations.type', 'out')
            ->where('stock_mutations.category', 'sale')
            ->whereBetween('stock_mutations.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'products.name',
                DB::raw('SUM(stock_mutations.qty) as total_qty_sold'),
                DB::raw('SUM(stock_mutations.qty * stock_mutations.price) as total_cost_hpp'),
                DB::raw('SUM(stock_mutations.qty * products.selling_price) as estimated_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty_sold', 'desc')
            ->get();
    }

    /**
     * Laporan Ringkasan Performa Toko (Dashboard / Profit Report HTML)
     */
    public function profitReport(Request $request)
    {
        // Default filter bulan ini jika user tidak memilih tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Ambil data performa menggunakan helper internal
        $productPerformances = $this->getReportData($startDate, $endDate);

        // Hitung total akumulasi dari data yang difilter
        $revenue = $productPerformances->sum('estimated_revenue');
        $totalHpp = $productPerformances->sum('total_cost_hpp');
        $grossProfit = $revenue - $totalHpp;

        return view('admin.reports.profit', compact('revenue', 'totalHpp', 'grossProfit', 'startDate', 'endDate', 'productPerformances'));
    }

    /**
     * Proses Download Berkas Excel via Server
     */
    public function exportProfitExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = $this->getReportData($startDate, $endDate);

        return Excel::download(new ProfitReportExport($data, $startDate, $endDate), "Laporan_LabaRugi_FIFO_{$startDate}_to_{$endDate}.xlsx");
    }

    /**
     * Proses Stream/Download Berkas PDF via Server
     */
    public function exportProfitPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $productPerformances = $this->getReportData($startDate, $endDate);
        
        $revenue = $productPerformances->sum('estimated_revenue');
        $totalHpp = $productPerformances->sum('total_cost_hpp');
        $grossProfit = $revenue - $totalHpp;

        $pdf = Pdf::loadView('admin.reports.profit_pdf', compact('revenue', 'totalHpp', 'grossProfit', 'startDate', 'endDate', 'productPerformances'))
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