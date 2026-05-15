@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Customer</h1>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mb-3">Kembali</a>
    <div class="card mb-3">
        <div class="card-body">
            <h5>Nama: {{ $customer->customers_name }}</h5>
            <p>Email: {{ $customer->email ?? '-' }}</p>
            <p>Alamat: {{ $customer->address ?? '-' }}</p>
            <p>Jumlah Order: {{ $customer->orders->count() }}</p>
        </div>
    </div>
    <h4>Daftar Order</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode Order</th>
                <th>Status</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer->orders as $order)
            <tr>
                <td>{{ $order->order_code }}</td>
                <td>{{ $order->status }}</td>
                <td>Rp{{ number_format($order->total_price,0,',','.') }}</td>
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
