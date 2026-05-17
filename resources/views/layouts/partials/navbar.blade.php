<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Larawaba Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Order</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.customers.index') }}">Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.kurirs.index') }}">Kurir</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.chats.index') }}">Chat</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('kasir.create') }}">Kasir</a></li>
            </ul>
        </div>
    </div>
</nav>