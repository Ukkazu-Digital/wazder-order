<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - {{ $transaction_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .cart-bounce { animation: bounce 0.3s ease-in-out; }
        @keyframes bounce { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-30">
        <div class="max-w-md mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-blue-600">Smart Store</h1>
                <p class="text-[10px] text-gray-400 font-mono">{{ $transaction_id }}</p>
            </div>
            <div class="relative" id="cart-icon-container">
                <div class="p-2 bg-blue-50 rounded-full text-xl text-blue-600">🛒</div>
                <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full hidden font-bold border-2 border-white">0</span>
            </div>
        </div>
    </header>

    <main class="max-w-md mx-auto min-h-screen">
        
        <!-- Search Bar -->
        <section class="p-4 sticky top-[68px] bg-gray-50/95 backdrop-blur-sm z-20">
            <div class="relative">
                <input type="text" id="searchInput" onkeyup="filterProducts()" 
                    placeholder="Cari dari {{ $products->count() }} produk..." 
                    class="w-full p-4 pl-12 rounded-2xl border-none shadow-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <span class="absolute left-4 top-4 text-xl opacity-30">🔍</span>
            </div>
        </section>

        <!-- Product Grid -->
        <section class="px-4 py-2 mb-10">
            <div id="productGrid" class="grid grid-cols-2 gap-3">
                @foreach($products as $product)
                <div class="product-item bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex flex-col {{ $product->stock <= 0 ? 'opacity-60' : '' }}" 
                     data-name="{{ strtolower($product->name) }}">
                    
                    <!-- Thumbnail & Badge Stok -->
                    <div class="relative">
                        <div class="aspect-square bg-slate-100 rounded-xl flex items-center justify-center text-4xl mb-2">
                            {{-- Anda bisa ganti dengan <img src="{{ asset('storage/'.$product->image) }}"> jika ada --}}
                            📦
                        </div>
                        @if($product->stock <= 0)
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="bg-red-500 text-white text-[10px] px-2 py-1 rounded font-bold uppercase rotate-12">Habis</span>
                            </div>
                        @elseif($product->stock <= 5)
                            <span class="absolute top-2 right-2 bg-orange-500 text-white text-[8px] px-2 py-1 rounded-lg font-bold">Limit</span>
                        @endif
                    </div>

                    <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 px-1">{{ $product->name }}</h3>
                    <p class="text-blue-600 font-bold text-sm mb-1 px-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    
                    <!-- Info Stok Dinamis -->
                    <p id="stock-info-{{ $product->id }}" class="text-[10px] mb-3 px-1 font-semibold {{ $product->stock <= 5 ? 'text-orange-500 italic' : 'text-gray-400' }}">
                        {{ $product->stock <= 0 ? 'Stok Habis' : ($product->stock <= 5 ? "Sisa $product->stock stok!" : 'Stok Tersedia') }}
                    </p>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-between mt-auto bg-gray-50 rounded-xl p-1">
                        <button onclick="updateQty({{ $product->id }}, -1)" class="w-8 h-8 bg-white shadow-sm rounded-lg font-bold text-gray-400">-</button>
                        <span id="qty-{{ $product->id }}" class="text-sm font-bold">0</span>
                        @if($product->stock > 0)
                            <button onclick="updateQty({{ $product->id }}, 1, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})" 
                                class="w-8 h-8 bg-blue-600 text-white rounded-lg font-bold shadow-sm">+</button>
                        @else
                            <button disabled class="w-8 h-8 bg-gray-200 text-white rounded-lg font-bold">x</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Form Order -->
        <section class="p-4 mb-40 border-t border-gray-100 pt-10">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Data Pengiriman</h2>
            <form id="orderForm" class="space-y-4">
                @csrf
                <input type="hidden" name="kode_pesanan" value="{{ $transaction_id }}">
                
                <input type="text" name="nama" value="{{ $customers['nama'] }}" placeholder="Nama Lengkap" required 
                    class="w-full p-4 border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-500 outline-none">
                
                <input type="tel" name="wa" value="{{ $customers['wa'] }}" placeholder="No. WhatsApp" required 
                    class="w-full p-4 border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-500 outline-none" readonly>
                
                <textarea name="alamat" rows="3" value="{{ $customers['alamat'] }}" placeholder="Alamat Lengkap" required
                    class="w-full p-4 border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </form>
        </section>
    </main>

    <!-- Checkout Panel (Bottom) -->
    <div id="checkout-panel" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 p-4 shadow-[0_-15px_30px_rgba(0,0,0,0.08)] z-40 transform translate-y-full transition-transform duration-500">
        <div class="max-w-md mx-auto">
            <div id="cart-details" class="mb-4 text-sm hidden">
                <div id="cart-list" class="space-y-3 max-h-48 overflow-y-auto hide-scrollbar"></div>
                <div class="border-t border-dashed border-gray-200 my-4"></div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex-1 p-3 bg-gray-50 rounded-2xl" onclick="toggleDetails()">
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Total</p>
                    <p id="total-amount" class="text-lg font-black text-blue-600">Rp 0</p>
                </div>
                <button type="submit" form="orderForm" id="btnSubmit"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 active:scale-95 transition-all">
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </div>

    <script>
        let cart = {};

        function filterProducts() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(input) ? "flex" : "none";
            });
        }

        function updateQty(id, change, name = '', price = 0, maxStock = 0) {
            if (!cart[id]) cart[id] = { name: name, price: price, qty: 0 };
            
            let newQty = cart[id].qty + change;
            if (newQty > maxStock) return alert("Stok tidak mencukupi!");
            
            cart[id].qty = newQty;
            if (cart[id].qty <= 0) delete cart[id];

            renderCart();
        }

        function renderCart() {
            const list = document.getElementById('cart-list');
            const panel = document.getElementById('checkout-panel');
            let total = 0;
            let count = 0;

            list.innerHTML = '';
            document.querySelectorAll('[id^="qty-"]').forEach(el => el.innerText = '0');

            Object.keys(cart).forEach(id => {
                const item = cart[id];
                total += item.price * item.qty;
                count += item.qty;
                document.getElementById(`qty-${id}`).innerText = item.qty;
                list.innerHTML += `<div class="flex justify-between"><span>${item.name} x${item.qty}</span><b>Rp ${(item.price * item.qty).toLocaleString('id-ID')}</b></div>`;
            });

            document.getElementById('total-amount').innerText = `Rp ${total.toLocaleString('id-ID')}`;
            document.getElementById('cart-badge').innerText = count;
            document.getElementById('cart-badge').classList.toggle('hidden', count === 0);
            panel.classList.toggle('translate-y-full', count === 0);
        }

        function toggleDetails() { document.getElementById('cart-details').classList.toggle('hidden'); }

        document.getElementById('orderForm').onsubmit = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerText = "Memproses...";

            const formData = new FormData(this);
            const payload = {
                nama: formData.get('nama'),
                wa: formData.get('wa'),
                alamat: formData.get('alamat'),
                kode_pesanan: formData.get('kode_pesanan'),
                cart: cart
            };

            try {
                const resp = await fetch('/order/store', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': formData.get('_token') },
                    body: JSON.stringify(payload)
                });
                const res = await resp.json();
                if (res.success) {
                    alert("Berhasil! " + res.message);
                    window.location.reload();
                } else {
                    alert(res.message);
                }
            } catch (err) {
                alert("Koneksi gagal "+err);
            } finally {
                btn.disabled = false;
                btn.innerText = "Pesan Sekarang";
            }
        };
    </script>
</body>
</html>