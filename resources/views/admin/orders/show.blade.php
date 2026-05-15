@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Order</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mb-3">Kembali</a>
    <div class="card mb-3">
        <div class="card-body">
            <h5>Kode Order: {{ $order->order_code }}</h5>
            <p>Status: <b>{{ $order->status }}</b></p>
            <p>Customer: {{ $order->customer->customers_name ?? '-' }}</p>
            <p>Total: Rp{{ number_format($order->total_price,0,',','.') }}</p>
            <p>Tanggal: {{ $order->created_at->format('d-m-Y H:i') }}</p>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="form-inline">
                @csrf
                <div class="form-group">
                    <label for="status">Update Status: </label>
                    <select name="status" id="status" class="form-control mx-2">
                        <option value="pending" @if($order->status=='pending') selected @endif>Pending</option>
                        <option value="paid" @if($order->status=='paid') selected @endif>Paid</option>
                        <option value="shipped" @if($order->status=='shipped') selected @endif>Shipped</option>
                        <option value="completed" @if($order->status=='completed') selected @endif>Completed</option>
                        <option value="cancelled" @if($order->status=='cancelled') selected @endif>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
    <h4>Detail Produk</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $detail)
            <tr>
                <td>{{ $detail->product->name ?? '-' }}</td>
                <td>{{ $detail->qty }}</td>
                <td>Rp{{ number_format($detail->buy_price,0,',','.') }}</td>
                <td>Rp{{ number_format($detail->subtotal,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
