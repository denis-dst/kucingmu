<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $app_settings['app_name'] ?? 'KucingMu' }} - {{ $app_settings['app_description'] ?? 'E-Surveillance & Kesehatan Kucing Komunitas' }}</title>

    @if(isset($app_settings['app_description']))
        <meta name="description" content="{{ $app_settings['app_description'] }}">
    @endif

    @if(isset($app_settings['app_favicon']))
        <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800" x-data="{ mobileNavOpen: false }">

    <!-- Skip Link for Keyboard Accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-800 focus:text-white focus:rounded-md focus:shadow-md focus:font-semibold">
        Lewati ke konten utama
    </a>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-2 focus-visible:ring-2 focus-visible:ring-teal-700 rounded-lg p-1">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="{{ $app_settings['app_name'] ?? 'KucingMu' }}" class="h-8 w-auto object-contain">
                @else
                    <span class="text-2xl" aria-hidden="true">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-teal-900 text-xl tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-700" aria-label="Navigasi Halaman">
                <a href="#tentang" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">{{ app()->getLocale() == 'en' ? 'About' : 'Tentang' }}</a>
                <a href="#fitur" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">{{ app()->getLocale() == 'en' ? 'Services' : 'Layanan' }}</a>
                @if(isset($events) && $events->isNotEmpty())
                    <a href="#events" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">{{ app()->getLocale() == 'en' ? 'Events' : 'Kegiatan' }}</a>
                @endif
                <a href="#faq" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">FAQ</a>
                <a href="#verifikasi" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">{{ app()->getLocale() == 'en' ? 'KTAM Verification' : 'Verifikasi KTAM' }}</a>
                <a href="#kontak" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">{{ app()->getLocale() == 'en' ? 'Contact' : 'Kontak' }}</a>
            </nav>

            <!-- Language Switcher & Auth Buttons -->
            <div class="flex items-center gap-2.5">
                <div class="flex border border-slate-300 rounded-lg overflow-hidden text-xs bg-slate-100 font-semibold shadow-xs" role="group" aria-label="Pilih Bahasa">
                    <a href="{{ route('lang.switch', 'id') }}" aria-label="Bahasa Indonesia" class="min-h-[38px] px-2.5 flex items-center gap-1 {{ app()->getLocale() == 'id' ? 'bg-teal-800 text-white font-bold' : 'text-slate-700 hover:bg-slate-200' }}">
                        <span aria-hidden="true">🇮🇩</span> <span>ID</span>
                    </a>
                    <a href="{{ route('lang.switch', 'en') }}" aria-label="English Language" class="min-h-[38px] px-2.5 flex items-center gap-1 {{ app()->getLocale() == 'en' ? 'bg-teal-800 text-white font-bold' : 'text-slate-700 hover:bg-slate-200' }}">
                        <span aria-hidden="true">🇬🇧</span> <span>EN</span>
                    </a>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="button-primary min-h-[40px] px-4 py-2 text-xs">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-secondary min-h-[40px] px-3.5 py-2 text-xs">
                            {{ app()->getLocale() == 'en' ? 'Login' : 'Masuk' }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="button-primary min-h-[40px] px-3.5 py-2 text-xs">
                                {{ app()->getLocale() == 'en' ? 'Register' : 'Daftar' }}
                            </a>
                        @endif
                    @endauth
                @endif

                <!-- Mobile Hamburger -->
                <button type="button" @click="mobileNavOpen = !mobileNavOpen" aria-label="Buka Menu" class="md:hidden inline-flex min-h-[44px] min-w-[44px] items-center justify-center p-2 rounded-lg text-slate-700 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-teal-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path :class="{'hidden': mobileNavOpen, 'inline-flex': !mobileNavOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileNavOpen, 'inline-flex': mobileNavOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div x-show="mobileNavOpen" x-transition class="md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
            <a href="#tentang" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">{{ app()->getLocale() == 'en' ? 'About' : 'Tentang' }}</a>
            <a href="#fitur" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">{{ app()->getLocale() == 'en' ? 'Services' : 'Layanan' }}</a>
            @if(isset($events) && $events->isNotEmpty())
                <a href="#events" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">{{ app()->getLocale() == 'en' ? 'Events' : 'Kegiatan' }}</a>
            @endif
            <a href="#faq" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">FAQ</a>
            <a href="#verifikasi" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">{{ app()->getLocale() == 'en' ? 'KTAM Verification' : 'Verifikasi KTAM' }}</a>
            <a href="#kontak" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">{{ app()->getLocale() == 'en' ? 'Contact' : 'Kontak' }}</a>
        </div>
    </header>

    <main id="main-content" tabindex="-1" class="focus:outline-none">
        <!-- Hero Section -->
        <section class="bg-teal-900 text-white py-16 lg:py-24 border-b border-teal-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-12 lg:grid-cols-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-800 border border-teal-700 text-teal-100 text-xs font-semibold uppercase tracking-wider">
                        Inisiatif Majelis Lingkungan Hidup PP Muhammadiyah
                    </span>
                    <h1 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $app_settings['app_name'] ?? 'KucingMu' }}
                    </h1>
                    <p class="text-base sm:text-lg text-teal-100 leading-relaxed max-w-xl">
                        {{ $app_settings['app_description'] ?? 'Platform terpadu untuk pendataan kesehatan kucing, surveilans populasi kucing liar, dan penerbitan Kartu Tanda Anggota Muhammadiyah (KTAM) Kucing secara digital.' }}
                    </p>
                    <div class="pt-2 flex flex-wrap gap-3.5">
                        <a href="{{ route('register') }}" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-white text-teal-950 px-6 py-3 text-sm font-bold shadow-sm hover:bg-teal-50 focus-visible:ring-2 focus-visible:ring-white">
                            Daftarkan Kucing Peliharaan
                        </a>
                        <a href="#verifikasi" class="min-h-[44px] inline-flex items-center justify-center rounded-lg border border-teal-600 bg-teal-800/80 text-white px-5 py-3 text-sm font-semibold hover:bg-teal-800 focus-visible:ring-2 focus-visible:ring-white">
                            Periksa Nomor KTAM
                        </a>
                    </div>
                </div>
                
                <!-- Preview Card Representation -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm rounded-2xl border border-teal-700/60 bg-teal-800/90 p-6 shadow-md text-white">
                        <div class="flex justify-between items-start border-b border-teal-700 pb-3 mb-4">
                            <div>
                                <span class="text-xs font-bold text-teal-200 uppercase tracking-wide">Kartu Keanggotaan</span>
                                <h3 class="font-outfit text-lg font-bold text-white mt-0.5">KTAM KucingMu</h3>
                            </div>
                            <span class="bg-teal-100 text-teal-950 text-[11px] font-bold px-2 py-0.5 rounded">Resmi Terverifikasi</span>
                        </div>

                        <div class="space-y-2.5 text-xs text-teal-100">
                            <div class="flex justify-between border-b border-teal-700/40 pb-1.5">
                                <span class="text-teal-200">Nama Kucing:</span>
                                <strong class="text-white">Mochi</strong>
                            </div>
                            <div class="flex justify-between border-b border-teal-700/40 pb-1.5">
                                <span class="text-teal-200">Ras / Jenis:</span>
                                <strong class="text-white">Domestik Campuran</strong>
                            </div>
                            <div class="flex justify-between border-b border-teal-700/40 pb-1.5">
                                <span class="text-teal-200">Nomor Registrasi:</span>
                                <strong class="text-white font-mono">KM-20260707-0001</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-teal-200">Pemilik Terdaftar:</span>
                                <strong class="text-white">Siti Rahma (NBM Terverifikasi)</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Panel -->
        <section class="bg-white border-b border-slate-200 py-8" aria-label="Ringkasan Program">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 grid-cols-2 md:grid-cols-4 text-center">
                <div class="p-3">
                    <div class="font-outfit text-2xl sm:text-3xl font-extrabold text-teal-800">60 Target</div>
                    <div class="text-xs text-slate-600 font-semibold uppercase tracking-wide mt-1">Pilot Project Komunitas</div>
                </div>
                <div class="p-3">
                    <div class="font-outfit text-2xl sm:text-3xl font-extrabold text-teal-800">Layanan Gratis</div>
                    <div class="text-xs text-slate-600 font-semibold uppercase tracking-wide mt-1">Pemeriksaan Dasar</div>
                </div>
                <div class="p-3">
                    <div class="font-outfit text-2xl sm:text-3xl font-extrabold text-teal-800">4 Peran</div>
                    <div class="text-xs text-slate-600 font-semibold uppercase tracking-wide mt-1">Kolaborasi Terpadu</div>
                </div>
                <div class="p-3">
                    <div class="font-outfit text-2xl sm:text-3xl font-extrabold text-teal-800">Tercatat Digital</div>
                    <div class="text-xs text-slate-600 font-semibold uppercase tracking-wide mt-1">Rekam Medis & Kartu</div>
                </div>
            </div>
        </section>

        <!-- Program Overview (Tentang) -->
        <section id="tentang" class="py-16 bg-slate-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Tentang Inisiatif</span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900 font-outfit">Kepedulian Lingkungan & Kesejahteraan Kucing</h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        KucingMu adalah sarana integrasi komunitas pemerhati kucing di lingkungan persyarikatan Muhammadiyah. Program ini memfasilitasi pemeriksaan medis hewan gratis serta pendataan surveilans lapangan yang akuntabel.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="content-card">
                        <div class="text-2xl" aria-hidden="true">🏥</div>
                        <h3 class="font-bold text-slate-900 mt-3 text-base">Pemeriksaan Dokter Hewan</h3>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Pemeriksaan klinis umum, konsultasi riwayat kesehatan, dan pemberian obat non-invasif oleh dokter hewan mitra.
                        </p>
                    </div>

                    <div class="content-card">
                        <div class="text-2xl" aria-hidden="true">🎫</div>
                        <h3 class="font-bold text-slate-900 mt-3 text-base">Penerbitan KTAM Kucing</h3>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Penerbitan nomor identitas resmi KucingMu lengkap dengan QR code validasi verifikasi digital.
                        </p>
                    </div>

                    <div class="content-card">
                        <div class="text-2xl" aria-hidden="true">📋</div>
                        <h3 class="font-bold text-slate-900 mt-3 text-base">Surveilans Lapangan</h3>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Relawan dapat mendata sebaran kucing liar, kondisi kesehatan, dan lokasi koordinat secara terstruktur.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Grid (Fitur) -->
        <section id="fitur" class="py-16 bg-white border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-12 lg:grid-cols-2 items-center">
                <div>
                    <span class="eyebrow">Fasilitas Medis</span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900 font-outfit">Layanan Pemeriksaan Kesehatan</h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Kucing yang terdaftar dalam program pemeriksaan mendapatkan perawatan pencegahan standar berikut:
                    </p>

                    <div class="mt-6 space-y-4">
                        <div class="flex gap-3.5 items-start">
                            <div class="h-6 w-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">✓</div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Pemberian Obat Cacing (Deworming)</h3>
                                <p class="text-xs text-slate-600 mt-0.5">Mencegah dan mengeliminasi parasit saluran pencernaan.</p>
                            </div>
                        </div>
                        <div class="flex gap-3.5 items-start">
                            <div class="h-6 w-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">✓</div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Pengobatan Kutu (Anti-Flea)</h3>
                                <p class="text-xs text-slate-600 mt-0.5">Penanganan parasit kulit luar untuk mencegah iritasi dan kerontokan bulu.</p>
                            </div>
                        </div>
                        <div class="flex gap-3.5 items-start">
                            <div class="h-6 w-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">✓</div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Suplemen & Vitamin</h3>
                                <p class="text-xs text-slate-600 mt-0.5">Diberikan untuk membantu daya tahan dan nutrisi tubuh kucing.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 sm:p-8 border border-slate-200">
                    <h3 class="font-outfit text-lg font-bold text-slate-900 mb-4 border-b border-slate-200 pb-2.5">4 Peran Dalam Platform</h3>
                    <div class="space-y-3">
                        <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center gap-3.5">
                            <span class="text-xl" aria-hidden="true">🐱</span>
                            <div>
                                <strong class="text-sm block text-slate-900">Pemilik Kucing (Member)</strong>
                                <span class="text-xs text-slate-600">Mendaftarkan kucing peliharaan, memilih jadwal periksa, dan mengunduh kartu KTAM.</span>
                            </div>
                        </div>
                        <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center gap-3.5">
                            <span class="text-xl" aria-hidden="true">🩺</span>
                            <div>
                                <strong class="text-sm block text-slate-900">Dokter Hewan Mitra</strong>
                                <span class="text-xs text-slate-600">Melakukan pemeriksaan klinis, memberikan diagnosis, dan mencatat rekam medis.</span>
                            </div>
                        </div>
                        <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center gap-3.5">
                            <span class="text-xl" aria-hidden="true">📋</span>
                            <div>
                                <strong class="text-sm block text-slate-900">Relawan Lapangan (Volunteer)</strong>
                                <span class="text-xs text-slate-600">Melakukan input data surveilans kucing liar dan verifikasi kehadiran peserta di lokasi.</span>
                            </div>
                        </div>
                        <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center gap-3.5">
                            <span class="text-xl" aria-hidden="true">🛡️</span>
                            <div>
                                <strong class="text-sm block text-slate-900">Majelis & Pengelola (Admin)</strong>
                                <span class="text-xs text-slate-600">Memverifikasi berkas, mengesahkan penerbitan KTAM, dan mengelola jadwal kegiatan.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Section (Event & Kegiatan Terdekat) -->
        @if(isset($events) && $events->isNotEmpty())
            <section id="events" class="py-16 bg-slate-50 border-t border-slate-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <div class="text-center max-w-2xl mx-auto">
                        <span class="eyebrow">Agenda Terdekat</span>
                        <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900 font-outfit">Kegiatan & Pemeriksaan Kesehatan Kucing</h2>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                            Jadwal sosialisasi, edukasi, dan pemeriksaan kesehatan hewan di lokasi mitra persyarikatan.
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($events as $event)
                            <div class="content-card flex flex-col justify-between">
                                <div>
                                    <!-- Banner -->
                                    <div class="h-44 w-full bg-slate-100 rounded-lg overflow-hidden relative border border-slate-200">
                                        @if($event->banner_path)
                                            <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex flex-col items-center justify-center text-slate-400">
                                                <span class="text-3xl" aria-hidden="true">📅</span>
                                                <span class="text-xs font-semibold uppercase tracking-wider mt-1 text-slate-500">Agenda Kegiatan</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3 bg-teal-800 text-white font-semibold text-xs px-2.5 py-1 rounded-md shadow-xs">
                                            {{ $event->date->format('d M Y') }}
                                        </div>
                                    </div>

                                    <!-- Body -->
                                    <div class="pt-4 space-y-2">
                                        <h3 class="font-outfit text-lg font-bold text-slate-900 leading-snug">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-xs text-slate-600 font-medium flex items-center gap-1.5">
                                            <span aria-hidden="true">📍</span> {{ $event->location }}
                                        </p>
                                        <p class="text-xs text-slate-600 leading-relaxed pt-1">
                                            {{ $event->description }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Footer / Register Link -->
                                <div class="pt-4 mt-4 border-t border-slate-100">
                                    @if($event->registration_link)
                                        <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" rel="noopener noreferrer" class="w-full button-primary text-center text-xs font-semibold py-2.5">
                                            Daftar Kegiatan
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-500 font-medium block text-center py-2 bg-slate-100 rounded-lg">Pendaftaran di Lokasi</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- FAQ Section -->
        <section id="faq" class="py-16 bg-white border-t border-slate-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">FAQ</span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900 font-outfit">
                        {{ app()->getLocale() == 'en' ? 'Frequently Asked Questions' : 'Pertanyaan Yang Sering Diajukan' }}
                    </h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        {{ app()->getLocale() == 'en' ? 'Frequently asked questions about the KucingMu platform and verification.' : 'Jawaban seputar pendaftaran, pemeriksaan kesehatan hewan, dan kartu KTAM Kucing.' }}
                    </p>
                </div>

                <div class="space-y-3" x-data="{ activeFaq: null }">
                    <!-- FAQ Item 1 -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                        <button type="button" @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full min-h-[44px] flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-900 focus-visible:ring-2 focus-visible:ring-teal-700">
                            <span>{{ app()->getLocale() == 'en' ? 'What is KucingMu and who is it for?' : 'Apa itu KucingMu dan untuk siapa platform ini?' }}</span>
                            <span class="text-teal-800 font-bold text-base transition-transform" :class="activeFaq === 1 ? 'rotate-45' : ''" aria-hidden="true">＋</span>
                        </button>
                        <div x-show="activeFaq === 1" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            {{ app()->getLocale() == 'en' ? 'KucingMu is a community platform for registering pet cats, recording veterinary checkups, and issuing digital KTAM cards.' : 'KucingMu adalah platform web terpadu bagi warga komunitas untuk mendaftarkan kucing peliharaan, mencatat riwayat pemeriksaan dokter hewan, serta menerbitkan Kartu Tanda Anggota Muhammadiyah Kucing (KTAM) secara digital.' }}
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                        <button type="button" @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full min-h-[44px] flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-900 focus-visible:ring-2 focus-visible:ring-teal-700">
                            <span>{{ app()->getLocale() == 'en' ? 'How can my cat get a KTAM Card?' : 'Bagaimana cara kucing saya mendapatkan kartu KTAM?' }}</span>
                            <span class="text-teal-800 font-bold text-base transition-transform" :class="activeFaq === 2 ? 'rotate-45' : ''" aria-hidden="true">＋</span>
                        </button>
                        <div x-show="activeFaq === 2" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            {{ app()->getLocale() == 'en' ? 'Register your cat profile, upload photos, and complete a health checkup with a partner veterinarian. Admin will review and issue the verified card.' : 'Daftarkan profil kucing di dashboard, unggah foto/biometrik, dan ikuti sesi pemeriksaan kesehatan bersama dokter hewan mitra. Administrator akan meninjau data sebelum menerbitkan kartu KTAM resmi.' }}
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                        <button type="button" @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full min-h-[44px] flex items-center justify-between p-4 text-left font-semibold text-sm text-slate-900 focus-visible:ring-2 focus-visible:ring-teal-700">
                            <span>{{ app()->getLocale() == 'en' ? 'Are the clinic checkups and KTAM cards free?' : 'Apakah pemeriksaan klinik dan kartu KTAM ini gratis?' }}</span>
                            <span class="text-teal-800 font-bold text-base transition-transform" :class="activeFaq === 3 ? 'rotate-45' : ''" aria-hidden="true">＋</span>
                        </button>
                        <div x-show="activeFaq === 3" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white">
                            {{ app()->getLocale() == 'en' ? 'Yes, the pilot project checkups and digital card issuance are provided free of charge for community members.' : 'Ya, pemeriksaan kesehatan dasar (obat cacing, obat kutu, vitamin) serta penerbitan kartu KTAM digital pada program percontohan ini disediakan gratis untuk warga komunitas.' }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Verification Section (Verifikasi KTAM) -->
        <section id="verifikasi" class="py-16 bg-slate-50 border-t border-slate-200">
            <div class="max-w-md mx-auto px-4 text-center space-y-5">
                <span class="eyebrow">Validasi Data</span>
                <h2 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 leading-snug">Periksa Keaslian Kartu KTAM</h2>
                <p class="text-xs text-slate-600 leading-relaxed">Masukkan nomor kartu KTAM Kucing (contoh: KM-YYYYMMDD-XXXX) untuk memeriksa status registrasi dan riwayat pemeriksaan medis resmi.</p>
                
                <form onsubmit="event.preventDefault(); const val = document.getElementById('verify-input').value.trim(); if(val) { window.location.href = '/verify/' + val; } else { alert('Silakan masukkan nomor KTAM.'); }" class="space-y-3 bg-white p-6 rounded-xl border border-slate-200 shadow-xs">
                    <div>
                        <label for="verify-input" class="form-label text-left text-xs">Nomor KTAM Kucing</label>
                        <input type="text" id="verify-input" placeholder="e.g. KM-20260707-0001" class="form-input text-center font-mono text-sm" required>
                    </div>
                    <button type="submit" class="w-full button-primary flex justify-center py-2.5 text-xs font-semibold">
                        Periksa Validitas Kartu
                    </button>
                </form>
            </div>
        </section>

        <!-- Contact Section (Kontak) -->
        <section id="kontak" class="py-16 bg-white border-t border-slate-200 text-center">
            <div class="max-w-2xl mx-auto px-4 space-y-4">
                <span class="eyebrow">Kontak & Kemitraan</span>
                <h2 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900">Kolaborasi Bersama KucingMu</h2>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Tertarik bergabung sebagai dokter hewan mitra, relawan pendataan lapangan, atau mendukung kegiatan kesehatan hewan di lingkungan persyarikatan?
                </p>
                <div class="pt-2 flex justify-center">
                    <a href="mailto:info@kucingmu.com" class="button-primary px-6 py-2.5 text-xs font-semibold">
                        Kirim Email Pertanyaan
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="flex items-center gap-2">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="{{ $app_settings['app_name'] ?? 'KucingMu' }}" class="h-7 w-auto object-contain">
                @else
                    <span class="text-2xl" aria-hidden="true">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-white text-base tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </div>
            
            <p class="text-xs text-slate-400">
                {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah.' !!}
            </p>
        </div>
    </footer>

    @include('partials.accessibility-widget')
</body>
</html>
