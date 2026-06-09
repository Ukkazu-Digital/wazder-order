@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">{{ $tank->name }}</h2>
        <a href="{{ route('tanks.index') }}" class="btn btn-sm btn-outline-secondary">
            Kembali ke Daftar
        </a>
    </div>

    @if ($lowStock->isNotEmpty())
        <div class="alert alert-danger mb-4">
            <strong>PERINGATAN STOK RENDAH!</strong> Tangki ini hanya tersisa {{ number_format($tank->volume_percentage, 2) }}% volume.
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Informasi Tangki</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th>Kapasitas</th><td>{{ number_format($tank->capacity, 2) }} {{ $tank->type }}</td></tr>
                        <tr><th>Volume Saat Ini</th><td>{{ number_format($tank->current_volume, 2) }} {{ $tank->type }}</td></tr>
                        <tr>
                            <th>Ketersediaan</th>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar @if ($tank->isLowStock) bg-danger @elseif ($tank->volume_percentage < 50) bg-warning @else bg-success @endif" 
                                         role="progressbar" style="width: {{ $tank->volume_percentage }}%">
                                        {{ number_format($tank->volume_percentage, 2) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr><th>Lokasi</th><td>{{ $tank->location }}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-secondary">{{ ucfirst($tank->status) }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Log Pengisian Tangki (terbaru)</div>
                <div class="card-body">
                    @if ($tank->logs->isEmpty())
                        <p class="text-center text-muted my-3">Belum ada catatan log.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($tank->logs->take(5) as $log)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Volume: {{ number_format($log->water_level, 2) }} {{ $tank->type }}</div>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
