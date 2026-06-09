@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">{{ isset($tank) ? 'Edit Tangki' : 'Tambah Tangki Baru' }}</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($tank) ? route('tanks.update', $tank) : route('tanks.store') }}" method="POST">
        @csrf
        @if (isset($tank))
            @method('PUT')
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Tangki</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" 
                               value="{{ old('name', $tank->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Tipe Tangki</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="L" {{ old('type', $tank->type ?? '') == 'L' ? 'selected' : '' }}>Liter</option>
                            <option value="Galon" {{ old('type', $tank->type ?? '') == 'Galon' ? 'selected' : '' }}>Galon</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="capacity" class="form-label">Kapasitas</label>
                        <input type="number" step="0.01" class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" name="capacity" 
                               value="{{ old('capacity', $tank->capacity ?? '') }}" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="current_volume" class="form-label">Volume Awal</label>
                        <input type="number" step="0.01" class="form-control @error('current_volume') is-invalid @enderror" 
                               id="current_volume" name="current_volume" 
                               value="{{ old('current_volume', $tank->current_volume ?? '0') }}">
                        @error('current_volume')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label">Lokasi / Deskripsi</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" 
                               value="{{ old('location', $tank->location ?? '') }}">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="latitude" class="form-label">Latitude (opsional)</label>
                        <input type="number" step="0.000001" class="form-control @error('latitude') is-invalid @enderror" 
                               id="latitude" name="latitude" 
                               value="{{ old('latitude', $tank->latitude ?? '') }}">
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label">Longitude (opsional)</label>
                        <input type="number" step="0.000001" class="form-control @error('longitude') is-invalid @enderror" 
                               id="longitude" name="longitude" 
                               value="{{ old('longitude', $tank->longitude ?? '') }}">
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', $tank->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="maintenance" {{ old('status', $tank->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="inactive" {{ old('status', $tank->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Pelanggan (opsional)</label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                            <option value="">-- Pilih Pelanggan --</option>
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Tangki</button>
    </form>
</div>
@endsection
