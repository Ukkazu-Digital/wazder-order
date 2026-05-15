@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Manajemen Orderan</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>Kode Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="fw-semibold">{{ $order->order_code }}</td>
                    <td>{{ $order->customer->customers_name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $order->status == 'pending' ? 'secondary' : ($order->status == 'paid' ? 'info' : ($order->status == 'shipped' ? 'warning' : 'success')) }} text-dark">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>Rp{{ number_format($order->total_price,0,',','.') }}</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-sm me-1">Detail</a>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus order?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
