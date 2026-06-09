<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Services\WhatsAppService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index($encoded_trx = null)
    {
        $orderCode = $encoded_trx ? base64_decode($encoded_trx) : null;
        $orderLink = null;
        $customer = null;
        $products = Product::where('is_active', true)->get();

        if ($orderCode) {
            $orderLink = DB::table('link_order')
                ->where('kd_trx', $orderCode)
                ->where('expired_at', '>', Carbon::now())
                ->first();

            if (!$orderLink) {
                return redirect()->route('order.failed', ['msg' => 'Link pesanan tidak valid atau sudah kadaluarsa.']);
            }

            $contact = DB::table('contacts')->where('wa_id', $orderLink->wa_id)->first();
            if ($contact && $contact->customer_id) {
                $customer = Customer::find($contact->customer_id);
            }
        }

        return view('order.index', compact('orderCode', 'orderLink', 'customer', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string|unique:orders,order_code',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'product_id.*' => 'required|exists:products,id',
            'quantity.*' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cash,transfer,qris',
            'notes' => 'nullable|string|max:1000',
            'wa_id' => 'required|string', // Hidden field for WhatsApp ID
        ]);

        DB::beginTransaction();
        try {
            // Find or create customer
            $contact = Contact::where('wa_id', $request->wa_id)->first();
            $customer = null;

            if ($contact && $contact->customer_id) {
                $customer = Customer::find($contact->customer_id);
                $customer->update([
                    'customers_name' => $request->customer_name,
                    'customers_phone' => $request->customer_phone,
                    'customers_address' => $request->address,
                ]);
            } else {
                $customer = Customer::create([
                    'customers_name' => $request->customer_name,
                    'customers_phone' => $request->customer_phone,
                    'customers_address' => $request->address,
                ]);
                if ($contact) {
                    $contact->update(['customer_id' => $customer->id]);
                } else {
                    Contact::create([
                        'wa_id' => $request->wa_id,
                        'customer_id' => $customer->id,
                        'last_message_at' => now(),
                    ]);
                }
            }

            $totalPrice = 0;
            $productsData = [];
            foreach ($request->product_id as $key => $productId) {
                $product = Product::lockForUpdate()->find($productId);
                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan: {$productId}");
                }
                $quantity = $request->quantity[$key];
                $productsData[$product->id] = ['quantity' => $quantity, 'price' => $product->price];
                $totalPrice += ($product->price * $quantity);

                // Handle stock (FIFO logic for now, will integrate generic storage/tank later)
                // For now, assume simple decrement for non-tank products
                if (!$product->is_tank_product) {
                    // Implement FIFO for general products if needed
                    // For now, just decrement stock from a generic product stock if any
                }
            }

            $order = Order::create([
                'order_code' => $request->order_code,
                'customer_id' => $customer->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'order_type' => 'standard', // Default, will be dynamic later
            ]);

            $order->products()->attach($productsData);

            // Invalidate order link after use
            DB::table('link_order')
                ->where('kd_trx', $request->order_code)
                ->update(['expired_at' => Carbon::now()]);

            DB::commit();

            // Send WhatsApp notification
            $this->whatsapp->sendText($request->wa_id, "Pesanan Anda #{$order->order_code} telah berhasil dibuat. Total: Rp " . number_format($totalPrice, 0, ',', '.') . ". Kami akan segera memprosesnya.");

            return redirect()->route('order.success', $order->order_code);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order Store Failed: " . $e->getMessage(), ['request' => $request->all(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('order.failed', ['msg' => $e->getMessage()]);
        }
    }

    public function track($encoded_trx)
    {
        $orderCode = base64_decode($encoded_trx);
        $order = Order::with('customer', 'products', 'paymentSchedules')
            ->where('order_code', $orderCode)
            ->firstOrFail();

        return view('order.track', compact('order'));
    }

    public function success($orderCode)
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();
        return view('order.success', compact('order'));
    }

    public function failed(Request $request)
    {
        $msg = $request->query('msg', 'Terjadi kesalahan saat memproses pesanan Anda.');
        return view('order.failed', compact('msg'));
    }

    // Private helper methods for WhatsApp, now handled by WhatsAppService
    // private function sendWhatsAppNotification($to, $message)
    // {
    //     $payload = [
    //         'messaging_product' => 'whatsapp',
    //         'to' => $to,
    //         'type' => 'text',
    //         'text' => ['body' => $message]
    //     ];
    //     Http::withToken(env('WHATSAPP_TOKEN'))->post("https://graph.facebook.com/v15.0/" . env('WHATSAPP_PHONE_NUMBER_ID') . "/messages", $payload);
    // }
}
