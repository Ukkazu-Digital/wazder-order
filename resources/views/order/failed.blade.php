<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Gagal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-3xl p-6 shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-100 text-center">
        <!-- Icon Failed Container -->
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 border border-red-100">
            ❌
        </div>

        <!-- Header Status -->
        <h1 class="text-2xl font-black text-gray-800 mb-2">Pesanan Gagal</h1>
        <p class="text-sm text-gray-500 mb-6">Mohon maaf, sistem tidak dapat memproses pesanan Anda saat ini.</p>

        <!-- Error Message Box -->
        <div class="bg-red-50/50 border border-red-100 rounded-2xl p-4 text-left mb-8">
            <p class="text-xs text-red-400 font-bold uppercase tracking-wider mb-1">Alasan Masalah:</p>
            <p class="text-sm text-red-700 font-semibold">
                {{ $message ?? 'Stok produk tiba-tiba habis atau terjadi gangguan koneksi server.' }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            {{-- Mengembalikan user ke halaman sebelumnya (keranjang/katalog) --}}
            <button onclick="window.history.back()" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 active:scale-95 transition-all text-sm">
                Coba Lagi
            </button>
            
            {{-- Opsional: Tombol Hubungi CS jika ada kendala --}}      
            <a href="https://wa.me/628817243541?text=Halo%20Admin,%20saya%20ingin%20tanya%20pesanan%20yang%20gagal" 
               target="_blank" 
               class="block w-full bg-white text-gray-600 border border-gray-200 py-4 rounded-2xl font-bold active:scale-95 transition-all text-sm hover:bg-gray-50">
               Hubungi Admin (WhatsApp)
        </div>
    </div>

</body>
</html>