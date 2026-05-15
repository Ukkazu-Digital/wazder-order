@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Dashboard Admin</h1>
    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#0d6efd;"><i class="bi bi-bag"></i></div>
                    <h5 class="card-title mb-3">Manajemen Order</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary w-100">Lihat Order</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#198754;"><i class="bi bi-people"></i></div>
                    <h5 class="card-title mb-3">Manajemen Customer</h5>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-success w-100">Lihat Customer</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#6610f2;"><i class="bi bi-chat-dots"></i></div>
                    <h5 class="card-title mb-3">Manajemen Chat</h5>
                    <a href="{{ route('admin.chats.index') }}" class="btn btn-secondary w-100">Lihat Chat</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
