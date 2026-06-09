@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Tangki</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('tanks.create') }}" class="btn btn-sm btn-outline-secondary">
                Tambah Tangki
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($lowStock->isNotEmpty())
        <div class="alert alert-danger mb-4">
            <strong>PERINGATAN STOK RENDAH!</strong> Berikut tangki dengan volume < 30%:
            <ul class="mb-0">
                @foreach ($lowStock as $tank)
                    <li>
                        {{ $tank->name }}: 
                        <strong>{{ number_format($tank->volume_percentage, 2) }}%</strong> 
                        ({{ number_format($tank->current_volume, 2) }} / {{ number_format($tank->capacity, 2) }} {{ $tank->type }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Tangki</th>
                    <th>Kapasitas</th>
                    <th>Volume Saat Ini</th>
                    <th>Persentase</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Pelanggan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($tanks->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center py-4">Belum ada data tangki.</td>
                    </tr>
                @else
                    @foreach ($tanks as $tank)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tank->name }}</td>
                            <td>{{ number_format($tank->capacity, 2) }} {{ $tank->type }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar 
                                        @if ($tank->isLowStock) bg-danger
                                        @elseif ($tank->volume_percentage < 50) bg-warning
                                        @else bg-success
                                        @endif"
                                         role="progressbar"
                                         style="width: {{ $tank->volume_percentage }}%"
                                         aria-valuenow="{{ $tank->volume_percentage }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($tank->current_volume, 2) }} {{ $tank->type }}
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($tank->volume_percentage, 2) }}%</td>
                            <td>{{ $tank->location }}</td>
                            <td>
                                <span class="badge 
                                    @if ($tank->status === 'active') bg-success
                                    @elseif ($tank->status === 'maintenance') bg-warning
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($tank->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($tank->customer)
                                    {{ $tank->customer->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('tanks.show', $tank) }}" class="btn btn-sm btn-outline-primary">
                                        Lihat
                                    </a>
                                    <a href="{{ route('tanks.edit', $tank) }}" class="btn btn-sm btn-outline-secondary">
                                        Edit
                                    </a>
                                    <form action="{{ route('tanks.destroy', $tank) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menghapus tangki ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    {{ $tanks->links() }}
</div>
@endsection