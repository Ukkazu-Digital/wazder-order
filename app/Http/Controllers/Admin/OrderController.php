<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\TermOfPayment;
use App\Models\OrderPaymentSchedule;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $orders = Order::with('customer', 'paymentTerm')
            ->tenant()
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('customer', 'products', 'paymentSchedules', 'customer.contact');
        return view('admin.orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        $order->load('customer.contact');
        $customer = $order->customer;
        $contact = $customer ? $customer->contact : null;
        
        if (!$contact || !$contact->wa_id) {
            return back()->with('error', 'Nomor WhatsApp customer tidak ditemukan atau belum terdaftar.');
        }

        $data = [
            'order_code' => $order->order_code,
            'total' => number_format($order->total_price, 0, ',', '.'),
            'link' => url('/order/' . base64_encode($order->order_code))
        ];

        $result = $this->whatsapp->sendInvoice($contact->wa_id, $data['order_code'], $data['total'], $data['link']);

        if ($result['status'] === 'success') {
            // Update order status?
            $order->status = 'processing'; // Or 'sent_invoice'
            $order->save();
            return back()->with('success', 'Invoice berhasil dikirim via WhatsApp.');
        } else {
            return back()->with('error', 'Gagal mengirim invoice via WhatsApp: ' . ($result['message'] ?? 'Unknown error'));
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,completed',
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Send WA notification if status changed and WA is enabled
        if ($order->customer && $order->customer->contact && $order->customer->contact->wa_id && $oldStatus !== $request->status) {
            $this->whatsapp->sendStatusUpdate($order->customer->contact->wa_id, $order->order_code, $request->status);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        // Delete related payment schedules first
        OrderPaymentSchedule::where('order_id', $order->id)->delete();
        $order->products()->detach(); // If many-to-many relationship with products
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    // --- Helper method for sending invoice, now using WhatsAppService ---
    // This was the direct Meta API call, now it should be handled by the service.
    // This method might be removed or kept as fallback if WhatsAppService fails.
    // private function executeSendInvoice(Order $order)
    // {
    //     $contact = $order->customer->contact ?? null;
    //     if (!$contact || !$contact->wa_id) {
    //         return ['status' => 'error', 'message' => 'Customer WA ID not found.'];
    //     }

    //     $payload = [
    //         'messaging_product' => 'whatsapp',
    //         'to' => $contact->wa_id,
    //         'type' => 'text',
    //         'text' => ['body' => 
    //             "\nInvoice Pesanan Anda:\n"
    //             . "Kode: *" . $order->order_code . "*\n"
    //             . "Total: *" . number_format($order->total_price, 0, ',', '.') . "*\n\n"
    //             . "Silakan klik link berikut untuk detail:\n" . url('/order/' . base64_encode($order->order_code)) . "\n\n"
    //             . "Terima kasih telah berbelanja!"
    //         ]
    //     ];

    //     $response = Http::withToken($this->token)->post($this->apiUrl, $payload);

    //     if ($response->successful()) {
    //         Log::info("Invoice sent successfully via WA to " . $contact->wa_id);
    //         return ['status' => 'success'];
    //     } else {
    //         Log::error("Failed to send invoice via WA: " . $response->body());
    //         return ['status' => 'error', 'message' => $response->body()];
    //     }
    // }
}
