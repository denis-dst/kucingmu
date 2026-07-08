<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $app_settings['app_name'] ?? 'KucingMu' }} - {{ $app_settings['app_description'] ?? 'Kesehatan Kucing & Syiar Dakwah Muhammadiyah' }}</title>

    @if(isset($app_settings['app_description']))
        <meta name="description" content="{{ $app_settings['app_description'] }}">
    @endif

    @if(isset($app_settings['app_favicon']))
        <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
    @endif

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
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo" class="h-8 w-auto object-contain">
                @else
                    <span class="text-3xl">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-teal-800 text-xl tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600">
                <a href="#tentang" class="hover:text-teal-700 transition">{{ app()->getLocale() == 'en' ? 'About' : 'Tentang' }}</a>
                <a href="#fitur" class="hover:text-teal-700 transition">{{ app()->getLocale() == 'en' ? 'Services' : 'Layanan' }}</a>
                @if(isset($events) && $events->isNotEmpty())
                    <a href="#events" class="hover:text-teal-700 transition">{{ app()->getLocale() == 'en' ? 'Events' : 'Kegiatan' }}</a>
                @endif
                <a href="#faq" class="hover:text-teal-700 transition">FAQ</a>
                <a href="#verifikasi" class="hover:text-teal-700 transition">{{ app()->getLocale() == 'en' ? 'KTAM Verification' : 'Verifikasi KTAM' }}</a>
                <a href="#kontak" class="hover:text-teal-700 transition">{{ app()->getLocale() == 'en' ? 'Contact' : 'Hubungi Kami' }}</a>
            </nav>

            <!-- Language Switcher & Auth Buttons -->
            <div class="flex items-center gap-3">
                <div class="flex border border-slate-200 rounded-xl overflow-hidden text-xs bg-slate-50 font-bold shadow-sm mr-2">
                    <a href="{{ route('lang.switch', 'id') }}" class="px-2.5 py-1.5 flex items-center gap-1.5 {{ app()->getLocale() == 'id' ? 'bg-teal-800 text-white lang-active' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>🇮🇩</span> <span>ID</span>
                    </a>
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2.5 py-1.5 flex items-center gap-1.5 {{ app()->getLocale() == 'en' ? 'bg-teal-800 text-white lang-active' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>🇬🇧</span> <span>EN</span>
                    </a>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="button-primary px-4 py-2 text-xs">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-secondary px-4 py-2 text-xs">
                            {{ app()->getLocale() == 'en' ? 'Login' : 'Masuk' }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="button-primary px-4 py-2 text-xs">
                                {{ app()->getLocale() == 'en' ? 'Register' : 'Daftar' }}
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
                    {{ $app_settings['app_name'] ?? 'KucingMu' }}
                </h1>
                <p class="text-base sm:text-lg text-teal-100/90 leading-relaxed max-w-xl">
                    {{ $app_settings['app_description'] ?? 'KucingMu adalah platform terpadu bagi warga Muhammadiyah untuk mendaftarkan kucing kesayangan, melacak riwayat medis pemeriksaan dokter hewan, serta menerbitkan Kartu Tanda Anggota Muhammadiyah (KTAM) khusus kucing.' }}
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

    <!-- Events Section (Event & Kegiatan Terdekat) -->
    @if(isset($events) && $events->isNotEmpty())
        <section id="events" class="py-20 bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Agenda Sosialisasi</span>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900 font-outfit">Kegiatan & Pemeriksaan Kesehatan Terdekat</h2>
                    <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                        Ikuti berbagai program sosialisasi, seminar edukasi, dan pemeriksaan kesehatan kucing gratis di lingkungan Anda.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($events as $event)
                        <div class="content-card bg-white rounded-3xl border border-slate-200 overflow-hidden flex flex-col justify-between hover:shadow-lg transition duration-300">
                            <div>
                                <!-- Banner -->
                                <div class="h-48 w-full bg-slate-100 relative">
                                    @if($event->banner_path)
                                        <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex flex-col items-center justify-center text-slate-400">
                                            <span class="text-4xl">📅</span>
                                            <span class="text-xs font-bold uppercase tracking-wider mt-2">KucingMu Event</span>
                                        </div>
                                    @endif
                                    <div class="absolute top-4 left-4 bg-teal-800 text-white font-outfit font-bold text-xs px-3 py-1 rounded-full shadow">
                                        {{ $event->date->format('d M Y') }}
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="p-6 space-y-3">
                                    <h3 class="font-outfit text-xl font-bold text-slate-900 leading-tight">
                                        {{ $event->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                                        <span>📍</span> {{ $event->location }}
                                    </p>
                                    <p class="text-sm text-slate-600 leading-relaxed pt-2">
                                        {{ $event->description }}
                                    </p>
                                </div>
                            </div>

                            <!-- Footer / Register Link -->
                            <div class="p-6 pt-0 border-t border-slate-100 mt-4 flex items-center justify-between">
                                @if($event->registration_link)
                                    <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" class="w-full button-primary text-center py-2.5 text-xs font-bold">
                                        Daftar Kegiatan (gentix-apps.com)
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">Pendaftaran Langsung di Tempat</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto">
                <span class="eyebrow">FAQ</span>
                <h2 class="mt-2 text-3xl font-bold text-slate-900 font-outfit">
                    {{ app()->getLocale() == 'en' ? 'Frequently Asked Questions' : 'Pertanyaan Yang Sering Diajukan' }}
                </h2>
                <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                    {{ app()->getLocale() == 'en' ? 'Got questions about KucingMu? Find answers to commonly asked questions below.' : 'Punya pertanyaan mengenai KucingMu? Temukan jawaban untuk pertanyaan umum di bawah ini.' }}
                </p>
            </div>

            <div class="space-y-4" x-data="{ activeFaq: null }">
                <!-- FAQ Item 1 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50 hover:bg-white transition">
                    <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-900 focus:outline-none">
                        <span>{{ app()->getLocale() == 'en' ? 'What is KucingMu and who is it for?' : 'Apa itu KucingMu dan untuk siapa platform ini?' }}</span>
                        <span class="text-teal-700 text-lg transition duration-200" :class="activeFaq === 1 ? 'rotate-45' : ''">＋</span>
                    </button>
                    <div x-show="activeFaq === 1" class="p-5 pt-0 text-sm text-slate-500 leading-relaxed border-t border-slate-100 bg-white">
                        {{ app()->getLocale() == 'en' ? 'KucingMu is an integrated web platform created for the Muhammadiyah community to register their cats, record veterinary clinic history, and issue a digital Muhammadiyah Cat Member Card (KTAM).' : 'KucingMu adalah platform web terpadu bagi warga Muhammadiyah untuk mendaftarkan kucing peliharaan mereka, mencatat riwayat klinik medis hewan, serta menerbitkan Kartu Tanda Anggota Muhammadiyah Kucing (KTAM) secara digital.' }}
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50 hover:bg-white transition">
                    <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-900 focus:outline-none">
                        <span>{{ app()->getLocale() == 'en' ? 'How can my cat get a KTAM Card?' : 'Bagaimana cara kucing saya mendapatkan kartu KTAM?' }}</span>
                        <span class="text-teal-700 text-lg transition duration-200" :class="activeFaq === 2 ? 'rotate-45' : ''">＋</span>
                    </button>
                    <div x-show="activeFaq === 2" class="p-5 pt-0 text-sm text-slate-500 leading-relaxed border-t border-slate-100 bg-white">
                        {{ app()->getLocale() == 'en' ? 'After registering your cat on the dashboard, book a health checkup session. Once a doctor vet examines your cat and inputs the checkup status, the KTAM Card will be automatically issued and ready to download.' : 'Setelah mendaftarkan data kucing Anda di dashboard, silakan buat janji temu pemeriksaan kesehatan. Setelah dokter hewan memeriksa kucing Anda dan mengonfirmasi rekam medisnya, kartu KTAM akan otomatis terbit dan siap diunduh.' }}
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50 hover:bg-white transition">
                    <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-900 focus:outline-none">
                        <span>{{ app()->getLocale() == 'en' ? 'Are the clinic checkups and KTAM cards free?' : 'Apakah pemeriksaan klinik dan kartu KTAM ini gratis?' }}</span>
                        <span class="text-teal-700 text-lg transition duration-200" :class="activeFaq === 3 ? 'rotate-45' : ''">＋</span>
                    </button>
                    <div x-show="activeFaq === 3" class="p-5 pt-0 text-sm text-slate-500 leading-relaxed border-t border-slate-100 bg-white">
                        {{ app()->getLocale() == 'en' ? 'Yes! All services including health checkups, deworming, anti-flea, vitamin supplements, and digital KTAM card issuance are 100% free of charge for the Muhammadiyah community.' : 'Ya! Seluruh layanan mulai dari pemeriksaan kesehatan kucing, pemberian obat cacing, obat kutu, vitamin/suplemen, hingga penerbitan kartu KTAM digital adalah 100% gratis tanpa dipungut biaya apapun bagi warga Muhammadiyah.' }}
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50 hover:bg-white transition">
                    <button @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-900 focus:outline-none">
                        <span>{{ app()->getLocale() == 'en' ? 'What registration link is used?' : 'Link pendaftaran apa yang digunakan?' }}</span>
                        <span class="text-teal-700 text-lg transition duration-200" :class="activeFaq === 4 ? 'rotate-45' : ''">＋</span>
                    </button>
                    <div x-show="activeFaq === 4" class="p-5 pt-0 text-sm text-slate-500 leading-relaxed border-t border-slate-100 bg-white">
                        {{ app()->getLocale() == 'en' ? 'For official health checkup events, the registration form links are integrated with gentix-apps.com. You can find active event links in the agenda section.' : 'Untuk kegiatan sosialisasi pemeriksaan kesehatan resmi, tautan formulir pendaftaran kami terintegrasi dengan gentix-apps.com. Anda dapat menemukannya pada daftar agenda terdekat.' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Verification Section (Verifikasi KTAM) -->
    <section id="verifikasi" class="py-20 bg-slate-50 border-t border-slate-200">
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
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo" class="h-8 w-auto object-contain">
                @else
                    <span class="text-3xl">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-white text-lg tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </div>
            
            <p class="text-xs text-slate-500">
                {!! $app_settings['app_footer'] ?? '&copy; 2026 KucingMu. Warga Muhammadiyah Peduli Hewan. Seluruh hak cipta dilindungi.' !!}
            </p>
        </div>
    </footer>

    @include('partials.accessibility-widget')
</body>
</html>
