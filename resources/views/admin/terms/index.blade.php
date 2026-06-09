@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Term Of Payment</h1>
        <a href="{{ route('terms.create') }}" class="btn btn-sm btn-outline-secondary">
            Tambah Term
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Term</th>
                    <th>Deskripsi</th>
                    <th>Jatuh Tempo (hari)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($terms->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada data term of payment.</td>
                    </tr>
                @else
                    @foreach ($terms as $term)
                        <tr>
                            <td>{{ $term->id }}</td>
                            <td>{{ $term->name }}</td>
                            <td>{{ $term->description }}</td>
                            <td>{{ $term->days_due }} hari</td>
                            <td>
                                <a href="{{ route('terms.edit', $term) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('terms.destroy', $term) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Yakin ingin menghapus term ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    {{ $terms->links() }}
</div>
@endsection
