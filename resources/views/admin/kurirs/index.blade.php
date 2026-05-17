@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Manajemen Kurir</h1>
        <a href="{{ route('admin.kurirs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kurir
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="kurirsTable" class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>Foto</th>
                    <th>Nama Kurir</th>
                    <th>No HP</th>
                    <th>Plat Nomor</th>
                    <th>Status</th>
                    <th>Total Order</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kurirs as $kurir)
                <tr>
                    <td>
                        @if($kurir->photo)
                            <img src="{{ asset('storage/kurir/' . $kurir->photo) }}" alt="Foto Kurir" width="40" height="40" class="rounded-circle object-cover">
                        @else
                            <div style="width: 40px; height: 40px;" class="bg-light rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person-fill text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $kurir->name }}</td>
                    <td>{{ $kurir->phone }}</td>
                    <td>{{ $kurir->plate_number }}</td>
                    <td>
                        <span class="badge bg-{{ $kurir->status == 'Aktif' ? 'success' : 'danger' }}">
                            {{ $kurir->status }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $kurir->orders->count() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.kurirs.show', $kurir) }}" class="btn btn-info btn-sm me-1">Detail</a>
                        <a href="{{ route('admin.kurirs.edit', $kurir) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                        <form action="{{ route('admin.kurirs.destroy', $kurir) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kurir?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada kurir</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kurirsTable').DataTable({
            language: {
                "sSearch": "Cari:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada kurir"
            },
            pageLength: 20,
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ]
        });
    });
</script>
@endpush
