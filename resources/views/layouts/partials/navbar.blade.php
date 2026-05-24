<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2 shadow-sm">
    <div class="container">
        <!-- Brand / Logo Platform -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
            <div class="bg-primary text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                <i class="bi bi-rocket-takeoff-fill fs-6"></i>
            </div>
            <span class="fw-bold tracking-tight text-dark" style="font-size: 1.15rem;">{{ config('app.name', 'Larawaba') }}<span class="text-primary">.admin</span></span>
        </a>

        <!-- Tombol Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdminMenu" aria-controls="navbarAdminMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- Menu Navigasi Konten -->
        <div class="collapse navbar-collapse" id="navbarAdminMenu">
            <ul class="navbar-nav me-auto ps-lg-4 mb-2 mb-lg-0 gap-1 gap-lg-2 navbar-custom-links">
                
                <!-- Dashboard Link -->
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active fw-bold text-primary' : 'text-secondary' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-1x2 me-2"></i> Dashboard
                    </a>
                </li>

                <!-- Order Link -->
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.orders.*') ? 'active fw-bold text-primary' : 'text-secondary' }}" href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-bag me-2"></i> Order
                    </a>
                </li>

                <!-- Chat Link (WhatsApp Integration) -->
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.chats.*') ? 'active fw-bold text-success' : 'text-secondary' }}" href="{{ route('admin.chats.index') }}">
                        <i class="bi bi-whatsapp me-2"></i> Chat
                    </a>
                </li>

                <!-- Produk Link -->
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.v2.products.*') ? 'active fw-bold text-primary' : 'text-secondary' }}" href="{{ route('admin.v2.products.index') }}">
                        <i class="bi bi-box-seam"></i> <span class="ms-2">Produk</span>
                    </a>
                </li>

                <!-- Data Relasi Dropdown (Menghemat Space Layar) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle py-2 px-3 rounded-2 d-flex align-items-center text-secondary {{ request()->routeIs(['admin.customers.*', 'admin.kurirs.*', 'admin.stocks.*']) ? 'active fw-bold text-primary' : '' }}" href="#" id="navbarDropdownData" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-folder2-open me-2"></i> Master Data
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-md p-2 mt-2 rounded-3" aria-labelledby="navbarDropdownData">
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.stocks.*') ? 'active bg-light fw-semibold' : '' }}" href="{{ route('admin.stocks.index') }}">
                                <i class="bi bi-tags text-warning me-2"></i> Batch FIFO Stok
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.customers.*') ? 'active bg-light fw-semibold' : '' }}" href="{{ route('admin.customers.index') }}">
                                <i class="bi bi-people text-info me-2"></i> Database Customer
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded-2 d-flex align-items-center {{ request()->routeIs('admin.kurirs.*') ? 'active bg-light fw-semibold' : '' }}" href="{{ route('admin.kurirs.index') }}">
                                <i class="bi bi-truck text-danger me-2"></i> Manajemen Kurir
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

            <!-- Sisi Kanan: Menu Cepat Kasir & Profil Admin -->
            <div class="d-flex align-items-center gap-3 pt-3 pt-lg-0 border-top border-lg-0 mt-3 mt-lg-0">
                <!-- Tombol Shortcut Kasir Utama -->
                <a href="{{ route('admin.kasir.create') }}" class="btn btn-primary d-flex align-items-center px-3 py-2 rounded-3 fw-bold shadow-sm btn-kasir-nav">
                    <i class="bi bi-receipt-cutoff me-1.5"></i> POS Kasir
                </a>

                <!-- Pembatas Garis Tegak (Sembunyi saat layar HP) -->
                <div class="vr d-none d-lg-block bg-secondary opacity-25" style="height: 24px;"></div>

                <!-- Informasi User Profil Singkat -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-light border text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; font-size: 0.85rem;">
                            AD
                        </div>
                        <span class="ms-2 d-none d-xl-inline fw-semibold text-secondary small">Admin Larawaba</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-md p-2 mt-2 rounded-3" aria-labelledby="profileDropdown">
                        <li><span class="dropdown-header text-muted small px-3">Sesi: Administrator</span></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item text-danger py-2 px-3 rounded-2 d-flex align-items-center" href="#">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar Sistem
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</nav>

<!-- Tambahan Style Efek Hover Elegan -->
<style>
    /* Styling link aktif bawaan bootstrap agar lebih modern */
    @media (min-width: 992px) {
        .navbar-custom-links .nav-link {
            transition: all 0.2s ease;
            position: relative;
        }
        .navbar-custom-links .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 1rem;
            right: 1rem;
            height: 3px;
            background-color: var(--bs-primary);
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        /* Penyesuaian khusus untuk link Chat agar garis bawahnya hijau */
        .navbar-custom-links .nav-link.active.text-success::after {
            background-color: var(--bs-success) !important;
        }
    }
    
    .btn-kasir-nav {
        transition: transform 0.2s ease;
    }
    .btn-kasir-nav:hover {
        transform: scale(1.02);
    }
</style>