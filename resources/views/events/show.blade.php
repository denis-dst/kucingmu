<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $event->title }} | {{ $app_settings['app_name'] ?? 'KucingMu' }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 160) }}">

    <!-- Open Graph / Social Sharing -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $event->title }} - {{ $app_settings['app_name'] ?? 'KucingMu' }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 160) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($event->banner_path)
        <meta property="og:image" content="{{ asset('storage/' . $event->banner_path) }}">
    @elseif(isset($app_settings['app_logo']))
        <meta property="og:image" content="{{ asset('storage/' . $app_settings['app_logo']) }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $event->title }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 160) }}">

    @if(isset($app_settings['app_favicon']))
        <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tracking & Analytics Scripts (GTM & Google Analytics) -->
    @include('partials.tracking-head')
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen flex flex-col" x-data="{ mobileNavOpen: false, copied: false }">
    <!-- Tracking Body (GTM Noscript) -->
    @include('partials.tracking-body')

    <!-- Skip Link for Keyboard Accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-800 focus:text-white focus:rounded-md focus:shadow-md focus:font-semibold">
        Lewati ke konten utama
    </a>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-2 focus-visible:ring-2 focus-visible:ring-teal-700 rounded-lg p-1 shrink-0">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo KucingMu" width="32" height="32" class="h-7 sm:h-8 w-auto object-contain" decoding="async">
                @else
                    <span class="text-xl sm:text-2xl" aria-hidden="true">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-teal-900 text-lg sm:text-xl tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-700" aria-label="Navigasi Halaman">
                <a href="{{ url('/#tentang') }}" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">Tentang</a>
                <a href="{{ url('/#fitur') }}" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">Layanan</a>
                <a href="{{ url('/#events') }}" class="text-teal-800 font-bold hover:text-teal-900 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">Agenda Kegiatan</a>
                <a href="{{ url('/#faq') }}" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">FAQ</a>
                <a href="{{ url('/#verifikasi') }}" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">Verifikasi KTAKuMu</a>
                <a href="{{ route('representative.register') }}" class="text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-2.5 py-1 rounded-lg text-xs font-bold transition">
                    🎖️ Representatif
                </a>
                <a href="{{ route('contact.index') }}" class="hover:text-teal-800 transition focus-visible:ring-2 focus-visible:ring-teal-700 rounded px-1.5 py-1">Kontak</a>
            </nav>

            <!-- Language Switcher & Auth Buttons -->
            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                <div class="flex border border-slate-300 rounded-lg overflow-hidden text-xs bg-slate-100 font-semibold shadow-xs" role="group" aria-label="Pilih Bahasa">
                    <a href="{{ route('lang.switch', 'id') }}" aria-label="Bahasa Indonesia" class="min-h-[36px] sm:min-h-[38px] px-2 sm:px-2.5 flex items-center gap-1 {{ app()->getLocale() == 'id' ? 'bg-teal-800 text-white font-bold' : 'text-slate-700 hover:bg-slate-200' }}">
                        <span aria-hidden="true">🇮🇩</span> <span>ID</span>
                    </a>
                    <a href="{{ route('lang.switch', 'en') }}" aria-label="English Language" class="min-h-[36px] sm:min-h-[38px] px-2 sm:px-2.5 flex items-center gap-1 {{ app()->getLocale() == 'en' ? 'bg-teal-800 text-white font-bold' : 'text-slate-700 hover:bg-slate-200' }}">
                        <span aria-hidden="true">🇬🇧</span> <span>EN</span>
                    </a>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="button-primary min-h-[36px] sm:min-h-[40px] px-3 sm:px-4 py-1.5 sm:py-2 text-xs">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-secondary hidden sm:inline-flex min-h-[40px] px-3.5 py-2 text-xs">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="button-primary hidden sm:inline-flex min-h-[40px] px-3.5 py-2 text-xs">
                                Daftar
                            </a>
                        @endif
                    @endauth
                @endif

                <!-- Mobile Hamburger -->
                <button type="button" @click="mobileNavOpen = !mobileNavOpen" aria-label="Buka Menu" class="md:hidden inline-flex min-h-[40px] min-w-[40px] items-center justify-center p-2 rounded-lg text-slate-700 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-teal-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path :class="{'hidden': mobileNavOpen, 'inline-flex': !mobileNavOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileNavOpen, 'inline-flex': mobileNavOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div x-show="mobileNavOpen" x-transition class="md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
            <a href="{{ url('/') }}" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">Beranda</a>
            <a href="{{ url('/#events') }}" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-bold text-teal-800 bg-teal-50">Agenda Kegiatan</a>
            <a href="{{ route('representative.register') }}" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-bold text-amber-900 bg-amber-50">🎖️ Penjaringan Representatif</a>
            <a href="{{ route('contact.index') }}" @click="mobileNavOpen = false" class="block min-h-[44px] px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-100">Kontak</a>
            @if (Route::has('login'))
                <div class="pt-3 mt-2 border-t border-slate-100 flex flex-col gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full button-primary text-center py-2.5 text-xs font-bold">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full button-secondary text-center py-2.5 text-xs font-semibold">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full button-primary text-center py-2.5 text-xs font-bold">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </header>

    <!-- Main Content Container -->
    <main id="main-content" class="flex-1 py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 flex-wrap" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-teal-800 transition">Beranda</a>
                <span class="text-slate-400">/</span>
                <a href="{{ url('/#events') }}" class="hover:text-teal-800 transition">Agenda Terdekat</a>
                <span class="text-slate-400">/</span>
                <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md" aria-current="page">{{ $event->title }}</span>
            </nav>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left Column: Article & Event Details (8 cols) -->
                <article class="lg:col-span-8 space-y-6">

                    <!-- Header Card -->
                    <div class="content-card space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2.5">
                            <span class="eyebrow">Agenda Terdekat Kegiatan & Pemeriksaan</span>
                            @if($event->status === 'active')
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                    ● Agenda Aktif
                                </span>
                            @elseif($event->status === 'draft')
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Draft (Pratinjau Admin)
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Selesai / Terlaksana
                                </span>
                            @endif
                        </div>

                        <h1 class="font-outfit text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">
                            {{ $event->title }}
                        </h1>

                        <!-- Key Meta Badges -->
                        <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-xs sm:text-sm text-slate-600 border-t border-b border-slate-100 py-3.5">
                            <div class="flex items-center gap-2 font-medium">
                                <span class="text-base" aria-hidden="true">📅</span>
                                <span>{{ $event->date ? $event->date->translatedFormat('l, d F Y') : '-' }}</span>
                            </div>
                            <span class="text-slate-300 hidden sm:inline">&bull;</span>
                            <div class="flex items-center gap-2 font-medium">
                                <span class="text-base" aria-hidden="true">📍</span>
                                <span>{{ $event->location }}</span>
                            </div>
                        </div>

                        <!-- Banner Image -->
                        <div class="w-full rounded-xl overflow-hidden border border-slate-200 bg-slate-100 relative">
                            @if($event->banner_path)
                                <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" width="800" height="420" class="w-full h-auto max-h-[460px] object-cover" priority fetchpriority="high">
                            @else
                                <div class="h-56 sm:h-72 w-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-slate-100 to-slate-200">
                                    <span class="text-5xl" aria-hidden="true">📅</span>
                                    <span class="text-sm font-semibold uppercase tracking-wider mt-2 text-slate-500">Agenda Kegiatan KucingMu</span>
                                </div>
                            @endif
                        </div>

                        <!-- Description & Detailed Content -->
                        <div class="pt-3 space-y-4">
                            <h2 class="font-outfit text-lg sm:text-xl font-bold text-slate-900">Deskripsi Lengkap Kegiatan</h2>
                            
                            <div class="text-sm sm:text-base text-slate-700 leading-relaxed space-y-3 whitespace-pre-line font-normal break-words">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>

                        <!-- Mobile Action Button (Visible on small screens) -->
                        <div class="lg:hidden pt-4 border-t border-slate-100">
                            @if($event->registration_link)
                                @auth
                                    <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" rel="noopener noreferrer" class="button-primary w-full py-3 text-center text-sm font-bold flex items-center justify-center gap-2">
                                        <span>✍️</span> Daftar Kegiatan Sekarang
                                    </a>
                                @else
                                    <div class="space-y-2">
                                        <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl text-center text-xs sm:text-sm font-bold flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition shadow-2xs">
                                            <span class="text-base" aria-hidden="true">🔒</span>
                                            <span>Daftar Kegiatan Sekarang (Perlu Login Member)</span>
                                        </a>
                                        <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                                            Silakan <a href="{{ route('login') }}" class="text-teal-700 font-bold hover:underline">Masuk</a> atau <a href="{{ route('register') }}" class="text-teal-700 font-bold hover:underline">Daftar Member</a> untuk membuka link pendaftaran kegiatan.
                                        </p>
                                    </div>
                                @endauth
                            @else
                                <div class="p-3 bg-slate-100 rounded-xl text-center text-xs font-semibold text-slate-600">
                                    Pendaftaran langsung di lokasi acara (On the spot)
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Back to Home Link -->
                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ url('/#events') }}" class="button-secondary text-xs sm:text-sm font-semibold px-4 py-2">
                            ← Kembali ke Agenda Lainnya
                        </a>

                        @auth
                            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-200 px-3.5 py-2 rounded-xl transition">
                                    <span>✏️</span> Edit Data Kegiatan (Admin)
                                </a>
                            @endif
                        @endauth
                    </div>
                </article>

                <!-- Right Column: Sidebar Summary & Share (4 cols) -->
                <aside class="lg:col-span-4 space-y-6">

                    <!-- Event Information Card -->
                    <div class="content-card space-y-5 sticky top-24">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-outfit text-base font-bold text-slate-900">Informasi Pelaksanaan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Jadwal dan rincian lokasi kegiatan</p>
                        </div>

                        <div class="space-y-4 text-xs sm:text-sm">
                            <div class="flex items-start gap-3">
                                <span class="p-2 bg-teal-50 border border-teal-100 text-teal-800 rounded-lg shrink-0 text-base" aria-hidden="true">📅</span>
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Hari & Tanggal</span>
                                    <span class="font-semibold text-slate-800">{{ $event->date ? $event->date->translatedFormat('l, d F Y') : '-' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="p-2 bg-teal-50 border border-teal-100 text-teal-800 rounded-lg shrink-0 text-base" aria-hidden="true">📍</span>
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Lokasi Penyelenggaraan</span>
                                    <span class="font-semibold text-slate-800 leading-snug">{{ $event->location }}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="p-2 bg-teal-50 border border-teal-100 text-teal-800 rounded-lg shrink-0 text-base" aria-hidden="true">📋</span>
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Pendaftaran</span>
                                    <span class="font-semibold text-slate-800">
                                        @if($event->registration_link)
                                            @auth
                                                Pendaftaran Online Terbuka
                                            @else
                                                Pendaftaran Online (Khusus Member)
                                            @endauth
                                        @else
                                            Pendaftaran di Tempat (On-site)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div class="pt-2 border-t border-slate-100">
                            @if($event->registration_link)
                                @auth
                                    <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" rel="noopener noreferrer" class="button-primary w-full py-3 text-center text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-xs">
                                        <span>✍️</span> Daftar Kegiatan Sekarang
                                    </a>
                                @else
                                    <div class="space-y-2.5">
                                        <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl text-center text-xs sm:text-sm font-bold flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition shadow-2xs">
                                            <span class="text-base" aria-hidden="true">🔒</span>
                                            <span>Daftar Kegiatan Sekarang</span>
                                        </a>
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-center space-y-1.5">
                                            <p class="text-xs font-bold text-amber-900 flex items-center justify-center gap-1">
                                                <span>🔒</span> Khusus Member KucingMu
                                            </p>
                                            <p class="text-[11px] text-amber-800 leading-relaxed">
                                                Tombol pendaftaran aktif setelah Anda login sebagai member.
                                            </p>
                                            <div class="flex items-center gap-2 pt-1">
                                                <a href="{{ route('login') }}" class="flex-1 button-primary py-1.5 text-xs font-bold text-center">
                                                    Masuk
                                                </a>
                                                @if(Route::has('register'))
                                                    <a href="{{ route('register') }}" class="flex-1 button-secondary py-1.5 text-xs font-bold text-center border-amber-300 bg-white hover:bg-amber-100/50">
                                                        Daftar
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endauth
                            @else
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs font-semibold text-slate-600">
                                    Pendaftaran langsung di lokasi acara
                                </div>
                            @endif
                        </div>

                        <!-- Share Section -->
                        <div class="pt-4 border-t border-slate-100 space-y-2.5">
                            <span class="block text-xs font-bold text-slate-700">Bagikan Agenda Ini:</span>
                            <div class="flex items-center gap-2">
                                <a href="https://api.whatsapp.com/send?text={{ rawurlencode($event->title . ' - Info lengkap: ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs" aria-label="Bagikan ke WhatsApp">
                                    <span>💬</span> WhatsApp
                                </a>

                                <button type="button" @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2500)" class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition border border-slate-200">
                                    <span x-text="copied ? '✅ Disalin!' : '🔗 Salin Tautan'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Organization Context Card -->
                        <div class="bg-teal-50/70 border border-teal-200/80 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-base" aria-hidden="true">🩺</span>
                                <h4 class="font-outfit font-bold text-teal-950 text-xs">Layanan Terpadu KucingMu</h4>
                            </div>
                            <p class="text-[11px] text-teal-900 leading-relaxed">
                                Agenda sosialisasi, edukasi kesejahteraan hewan, dan pemeriksaan kesehatan hewan mitra persyarikatan untuk kesehatan kucing peliharaan maupun kucing komunitas.
                            </p>
                        </div>
                    </div>

                </aside>

            </div>

            <!-- Other Upcoming Events Section -->
            @if(isset($otherEvents) && $otherEvents->isNotEmpty())
                <section class="pt-10 border-t border-slate-200 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="eyebrow">Agenda Lainnya</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">Kegiatan & Pemeriksaan Terdekat Lainnya</h2>
                        </div>
                        <a href="{{ url('/#events') }}" class="text-xs font-bold text-teal-800 hover:text-teal-950 hover:underline">
                            Lihat Semua Agenda →
                        </a>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($otherEvents as $other)
                            <div class="content-card flex flex-col justify-between">
                                <div>
                                    <!-- Banner Thumbnail -->
                                    <a href="{{ route('events.show', $other) }}" class="block focus:outline-none focus:ring-2 focus:ring-teal-700 rounded-lg">
                                        <div class="h-36 w-full bg-slate-100 rounded-lg overflow-hidden relative border border-slate-200">
                                            @if($other->banner_path)
                                                <img src="{{ asset('storage/' . $other->banner_path) }}" alt="{{ $other->title }}" width="360" height="144" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex flex-col items-center justify-center text-slate-400">
                                                    <span class="text-2xl" aria-hidden="true">📅</span>
                                                    <span class="text-[10px] font-semibold uppercase tracking-wider mt-1 text-slate-500">Agenda Kegiatan</span>
                                                </div>
                                            @endif
                                            <div class="absolute top-2.5 left-2.5 bg-teal-800 text-white font-semibold text-[11px] px-2 py-0.5 rounded-md shadow-xs">
                                                {{ $other->date ? $other->date->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </a>

                                    <!-- Content -->
                                    <div class="pt-3 space-y-1.5">
                                        <h3 class="font-outfit text-base font-bold text-slate-900 leading-snug">
                                            <a href="{{ route('events.show', $other) }}" class="hover:text-teal-800 transition-colors">
                                                {{ $other->title }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-slate-600 font-medium flex items-center gap-1.5">
                                            <span aria-hidden="true">📍</span> {{ $other->location }}
                                        </p>
                                        <p class="text-xs text-slate-600 leading-relaxed pt-1">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($other->description), 95, '...') }}
                                        </p>
                                        <a href="{{ route('events.show', $other) }}" class="inline-flex items-center gap-1 text-xs font-bold text-teal-800 hover:text-teal-950 transition-colors pt-1">
                                            Baca Selengkapnya <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="pt-3 mt-3 border-t border-slate-100">
                                    <a href="{{ route('events.show', $other) }}" class="button-secondary w-full text-center text-xs font-semibold py-2">
                                        Detail Agenda
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-10 border-t border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="flex items-center gap-2">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="" aria-hidden="true" width="28" height="28" loading="lazy" decoding="async" class="h-7 w-auto object-contain">
                @else
                    <span class="text-2xl" aria-hidden="true">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-white text-base tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('representative.register') }}" class="text-slate-400 hover:text-amber-300 transition font-semibold">
                    Penjaringan Representatif
                </a>
                <span class="text-slate-700">&bull;</span>
                <a href="{{ route('privacy.policy') }}" class="text-slate-400 hover:text-teal-300 transition">
                    Kebijakan Privasi
                </a>
                <span class="text-slate-700">&bull;</span>
                <a href="{{ route('contact.index') }}" class="text-slate-400 hover:text-teal-300 transition">
                    Pusat Bantuan
                </a>
            </div>
            
            <p class="text-xs text-slate-400 footer-text">
                {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. All rights reserved.' !!}
            </p>
        </div>
    </footer>

    @include('partials.accessibility-widget')
</body>
</html>
