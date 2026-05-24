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
    .info-alert-box {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 12px;
        padding: 16px;
        color: #78350f;
        font-size: 13px;
    }
</style>

<div class="container py-4">
    <!-- Cancel & Go Back Navigation -->
    <div class="mb-3">
        <a href="{{ route('admin.v2.products.index') }}" class="text-decoration-none text-secondary fw-semibold small">
            <i class="bi bi-x-circle me-1"></i>Batalkan & Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="form-wrapper">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Perbarui Informasi Produk</h3>
                    <p class="text-muted small">Ubah pengaturan parameter luar produk tanpa mengintervensi record database ledger batch FIFO.</p>
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

                <form action="{{ route('admin.v2.products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary small">Nama Produk *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="form-control rounded-3 p-2.5 fs-6">
                    </div>

                    <div class="mb-4">
                        <label for="selling_price" class="form-label fw-semibold text-secondary small">Harga Jual Baru Retail (Rp) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">Rp</span>
                            <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required min="0" class="form-control rounded-end-3 p-2.5 font-monospace fs-6 border-start-0">
                        </div>
                    </div>

                    <!-- Informational Callout -->
                    <div class="info-alert-box mb-4">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                            <div>
                                <strong class="d-block mb-1">Catatan Akuntansi FIFO:</strong>
                                <span class="opacity-90 leading-relaxed d-block text-secondary">
                                    Pengubahan harga jual di sini hanya berlaku untuk transaksi keluar baru pada kasir. Harga pokok aset/harga beli riil bawaan awal yang mengendap di dalam antrean antrean batch inventori gudang akan tetap dikunci demi menjaga ketepatan kalkulasi HPP (COGS).
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection