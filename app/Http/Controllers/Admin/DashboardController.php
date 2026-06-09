<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tank;
use App\Models\OrderPaymentSchedule;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // HANYA mengambil alert dan counter ringan menggunakan query agregat count()
        // Tidak ada query SUM(), tidak ada JOIN tabel besar, tidak ada pemrosesan koleksi di memori PHP.
        
        $lowStockTanks = $this->checkLowStockTanks();
        $pendingOrdersCount = $this->checkPendingOrders();
        $overduePaymentReminders = $this->checkOverduePaymentReminders();

        return view('admin.dashboard', compact(
            'lowStockTanks',
            'pendingOrdersCount',
            'overduePaymentReminders'
        ));
    }

    public function checkLowStockTanks()
    {
        // Eager loading 'product' untuk mencegah N+1 query saat rendering di Blade template
        return Tank::with('product')
                    ->whereRaw('current_volume / capacity < 0.3')
                    ->where('status', 'active')
                    ->select('id', 'name', 'product_id', 'current_volume', 'capacity')
                    ->get();
    }

    private function checkPendingOrders()
    {
        // Query COUNT sangat cepat dan aman bagi RAM karena hanya mengembalikan satu angka integer
        return Order::where('status', 'pending')->count();
    }

    private function checkOverduePaymentReminders()
    {
        // Menggunakan DB fluent builder langsung untuk menghitung jumlah piutang jatuh tempo
        return DB::table('orders')
                    ->join('term_of_payments', 'orders.id', '=', 'term_of_payments.order_id')
                    ->where('orders.status', 'pending')
                    ->where('term_of_payments.payment_due_date', '<=', Carbon::now())
                    ->count();
    }

    public function sendOverdueReminders()
    {
        $sent = 0;
        
        // chunk(50) tetap dipertahankan untuk mengamankan RAM saat eksekusi pengiriman massal via WA
        OrderPaymentSchedule::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now())
            ->with(['order.customer.contact'])
            ->chunk(50, function ($schedules) use (&$sent) {
                $wa = new WhatsAppService();
                foreach ($schedules as $schedule) {
                    $order = $schedule->order;
                    if ($order && $order->customer) {
                        $waNumber = $order->customer->wa_number ?? $order->customer->contact->wa_id ?? null;
                        
                        if ($waNumber) {
                            $wa->sendText($waNumber, "Pengingat Pembayaran...");
                            $schedule->update(['status' => 'overdue']);
                            $sent++;
                        }
                    }
                }
            });

        return response()->json(['sent' => $sent]);
    }
}
