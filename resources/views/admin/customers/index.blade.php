@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manajemen Customers</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Jumlah Order</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->customers_name }}</td>
                <td>{{ $customer->email ?? '-' }}</td>
                <td>{{ $customer->address ?? '-' }}</td>
                <td>{{ $customer->orders->count() }}</td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-info btn-sm">Detail</a>
                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus customer?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $customers->links() }}
</div>
@endsection
