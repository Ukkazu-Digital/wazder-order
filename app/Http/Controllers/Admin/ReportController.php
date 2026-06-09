<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\Product;
use App\Models\v2\StockEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function getReportData($startDate, $endDate)
    {
        $totalIn = DB::table('stock_entries')
            ->select('product_id', DB::raw('SUM(qty_received) as total_in'))
            ->where('created_at', '<=', $endDate . ' 23:59:59')
            ->groupBy('product_id');

        $totalOut = DB::table('stock_mutations')
            ->select('product_id', DB::raw('SUM(qty) as total_out'))
            ->where('type', 'out')
            ->where('created_at', '<=', $endDate . ' 23:59:59')
            ->groupBy('product_id');

        return DB::table('products')
            ->leftJoin('stock_mutations', function ($join) use ($startDate, $endDate) {
                $join->on('products.id', '=', 'stock_mutations.product_id')
                    ->where('stock_mutations.type', '=', 'out')
                    ->where('stock_mutations.category', '=', 'sale')
                    ->whereBetween('stock_mutations.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->leftJoinSub($totalIn, 'stock_in', 'products.id', '=', 'stock_in.product_id')
            ->leftJoinSub($totalOut, 'stock_out', 'products.id', '=', 'stock_out.product_id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('COALESCE(SUM(stock_mutations.qty), 0) as total_qty_sold'),
                DB::raw('COALESCE(SUM(stock_mutations.qty * stock_mutations.price), 0) as total_hpp_sold'),
                DB::raw('COALESCE(SUM(stock_mutations.qty * products.selling_price), 0) as estimated_revenue'),
                DB::raw('COALESCE(stock_in.total_in, 0) - COALESCE(stock_out.total_out, 0) as total_qty_in_stock'),
                DB::raw('(COALESCE(stock_in.total_in, 0) - COALESCE(stock_out.total_out, 0)) * products.selling_price as total_hpp_in_stock')
            )
            ->groupBy('products.id', 'products.name', 'stock_in.total_in', 'stock_out.total_out', 'products.selling_price');
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $sortBy = $request->get('sort', 'desc');

        $baseQuery = $this->getReportData($startDate, $endDate);

        // OPTIMASI: Hitung total langsung dari Database (Hanya mengembalikan 1 baris data angka)
        $totals = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->mergeBindings($baseQuery) // Sangat penting untuk menyertakan bindings WHERE dates
            ->select(
                DB::raw('SUM(estimated_revenue) as revenue'),
                DB::raw('SUM(total_hpp_sold) as total_hpp'),
                DB::raw('SUM(total_hpp_in_stock) as total_asset')
            )->first();

        $revenue = $totals->revenue ?? 0;
        $totalHpp = $totals->total_hpp ?? 0;
        $grossProfit = $revenue - $totalHpp;
        $totalAssetValue = $totals->total_asset ?? 0;

        // Gunakan Paginasi untuk tampilan Web agar memori PHP hemat!
        $sortColumn = 'total_qty_in_stock';
        $productPerformances = $baseQuery->orderBy($sortColumn, $sortBy)->paginate(15); 

        return view('admin.reports.profit', compact(
            'revenue', 'totalHpp', 'grossProfit', 'totalAssetValue', 
            'startDate', 'endDate', 'productPerformances', 'sortBy'
        ));
    }

    public function exportProfitExcel(Request $request)
    {
        // Untuk Excel, naikkan memory limit sementara HANYA saat ekspor jika datanya sangat masif
        ini_set('memory_limit', '512M');
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = $this->getReportData($startDate, $endDate)->get();

        return Excel::download(new ProfitReportExport($data, $startDate, $endDate), "Laporan_LabaRugi_FIFO_{$startDate}_to_{$endDate}.xlsx");
    }

    public function exportProfitPdf(Request $request)
    {
        // Batasi kuota ekspor PDF jika terlalu besar karena DomPDF sangat memakan memori
        ini_set('memory_limit', '512M');

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $baseQuery = $this->getReportData($startDate, $endDate);
        
        // Ambil summary langsung dari database
        $totals = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->mergeBindings($baseQuery)
            ->select(
                DB::raw('SUM(estimated_revenue) as revenue'),
                DB::raw('SUM(total_hpp_sold) as total_hpp'),
                DB::raw('SUM(total_hpp_in_stock) as total_asset')
            )->first();

        $revenue = $totals->revenue ?? 0;
        $totalHpp = $totals->total_hpp ?? 0;
        $grossProfit = $revenue - $totalHpp;
        $totalAssetValue = $totals->total_asset ?? 0;

        $productPerformances = $baseQuery->get();
        
        $pdf = Pdf::loadView('admin.reports.profit_pdf', compact(
            'revenue', 'totalHpp', 'grossProfit', 'totalAssetValue', 'startDate', 'endDate', 'productPerformances'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_Keuntungan_FIFO_{$startDate}_s_d_{$endDate}.pdf");
    }
}
