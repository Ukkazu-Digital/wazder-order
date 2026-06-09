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

class DashboardController extends Controller
{
    /**
     * Tampilan Utama Halaman Dashboard Admin
     */
    public function index()
    {
        $startMonth = Carbon::now()->startOfMonth()->toDateString();
        $endMonth = Carbon::now()->endOfMonth()->toDateString();

        $thisMonthRevenue = Order::whereIn('status', ['completed', 'pending'])
            ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
            ->sum('total_price') ?? 0;

        $thisMonthHpp = StockMutation::where('type', 'out')
            ->where('category', 'sale')
            ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
            ->select(DB::raw('SUM(qty * price) as total_hpp'))
            ->value('total_hpp') ?? 0;

        $thisMonthProfit = $thisMonthRevenue - $thisMonthHpp;

        $currentAssetValuation = StockEntry::where('qty_remaining', '>', 0)
            ->select(DB::raw('SUM(qty_remaining * purchase_price) as total_asset'))
            ->value('total_asset') ?? 0;

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

        $lowStockTanks = $this->checkLowStockTanks();
        $pendingOrdersCount = $this->checkPendingOrders();
        $overduePaymentReminders = $this->checkOverduePaymentReminders();

        return view('admin.dashboard', compact(
            'thisMonthRevenue', 
            'thisMonthHpp', 
            'thisMonthProfit', 
            'currentAssetValuation',
            'topProducts',
            'lowStockTanks',
            'pendingOrdersCount',
            'overduePaymentReminders'
        ));
    }

    public function checkLowStockTanks()
    {
        return Tank::whereRaw('current_volume / capacity < 0.3')
                    ->where('status', 'active')
                    ->get();
    }

    private function checkPendingOrders()
    {
        return Order::where('status', 'pending')
            ->count();
    }

    private function checkOverduePaymentReminders()
    {
        return OrderPaymentSchedule::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now())
            ->count();
    }

    /**
     * Kirim pengingat WA ke semua payment schedule jatuh tempo.
     * Bisa dipanggil manual atau cron.
     */
    public function sendOverdueReminders()
    {
        $schedules = OrderPaymentSchedule::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now())
            ->with('order.customer')
            ->get();

        $sent = 0;
        foreach ($schedules as $schedule) {
            $order = $schedule->order;
            $customer = $order->customer ?? null;
            if (!$customer || !$customer->wa_number) continue;

            $wa = new WhatsAppService();
            $wa->sendText($customer->wa_number, 
                "🕐 *Pengingat Pembayaran*\n\n"
                . "Pesanan #{$order->order_code}\n"
                . "Jatuh tempo: {$schedule->due_date}\n"
                . "Sisa tagihan: Rp " . number_format($schedule->amount_due, 0, ',', '.') . "\n\n"
                . "Segera lakukan pembayaran untuk menghindari denda. Terima kasih."
            );

            $schedule->update(['status' => 'overdue']);
            $sent++;
        }

        return response()->json(['sent' => $sent]);
    }
}
