<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Menggunakan yield title agar judul tab browser dinamis sesuai halaman -->
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Larawaba') }}</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>{{ config('app.name', 'Larawaba') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.dashboard') ? 'active fw-bold' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    
                    <!-- POS Kasir: Diberi highlight khusus berupa border/tombol kecil agar mudah dibidik -->
                    <li class="nav-item mx-lg-2 my-2 my-lg-0">
                        <a class="btn btn-sm {{ Route::is('kasir.*') ? 'btn-primary' : 'btn-outline-primary' }} px-3" href="{{ route('kasir.create') }}">
                            <i class="bi bi-receipt me-1"></i> POS Kasir
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.orders.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.orders.index') }}">Order</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('admin.chats.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.chats.index') }}">Chat</a>
                    </li>
                    
                    <!-- Pembatas Visual di Desktop, Dropdown untuk Master Data agar Navbar tidak penuh -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ Route::is('admin.products.*', 'admin.customers.*', 'admin.kurirs.*') ? 'active fw-bold' : '' }}" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Data Master
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-content dropdown-item {{ Route::is('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                    <i class="bi bi-box me-2"></i>Produk
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::is('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                                    <i class="bi bi-people me-2"></i>Customer
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::is('admin.kurirs.*') ? 'active' : '' }}" href="{{ route('admin.kurirs.index') }}">
                                    <i class="bi bi-truck me-2"></i>Kurir
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    
    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.7/js/dataTables.bootstrap5.min.js"></script>
    @stack('scripts')
</body>
</html>