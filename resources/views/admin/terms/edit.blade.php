@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">{{ isset($term) ? 'Edit Term Of Payment' : 'Tambah Term Of Payment Baru' }}</h2>
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

    <form action="{{ isset($term) ? route('terms.update', $term) : route('terms.store') }}" method="POST">
        @csrf
        @if (isset($term))
            @method('PUT')
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Term (contoh: COD, Net 7, Net 15)</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" 
                               value="{{ old('name', $term->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="days_due" class="form-label">Batas Hari Pembayaran (Jatuh Tempo)</label>
                        <input type="number" class="form-control @error('days_due') is-invalid @enderror" 
                               id="days_due" name="days_due" min="0"
                               value="{{ old('days_due', $term->days_due ?? '0') }}" required>
                        @error('days_due')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Deskripsi Singkat (opsional)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $term->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Term</button>
    </form>
</div>
@endsection
