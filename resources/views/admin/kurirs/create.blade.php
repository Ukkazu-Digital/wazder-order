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
    .form-container-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.01);
    }
    .form-label {
        font-size: 13px;
        color: #475569;
        letter-spacing: 0.3px;
    }
    .form-control, .form-select {
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.2s ease-in-out;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .input-group-text-custom {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 10px 0 0 10px;
        color: #64748b;
    }
    .has-icon-group .form-control {
        border-radius: 0 10px 10px 0;
    }
</style>

<div class="container py-4">
    <!-- Header Page Navigasi -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.kurirs.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold me-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-0">Tambah Kurir Baru</h2>
            <p class="text-muted small mb-0">Dafrarkan personil kurir baru ke dalam sistem operasional logistik.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-10 col-md-12">
            <!-- Form Card -->
            <div class="card form-container-card border-0">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.kurirs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Sesi Unggah Foto -->
                        <div class="mb-4">
                            <label for="photo" class="form-label fw-bold mb-2">Foto Profil Kurir</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light d-flex align-items-center justify-content-center rounded-3 border text-muted" style="width: 100px; height: 100px; border-style: dashed !important;">
                                    <i class="bi bi-camera fs-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                    <div class="form-text text-muted small mt-1">Format berkas yang didukung: <strong>JPEG, PNG</strong> (Maks. 2MB).</div>
                                </div>
                            </div>
                            @error('photo')
                                <div class="invalid-feedback d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="text-black-50 my-4 opacity-10">

                        <!-- Input Data Form -->
                        <div class="row g-4">
                            <!-- Nama Lengkap -->
                            <div class="col-md-12">
                                <label for="name" class="form-label fw-bold">Nama Lengkap Kurir <span class="text-danger">*</span></label>
                                <div class="input-group has-icon-group">
                                    <span class="input-group-text input-group-text-custom"><i class="bi bi-person-vcard"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap kurir" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- No. Handphone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-bold">Nomor Handphone Aktif <span class="text-danger">*</span></label>
                                <div class="input-group has-icon-group">
                                    <span class="input-group-text input-group-text-custom"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" required>
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Plat Nomor Kendaraan -->
                            <div class="col-md-6">
                                <label for="plate_number" class="form-label fw-bold">Plat Nomor Kendaraan <span class="text-danger">*</span></label>
                                <div class="input-group has-icon-group">
                                    <span class="input-group-text input-group-text-custom"><i class="bi bi-car-front"></i></span>
                                    <input type="text" class="form-control text-uppercase @error('plate_number') is-invalid @enderror" id="plate_number" name="plate_number" value="{{ old('plate_number') }}" placeholder="Contoh: B 1234 ABC" required>
                                </div>
                                @error('plate_number')
                                    <div class="invalid-feedback d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Operasional -->
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-bold">Status Awal Operasional <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">-- Pilih Status Ketersediaan --</option>
                                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif (Langsung Tersedia untuk Pengiriman)</option>
                                    <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif (Off-Duty / Cadangan)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tombol Aksi Simpan / Batal -->
                        <div class="d-flex gap-2 mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                                <i class="bi bi-plus-circle me-2"></i>Daftarkan Kurir
                            </button>
                            <a href="{{ route('admin.kurirs.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-secondary border fw-semibold">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection