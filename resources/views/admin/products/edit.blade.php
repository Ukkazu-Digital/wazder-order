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
    .form-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.01);
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 14px;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    .input-group-text-custom {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: #64748b;
        font-weight: 600;
        font-size: 14px;
    }
    .has-prefix {
        border-radius: 0 10px 10px 0 !important;
    }
</style>

<div class="container py-4">
    <!-- Header Page Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Edit Produk</h2>
            <p class="text-muted small mb-0">Perbarui detail spesifikasi, harga, maupun status inventaris komoditas.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
            <i class="bi bi-arrow-left me-2"></i>Batal & Kembali
        </a>
    </div>

    <!-- Alert Validasi Utama Global (Opsional, namun dipercantik jika dipertahankan) -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1"></i>
                <div>
                    <span class="fw-bold">Periksa kembali data input Anda:</span>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Edit Utama -->
    <div class="card form-card border-0">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Nama Produk -->
                    <div class="col-md-8 mb-4">
                        <label Lifor="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" placeholder="Contoh: Sepatu Olahraga Running Pro" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SKU -->
                    <div class="col-md-4 mb-4">
                        <label for="sku" class="form-label">SKU (Stock Keeping Unit) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="CONTOH-SKU-001" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Harga Jual -->
                    <div class="col-md-6 mb-4">
                        <label for="price" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text input-group-text-custom">Rp</span>
                            <input type="number" class="form-control has-prefix @error('price') is-invalid @enderror" id="price" name="price" step="0.01" value="{{ old('price', $product->price) }}" placeholder="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Stok Gudang -->
                    <div class="col-md-6 mb-4">
                        <label for="stock" class="form-label">Stok Fisik <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Kategori -->
                    <div class="col-md-6 mb-4">
                        <label for="category" class="form-label">Kategori</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $product->category) }}" placeholder="Masukkan nama kategori">
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status Distribusi -->
                    <div class="col-md-6 mb-4">
                        <label for="status" class="form-label">Status Penjualan <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="" disabled>-- Pilih Status --</option>
                            <option value="active" {{ old('status', $product->status) === 'active' || old('status', $product->status) === 'aktif' ? 'selected' : '' }}>Aktif (Tampilkan di Katalog)</option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' || old('status', $product->status) === 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif (Arsip/Sembunyikan)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Deskripsi Produk -->
                <div class="mb-4">
                    <label for="description" class="form-label">Deskripsi Lengkap Produk</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Tuliskan detail deskripsi spesifikasi produk secara transparan...">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="text-secondary opacity-25 my-4">

                <!-- Tombol Submit Aksi -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light px-4 py-2 rounded-3 fw-semibold border text-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-cloud-arrow-up me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection