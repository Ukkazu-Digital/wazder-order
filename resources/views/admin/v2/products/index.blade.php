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
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Kelola Data Produk</h2>
            <p class="text-muted small mb-0">Pantau valuasi aset dan sisa stok riil dari setiap batch penjualan.</p>
        </div>
        <a href="{{ route('admin.v2.products.create') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru
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
            <table id="fifoProductsTable" class="table align-middle table-hover dt-responsive nowrap w-100 m-0">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Total Sisa Stok</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $product->name }}</div>
                        </td>
                        <td class="fw-semibold text-dark text-end font-monospace">
                            Rp{{ number_format($product->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($product->totalStock() > 10)
                                <span class="badge badge-status bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-box-seam me-1"></i>{{ $product->totalStock() }} Pcs
                                </span>
                            @elseif($product->totalStock() <= 10 && $product->totalStock() > 0)
                                <span class="badge badge-status bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Sisa {{ $product->totalStock() }}
                                </span>
                            @else
                                <span class="badge badge-status bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i>Habis
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.v2.products.show', $product->id) }}" class="btn btn-outline-info btn-action" title="Kartu Stok / Ledger">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <a href="{{ route('admin.v2.products.edit', $product->id) }}" class="btn btn-outline-warning btn-action" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.v2.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-action" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-box2 fs-2 d-block text-black-50 mb-2"></i>
                            Belum ada data produk di database gudang.
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
        $('#fifoProductsTable').DataTable({
            language: {
                "sSearch": "Cari:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "<i class='bi bi-chevron-left'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada produk terdaftar di database"
            },
            pageLength: 20,
            columnDefs: [
                { orderable: false, targets: 3 }
            ]
        });
    });
</script>
@endpush