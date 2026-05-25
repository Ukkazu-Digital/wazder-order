<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
            <div class="bg-primary text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                <i class="bi bi-rocket-takeoff-fill fs-6"></i>
            </div>
            <span class="fw-bold tracking-tight text-dark" style="font-size: 1.15rem;">{{ config('app.name', 'Larawaba') }}<span class="text-primary">.admin</span></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdminMenu">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdminMenu">
            <ul class="navbar-nav me-auto ps-lg-4 mb-2 mb-lg-0 gap-1 gap-lg-2 navbar-custom-links">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 {{ request()->routeIs('admin.dashboard') ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 {{ request()->routeIs('admin.orders.*') ? 'active text-primary' : 'text-secondary' }}" href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-bag me-1"></i> Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 {{ request()->routeIs('admin.chats.*') ? 'active text-success' : 'text-secondary' }}" href="{{ route('admin.chats.index') }}">
                        <i class="bi bi-whatsapp me-1"></i> Chat
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle py-2 px-3 rounded-2 text-secondary" href="#" id="navbarDropdownData" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-folder2-open me-1"></i> Data Utama
                    </a>
                    <ul class="dropdown-menu border-0 shadow-md p-2 mt-2 rounded-3">
                        <li><a class="dropdown-item py-2 px-3 rounded-2 {{ request()->routeIs('admin.v2.products.*') ? 'active-link' : '' }}" href="{{ route('admin.v2.products.index') }}"><i class="bi bi-box-seam me-2"></i> Produk</a></li>
                        <li><a class="dropdown-item py-2 px-3 rounded-2 {{ request()->routeIs('admin.stocks.*') ? 'active-link' : '' }}" href="{{ route('admin.stocks.index') }}"><i class="bi bi-tags me-2"></i> Persediaan</a></li>
                        <li><a class="dropdown-item py-2 px-3 rounded-2 {{ request()->routeIs('admin.customers.*') ? 'active-link' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-people me-2"></i> Pelanggan</a></li>
                        <li><a class="dropdown-item py-2 px-3 rounded-2 {{ request()->routeIs('admin.kurirs.*') ? 'active-link' : '' }}" href="{{ route('admin.kurirs.index') }}"><i class="bi bi-truck me-2"></i> Manajemen Kurir</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle py-2 px-3 rounded-2 text-secondary" href="#" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-graph-up-arrow me-1"></i> Laporan
                    </a>
                    <ul class="dropdown-menu border-0 shadow-md p-2 mt-2 rounded-3">
                        <li><a class="dropdown-item py-2 px-3 rounded-2 {{ request()->routeIs('admin.reports.profit.*') ? 'active-link' : '' }}" href="{{ route('admin.reports.profit.index') }}"><i class="bi bi-currency-dollar me-2"></i> Laba Rugi</a></li>
                        <li><a class="dropdown-item py-2 px-3 rounded-2 {{ request()->routeIs('admin.reports.assets.*') ? 'active-link' : '' }}" href="{{ route('admin.reports.inventory_valuation') }}"><i class="bi bi-box-seam me-2"></i> Nilai Aset Gudang</a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 pt-3 pt-lg-0 border-top border-lg-0 mt-3 mt-lg-0">
                <a href="{{ route('admin.kasir.create') }}" class="btn btn-primary px-3 py-2 rounded-3 fw-bold shadow-sm">
                    <i class="bi bi-receipt-cutoff me-1"></i> POS
                </a>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.85rem;">
                            {{ substr(auth()->user()->name ?? 'AD', 0, 2) }}
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-md p-2 mt-2 rounded-3">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2 px-3 rounded-2"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Menentukan warna background saat link aktif di dropdown agar tidak putih */
    .active-link {
        background-color: var(--bs-primary) !important;
        color: white !important;
    }
    .active-link i { color: white !important; }
    
    /* Hover effect untuk dropdown item agar tetap nyaman dilihat */
    .dropdown-item:hover:not(.active-link) {
        background-color: #f8f9fa;
    }
</style>