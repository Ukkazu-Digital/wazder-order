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
    .form-wrapper {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        padding: 32px;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .section-divider {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 6px;
        margin-bottom: 20px;
    }
</style>

<div class="container py-4">
    <!-- Back Navigation -->
    <div class="mb-3">
        <a href="{{ route('admin.v2.products.index') }}" class="text-decoration-none text-primary fw-semibold small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Produk
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-wrapper">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Tambah Produk & Data Utama</h3>
                    <p class="text-muted small">Daftarkan produk baru beserta inisiasi batch persediaan perdana Anda.</p>
                </div>

                <!-- Form Validation Errors -->
                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
                        <ul class="mb-0 small fw-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.v2.products.store') }}" method="POST">
                    @csrf

                    <!-- Section 1 -->
                    <div class="section-divider">1. Informasi Dasar Produk</div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary small">Nama Produk *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Aqua Galon 19L" class="form-index form-control rounded-3 p-2.5 fs-6">
                    </div>

                    <div class="mb-4">
                        <label for="selling_price" class="form-label fw-semibold text-secondary small">Harga Jual Retail (Rp) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">Rp</span>
                            <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required min="0" placeholder="0" class="form-control rounded-end-3 p-2.5 font-monospace fs-6 border-start-0">
                        </div>
                    </div>

                    <!-- Section 2 -->
                    <div class="d-flex justify-content-between align-items-center section-divider">
                        <span>2. Inisiasi Stok Perdana (Opsional)</span>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded">Batch Awal</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="qty_received" class="form-label fw-semibold text-secondary small">Jumlah Masuk (Qty)</label>
                            <input type="number" name="qty_received" id="qty_received" value="{{ old('qty_received') }}" min="1" placeholder="Kosongkan jika stok 0" class="form-control rounded-3 p-2.5 fs-6">
                        </div>
                        <div class="col-md-6">
                            <label for="purchase_price" class="form-label fw-semibold text-secondary small">Harga Beli Kulakan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">Rp</span>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" min="0" placeholder="Harga modal per pcs" class="form-control rounded-end-3 p-2.5 font-monospace fs-6 border-start-0">
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-full py-2.5 rounded-3 fw-semibold shadow-sm w-100">
                        <i class="bi bi-check-circle me-2"></i>Simpan Produk Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection