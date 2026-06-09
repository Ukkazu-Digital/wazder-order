<div class="d-flex flex-column p-3 h-100">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-4 text-dark text-decoration-none">
        <div class="bg-primary text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
            <i class="bi bi-rocket-takeoff-fill"></i>
        </div>
        <span class="fw-bold fs-5">{{ config('app.name', 'Larawaba') }}.admin</span>
    </a>

    <ul class="nav nav-pills flex-column mb-auto gap-1">
    <li class="nav-item mt-2 text-uppercase text-secondary fw-bold fs-7 ps-2 mb-1" style="font-size: 0.75rem;">Menu Utama</li>
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-secondary' }}">
                <i class="bi bi-grid-1x2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.kasir.create') }}" class="nav-link {{ request()->routeIs('admin.kasir.*') ? 'active' : 'text-secondary' }}">
                <i class="bi bi-receipt-cutoff me-2"></i> POS
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.chats.index') }}" class="nav-link {{ request()->routeIs('admin.chats.*') ? 'active-success' : 'text-secondary' }}">
                <i class="bi bi-whatsapp me-2"></i> Chat
            </a>
        </li>

        <li class="nav-item mt-2 text-uppercase text-secondary fw-bold fs-7 ps-2 mb-1" style="font-size: 0.75rem;">Data Utama</li>
        <li><a href="{{ route('admin.v2.products.index') }}" class="nav-link {{ request()->routeIs('admin.v2.products.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-box-seam me-2"></i> Produk</a></li>
        <li><a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-people me-2"></i> Pelanggan</a></li>
        <li><a href="{{ route('admin.kurirs.index') }}" class="nav-link {{ request()->routeIs('admin.kurirs.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-truck me-2"></i> Manajemen Kurir</a></li>

        <li class="nav-item mt-2 text-uppercase text-secondary fw-bold fs-7 ps-2 mb-1" style="font-size: 0.75rem;">Persediaan</li>
        <li><a href="{{ route('admin.stocks.index') }}" class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-tags me-2"></i> Umum</a></li>
        <li><a href="{{ route('admin.stocks.index') }}" class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-tags me-2"></i> Tangki</a></li>
        <li><a href="{{ route('admin.stocks.index') }}" class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-tags me-2"></i> Inventaris</a></li>

        <li class="nav-item mt-2 text-uppercase text-secondary fw-bold fs-7 ps-2 mb-1" style="font-size: 0.75rem;">Laporan</li>
        <li class="nav-item">
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : 'text-secondary' }}">
                <i class="bi bi-bag me-2"></i> Order
            </a>
        </li>
        <li><a href="{{ route('admin.reports.profit.index') }}" class="nav-link {{ request()->routeIs('admin.reports.profit.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-currency-dollar me-2"></i> Laba Rugi</a></li>
        <li><a href="{{ route('admin.reports.inventory_valuation') }}" class="nav-link {{ request()->routeIs('admin.reports.assets.*') ? 'active' : 'text-secondary' }}"><i class="bi bi-box-seam me-2"></i> Nilai Aset</a></li>
    </ul>

    <hr class="my-3">

    <div class="d-flex flex-column gap-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </button>
        </form>
    </div>
</div>

<style>
    /* Styling tambahan untuk sidebar */
    .nav-link { border-radius: 0.5rem; transition: 0.2s; }
    .nav-link:hover { background-color: #f8f9fa; }
    .active { background-color: var(--bs-primary) !important; color: white !important; }
    .active-success { background-color: #198754 !important; color: white !important; }
</style>