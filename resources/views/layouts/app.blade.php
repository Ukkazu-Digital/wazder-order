<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Larawaba') }}</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; }
        #sidebar { width: 260px; flex-shrink: 0; background: #fff; border-right: 1px solid #dee2e6; }
        main { flex-grow: 1; padding: 20px; background-color: #f8f9fa; }
        .nav-link { color: #6c757d; }
        .nav-link.active { background-color: #e9ecef; color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>

    <aside id="sidebar" class="d-flex flex-column p-3">
        @include('layouts.partials.sidebar')
    </aside>
    
    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>