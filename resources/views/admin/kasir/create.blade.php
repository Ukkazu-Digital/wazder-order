@extends('layouts.app')

@section('content')
<!-- Memasukkan font modern untuk area POS -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    .pos-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .product-card {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-2px);
    }
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    /* Mengatasi konflik tinggi kolom */
    .pos-scroll-area {
        max-height: calc(100vh - 180px);
        overflow-y: auto;
    }
</style>

<div class="container-fluid px-4 py-3 pos-container">
    <!-- Header POS -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="fw-bold h3 mb-0 text-primary"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Katalog POS</h1>
            <small class="text-muted font-monospace">ID Transaksi: {{ $transaction_id }}</small>
        </div>
        <span class="badge bg-secondary px-3 py-2">Kasir: {{ Auth::user()->name ?? 'Admin' }}</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-3">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Utama Terintegrasi -->
    <form action="{{ route('kasir.store') }}" method="POST" id="mainOrderForm" class="needs-validation">
        @csrf
        <!-- Hidden input pendukung data kontrol form -->
        <input type="hidden" name="kode_pesanan" value="{{ $transaction_id ?? '' }}">

        <div class="row g-4">
            
            <!-- KOLOM KIRI: KATALOG PRODUK GRID DENGAN GAMBAR -->
            <div class="col-12 col-lg-7 col-xl-8">
                <!-- Search Bar -->
                <div class="input-group shadow-sm mb-3 rounded-3 overflow-hidden border-0">
                    <span class="input-group-text bg-white border-0 text-muted fs-5"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" onkeyup="filterProducts()" 
                           placeholder="Cari dari {{ $products->count() }} produk..." 
                           class="form-control form-control-lg border-0 bg-white shadow-none fs-6 py-3">
                </div>

                <!-- Grid Konten Berkasur Gambar -->
                <div class="pos-scroll-area hide-scrollbar pe-1">
                    <div id="productGrid" class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3">
                        @foreach($products as $product)
                        <div class="col product-item" data-name="{{ strtolower($product->name) }}">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card position-relative p-2 bg-white {{ $product->stock <= 0 ? 'opacity-50' : '' }}">
                                
                                <!-- Thumbnail Produk -->
                                <div class="position-relative bg-light rounded-3 overflow-hidden mb-2 d-flex align-items-center justify-content-center" style="aspect-ratio: 1/1;">
                                    @if(!empty($product->image))
                                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <span class="fs-1 opacity-20">📦</span>
                                    @endif

                                    <!-- Status Badge Stok overlay -->
                                    @if($product->stock <= 0)
                                        <div class="position-absolute inset-0 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center w-100 h-100">
                                            <span class="badge bg-danger text-uppercase px-2 py-1 fw-bold fs-7">Habis</span>
                                        </div>
                                    @elseif($product->stock <= 5)
                                        <span class="position-absolute top-2 right-2 badge bg-warning text-dark fw-bold" style="font-size: 10px;">Limit</span>
                                    @endif
                                </div>

                                <!-- Metadata info produk -->
                                <div class="card-body p-1 d-flex flex-column h-100">
                                    <h6 class="card-title fw-bold text-dark text-truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h6>
                                    <p class="text-primary fw-bold mb-1 small">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    
                                    <p id="stock-info-{{ $product->id }}" class="mb-2" style="font-size: 11px; font-weight: 600; color: {{ $product->stock <= 5 ? '#f0ad4e' : '#a0a0a0' }}">
                                        {{ $product->stock <= 0 ? 'Stok Habis' : ($product->stock <= 5 ? "Sisa $product->stock stok!" : 'Stok Tersedia') }}
                                    </p>

                                    <!-- Controller Qty di bawah kartu -->
                                    <div class="d-flex align-items-center justify-content-between mt-auto bg-light rounded-3 p-1">
                                        <button type="button" onclick="updateQty({{ $product->id }}, -1)" class="btn btn-sm btn-white shadow-sm fw-bold border-0 px-2">-</button>
                                        <span id="qty-text-{{ $product->id }}" class="fw-bold small text-dark">0</span>
                                        
                                        @if($product->stock > 0)
                                            <button type="button" onclick="updateQty({{ $product->id }}, 1, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})" 
                                                    class="btn btn-sm btn-primary shadow-sm fw-bold border-0 px-2">+</button>
                                        @else
                                            <button type="button" disabled class="btn btn-sm btn-secondary opacity-50 px-2">x</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN BELANJA & INPUT DATA PELANGGAN -->
            <div class="col-12 col-lg-5 col-xl-4">
                <div class="card shadow-sm border-0 bg-white rounded-4 p-2 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-body">
                        
                        <h5 class="fw-bold mb-3 text-secondary border-bottom pb-2">Detail Transaksi</h5>

                        <!-- List Keranjang Dinamis -->
                        <label class="form-label fw-bold text-muted small uppercase">ITEM DIKERANJANG</label>
                        <div class="bg-light rounded-3 p-3 mb-3">
                            <div id="cart-list" class="space-y-2 max-h-48 overflow-y-auto hide-scrollbar text-sm text-dark">
                                <div class="text-muted text-center py-3 italic" id="emptyCartMessage">Belum ada produk dipilih</div>
                            </div>
                            <!-- Elemen kontainer tampung hidden inputs data array untuk dipost ke Laravel -->
                            <div id="hiddenInputContainer"></div>
                        </div>

                        <!-- Data Customer -->
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-bold text-muted small">INFORMASI PELANGGAN</label>
                            <select class="form-select" id="customer_id" name="customer_id" required onchange="toggleNewCustomerFields()">
                                <option value="">-- Pilih Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customers_name }}</option>
                                @endforeach
                                <option value="new">+ Buat Customer Baru</option>
                            </select>
                            
                            <!-- Fields input pelanggan baru seandainya dibutuhkan -->
                            <div id="newCustomerFields" class="p-3 bg-light border border-dashed rounded-3 mt-2" style="display: none;">
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Nama</label>
                                    <input type="text" name="new_customer_name" class="form-control form-control-sm">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Alamat</label>
                                    <input type="text" name="new_customer_address" class="form-control form-control-sm">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small mb-1">No HP (WhatsApp)</label>
                                    <input type="text" name="new_customer_phone" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Status Bayar -->
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold text-muted small">STATUS</label>
                            <select class="form-select" id="status" name="status" required onchange="handleStatusChange()">
                                <option value="pending">Pending</option>
                                <option value="paid">Paid (Lunas)</option>
                                <option value="shipped">Shipped (Dikirim Kurir)</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>

                            <div id="kurirFieldCreate" class="mt-2" style="display: none;">
                                <label for="kurir_id" class="form-label small mb-1 text-muted">Petugas Kurir</label>
                                <select name="kurir_id" id="kurir_id" class="form-select form-select-sm">
                                    <option value="">-- Pilih Kurir --</option>
                                    @foreach($kurirs as $kurir)
                                        <option value="{{ $kurir->id }}">{{ $kurir->name }} ({{ $kurir->plate_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold text-muted small">CATATAN NOTA</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan opsional..."></textarea>
                        </div>

                        <!-- Panel Display Total Tagihan Super Besar -->
                        <div class="card border-0 bg-primary text-white p-3 mb-3 rounded-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-uppercase opacity-75 fw-bold">Grand Total</span>
                                <h2 class="fw-black mb-0 text-white" id="total-amount" style="font-weight: 800;">Rp 0</h2>
                            </div>
                        </div>

                        <!-- Tombol Submit Form -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100 py-2 rounded-3">Batal</a>
                            </div>
                            <div class="col-8">
                                <button type="submit" id="btnSubmit" class="btn btn-success w-100 py-2 fw-bold rounded-3 shadow-sm">
                                    Simpan & Cetak
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Penampung data keranjang objek tunggal global
    let cart = {};

    // 1. Filter Fungsi Pencarian Realtime
    function filterProducts() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(input)) {
                item.style.setProperty("display", "block", "important");
            } else {
                item.style.setProperty("display", "none", "important");
            }
        });
    }

    // 2. Fungsi Manajemen Kuantitas Plus / Minus 
    function updateQty(id, change, name = '', price = 0, maxStock = 0) {
        if (!cart[id]) {
            // Jika diklik tombol minus padahal belum ada di keranjang
            if (change < 0) return; 
            cart[id] = { name: name, price: price, qty: 0, maxStock: maxStock };
        }
        
        let newQty = cart[id].qty + change;
        
        if (newQty > cart[id].maxStock) {
            alert("Stok produk tidak mencukupi batas maksimum!");
            return;
        }
        
        cart[id].qty = newQty;
        
        // Hapus referensi jika qty bernilai nol atau di bawahnya
        if (cart[id].qty <= 0) {
            delete cart[id];
        }

        renderCart();
    }

    // 3. Menggambar ulang Tampilan Keranjang Belanjaan dan Sinkronisasi Form HTML
    function renderCart() {
        const cartList = document.getElementById('cart-list');
        const hiddenContainer = document.getElementById('hiddenInputContainer');
        const emptyMsg = document.getElementById('emptyCartMessage');
        
        let total = 0;
        let index = 0;

        // Reset semua tampilan teks counter qty di kartu grid katalog menjadi 0
        document.querySelectorAll('[id^="qty-text-"]').forEach(el => el.innerText = '0');
        
        // Bersihkan HTML keranjang lama dan container input bawaan
        cartList.innerHTML = '';
        hiddenContainer.innerHTML = '';

        // Looping data item belanja
        const keys = Object.keys(cart);
        
        if(keys.length === 0) {
            if(emptyMsg) cartList.appendChild(emptyMsg);
        } else {
            keys.forEach(id => {
                const item = cart[id];
                const subtotal = item.price * item.qty;
                total += subtotal;

                // Perbarui teks kuantitas pada kartu grid produk terkait
                const cardQtyText = document.getElementById(`qty-text-${id}`);
                if (cardQtyText) cardQtyText.innerText = item.qty;

                // Masukkan baris info list barang ke panel rangkuman kanan
                const rowHtml = `
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
                        <div>
                            <span class="fw-bold text-dark">${item.name}</span>
                            <br><small class="text-muted">${item.qty} pcs x Rp ${formatPrice(item.price)}</small>
                        </div>
                        <span class="fw-bold text-primary">Rp ${formatPrice(subtotal)}</span>
                    </div>`;
                cartList.insertAdjacentHTML('beforeend', rowHtml);

                // Buat Elemen Hidden Input Otomatis agar dibaca Array Request `products` oleh Controller Laravel
                hiddenContainer.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="products[${index}][product_id]" value="${id}">
                    <input type="hidden" name="products[${index}][qty]" value="${item.qty}">
                `);
                index++;
            });
        }

        // Tampilkan total akumulasi nilai belanja uang rupiah
        document.getElementById('total-amount').innerText = 'Rp ' + formatPrice(total);
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID').format(price);
    }

    // 4. Manajemen Event Logika Field Sekunder UI
    function toggleNewCustomerFields() {
        const sel = document.getElementById('customer_id');
        const newFields = document.getElementById('newCustomerFields');
        if (!sel || !newFields) return;
        newFields.style.display = sel.value === 'new' ? 'block' : 'none';
    }

    function handleStatusChange() {
        const status = document.getElementById('status').value;
        const kurirField = document.getElementById('kurirFieldCreate');
        if (!kurirField) return;
        kurirField.style.display = status === 'shipped' ? 'block' : 'none';
    }

    // Proteksi pengiriman form jika keranjang belanja masih kosong gulung tikar
    document.getElementById('mainOrderForm').addEventListener('submit', function(e) {
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            alert("Keranjang belanja kosong! Silakan pilih minimal 1 item produk terlebih dahulu.");
        }
    });

    window.addEventListener('load', function() {
        renderCart();
        toggleNewCustomerFields();
        handleStatusChange();
    });
</script>
@endpush