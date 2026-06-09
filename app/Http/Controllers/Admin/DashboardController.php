<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\v2\StockEntry;
use App\Models\v2\StockMutation;
use App\Models\Order;
use App\Models\Tank;
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

        // 1. Amankan SELURUH query agregat berat ke dalam satu block Cache tunggal
        // Menggunakan key yang unik per tenant/user (jika multi-tenant) atau per menit/jam
        $dashboardData = Cache::remember('dashboard_analytics_v2_' . $startMonth, 300, function () use ($startMonth, $endMonth) {
            
            $revenue = Order::whereIn('status', ['completed', 'pending'])
                ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
                ->sum('total_price') ?? 0;

            $hpp = StockMutation::where('type', 'out')
                ->where('category', 'sale')
                ->whereBetween('created_at', [$startMonth . ' 00:00:00', $endMonth . ' 23:59:59'])
                ->select(DB::raw('SUM(qty * price) as total_hpp'))
                ->value('total_hpp') ?? 0;

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

            // Masukkan valuasi aset ke dalam cache karena nilainya tidak perlu realtime per detik
            $assetValuation = StockEntry::where('qty_remaining', '>', 0)
                ->select(DB::raw('SUM(qty_remaining * purchase_price) as total_asset'))
                ->value('total_asset') ?? 0;

            return [
                'revenue' => $revenue,
                'hpp' => $hpp,
                'topProducts' => $topProducts,
                'assetValuation' => $assetValuation
            ];
        });

        $thisMonthRevenue = $dashboardData['revenue'];
        $thisMonthHpp = $dashboardData['hpp'];
        $thisMonthProfit = $thisMonthRevenue - $thisMonthHpp;
        $currentAssetValuation = $dashboardData['assetValuation'];
        $topProducts = $dashboardData['topProducts'];

        // 2. Query ringan yang wajib real-time (notifikasi / alert dashboard)
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
        // OPTIMASI: Jika di blade Anda menampilkan nama produk dari tanki tersebut, 
        // pastikan menambahkan eager loading ->with('product') di sini agar tidak memicu N+1 Query!
        return Tank::with('product') // Sesuaikan nama relasi produk di model Tank Anda
                    ->whereRaw('current_volume / capacity < 0.3')
                    ->where('status', 'active')
                    ->select('id', 'name', 'product_id', 'current_volume', 'capacity') // Ambil Fk 'product_id'
                    ->get();
    }

    private function checkPendingOrders()
    {
        return Order::where('status', 'pending')->count();
    }

    private function checkOverduePaymentReminders()
    {
        return Order::join('term_of_payments', 'orders.id', '=', 'term_of_payments.order_id')
                    ->where('orders.status', 'pending')
                    ->where('term_of_payments.payment_due_date', '<=', Carbon::now())
                    ->count();
    }

    public function sendOverdueReminders()
    {
        $sent = 0;
        
        // chunk(50) sudah sangat bagus untuk mencegah penumpukan data model di RAM
        OrderPaymentSchedule::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now())
            ->with(['order.customer.contact']) // Sempurnakan eager loading ke sub-relasi terdalam
            ->chunk(50, function ($schedules) use (&$sent) {
                $wa = new WhatsAppService();
                foreach ($schedules as $schedule) {
                    $order = $schedule->order;
                    // Amankan pengecekan nomor WA agar tidak error null pointer
                    $waNumber = $order->customer->wa_number ?? $order->customer->contact->wa_id ?? null;
                    
                    if ($order && $order->customer && $waNumber) {
                        $wa->sendText($waNumber, "Pengingat Pembayaran...");
                        $schedule->update(['status' => 'overdue']);
                        $sent++;
                    }
                }
            });

        return response()->json(['sent' => $sent]);
    }
}
