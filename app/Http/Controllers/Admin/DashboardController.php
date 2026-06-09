<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\StockEntry;
use App\Models\v2\StockMutation;
use App\Models\v2\Product;
use App\Models\Tank;
use App\Models\Order;
use App\Models\OrderPaymentSchedule;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $startMonth = Carbon::now()->startOfMonth()->toDateString();
        $endMonth = Carbon::now()->endOfMonth()->toDateString();

        // Menggunakan Cache untuk data yang tidak perlu real-time setiap detik
        // Cache akan expire dalam 10 menit
        $data = Cache::remember('dashboard_data_' . $startMonth, 600, function () use ($startMonth, $endMonth) {
            return [
                'revenue' => Order::whereIn('status', ['completed', 'pending'])
                    ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
                    ->sum('total_price') ?? 0,

                'hpp' => StockMutation::where('type', 'out')
                    ->where('category', 'sale')
                    ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
                    ->select(DB::raw('SUM(qty * price) as total_hpp'))
                    ->value('total_hpp') ?? 0,

                'topProducts' => DB::table('stock_mutations')
                    ->join('products', 'products.id', '=', 'stock_mutations.product_id')
                    ->where('stock_mutations.type', 'out')
                    ->where('stock_mutations.category', 'sale')
                    ->whereBetween('stock_mutations.created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
                    ->select('products.name', DB::raw('SUM(stock_mutations.qty) as total_qty'))
                    ->groupBy('products.id', 'products.name')
                    ->orderBy('total_qty', 'desc')
                    ->take(5)
                    ->get()
            ];
        });

        $thisMonthRevenue = $data['revenue'];
        $thisMonthHpp = $data['hpp'];
        $thisMonthProfit = $thisMonthRevenue - $thisMonthHpp;

        $currentAssetValuation = StockEntry::where('qty_remaining', '>', 0)
            ->select(DB::raw('SUM(qty_remaining * purchase_price) as total_asset'))
            ->value('total_asset') ?? 0;

        $topProducts = $data['topProducts'];
        $lowStockTanks = $this->checkLowStockTanks();
        $pendingOrdersCount = $this->checkPendingOrders();
        $overduePaymentReminders = $this->checkOverduePaymentReminders();

        return view('admin.dashboard', compact(
            'thisMonthRevenue', 'thisMonthHpp', 'thisMonthProfit', 
            'currentAssetValuation', 'topProducts', 'lowStockTanks',
            'pendingOrdersCount', 'overduePaymentReminders'
        ));
    }

    public function checkLowStockTanks()
    {
        // Ambil hanya kolom yang dibutuhkan saja untuk menghemat RAM
        return Tank::whereRaw('current_volume / capacity < 0.3')
                    ->where('status', 'active')
                    ->select('id', 'name', 'current_volume', 'capacity')
                    ->get();
    }

    private function checkPendingOrders()
    {
        return Order::where('status', 'pending')->count();
    }

    private function checkOverduePaymentReminders()
    {
        return Order::join('term_of_payments', 'orders.id', '=', 'term_of_payments.order_id')
        ->where('orders.status', 'pending') // <--- Berikan prefix 'orders.' di sini
        ->where('term_of_payments.payment_due_date', '<=', Carbon::now())
        ->count();
    }

    public function sendOverdueReminders()
    {
        $sent = 0;
        // Gunakan chunk untuk mencegah Memory Exhaustion saat mengirim banyak WA
        OrderPaymentSchedule::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now())
            ->with('order.customer')
            ->chunk(50, function ($schedules) use (&$sent) {
                $wa = new WhatsAppService();
                foreach ($schedules as $schedule) {
                    $order = $schedule->order;
                    if ($order && $order->customer && $order->customer->wa_number) {
                        $wa->sendText($order->customer->wa_number, "Pengingat Pembayaran...");
                        $schedule->update(['status' => 'overdue']);
                        $sent++;
                    }
                }
            });

        return response()->json(['sent' => $sent]);
    }
}