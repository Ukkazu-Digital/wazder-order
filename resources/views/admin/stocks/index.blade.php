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
    .table devastation-thead th {
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
        font-size: 13.5px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
    }
</style>

<div class="container py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Batch Stok (FIFO Batches)</h2>
        <p class="text-muted small mb-0">Kelola kulakan barang masuk dan pantau porsi sisa tiap kuota batch persediaan.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stocks.adjustment') }}" class="btn btn-outline-danger px-4 py-2 rounded-3 fw-semibold shadow-sm">
            <i class="bi bi-dash-circle me-2"></i>Stok Keluar / Adjustment
        </a>
        <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Catat Stok Masuk
        </a>
    </div>
</div>

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content Card Wrapper -->
    <div class="card-table-wrapper">
        <div class="table-responsive">
            <table id="fifoStockTable" class="table align-middle table-hover dt-responsive nowrap w-100 m-0">
                <thead class="table devastation-thead">
                    <tr>
                        <th>Kode Batch</th>
                        <th>Nama Produk</th>
                        <th class="text-end">Harga Beli (Modal)</th>
                        <th class="text-center">Kuota Awal</th>
                        <th class="text-center">Sisa Stok Riil</th>
                        <th>Status Antrean</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>
                            <span class="font-monospace text-secondary small bg-light px-2 py-1 rounded border fw-semibold">BATCH-#{{ $entry->id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $entry->product->name }}</div>
                            <small class="text-muted font-monospace" style="font-size: 11px;">Tgl Masuk: {{ $entry->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td class="fw-semibold text-dark text-end font-monospace">
                            Rp{{ number_format($entry->purchase_price, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-monospace">{{ $entry->qty_received }} Pcs</td>
                        <td class="text-center font-monospace fw-bold">
                            {{ $entry->qty_remaining }} Pcs
                        </td>
                        <td>
                            @if($entry->qty_remaining == 0)
                                <span class="badge badge-status bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i>Alokasi Habis
                                </span>
                            @elseif($entry->qty_remaining < $entry->qty_received)
                                <span class="badge badge-status bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    <i class="bi bi-hourglass-split me-1"></i>Terpotong FIFO
                                </span>
                            @else
                                <span class="badge badge-status bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check2-all me-1"></i>Utuh (Aktif)
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#fifoStockTable').DataTable({
            language: {
                "sSearch": "Cari Batch:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ batch",
                "sPrevious": "<i class='bi bi-chevron-left'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada riwayat batch stok kulakan yang terdaftar"
            },
            pageLength: 25,
            order: [[0, "desc"]] // Urutkan dari batch terbaru berdasarkan index DOM/Tanggal
        });
    });
</script>
@endpush