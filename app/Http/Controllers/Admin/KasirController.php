<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Kurir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    /**
     * Display list of kasir orders (created via POS system)
     */
    public function index()
    {
        // Get orders created via kasir source, ordered by latest
        $orders = Order::where('source', 'kasir')->latest()->get();
        return view('admin.kasir.index', compact('orders'));
    }

    /**
     * Show form to create new order via kasir
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('is_active', 1)->get();
        $kurirs = Kurir::where('status', 'Aktif')->get();
        $transaction_id = 'ORD-' . strtoupper(Str::random(6)) . '-' . date('YmdHis');
        return view('admin.kasir.create', compact('customers', 'products', 'kurirs', 'transaction_id'));
    }

    /**
     * Store new order from kasir
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required', // allow 'new' value
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        // additional validation when creating new customer
        if ($request->customer_id === 'new') {
            $request->validate([
                'new_customer_name' => 'required|string|max:255',
                'new_customer_address' => 'nullable|string',
                'new_customer_phone' => 'nullable|string|max:20',
            ]);
        }

        try {
            // Generate order code
            $orderCode = 'ORD-' . strtoupper(Str::random(6)) . '-' . date('YmdHis');

            // Calculate total price
            $totalPrice = 0;
            $productsList = $request->products;

            foreach ($productsList as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->price * $item['qty'];
                $totalPrice += $subtotal;
            }

            // Create order with kasir source
            // Determine or create customer
            if ($request->customer_id === 'new') {
                $customer = Customer::create([
                    'customers_name' => $request->new_customer_name,
                    'address' => $request->new_customer_address ?? null,
                    'customers_wa_id' => $request->new_customer_phone ?? null,
                ]);
            } else {
                $customer = Customer::find($request->customer_id);
            }

            // Create order with kasir source
            $orderData = [
                'order_code' => $orderCode,
                'customer_id' => $customer->id,
                'total_price' => $totalPrice,
                'status' => $request->status,
                'source' => 'kasir',
                'notes' => $request->notes,
            ];

            // allow kurir assignment if provided (when status = shipped)
            if ($request->has('kurir_id') && $request->kurir_id) {
                $orderData['kurir_id'] = $request->kurir_id;
            }

            $order = Order::create($orderData);

            // Create order details
            foreach ($productsList as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = $product->price * $item['qty'];

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'buy_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock
                $product->stock -= $item['qty'];
                $product->save();
            }

            // Create initial order history
            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'status' => $request->status,
                'note' => 'Pesanan dibuat melalui sistem kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('kasir.show', $order)->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        // Make sure it's a kasir order
        if ($order->source !== 'kasir') {
            return redirect()->route('admin.orders.index')->with('error', 'Pesanan tidak ditemukan');
        }

        $kurirs = Kurir::where('status', 'Aktif')->get();
        return view('admin.kasir.show', compact('order', 'kurirs'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,completed',
            'kurir_id' => $request->status === 'shipped' ? 'required|exists:kurirs,id' : 'nullable',
        ]);

        $order->status = $request->status;

        // Assign kurir if status is shipped
        if ($request->status === 'shipped' && $request->kurir_id) {
            $order->kurir_id = $request->kurir_id;
        }

        if ($order->save()) {
            $note = 'Status berubah menjadi ' . $request->status;
            if ($request->status === 'shipped' && $request->kurir_id) {
                $kurir = Kurir::find($request->kurir_id);
                $note .= ' - Kurir: ' . $kurir->name;
            }

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'status' => $request->status,
                'note' => $note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('kasir.show', $order)->with('success', 'Status berhasil diperbarui!');
    }

    /**
     * Delete kasir order
     */
    public function destroy(Order $order)
    {
        if ($order->source !== 'kasir') {
            return redirect()->route('admin.orders.index')->with('error', 'Pesanan tidak ditemukan');
        }

        // Restore stock from order details
        foreach ($order->details as $detail) {
            $product = Product::find($detail->product_id);
            $product->stock += $detail->qty;
            $product->save();
        }

        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus!');
    }
}
