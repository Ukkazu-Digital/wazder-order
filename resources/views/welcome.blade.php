<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rismawan Junandia | Full-Stack Web & Mobile Developer</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans">

    <!-- NAVIGATION BAR -->
    <nav class="fixed top-0 left-0 right-0 bg-slate-900/90 backdrop-blur-md z-50 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="#" class="text-xl font-bold text-emerald-400 tracking-wider">JUNANDIA<span class="text-white">.MY.ID</span></a>
            <div class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="#tentang" class="hover:text-emerald-400 transition">Tentang</a>
                <a href="#keahlian" class="hover:text-emerald-400 transition">Keahlian</a>
                <a href="#proyek" class="hover:text-emerald-400 transition">Proyek</a>
                <a href="#kontak" class="bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-4 py-2 rounded-md font-semibold transition">Hubungi Saya</a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="min-h-screen flex items-center justify-center pt-20 px-4">
        <div class="max-w-4xl text-center">
            <h2 class="text-emerald-400 font-semibold tracking-wide uppercase text-sm md:text-base mb-3">Halo semua, saya</h2>
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-4 bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">
                Rismawan Junandia
            </h1>
            <p class="text-xl md:text-2xl text-slate-400 mb-8 max-w-2xl mx-auto">
                Seorang <span class="text-emerald-400 font-medium">Full-Stack Web & Mobile Developer</span> yang berfokus pada pembangunan aplikasi berkinerja tinggi dan otomatisasi IT.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#proyek" class="bg-emerald-500 hover:bg-emerald-600 text-slate-900 px-6 py-3 rounded-md font-bold transition shadow-lg shadow-emerald-500/20">
                    Lihat Portofolio
                </a>
                <a href="#kontak" class="border border-slate-700 hover:border-emerald-400 px-6 py-3 rounded-md font-bold transition">
                    Diskusikan Proyek
                </a>
            </div>
        </div>
    </section>

    <!-- TENTANG SAYA -->
    <section id="tentang" class="py-24 bg-slate-950/50 border-y border-slate-800/50">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-12 items-center">
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4 text-emerald-400">Tentang Saya</h3>
                    <p class="text-slate-400 leading-relaxed mb-4">
                        Saya memiliki minat yang besar dalam mengembangkan sistem berbasis web dan aplikasi mobile. Selain pengodean standar, saya juga aktif dalam membangun solusi SaaS (Software as a Service) dan integrasi otomatisasi pihak ketiga.
                    </p>
                    <p class="text-slate-400 leading-relaxed">
                        Selalu berkomitmen untuk menghasilkan kode yang bersih, struktur database yang efisien, serta pengalaman pengguna (*user experience*) yang lancar.
                    </p>
                </div>
                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 text-center">
                    <div class="text-4xl font-bold text-emerald-400 mb-2">5+</div>
                    <div class="text-sm uppercase tracking-wider text-slate-500 font-semibold">Tahun Pengalaman di Bidang IT</div>
                </div>
            </div>
        </div>
    </section>

    <!-- KEAHLIAN / TECH STACK -->
    <section id="keahlian" class="py-24">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h3 class="text-2xl font-bold mb-2 text-emerald-400">Keahlian & Teknologi</h3>
            <p class="text-slate-400 mb-12 max-w-xl mx-auto">Beberapa teknologi dan framework yang sering saya gunakan untuk menyelesaikan berbagai solusi digital.</p>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="p-6 bg-slate-950 rounded-xl border border-slate-800 hover:border-emerald-500/50 transition">
                    <i class="fab fa-laravel text-4xl text-red-500 mb-4"></i>
                    <h4 class="font-semibold">Laravel / PHP</h4>
                </div>
                <div class="p-6 bg-slate-950 rounded-xl border border-slate-800 hover:border-emerald-500/50 transition">
                    <i class="fab fa-js text-4xl text-yellow-400 mb-4"></i>
                    <h4 class="font-semibold">JavaScript</h4>
                </div>
                <div class="p-6 bg-slate-950 rounded-xl border border-slate-800 hover:border-emerald-500/50 transition">
                    <i class="fas fa-database text-4xl text-blue-400 mb-4"></i>
                    <h4 class="font-semibold">MySQL / SQL</h4>
                </div>
                <div class="p-6 bg-slate-950 rounded-xl border border-slate-800 hover:border-emerald-500/50 transition">
                    <i class="fab fa-whatsapp text-4xl text-green-400 mb-4"></i>
                    <h4 class="font-semibold">Integrasi API</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- PROYEK -->
    <section id="proyek" class="py-24 bg-slate-950/50 border-t border-slate-800/50">
        <div class="max-w-5xl mx-auto px-4">
            <h3 class="text-2xl font-bold mb-2 text-center text-emerald-400">Proyek Terbaru</h3>
            <p class="text-slate-400 text-center mb-12 max-w-xl mx-auto">Beberapa sistem dan aplikasi yang telah berhasil saya kembangkan.</p>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Proyek 1 -->
                <div class="bg-slate-900 rounded-xl overflow-hidden border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between">
                    <div class="p-6">
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-1">Web Application</div>
                        <h4 class="text-xl font-bold mb-2">Sistem ERP & POS Pintar</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Sistem manajemen inventaris dan penjualan yang diintegrasikan dengan notifikasi otomatis untuk memudahkan operasional bisnis harian.
                        </p>
                    </div>
                    <div class="p-6 pt-0 flex gap-2 text-xs font-semibold text-slate-400">
                        <span class="bg-slate-950 px-3 py-1 rounded">Laravel</span>
                        <span class="bg-slate-950 px-3 py-1 rounded">MySQL</span>
                        <span class="bg-slate-950 px-3 py-1 rounded">Tailwind</span>
                    </div>
                </div>

                <!-- Proyek 2 -->
                <div class="bg-slate-900 rounded-xl overflow-hidden border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between">
                    <div class="p-6">
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-1">SaaS Platform</div>
                        <h4 class="text-xl font-bold mb-2">Platform Undangan Digital</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Sistem mandiri (DIY) yang ramah SEO, memungkinkan pengguna membuat halaman undangan pernikahan digital mereka sendiri secara real-time.
                        </p>
                    </div>
                    <div class="p-6 pt-0 flex gap-2 text-xs font-semibold text-slate-400">
                        <span class="bg-slate-950 px-3 py-1 rounded">PHP</span>
                        <span class="bg-slate-950 px-3 py-1 rounded">Automation</span>
                        <span class="bg-slate-950 px-3 py-1 rounded">Blade</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HUBUNGI SAYA / KONTAK -->
    <section id="kontak" class="py-24">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h3 class="text-2xl font-bold mb-2 text-emerald-400">Mari Bekerja Sama</h3>
            <p class="text-slate-400 mb-8">Apakah Anda memiliki ide proyek atau membutuhkan dukungan teknis untuk sistem IT Anda? Hubungi saya sekarang.</p>
            
            <div class="bg-slate-950 p-8 rounded-2xl border border-slate-800 max-w-md mx-auto">
                <p class="text-sm text-slate-400 mb-6">Anda bisa langsung mengirimkan email atau terhubung dengan saya melalui tautan berikut:</p>
                <div class="flex flex-col gap-3">
                    <a href="mailto:rismawan.email@gmail.com" class="flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-850 border border-slate-800 py-3 rounded-lg transition font-medium">
                        <i class="fas fa-envelope text-emerald-400"></i> rismawan.email@gmail.com
                    </a>
                    <a href="https://www.linkedin.com/in/rjunandia/" target="_blank" class="flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-850 border border-slate-800 py-3 rounded-lg transition font-medium">
                        <i class="fab fa-linkedin text-blue-400"></i> Terhubung di LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-8 border-t border-slate-800 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4">
            <p>&copy; 2026 Rismawan Junandia. Hak Cipta Dilindungi Undang-Undang.</p>
        </div>
    </footer>

</body>
</html>