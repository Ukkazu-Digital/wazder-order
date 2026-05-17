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
    /* Style DataTables Setup Custom */
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
        font-size: 11px;
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
    .avatar-wrapper {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .avatar-placeholder {
        width: 44px;
        height: 44px;
        background-color: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
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
    .plate-badge {
        font-family: var(--bs-font-monospace);
        background-color: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="container py-4">
    <!-- Header Page Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Manajemen Kurir</h2>
            <p class="text-muted small mb-0">Kelola ketersediaan armada, nomor kontak operasional, dan pantau utilitas performa pengiriman.</p>
        </div>
        <a href="{{ route('admin.kurirs.create') }}" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Tambah Kurir
        </a>
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
            <table id="kurirsTable" class="table align-middle table-hover dt-responsive nowrap w-100 m-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Profil</th>
                        <th>Nama Kurir</th>
                        <th>No. Handphone</th>
                        <th>Plat Nomor</th>
                        <th>Status</th>
                        <th>Total Order</th>
                        <th class="text-end" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kurirs as $kurir)
                    <tr>
                        <td>
                            @if($kurir->photo)
                                <img src="{{ asset('storage/kurir/' . $kurir->photo) }}" alt="Foto {{ $kurir->name }}" class="avatar-wrapper">
                            @else
                                <div class="avatar-placeholder">
                                    <i class="bi bi-person text-secondary fs-5"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $kurir->name }}</div>
                            <small class="text-muted small">ID: KR-{{ str_pad($kurir->id, 3, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        <td>
                            <span class="text-secondary">
                                <i class="bi bi-telephone-outbound text-black-50 me-1"></i> {{ $kurir->phone }}
                            </span>
                        </td>
                        <td>
                            @if($kurir->plate_number)
                                <span class="plate-badge">{{ strtoupper($kurir->plate_number) }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if(trim(strtolower($kurir->status)) === 'aktif' || trim(strtolower($kurir->status)) === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 fw-bold" style="font-size: 13px;">
                                <i class="bi bi-truck me-1"></i> {{ $kurir->orders->count() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.kurirs.show', $kurir) }}" class="btn btn-outline-info btn-action" title="Detail Performa">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.kurirs.edit', $kurir) }}" class="btn btn-outline-warning btn-action text-warning-emphasis" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kurirs.destroy', $kurir) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data kurir ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-truck-front fs-2 d-block text-black-50 mb-2"></i>
                            Belum ada data armada kurir terdaftar
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
        $('#kurirsTable').DataTable({
            language: {
                "sSearch": "Cari:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "<i class='bi bi-chevron-left'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada kurir terdaftar di database"
            },
            pageLength: 20,
            columnDefs: [
                { orderable: false, targets: [0, 6] } // Menonaktifkan sorting pada kolom Foto dan Aksi
            ]
        });
    });
</script>
@endpush