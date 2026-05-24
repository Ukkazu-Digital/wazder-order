<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - {{ $transaction_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .cart-bounce { animation: bounce 0.3s ease-in-out; }
        @keyframes bounce { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <header class="bg-white/80 backdrop-blur-xl shadow-sm sticky top-0 z-30 border-b border-slate-100">
        <div class="max-w-md mx-auto px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-md shadow-indigo-200">
                    {{ strtoupper(config('app.name', 'Larawaba')[0]) }}
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight">{{ config('app.name', 'Larawaba') }}</h1>
                    <p class="text-[10px] text-slate-400 font-medium font-mono tracking-wider">{{ $transaction_id }}</p>
                </div>
            </div>
            <div class="relative cursor-pointer transition-transform active:scale-90" id="cart-icon-container" onclick="scrollToCheckout()">
                <div class="p-2.5 bg-slate-50 rounded-xl text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </div>
                <span id="cart-badge" class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full hidden font-bold border-2 border-white shadow-sm">0</span>
            </div>
        </div>
    </header>

    <main class="max-w-md mx-auto min-h-screen px-4 pb-36">
        
        <section class="py-3 sticky top-[65px] bg-slate-50/95 backdrop-blur-sm z-20">
            <div class="relative group">
                <input type="text" id="searchInput" onkeyup="filterProducts()" 
                    placeholder="Cari dari {{ $products->count() }} pilihan produk..." 
                    class="w-full p-4 pl-12 rounded-2xl border border-slate-200/60 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white transition-all text-sm placeholder:text-slate-400">
                <span class="absolute left-4 top-4 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604z" />
                    </svg>
                </span>
            </div>
        </section>

        <section class="py-2">
            <div id="productGrid" class="grid grid-cols-2 gap-3.5">
                @foreach($products as $product)
                @php
                    $currentStock = $product->totalStock();
                @endphp
                <div class="product-item bg-white p-2.5 rounded-2xl shadow-sm border border-slate-100 flex flex-col transition-all duration-300 relative group {{ $currentStock <= 0 ? 'opacity-50 select-none' : 'hover:shadow-md' }}" 
                     id="product-card-{{ $product->id }}"
                     data-name="{{ strtolower($product->name) }}">
                    
                    <div class="relative overflow-hidden rounded-xl mb-3">
                        <div class="aspect-square bg-gradient-to-tr from-slate-50 to-slate-100 flex items-center justify-center text-slate-300 group-hover:scale-105 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-14 h-14 opacity-60">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                        </div>
                        @if($currentStock <= 0)
                            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[1px] flex items-center justify-center">
                                <span class="bg-rose-600 text-white text-[10px] tracking-wider px-2.5 py-1 rounded-lg font-black uppercase shadow-sm">Habis</span>
                            </div>
                        @elseif($currentStock <= 5)
                            <span class="absolute top-2 right-2 bg-amber-500 text-white text-[9px] px-2 py-0.5 rounded-md font-bold tracking-wide shadow-sm animate-pulse">Sisa {{ $currentStock }}</span>
                        @endif
                    </div>

                    <h3 class="text-xs font-bold text-slate-800 line-clamp-2 h-9 px-1 leading-relaxed">{{ $product->name }}</h3>
                    <p class="text-indigo-600 font-extrabold text-sm mt-1 px-1">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    
                    <p id="stock-info-{{ $product->id }}" class="text-[9px] mt-1 mb-3 px-1 font-medium tracking-wide {{ $currentStock <= 5 ? 'text-amber-600 italic' : 'text-slate-400' }}">
                        {{ $currentStock <= 0 ? 'Stok Tidak Tersedia' : ($currentStock <= 5 ? 'Segera Amankan!' : 'Stok Siap Kirim') }}
                    </p>

                    <div class="flex items-center justify-between mt-auto bg-slate-50 border border-slate-100 rounded-xl p-1">
                        <button onclick="updateQty({{ $product->id }}, -1)" class="w-8 h-8 bg-white shadow-sm rounded-lg font-bold text-slate-500 hover:bg-slate-100 active:scale-90 transition-all">-</button>
                        <span id="qty-{{ $product->id }}" class="text-xs font-bold text-slate-800">0</span>
                        
                        @if($currentStock > 0)
                            <button onclick="updateQty({{ $product->id }}, 1, '{{ $product->name }}', {{ $product->selling_price }}, {{ $currentStock }})" 
                                class="w-8 h-8 bg-indigo-600 text-white rounded-lg font-bold shadow-sm shadow-indigo-100 hover:bg-indigo-700 active:scale-90 transition-all">+</button>
                        @else
                            <button disabled class="w-8 h-8 bg-slate-200 text-slate-400 rounded-lg font-bold cursor-not-allowed">×</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <section id="shipping-section" class="mt-8 border-t border-slate-200/60 pt-6">
            <div class="bg-indigo-900 text-white p-4 rounded-2xl shadow-sm mb-5 flex gap-3 items-center">
                <div class="p-2.5 bg-white/10 rounded-xl text-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-200">Informasi Pelanggan</h4>
                    <p class="text-[11px] text-white/80 leading-normal">Data alamat akan dicatat otomatis ke riwayat akun WA Anda.</p>
                </div>
            </div>

            <form id="orderForm" class="space-y-4">
                @csrf
                <input type="hidden" name="kode_pesanan" value="{{ $transaction_id }}">
                
                <div class="relative">
                    <input type="text" name="nama" value="{{ $customers['nama'] }}" placeholder="Nama Penerima" required 
                        class="w-full p-4 pl-12 border border-slate-200 bg-white rounded-2xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm placeholder:text-slate-400">
                    <span class="absolute left-4 top-4 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </span>
                </div>
                
                <div class="relative">
                    <input type="tel" name="wa" value="{{ $customers['wa'] }}" placeholder="No. WhatsApp" required 
                        class="w-full p-4 pl-12 border border-slate-200 bg-slate-50 text-slate-500 rounded-2xl shadow-sm outline-none text-sm font-semibold" readonly>
                    <span class="absolute left-4 top-4 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                    </span>
                </div>
                
                <div class="relative">
                    <textarea name="alamat" rows="3" placeholder="Alamat Pengiriman Lengkap" required
                        class="w-full p-4 pl-12 border border-slate-200 bg-white rounded-2xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm placeholder:text-slate-400 leading-relaxed">{{ $customers['alamat'] }}</textarea>
                    <span class="absolute left-4 top-4 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21.75h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21.75h8.25" />
                        </svg>
                    </span>
                </div>
            </form>
        </section>
    </main>

    <div id="checkout-panel" class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-xl border-t border-slate-100 p-4 shadow-[0_-12px_30px_rgba(15,23,42,0.08)] z-40 transform translate-y-full transition-transform duration-500">
        <div class="max-w-md mx-auto">
            <div id="cart-details" class="mb-4 text-xs font-medium hidden">
                <div class="text-slate-400 uppercase tracking-widest text-[9px] font-bold mb-3">Item Dipilih</div>
                <div id="cart-list" class="space-y-3.5 max-h-44 overflow-y-auto hide-scrollbar"></div>
                <div class="border-t border-dashed border-slate-200 my-4"></div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="flex-1 p-3 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between cursor-pointer" onclick="toggleDetails()">
                    <div>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Total Tagihan</p>
                        <p id="total-amount" class="text-base font-extrabold text-indigo-600">Rp 0</p>
                    </div>
                    <div class="text-slate-400 pr-1" id="chevron-indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 transition-transform" id="cart-chevron">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                        </svg>
                    </div>
                </div>
                <button type="submit" form="orderForm" id="btnSubmit"
                    class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-bold text-sm shadow-md shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all">
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
            if (!cart[id]) {
                cart[id] = { name: name, price: price, qty: 0, maxStock: maxStock };
            } else {
                if (maxStock > 0) {
                    cart[id].maxStock = maxStock;
                }
            }
            
            let newQty = cart[id].qty + change;
            
            if (change > 0 && newQty > cart[id].maxStock) {
                return alert("Maaf, stok produk tidak mencukupi batas!");
            }
            
            cart[id].qty = newQty;
            
            const cardElement = document.getElementById(`product-card-${id}`);
            if (cart[id].qty <= 0) {
                delete cart[id];
                if (cardElement) cardElement.classList.remove('ring-2', 'ring-indigo-500', 'border-transparent');
            } else {
                if (cardElement) cardElement.classList.add('ring-2', 'ring-indigo-500', 'border-transparent');
            }

            // Animasi Bounce di Ikon Keranjang saat ada perubahan jumlah
            const cartIcon = document.getElementById('cart-icon-container');
            cartIcon.classList.add('cart-bounce');
            setTimeout(() => cartIcon.classList.remove('cart-bounce'), 300);

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
                
                list.innerHTML += `
                    <div class="flex justify-between items-center text-slate-700">
                        <span class="font-medium text-xs">${item.name} <span class="text-indigo-600 font-bold ml-1">x${item.qty}</span></span>
                        <b class="text-xs font-bold text-slate-900">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</b>
                    </div>`;
            });

            document.getElementById('total-amount').innerText = `Rp ${total.toLocaleString('id-ID')}`;
            document.getElementById('cart-badge').innerText = count;
            document.getElementById('cart-badge').classList.toggle('hidden', count === 0);
            panel.classList.toggle('translate-y-full', count === 0);
        }

        function toggleDetails() { 
            const details = document.getElementById('cart-details');
            const chevron = document.getElementById('cart-chevron');
            details.classList.toggle('hidden');
            if (details.classList.contains('hidden')) {
                chevron.style.transform = 'rotate(0deg)';
            } else {
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        function scrollToCheckout() {
            document.getElementById('shipping-section').scrollIntoView({ behavior: 'smooth' });
        }

        document.getElementById('orderForm').onsubmit = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = `
                <span class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>`;

            const formData = new FormData(this);
            const payload = {
                nama: formData.get('nama'),
                wa: formData.get('wa'),
                alamat: formData.get('alamat'),
                kode_pesanan: formData.get('kode_pesanan'),
                cart: cart
            };

            try {
                const resp = await fetch('{{ route("order.store") }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': formData.get('_token') 
                    },
                    body: JSON.stringify(payload)
                });
                
                const res = await resp.json();
                
                if (res.success) {
                    // Berhasil: Mengarah ke route('order.success', ['kode_pesanan' => $id])
                    let successUrl = '{{ route("order.success", ["kode_pesanan" => ":kode_pesanan"]) }}';
                    window.location.href = successUrl.replace(":kode_pesanan", payload.kode_pesanan);
                } else {
                    // Gagal: Mengarah ke route('order.failed', ['msg' => $message])
                    let failedUrl = '{{ route("order.failed", ["msg" => ":msg"]) }}';
                    window.location.href = failedUrl.replace(":msg", encodeURIComponent(res.message));
                }
            } catch (err) {
                alert("Koneksi gagal " + err);
            } finally {
                btn.disabled = false;
                btn.innerText = "Pesan Sekarang";
            }
        };
    </script>
</body>
</html>