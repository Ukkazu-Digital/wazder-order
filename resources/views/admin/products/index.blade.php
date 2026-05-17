@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Manajemen Produk</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="productsTable" class="table align-middle table-hover dt-responsive nowrap">
            <thead class="table-light">
                <tr>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category ?? '-' }}</td>
                    <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td>{{ $product->status ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm me-1">Detail</a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus produk?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada produk</td>
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
        $('#productsTable').DataTable({
            language: {
                "sSearch": "Cari:",
                "sLengthMenu": "Tampilkan _MENU_ data",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "sEmptyTable": "Belum ada produk"
            },
            pageLength: 20,
            columnDefs: [
                { orderable: false, targets: 6 }
            ]
        });
    });
</script>
@endpush
