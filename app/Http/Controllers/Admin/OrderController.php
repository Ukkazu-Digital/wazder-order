<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);
        $order->status = $request->status;
        if ($order->save()) {
            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'status' => $request->status,
                'note' => 'Pesanan berubah status menjadi ' . $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('admin.orders.index')->with('success', 'Status updated!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted!');
    }
}
