@extends('layouts.app')

@section('content')
<!-- Import Font Modern & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8f9fa;
    }
    .card-table-wrapper {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        padding: 24px;
    }
    /* Style DataTables Setup */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 12px;
        font-size: 14px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        padding: 14px 16px;
    }
    .table tbody td {
        padding: 16px;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }
    .avatar-initial {
        width: 38px;
        height: 38px;
        background-color: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 14px;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }
</style>

<div class="container py-4">
    <!-- Header Page -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">
            Kelola Data Pelanggan
        </h2>
        <p class="text-muted small mb-0">Pantau loyalitas data pelanggan, riwayat kuantitas order, serta informasi domisili pengiriman.</p>
    </div>

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content Table Card -->
    <div class="card-table-wrapper">
        <div class="table-responsive">
            <table id="customersTable" class="table align-middle table-hover dt-responsive nowrap w-100 m-0">
                <thead>
                    <tr>
                        <th>Nama Customer</th>
                        <th>Email</th>
                        <th>Alamat Domisili</th>
                        <th>Jumlah Order</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-initial">
                                    {{ strtoupper(substr($customer->customers_name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $customer->customers_name }}</div>
                                    <small class="text-muted font-monospace" style="font-size: 11px;">ID: Cust-{{ $customer->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($customer->email)
                                <span class="text-dark"><i class="bi bi-envelope text-secondary me-1"></i>{{ $customer->email }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-truncate text-secondary" style="max-width: 250px;" title="{{ $customer->address }}">
                                <i class="bi bi-geo-alt text-secondary me-1"></i>{{ $customer->address ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @if($customer->orders->count() > 0)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold" style="font-size: 13px;">
                                    <i class="bi bi-bag-check me-1"></i>{{ $customer->orders->count() }} Transaksi
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 fw-normal" style="font-size: 13px;">
                                    Belum Belanja
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-info btn-action" title="Detail Profil">
                                    <i class="bi bi-person-badge"></i>
                                </a>
                                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action" title="Hapus Akun" onclick="return confirm('Apakah Anda yakin ingin menghapus data customer ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-people fs-2 d-block text-black-50 mb-2"></i>
                            Belum ada data customer terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                "sPrevious": "<i class='bi bi-chevron-left'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada customer terdaftar di database"
            },
            pageLength: 20,
            columnDefs: [
                { orderable: false, targets: 4 } // Mematikan sorting pada kolom aksi
            ]
        });
    });
</script>
@endpush