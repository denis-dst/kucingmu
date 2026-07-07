<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KucingMu - Kesehatan Kucing & Syiar Dakwah Muhammadiyah</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-2">
                <span class="text-3xl">🐱</span>
                <span class="font-outfit font-extrabold text-teal-800 text-xl tracking-tight">KucingMu</span>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#tentang" class="hover:text-teal-700 transition">Tentang</a>
                <a href="#fitur" class="hover:text-teal-700 transition">Layanan</a>
                <a href="#verifikasi" class="hover:text-teal-700 transition">Verifikasi KTAM</a>
                <a href="#kontak" class="hover:text-teal-700 transition">Hubungi Kami</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="button-primary px-4 py-2 text-xs">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-secondary px-4 py-2 text-xs">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="button-primary px-4 py-2 text-xs">
                                Daftar
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-teal-900 via-teal-800 to-sky-800 text-white py-20 lg:py-28 overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-12 lg:grid-cols-12 items-center relative">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-teal-200 text-xs font-semibold uppercase tracking-wider">
                    🐱 Inisiatif Komunitas & Dakwah Muhammadiyah
                </span>
                <h1 class="font-outfit text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                    Kepedulian Kesehatan Kucing & Syiar Manfaat Nyata
                </h1>
                <p class="text-base sm:text-lg text-teal-100/90 leading-relaxed max-w-xl">
                    KucingMu adalah platform terpadu bagi warga Muhammadiyah untuk mendaftarkan kucing kesayangan, melacak riwayat medis pemeriksaan dokter hewan, serta menerbitkan Kartu Tanda Anggota Muhammadiyah (KTAM) khusus kucing.
                </p>
                <div class="pt-4 flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="rounded-xl bg-white text-teal-900 px-6 py-3.5 text-sm font-bold shadow-md hover:bg-teal-50 transition">
                        Daftar Kucing Anda (Free)
                    </a>
                    <a href="#verifikasi" class="rounded-xl border border-white/30 bg-white/10 text-white px-6 py-3.5 text-sm font-bold hover:bg-white/20 transition backdrop-blur-sm">
                        Cek Validitas KTAM
                    </a>
                </div>
            </div>
            
            <!-- Graphic Card Presentation -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="w-full max-w-sm rounded-3xl border border-white/20 bg-white/10 p-6 shadow-2xl backdrop-blur-md relative transform hover:rotate-2 transition duration-500">
                    <div class="flex justify-between items-start border-b border-white/20 pb-4 mb-4">
                        <div>
                            <span class="text-2xl">🐱</span>
                            <h3 class="font-outfit text-lg font-bold text-white mt-1">KucingMu Card</h3>
                        </div>
                        <span class="bg-teal-400 text-teal-950 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Pilot Test</span>
                    </div>

                    <div class="space-y-3 text-xs text-teal-100">
                        <div class="flex justify-between">
                            <span>Nama Kucing:</span>
                            <strong class="text-white">Mochi</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Ras / Breed:</span>
                            <strong class="text-white">Persia</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Nomor KTAM:</span>
                            <strong class="text-white font-mono">KM-20260707-0001</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Pemilik / NBM:</span>
                            <strong class="text-white">Siti Rahma (NBM-MEMBER-01)</strong>
                        </div>
                    </div>

                    <!-- Mini decorative QR -->
                    <div class="absolute bottom-6 right-6 h-10 w-10 bg-white rounded p-0.5 opacity-80">
                        <div class="w-full h-full bg-slate-800"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Panel -->
    <section class="bg-white border-b border-slate-200 py-10 shadow-sm relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 grid-cols-2 md:grid-cols-4 text-center">
            <div>
                <div class="font-outfit text-3xl sm:text-4xl font-extrabold text-teal-700">60 Ekor</div>
                <div class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Pilot Project Kucing</div>
            </div>
            <div>
                <div class="font-outfit text-3xl sm:text-4xl font-extrabold text-teal-700">100% Free</div>
                <div class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Layanan Pemeriksaan</div>
            </div>
            <div>
                <div class="font-outfit text-3xl sm:text-4xl font-extrabold text-teal-700">4+ Peran</div>
                <div class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Kolaborasi Terpadu</div>
            </div>
            <div>
                <div class="font-outfit text-3xl sm:text-4xl font-extrabold text-teal-700">Real-Time</div>
                <div class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Sinkronisasi Lapangan</div>
            </div>
        </div>
    </section>

    <!-- Program Overview (Tentang) -->
    <section id="tentang" class="py-20 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto">
                <span class="eyebrow">Tentang KucingMu</span>
                <h2 class="mt-2 text-3xl font-bold text-slate-900 font-outfit">Sinergi Komunitas & Etika Dakwah</h2>
                <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                    KucingMu adalah langkah inovatif dalam merangkul komunitas pecinta kucing di lingkungan Muhammadiyah. Program ini menggabungkan layanan medis veteriner gratis dengan pencatatan digital anggota berbasis dakwah kultural bil-hal (manfaat nyata).
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                <div class="content-card bg-white p-6 rounded-2xl border border-slate-200">
                    <div class="text-2xl">🏥</div>
                    <h3 class="font-bold text-slate-900 mt-4">Klinik & Dokter Mitra</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Pemeriksaan klinis umum, konsultasi medis, serta pemberian obat-obatan non-invasif yang diawasi oleh dokter hewan profesional berlisensi.
                    </p>
                </div>

                <div class="content-card bg-white p-6 rounded-2xl border border-slate-200">
                    <div class="text-2xl">🎫</div>
                    <h3 class="font-bold text-slate-900 mt-4">Penerbitan KTAM Kucing</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Tanda apresiasi berupa nomor keanggotaan khusus KucingMu lengkap dengan QR Code validasi rekam medis yang dapat diakses publik.
                    </p>
                </div>

                <div class="content-card bg-white p-6 rounded-2xl border border-slate-200">
                    <div class="text-2xl">📡</div>
                    <h3 class="font-bold text-slate-900 mt-4">Pencatatan Offline-First</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        Relawan dapat mendaftarkan kucing dan melakukan check-in tanpa hambatan di lokasi meskipun jaringan internet lokal tidak stabil.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid (Fitur) -->
    <section id="fitur" class="py-20 bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-12 lg:grid-cols-2 items-center">
            <div>
                <span class="eyebrow">Fitur Layanan Medis</span>
                <h2 class="mt-2 text-3xl font-bold text-slate-900 font-outfit leading-snug">Layanan Lengkap untuk 60 Kucing Pertama</h2>
                <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                    Setiap kucing yang terdaftar dalam proyek percontohan (pilot project) ini berhak mendapatkan serangkaian fasilitas pemeriksaan medis dasar tanpa dipungut biaya apapun.
                </p>

                <div class="mt-8 space-y-4">
                    <div class="flex gap-4 items-start">
                        <div class="h-6 w-6 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center text-xs font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Pemberian Obat Cacing (Deworming)</h4>
                            <p class="text-xs text-slate-500 mt-1">Mengeliminasi parasit usus guna meningkatkan penyerapan nutrisi kucing.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="h-6 w-6 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center text-xs font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Pengobatan Kutu (Anti-Flea)</h4>
                            <p class="text-xs text-slate-500 mt-1">Mencegah iritasi kulit, kerontokan bulu, serta penularan parasit eksternal.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="h-6 w-6 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center text-xs font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Suplemen & Vitamin</h4>
                            <p class="text-xs text-slate-500 mt-1">Diberikan sesuai kondisi kebutuhan tubuh kucing agar imunitas tetap terjaga.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200">
                <h3 class="font-outfit text-xl font-bold text-slate-950 mb-4 border-b border-slate-200/60 pb-3">4 Peran Saling Terintegrasi</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center gap-4">
                        <span class="text-xl">🐱</span>
                        <div>
                            <strong class="text-sm block text-slate-900">Pemilik Kucing (Member)</strong>
                            <span class="text-xs text-slate-500">Mendaftarkan kucing, memesan sesi waktu kunjungan, dan unduh KTAM.</span>
                        </div>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center gap-4">
                        <span class="text-xl">🩺</span>
                        <div>
                            <strong class="text-sm block text-slate-900">Dokter Hewan (Vet)</strong>
                            <span class="text-xs text-slate-500">Memeriksa antrian, menginput rekam medis, dan menerbitkan kartu KTAM.</span>
                        </div>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center gap-4">
                        <span class="text-xl">📋</span>
                        <div>
                            <strong class="text-sm block text-slate-900">Relawan (Volunteer)</strong>
                            <span class="text-xs text-slate-500">Check-in peserta di lokasi, pendaftaran lapangan, dan sinkronisasi offline.</span>
                        </div>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center gap-4">
                        <span class="text-xl">🏢</span>
                        <div>
                            <strong class="text-sm block text-slate-900">Administrator Utama</strong>
                            <span class="text-xs text-slate-500">Menganalisis statistik keikutsertaan, verifikasi kartu, dan ekspor CSV.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Verification Section (Verifikasi KTAM) -->
    <section id="verifikasi" class="py-20 bg-slate-50">
        <div class="max-w-md mx-auto px-4 text-center space-y-6">
            <span class="eyebrow">Verifikasi Kartu</span>
            <h2 class="font-outfit text-3xl font-bold text-slate-900 leading-snug">Periksa Keaslian KTAM Kucing</h2>
            <p class="text-sm text-slate-500">Masukkan nomor kartu KTAM (misal: KM-YYYYMMDD-XXXX) di bawah ini untuk memverifikasi validitas keanggotaan dan riwayat pemeriksaan medis.</p>
            
            <form onsubmit="event.preventDefault(); const val = document.getElementById('verify-input').value.trim(); if(val) { window.location.href = '/verify/' + val; } else { alert('Silakan masukkan nomor KTAM.'); }" class="space-y-3 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <label for="verify-input" class="form-label text-left">Nomor KTAM Kucing</label>
                    <input type="text" id="verify-input" placeholder="e.g. KM-20260707-0001" class="form-input text-center font-mono">
                </div>
                <button type="submit" class="w-full button-primary flex justify-center py-2.5">
                    Verifikasi Kartu
                </button>
            </form>
        </div>
    </section>

    <!-- Contact Section (Kontak) -->
    <section id="kontak" class="py-20 bg-white border-t border-slate-200 text-center">
        <div class="max-w-2xl mx-auto px-4 space-y-6">
            <span class="eyebrow">Hubungi Kami</span>
            <h2 class="font-outfit text-3xl font-bold text-slate-900">Bergabunglah dalam Gerakan Kami</h2>
            <p class="text-sm text-slate-500 leading-relaxed">
                Ingin berkontribusi sebagai relawan, dokter hewan mitra, atau mensponsori kegiatan kesehatan hewan di lingkungan Muhammadiyah lainnya?
            </p>
            <div class="pt-4 flex justify-center gap-4">
                <a href="mailto:info@kucingmu.com" class="button-primary px-6 py-3 text-sm">
                    Kirim Email Kemitraan
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2">
                <span class="text-3xl">🐱</span>
                <span class="font-outfit font-extrabold text-white text-lg tracking-tight">KucingMu</span>
            </div>
            
            <p class="text-xs text-slate-500">
                &copy; 2026 KucingMu. Warga Muhammadiyah Peduli Hewan. Seluruh hak cipta dilindungi.
            </p>
        </div>
    </footer>

</body>
</html>
