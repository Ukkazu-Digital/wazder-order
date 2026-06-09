<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\StockEntry;
use App\Models\v2\StockMutation;
use App\Models\v2\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilan Utama Halaman Dashboard Admin
     */
    public function index()
    {
        // 1. Tentukan batas waktu rentang bulan berjalan ini
        $startMonth = Carbon::now()->startOfMonth()->toDateString();
        $endMonth = Carbon::now()->endOfMonth()->toDateString();

        // 2. Hitung Total Omset Penjualan Bulan Ini dari tabel orders
        // Mengasumsikan status pesanan yang valid masuk hitungan keuangan adalah 'completed' atau 'pending'
        $thisMonthRevenue = DB::table('orders')
            ->whereIn('status', ['completed', 'pending'])
            ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
            ->sum('total_price') ?? 0;

        // 3. Hitung Total HPP Berjalan Bulan Ini berdasarkan mutasi keluar (FIFO)
        $thisMonthHpp = StockMutation::where('type', 'out')
            ->where('category', 'sale')
            ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
            ->select(DB::raw('SUM(qty * price) as total_hpp'))
            ->value('total_hpp') ?? 0;

        // 4. Hitung Laba Bersih (Omset - HPP)
        $thisMonthProfit = $thisMonthRevenue - $thisMonthHpp;

        // 5. Hitung Total Nilai Aset yang mengendap di gudang saat ini (Real-time dari sisa batch FIFO)
        $currentAssetValuation = StockEntry::where('qty_remaining', '>', 0)
            ->select(DB::raw('SUM(qty_remaining * purchase_price) as total_asset'))
            ->value('total_asset') ?? 0;

        // 6. Opsional: Mengambil data performa 5 produk terlaris bulan ini untuk grafik/list dashboard
        $topProducts = DB::table('stock_mutations')
            ->join('products', 'products.id', '=', 'stock_mutations.product_id')
            ->where('stock_mutations.type', 'out')
            ->where('stock_mutations.category', 'sale')
            ->whereBetween('stock_mutations.created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
            ->select('products.name', DB::raw('SUM(stock_mutations.qty) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // $notification = [
        //     'lowStock' => $this->checkLowStock(), // Fungsi untuk memeriksa stok rendah
        //     'pendingOrders' => $this->checkPendingOrders(), // Fungsi untuk memeriksa pesanan yang belum selesai
        //     'reminderPayments' => $this->checkPaymentReminders(), // Fungsi untuk memeriksa pengingat pembayaran
        // ];

        // Kirim semua variabel ringkasan ke file view dashboard
        return view('admin.dashboard', compact(
            'thisMonthRevenue', 
            'thisMonthHpp', 
            'thisMonthProfit', 
            'currentAssetValuation',
            'topProducts'
        ));
    }

    public function checkLowStock()
    {
        return Carbon::parse('2026-06-30')->format('Y-m-d 23:59:59');
    }

    private function checkPendingOrders()
    {
        // Logika untuk memeriksa pesanan yang belum selesai
        return DB::table('orders')
            ->where('status', 'pending')
            ->count();
    }

    private function checkPaymentReminders()
    {
        // Logika untuk memeriksa pengingat pembayaran yang belum dibayar
        return DB::table('orders')
            ->join('term_of_payments', 'orders.id', '=', 'term_of_payments.order_id')
            ->where('status', 'TOP')
            ->where('payment_due_date', '<=', Carbon::now())
            ->count();
    }
}