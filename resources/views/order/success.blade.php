<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Sukses - {{ $transaction_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-3xl p-6 shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-100 text-center">
        <!-- Icon Success Animation/Container -->
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 border border-green-100 animate-bounce">
            ✅
        </div>

        <!-- Header Status -->
        <h1 class="text-2xl font-black text-gray-800 mb-2">Pesanan Berhasil!</h1>
        <p class="text-sm text-gray-500 mb-6">Terima kasih telah berbelanja. Pesanan Anda sedang kami proses.</p>

        <!-- Detail Box -->
        <div class="bg-gray-50 rounded-2xl p-4 text-left space-y-3 mb-8">
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-medium">KODE PESANAN</span>
                <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-md">{{ $transaction_id }}</span>
            </div>
            <div class="border-t border-dashed border-gray-200 my-2"></div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Status Pembayaran</span>
                <span class="text-xs bg-green-100 text-green-700 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Menunggu Verifikasi</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            
            {{-- Opsional: Tombol Hubungi CS jika ada kendala --}}
            <a href="https://wa.me/628817243541?text=Halo%20Admin,%20saya%20ingin%20tanya%20pesanan%20{{ $transaction_id }}" 
               target="_blank" 
               class="block w-full bg-white text-gray-600 border border-gray-200 py-4 rounded-2xl font-bold active:scale-95 transition-all text-sm hover:bg-gray-50">
               Hubungi Admin (WhatsApp)
            </a>
        </div>
    </div>

</body>
</html>