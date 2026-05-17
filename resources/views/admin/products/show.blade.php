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
    .main-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.01);
    }
    .table-detail th {
        width: 25%;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
        padding: 16px 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-detail td {
        color: #1e293b;
        font-size: 14px;
        padding: 16px 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .side-metric-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
    }
    .font-monospace-custom {
        font-family: var(--bs-font-monospace);
        background-color: #f1f5f9;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
    }
</style>

<div class="container py-4">
    <!-- Header Page Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detail Produk</h2>
            <p class="text-muted small mb-0">Informasi lengkap spesifikasi data barang dan status inventaris.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning px-3 py-2 rounded-3 fw-semibold text-dark">
                <i class="bi bi-pencil-square me-2"></i>Edit Data
            </a>
        </div>
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

    <!-- Main Content Card -->
    <div class="card main-card border-0 p-2">
        <div class="card-body p-4">
            <div class="row g-4">
                
                <!-- SISI KIRI: Tabel Spesifikasi Detil -->
                <div class="col-md-8 border-end-md">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Spesifikasi Produk
                    </h5>
                    
                    <table class="table table-detail table-borderless m-0">
                        <tr>
                            <th>Nama Produk</th>
                            <td class="fw-bold text-dark fs-5">{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>SKU Barang</th>
                            <td>
                                <span class="font-monospace-custom text-dark">{{ $product->sku }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $product->category ?? 'Tanpa Kategori' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>
                                <div class="text-secondary lh-base" style="white-space: pre-line;">
                                    {{ $product->description ?? 'Tidak ada deskripsi produk.' }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Waktu Input</th>
                            <td class="text-muted small">
                                <i class="bi bi-calendar-plus me-1"></i> {{ $product->created_at->format('d M Y, H:i') }} WIB
                            </td>
                        </tr>
                        <tr>
                            <th>Pembaruan Terakhir</th>
                            <td class="text-muted small">
                                <i class="bi bi-clock-history me-1"></i> {{ $product->updated_at->format('d M Y, H:i') }} WIB
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- SISI KANAN: Status Finansial & Gudang -->
                <div class="col-md-4">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="bi bi-activity text-primary me-2"></i> Ringkasan Status
                    </h5>
                    
                    <div class="side-metric-box mb-3">
                        <small class="text-muted d-block uppercase fw-semibold tracking-wider mb-1" style="font-size: 11px;">HARGA JUAL KASIR</small>
                        <h3 class="fw-bold text-primary m-0">Rp{{ number_format($product->price, 0, ',', '.') }}</h3>
                    </div>

                    <div class="side-metric-box mb-3">
                        <small class="text-muted d-block uppercase fw-semibold tracking-wider mb-1" style="font-size: 11px;">PERSEDIAAN GUDANG</small>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <h4 class="fw-bold m-0 text-dark">{{ $product->stock }} <span class="fs-6 fw-normal text-muted">Item</span></h4>
                            
                            @if($product->stock > 10)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aman</span>
                            @elseif($product->stock <= 10 && $product->stock > 0)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Kritis</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Habis</span>
                            @endif
                        </div>
                    </div>

                    <div class="side-metric-box mb-4">
                        <small class="text-muted d-block uppercase fw-semibold tracking-wider mb-1" style="font-size: 11px;">STATUS DISTRIBUSI</small>
                        <div class="mt-2">
                            @if($product->status === 'active' || strtolower($product->status ?? '') === 'aktif')
                                <span class="badge bg-success px-3 py-2 rounded-pill fw-semibold w-100 text-center shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Aktif di Katalog
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill fw-semibold w-100 text-center">
                                    <i class="bi bi-eye-slash me-1"></i> Non-Aktif (Arsip)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Destruktif (Hapus) -->
                    <div class="pt-2">
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="w-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger fw-semibold d-flex align-items-center justify-content-center w-100 text-decoration-none border rounded-3 py-2 bg-white" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari database?')">
                                <i class="bi bi-trash3 me-2"></i> Hapus Produk Ini
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection