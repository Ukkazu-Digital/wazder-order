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
        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 border border-red-100">
            ❌
        </div>

        <h1 class="text-2xl font-black text-gray-800 mb-2">Akses Gagal</h1>
        <p class="text-sm text-gray-500 mb-6">Mohon maaf, sistem tidak dapat memproses permintaan Anda saat ini.</p>

        <div class="bg-red-50/50 border border-red-100 rounded-2xl p-4 text-left mb-8">
            <p class="text-xs text-red-400 font-bold uppercase tracking-wider mb-1">Alasan Masalah:</p>
            <p class="text-sm text-red-700 font-semibold">
                {{-- Diubah ke $msg sesuai dengan parameter di route group --}}
                {{ isset($msg) ? urldecode($msg) : 'Stok produk tiba-tiba habis atau terjadi gangguan koneksi server.' }}
            </p>
        </div>

        <div class="space-y-3">
            {{-- Deteksi jika pesan mengandung kata kedaluwarsa / expired, sembunyikan tombol Coba Lagi --}}
            @if(!Str::contains(strtolower($msg ?? ''), ['kedaluwarsa', 'expired']))
                <button onclick="window.history.back()" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 active:scale-95 transition-all text-sm">
                    Coba Lagi
                </button>
            @else
                <button onclick="window.close()" class="w-full bg-gray-100 text-gray-600 py-4 rounded-2xl font-bold active:scale-95 transition-all text-sm hover:bg-gray-200">
                    Tutup Halaman
                </button>
            @endif
            
            {{-- Tombol Hubungi CS --}}      
            <a href="https://wa.me/628817243541?text=Halo%20Admin,%20tautan%20katalog%20saya%20bermasalah:%20{{ $msg ?? '' }}" 
               target="_blank" 
               class="block w-full bg-white text-gray-600 border border-gray-200 py-4 rounded-2xl font-bold active:scale-95 transition-all text-sm hover:bg-gray-50">
                Hubungi Admin (WhatsApp)
            </a>
        </div>
    </div>

</body>
</html>