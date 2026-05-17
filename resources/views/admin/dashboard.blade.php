@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Dashboard Admin</h1>
    <div class="row g-4">
        
        <!-- BARIS 1: OPERASIONAL UTAMA / UTILITY TINGGI -->
        
        <!-- 1. POS Kasir (Paling Kiri Atas - Prime Real Estate) -->
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-light">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="mb-2" style="font-size:2.5rem; color:#0d6efd;"><i class="bi bi-receipt"></i></div>
                    <h5 class="card-title mb-3 fw-bold">POS Kasir</h5>
                    <a href="{{ route('kasir.create') }}" class="btn btn-primary w-100 py-2 fw-bold">Buka Kasir</a>
                </div>
            </div>
        </div>

        <!-- 2. Manajemen Order -->
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#0d6efd;"><i class="bi bi-bag"></i></div>
                    <h5 class="card-title mb-3">Manajemen Order</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary w-100">Lihat Order</a>
                </div>
            </div>
        </div>

        <!-- 3. Manajemen Chat -->
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#0d6efd;"><i class="bi bi-chat-dots"></i></div>
                    <h5 class="card-title mb-3">Manajemen Chat</h5>
                    <a href="{{ route('admin.chats.index') }}" class="btn btn-primary w-100">Lihat Chat</a>
                </div>
            </div>
        </div>

        <!-- BARIS 2: MASTER DATA PENDUKUNG -->

        <!-- 4. Manajemen Produk -->
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#495057;"><i class="bi bi-box"></i></div>
                    <h5 class="card-title mb-3">Manajemen Produk</h5>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary w-100">Lihat Produk</a>
                </div>
            </div>
        </div>

        <!-- 5. Manajemen Customer -->
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#495057;"><i class="bi bi-people"></i></div>
                    <h5 class="card-title mb-3">Manajemen Customer</h5>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary w-100">Lihat Customer</a>
                </div>
            </div>
        </div>

        <!-- 6. Manajemen Kurir -->
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="font-size:2.5rem; color:#495057;"><i class="bi bi-truck"></i></div>
                    <h5 class="card-title mb-3">Manajemen Kurir</h5>
                    <a href="{{ route('admin.kurirs.index') }}" class="btn btn-outline-primary w-100">Lihat Kurir</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection