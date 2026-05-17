@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Manajemen Customer</h1>
    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table id="customersTable" class="table align-middle table-hover dt-responsive nowrap">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Jumlah Order</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="fw-semibold">{{ $customer->customers_name }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>{{ $customer->address ?? '-' }}</td>
                    <td>
                        <span class="badge bg-info">{{ $customer->orders->count() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-info btn-sm me-1">Detail</a>
                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus customer?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada customer</td>
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
        $('#customersTable').DataTable({
            language: {
                "sSearch": "Cari:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada customer"
            },
            pageLength: 20,
            columnDefs: [
                { orderable: false, targets: 4 }
            ]
        });
    });
</script>
@endpush
