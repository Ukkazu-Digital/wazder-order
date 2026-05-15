<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan - {{ $order->order_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <nav class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-md mx-auto px-4 py-4 flex items-center gap-4">
            <a href="javascript:history.back()" class="p-2 hover:bg-gray-100 rounded-full transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-lg font-bold">Detail Pesanan</h1>
        </div>
    </nav>

    <main class="max-w-md mx-auto p-4 space-y-6">

        <section class="bg-blue-600 rounded-3xl p-6 text-white shadow-xl shadow-blue-100">
            <p class="text-blue-100 text-xs font-semibold uppercase tracking-widest mb-1">Status Saat Ini</p>
            <h2 class="text-2xl font-black mb-4">
                @if($order->status == 'pending') Sedang Diproses 📦
                @elseif($order->status == 'paid') Sudah Dibayar ✅
                @elseif($order->status == 'shipped') Dalam Perjalanan 🚚
                @elseif($order->status == 'completed') Selesai Diterima ✨
                @else Dibatalkan ❌ @endif
            </h2>
            <div class="flex justify-between items-end border-t border-blue-500 pt-4 mt-4">
                <div>
                    <p class="text-[10px] text-blue-200 uppercase">ID Transaksi</p>
                    <p class="text-sm font-mono font-bold">{{ $order->order_code }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-blue-200 uppercase">Estimasi Tiba</p>
                    <p class="text-sm font-bold">-</p>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Lacak Progress</h3>
            
            <div class="space-y-8 relative">
                <div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-gray-100"></div>

                <div class="flex items-start gap-4 relative">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs z-10 border-4 border-white">✓</div>
                    <div>
                        <p class="text-sm font-bold">Pesanan Berhasil Dibuat</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 relative">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['paid','shipped','completed']) ? 'bg-blue-600' : 'bg-gray-200' }} flex items-center justify-center text-white text-xs z-10 border-4 border-white">✓</div>
                    <div>
                        <p class="text-sm font-bold {{ in_array($order->status, ['paid','shipped','completed']) ? '' : 'text-gray-300' }}">Konfirmasi Pembayaran</p>
                        <p class="text-xs text-gray-400">Pembayaran tervalidasi</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 relative">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['shipped','completed']) ? 'bg-blue-600 shadow-lg shadow-blue-200' : 'bg-gray-200' }} flex items-center justify-center text-white text-xs z-10 border-4 border-white">🚚</div>
                    <div>
                        <p class="text-sm font-bold {{ in_array($order->status, ['shipped','completed']) ? '' : 'text-gray-300' }}">Sedang Dikirim</p>
                        <p class="text-xs text-gray-400">{{ in_array($order->status, ['shipped','completed']) ? 'Kurir sedang menuju lokasi Anda' : '' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Rincian Barang</h3>
            <div class="space-y-4">
                @foreach($order->details as $detail)
                <div class="flex justify-between items-center text-sm">
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">{{ $detail->product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $detail->qty }} x Rp {{ number_format($detail->buy_price, 0, ',', '.') }}</p>
                    </div>
                    <p class="font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
                <div class="border-t border-dashed pt-4 flex justify-between items-center mt-4">
                    <p class="text-sm font-bold">Total Pembayaran</p>
                    <p class="text-lg font-black text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3">Tujuan Pengiriman</h3>
            <p class="font-bold text-sm text-gray-800 mb-1">{{ $order->customer->customers_name }}</p>
            <p class="text-xs text-gray-500 leading-relaxed">{{ $order->customer->address }}</p>
            
            {{-- Tombol Buka Maps --}}
            <a href="https://www.google.com/maps?q={{ $order->customer->latitude }},{{ $order->customer->longitude }}" 
               target="_blank"
               class="mt-4 flex items-center justify-center gap-2 w-full p-3 bg-gray-50 text-gray-600 rounded-2xl text-xs font-bold border border-gray-100 hover:bg-gray-100 transition">
                📍 Lihat di Peta
            </a>
        </section>

    </main>

    <div class="max-w-md mx-auto p-4 pb-10">
        <button onclick="window.location.reload()" class="w-full bg-white border-2 border-blue-600 text-blue-600 font-bold py-4 rounded-2xl hover:bg-blue-50 transition">
            Refresh Status
        </button>
        <p class="text-center text-[10px] text-gray-400 mt-4 uppercase tracking-tighter">Terakhir diperbarui: {{ now()->format('H:i') }} WIB</p>
    </div>

</body>
</html>