@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    <div class="mb-5">
        <h2 class="fw-bold text-dark mb-1">Dashboard Admin</h2>
        <p class="text-secondary mb-0">Selamat datang kembali! Berikut adalah ringkasan kontrol sistem Anda hari ini.</p>
    </div>

    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-success opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-secondary small mb-0">Ringkasan Finansial Bulan Ini</h5>
        </div>
        
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-3 h-100">
                    <div class="card-body p-2 flex-grow-1">
                        <small class="text-uppercase tracking-wider text-muted font-semibold small">Omset Berjalan</small>
                        <h3 class="fw-bold text-dark mt-2 mb-0">Rp {{ number_format($thisMonthRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 px-2">
                        <small class="text-muted text-xs">Total penjualan bruto kotor</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-3 h-100">
                    <div class="card-body p-2 flex-grow-1">
                        <small class="text-uppercase tracking-wider text-danger font-semibold small">Beban HPP (FIFO)</small>
                        <h3 class="fw-bold text-danger mt-2 mb-0">Rp {{ number_format($thisMonthHpp, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 px-2">
                        <small class="text-muted text-xs">Nilai pokok tumpukan keluar</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm bg-emerald-soft rounded-3 p-3 h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body p-2 flex-grow-1">
                        <small class="text-uppercase tracking-wider text-success font-semibold small">Laba Bersih Operasional</small>
                        <h3 class="fw-bold text-success mt-2 mb-0">Rp {{ number_format($thisMonthProfit, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 px-2">
                        <small class="text-success text-xs fw-medium">Net margin performa ritel</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm bg-blue-soft rounded-3 p-3 h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body p-2 flex-grow-1">
                        <small class="text-uppercase tracking-wider text-primary font-semibold small">Nilai Aset Gudang</small>
                        <h3 class="fw-bold text-primary mt-2 mb-0">Rp {{ number_format($currentAssetValuation, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 px-2">
                        <small class="text-primary text-xs fw-medium">Uang modal mengendap di stok</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-secondary small mb-0">Operasional Utama</h5>
        </div>
        
        <div class="row g-3">
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
                    <div class="position-absolute end-0 bottom-0 opacity-10 translate-middle-x mb-n4 me-n4" style="font-size: 10rem;">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3.5">
                <div class="card border-0 shadow-sm bg-white h-100 rounded-3 card-clickable">
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

            <div class="col-12 col-md-6 col-lg-3.5">
                <div class="card border-0 shadow-sm bg-white h-100 rounded-3 card-clickable">
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

    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-secondary opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-secondary small mb-0">Master Data & Logistik</h5>
        </div>

        <div class="row g-3">
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

    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-info opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-secondary small mb-0">Pusat Laporan Komprehensif</h5>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-4 card-clickable">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-3 bg-emerald-soft text-success fs-4 me-3">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Analisis Untung / Rugi Ritel</h6>
                                <p class="text-muted small mb-0">Audit rincian performa margin keuntungan per item.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports.profit.index') }}" class="btn btn-light rounded-circle text-success px-3 py-2">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-4 card-clickable">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-3 bg-blue-soft text-primary fs-4 me-3">
                                <i class="bi bi-pie-chart-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Valuasi Inventaris & Gudang</h6>
                                <p class="text-muted small mb-0">Pengecekan tumpukan modal pada sisa antrean kuota batch.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports.inventory_valuation') }}" class="btn btn-light rounded-circle text-primary px-3 py-2">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .bg-blue-soft { background-color: rgba(13, 110, 253, 0.06); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.06); }
    .bg-emerald-soft { background-color: rgba(25, 135, 84, 0.05); }
    .btn-white { background-color: #fff; color: #0d6efd; }
    .btn-white:hover { background-color: #f8f9fa; color: #0a58ca; }
    
    .card-clickable {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-clickable:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.06)!important;
    }
</style>
@endsection