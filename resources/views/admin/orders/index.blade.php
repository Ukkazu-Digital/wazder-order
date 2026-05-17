@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Manajemen Orderan</h1>
    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table id="ordersTable" class="table align-middle table-hover dt-responsive nowrap">
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
                @forelse($orders as $order)
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
                        @if (in_array($order->status, ['paid', 'shipped', 'completed']))
                        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-outline-secondary btn-sm me-1">Struk</a>
                        @endif
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus order?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada order</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            language: {
                "sSearch": "Cari:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada order"
            },
            pageLength: 20,
            order: [[4, 'desc']],
            columnDefs: [
                { orderable: false, targets: 5 }
            ]
        });
    });
</script>
@endpush
