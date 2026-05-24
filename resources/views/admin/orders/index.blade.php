@extends('layouts.app')

@section('content')
<!-- Memasukkan font modern & icons agar selaras dengan interface POS Modern -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .order-management-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .font-monospace-id {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .custom-table-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    /* Sinkronisasi style DataTables filter wrapper agar rapi */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        background-color: #f8f9fa !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 4px 8px !important;
    }
</style>

<div class="container py-4 order-management-container">
    
    <!-- Header Terintegrasi Menu Kasir -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="mb-1 fw-bold text-dark"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Manajemen Orderan</h1>
            <p class="text-muted small mb-0">Lacak transaksi masuk, status pembayaran, dan cetak invoice struk belanja.</p>
        </div>
        <a href="{{ route('admin.kasir.create') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Buka POS Kasir
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center rounded-4 border-0 shadow-sm p-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Bungkus Tabel Menggunakan Card Style Ritel Modern -->
    <div class="card bg-white custom-table-card p-3">
        <div class="table-responsive">
            <table id="ordersTable" class="table align-middle table-hover dt-responsive nowrap w-100 mb-0">
                <thead class="table-light text-secondary text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 ps-3">Kode Order</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3">Total</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3 text-center pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <!-- Menampilkan ID Transaksi Format Baru dengan Font Monospace tebal -->
                        <td class="fw-semibold font-monospace-id text-primary fs-6 ps-3">
                            {{ $order->order_code }}
                        </td>
                        
                        <!-- Info Pelanggan -->
                        <td>
                            @if($order->customer)
                                <div class="fw-bold text-dark">{{ $order->customer->customers_name }}</div>
                            @else
                                <span class="text-muted italic small"><i class="bi bi-person me-1"></i>Walk-in Customer</span>
                            @endif
                        </td>
                        
                        <!-- Penataan Warna Badge Status agar Lebih Soft & Elegan -->
                        <td class="text-center">
                            @if($order->status == 'pending')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #fff3e0; color: #ef6c00;">Pending</span>
                            @elseif($order->status == 'paid')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #e8f5e9; color: #2e7d32;">Paid</span>
                            @elseif($order->status == 'shipped')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #e0f7fa; color: #00838f;">Shipped</span>
                            @elseif($order->status == 'completed')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #efebe9; color: #4e342e;">Completed</span>
                            @else
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #ffebee; color: #c62828;">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        
                        <!-- Nilai Total Transaksi -->
                        <td class="fw-bold text-dark">
                            Rp{{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        
                        <!-- Tanggal Dibuat -->
                        <td class="text-secondary small">
                            {{ $order->created_at->format('d-m-Y H:i') }}
                        </td>
                        
                        <!-- Kelompok Tombol Aksi Berbasis Ikon Minimalis -->
                        <td class="text-center pe-3">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light text-primary border rounded-3 px-2 py-1" title="Detail Order">
                                    <i class="bi bi-eye-fill"></i> <span class="d-none d-xl-inline ms-1">Detail</span>
                                </a>
                                
                                @if (in_array($order->status, ['paid', 'shipped', 'completed']))
                                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" title="Cetak Struk">
                                    <i class="bi bi-printer-fill"></i> <span class="d-none d-xl-inline ms-1">Struk</span>
                                </a>
                                @endif
                                
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" onclick="return confirm('Yakin hapus order {{ $order->order_code }}?')" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 opacity-50 d-block mb-2"></i>
                            Belum ada orderan masuk hari ini.
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
        $('#ordersTable').DataTable({
            language: {
                "sSearch": "Cari Nota / Nama:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "<i class='bi bi-chevron-left'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sInfoEmpty": "Menampilkan 0 data",
                "sEmptyTable": "Belum ada order"
            },
            pageLength: 20,
            order: [[4, 'desc']], // Tetap mengurutkan default berdasarkan tanggal terbaru
            columnDefs: [
                { orderable: false, targets: 5 } // Kolom aksi tidak bisa di-sorting
            ]
        });
    });
</script>
@endpush