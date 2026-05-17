@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.kurirs.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="fw-bold mb-0">Detail Kurir</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            @if($kurir->photo)
                                <img src="{{ asset('storage/kurir/' . $kurir->photo) }}" alt="Foto Kurir" width="200" class="rounded-3 img-fluid">
                            @else
                                <div class="bg-light rounded-3 p-5 d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-person-fill text-muted" style="font-size: 5rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-3">{{ $kurir->name }}</h3>
                            
                            <div class="mb-3">
                                <p class="text-muted mb-1">No HP</p>
                                <p class="fw-semibold">{{ $kurir->phone }}</p>
                            </div>

                            <div class="mb-3">
                                <p class="text-muted mb-1">Plat Nomor</p>
                                <p class="fw-semibold">{{ $kurir->plate_number }}</p>
                            </div>

                            <div class="mb-3">
                                <p class="text-muted mb-1">Status</p>
                                <span class="badge bg-{{ $kurir->status == 'Aktif' ? 'success' : 'danger' }} p-2">
                                    {{ $kurir->status }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <p class="text-muted mb-1">Total Order Terkirim</p>
                                <p class="fw-semibold">{{ $kurir->orders->count() }} pesanan</p>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ route('admin.kurirs.edit', $kurir) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                </a>
                                <form action="{{ route('admin.kurirs.destroy', $kurir) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus kurir?')">
                                        <i class="bi bi-trash me-2"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($kurir->orders->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light p-3">
                        <h5 class="mb-0 fw-semibold">Riwayat Order ({{ $kurir->orders->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Order</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kurir->orders as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_code }}</td>
                                        <td>{{ $order->customer->customers_name ?? '-' }}</td>
                                        <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'pending' ? 'secondary' : ($order->status == 'paid' ? 'info' : ($order->status == 'shipped' ? 'warning' : 'success')) }} text-dark">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
