<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | Kami Akan Segera Kembali</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#0b0f19] text-gray-200 flex h-full flex-col items-center justify-center px-6 relative overflow-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Background Glow Effects -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-900/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full bg-purple-900/20 blur-[120px] pointer-events-none"></div>

    <!-- Main Content Card -->
    <div class="w-full max-w-md text-center z-10">
        
        <!-- Animated Icon Container -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/20 mb-8 animate-float">
            <!-- Wrench / Tool Icon -->
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.67 2.67 0 1113.5 17.25l5.83-5.83m-7.91-1.34L3.75 3.75m16.5 16.5l-5.83-5.83m0 0l1.34-1.34M7.5 10.5L3.75 14.25L3.75 20.25L9.75 20.25L13.5 16.5M10.5 7.5l3.75-3.75h6v6L16.5 13.5M10.5 7.5L3.75 3.75M16.5 13.5l3.75 3.75"></path>
            </svg>
        </div>

        <!-- Heading -->
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            Sistem Sedang <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Pembaruan</span>
        </h1>
        
        <!-- Description -->
        <p class="mt-4 text-base text-gray-400 leading-relaxed">
Kami sedang memperbarui struktur basis data demi meningkatkan kecepatan dan stabilitas platform. Akses ditutup sementara untuk menjaga keamanan data Anda selama proses migrasi.        </p>

        <!-- Status Indicator -->
        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-900/60 border border-gray-800 backdrop-blur-sm text-sm font-medium text-indigo-400">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
            </span>
            Kami akan kembali
        </div>

        <!-- Contact Section -->
        <div class="mt-8 pt-6 border-t border-gray-900/60">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Ada pertanyaan mendesak?</p>
            <a href="mailto:rismawan.email@gmail.com" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900/40 border border-gray-800 text-sm text-gray-300 hover:text-white hover:border-indigo-500/50 transition-all duration-300 group">
                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                rismawan.email@gmail.com
            </a>
        </div>

        <!-- Footer Note -->
        <div class="mt-12 text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name', 'Platform') }}. All rights reserved.
        </div>
    </div>

</body>
</html>