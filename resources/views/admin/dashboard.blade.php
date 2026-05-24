@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    <!-- Welcome Header -->
    <div class="mb-5">
        <h2 class="fw-bold text-dark mb-1">Dashboard Admin</h2>
        <p class="text-secondary mb-0">Selamat datang kembali! Berikut adalah ringkasan kontrol sistem Anda hari ini.</p>
    </div>

    <!-- SEKSI 1: OPERASIONAL UTAMA (Aktivitas Tinggi) -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-secondary small mb-0">Operasional Utama</h5>
        </div>
        
        <div class="row g-3">
            <!-- 1. POS Kasir (Hero Card) -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm bg-primary text-white h-100 rounded-3 overflow-hidden position-relative">
                    <div class="card-body p-4 d-flex flex-column justify-content-between z-1">
                        <div>
                            <div class="bg-white bg-opacity-20 rounded-3 d-inline-flex p-3 mb-3 text-white fs-3">
                                <i class="bi bi-receipt-cutoff"></i>
                            </div>
                            <h4 class="fw-bold mb-1">POS Kasir</h4>
                            <p class="text-white text-opacity-75 small mb-4">Buka panel kasir untuk mencatat transaksi penjualan baru secara langsung.</p>
                        </div>
                        <a href="{{ route('admin.kasir.create') }}" class="btn btn-white text-primary fw-bold w-100 py-2.5 shadow-sm rounded-3">
                            <i class="bi bi-plus-circle me-1"></i> Buka Kasir Baru
                        </a>
                    </div>
                    <!-- Variasi background dekoratif halus -->
                    <div class="position-absolute end-0 bottom-0 opacity-10 translate-middle-x mb-n4 me-n4" style="font-size: 10rem;">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Manajemen Order -->
            <div class="col-12 col-md-6 col-lg-3.5">
                <div class="card border-0 shadow-sm bg-white h-100 rounded-3 transition-all hover-translate">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-blue-soft rounded-3 d-inline-flex p-3 mb-3 text-primary fs-3">
                                <i class="bi bi-bag-check-fill"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Manajemen Order</h5>
                            <p class="text-secondary small mb-3">Pantau pesanan masuk, proses pengiriman, dan status pembayaran.</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary w-100 py-2 rounded-3 fw-semibold">
                            Lihat Semua Order <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Manajemen Chat -->
            <div class="col-12 col-md-6 col-lg-3.5">
                <div class="card border-0 shadow-sm bg-white h-100 rounded-3 transition-all hover-translate">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="bg-success-soft rounded-3 d-inline-flex p-3 mb-3 text-success fs-3">
                                <i class="bi bi-whatsapp"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Manajemen Chat</h5>
                            <p class="text-secondary small mb-3">Hubungkan dengan pelanggan dan kelola integrasi pesan masuk.</p>
                        </div>
                        <a href="{{ route('admin.chats.index') }}" class="btn btn-outline-success w-100 py-2 rounded-3 fw-semibold">
                            Buka Ruang Chat <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKSI 2: MASTER DATA & LOGISTIK (Pendukung) -->
    <div class="pt-2">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-secondary opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-secondary small mb-0">Master Data & Logistik</h5>
        </div>

        <div class="row g-3">
            <!-- 4. Manajemen Produk -->
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.v2.products.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm bg-white rounded-3 text-center p-4 h-100 card-clickable">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 mx-auto text-dark fs-4">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Produk</h6>
                        <span class="text-muted small">Kelola Katalog</span>
                    </div>
                </a>
            </div>

            <!-- 5. Manajemen Batch Stok -->
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.stocks.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm bg-white rounded-3 text-center p-4 h-100 card-clickable">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 mx-auto text-warning fs-4">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Batch Stok</h6>
                        <span class="text-muted small">Antrean FIFO</span>
                    </div>
                </a>
            </div>

            <!-- 6. Manajemen Customer -->
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.customers.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm bg-white rounded-3 text-center p-4 h-100 card-clickable">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 mx-auto text-info fs-4">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Customer</h6>
                        <span class="text-muted small">Database Klien</span>
                    </div>
                </a>
            </div>

            <!-- 7. Manajemen Kurir -->
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.kurirs.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm bg-white rounded-3 text-center p-4 h-100 card-clickable">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 mx-auto text-danger fs-4">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Kurir / Logistik</h6>
                        <span class="text-muted small">Status Pengiriman</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Helper Styles Alternatif jika Bootstrap Anda belum mendukung soft colors -->
<style>
    .bg-blue-soft { background-color: rgba(13, 110, 253, 0.08); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.08); }
    .btn-white { background-color: #fff; color: #0d6efd; }
    .btn-white:hover { background-color: #f8f9fa; color: #0a58ca; }
    
    /* Efek hover halus agar dashboard terasa interaktif */
    .card-clickable {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-clickable:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
    }
</style>
@endsection