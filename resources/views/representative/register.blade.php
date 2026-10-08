<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Penjaringan Representatif KucingMu Seluruh Indonesia | {{ $app_settings['app_name'] ?? 'KucingMu' }}</title>
    <meta name="description" content="Formulir resmi pendaftaran Penjaringan Representatif KucingMu di seluruh wilayah Indonesia di bawah naungan KucingMu.">

    @if(isset($app_settings['app_favicon']))
        <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS for Map Tagging -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        #map-container {
            height: 280px;
            width: 100%;
            border-radius: 1rem;
            z-index: 10;
        }
        @media (min-width: 640px) {
            #map-container {
                height: 340px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-teal-100 selection:text-teal-900 min-h-screen flex flex-col">

    <!-- Accessibility Skip Link -->
    <a href="#form-pendaftaran" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-800 focus:text-white focus:rounded-md focus:shadow-md focus:font-semibold">
        Lewati ke formulir pendaftaran
    </a>

    <!-- Top Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand Logo & Title -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2 rounded-xl p-1">
                    @if(isset($app_settings['app_logo']))
                        <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo KucingMu" class="h-9 sm:h-10 w-auto object-contain">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-teal-700 text-white flex items-center justify-center font-outfit font-bold text-xl shadow-xs">
                            🐱
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="font-outfit font-extrabold text-slate-900 text-base sm:text-lg leading-none tracking-tight group-hover:text-teal-700 transition">
                            {{ $app_settings['app_name'] ?? 'KucingMu' }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase mt-1">
                            KucingMu
                        </span>
                    </div>
                </a>

                <!-- Nav Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ url('/') }}" class="button-secondary text-xs px-3.5 py-2 rounded-xl text-slate-700 hover:text-slate-900 font-semibold">
                        &larr; Beranda
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="button-primary text-xs px-4 py-2 rounded-xl font-bold bg-teal-700 hover:bg-teal-800 text-white shadow-xs">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="button-primary text-xs px-4 py-2 rounded-xl font-bold bg-teal-700 hover:bg-teal-800 text-white shadow-xs">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Header Hero Banner (Balanced & Centered on Desktop) -->
    <section class="bg-gradient-to-br from-teal-900 via-teal-800 to-sky-800 text-white py-12 sm:py-16 lg:py-20 border-b border-teal-950 relative overflow-hidden">
        <!-- Subtle ambient backdrop accents -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-teal-400 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-sky-400 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-4xl mx-auto text-center flex flex-col items-center space-y-5">
                
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-teal-100 border border-white/20 text-xs font-semibold backdrop-blur">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Program Inisiasi Nasional &bull; KucingMu
                </div>

                <h1 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight max-w-3xl">
                    Penjaringan Representatif KucingMu di Seluruh Wilayah Indonesia
                </h1>

                <p class="text-sm sm:text-base text-teal-100/90 leading-relaxed font-normal max-w-2xl">
                    Panggilan pengabdian bagi kader dan warga persyarikatan untuk menjadi simpul penggerak, edukator kesrawan (kesejahteraan hewan), serta duta relawan KucingMu di tingkat Pimpinan Wilayah (PWM), Daerah (PDM), dan Cabang (PCM).
                </p>

                <!-- Syarat Utama Cards (Centered & Balanced) -->
                <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl w-full text-left">
                    <div class="p-4 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm space-y-1">
                        <div class="flex items-center gap-2 text-emerald-300 font-bold text-xs uppercase tracking-wider">
                            <span>✅</span> Syarat 1
                        </div>
                        <p class="text-xs font-semibold text-white">Memiliki Nomor Baku Muhammadiyah (NBM)</p>
                        <p class="text-[11px] text-teal-100/80">Tercatat aktif sebagai anggota resmi Persyarikatan Muhammadiyah.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm space-y-1">
                        <div class="flex items-center gap-2 text-emerald-300 font-bold text-xs uppercase tracking-wider">
                            <span>✅</span> Syarat 2
                        </div>
                        <p class="text-xs font-semibold text-white">Memiliki SK Pimpinan Aktif</p>
                        <p class="text-[11px] text-teal-100/80">Minimal tingkat Pimpinan Ranting / Komisariat (Muhammadiyah atau Organisasi Otonom).</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Main Form Section -->
    <main id="form-pendaftaran" class="flex-1 py-10 sm:py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-8 p-5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 space-y-2 shadow-xs">
                    <div class="flex items-center gap-2 font-outfit font-bold text-sm text-rose-900">
                        <span>⚠️</span> Mohon periksa kembali isian formulir Anda:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('representative.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  x-data="representativeForm()"
                  x-init="initComponent()"
                  class="space-y-8">
                @csrf

                <!-- Section 1: Data Identitas Pribadi -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 flex items-center justify-center font-bold text-sm">
                            1
                        </span>
                        <div>
                            <h2 class="font-outfit font-bold text-lg text-slate-900">Data Identitas Calon Representatif</h2>
                            <p class="text-xs text-slate-500">Informasi identitas resmi kader persyarikatan</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label for="name" class="form-label">Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span></label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name ?? '') }}" 
                                   required 
                                   placeholder="Contoh: Ahmad Dahlan, S.Pt."
                                   class="form-input">
                            @error('name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NBM -->
                        <div>
                            <label for="nbm" class="form-label">Nomor Baku Muhammadiyah (NBM) <span class="text-rose-500">*</span></label>
                            <input type="text" 
                                   id="nbm" 
                                   name="nbm" 
                                   value="{{ old('nbm', $user->nbm ?? '') }}" 
                                   required 
                                   placeholder="Contoh: 1234567"
                                   class="form-input font-mono">
                            <p class="text-[11px] text-slate-400 mt-1">Sesuai yang tertera pada Kartu Tanda Anggota Muhammadiyah (KTAM).</p>
                            @error('nbm')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input type="date" 
                                   id="birth_date" 
                                   name="birth_date" 
                                   value="{{ old('birth_date', $user->birth_date ?? '') }}" 
                                   required 
                                   class="form-input">
                            @error('birth_date')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Aktif -->
                        <div>
                            <label for="email" class="form-label">Alamat Email Aktif <span class="text-rose-500">*</span></label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email ?? '') }}" 
                                   required 
                                   placeholder="nama@email.com"
                                   class="form-input">
                            <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk surat konfirmasi &amp; koordinasi resmi.</p>
                            @error('email')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor WhatsApp Aktif (Clean Input Group Container) -->
                        <div>
                            <label for="whatsapp_number" class="form-label">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-300 focus-within:border-teal-600 focus-within:ring-4 focus-within:ring-teal-100 overflow-hidden bg-white shadow-xs transition">
                                <span class="inline-flex items-center px-3.5 bg-slate-100 border-r border-slate-200 text-xs font-bold text-slate-700 select-none shrink-0">
                                    🇮🇩 +62
                                </span>
                                <input type="tel" 
                                       id="whatsapp_number" 
                                       name="whatsapp_number" 
                                       value="{{ old('whatsapp_number', $user->phone ?? '') }}" 
                                       required 
                                       placeholder="81234567890"
                                       class="w-full border-0 px-3.5 py-2.5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Contoh: 81234567890 (tanpa angka 0 di depan).</p>
                            @error('whatsapp_number')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username Instagram (Clean Input Group Container) -->
                        <div class="sm:col-span-2">
                            <label for="instagram_username" class="form-label">Username Instagram <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <div class="flex rounded-xl border border-slate-300 focus-within:border-teal-600 focus-within:ring-4 focus-within:ring-teal-100 overflow-hidden bg-white shadow-xs transition">
                                <span class="inline-flex items-center px-3.5 bg-slate-100 border-r border-slate-200 text-xs font-bold text-slate-500 select-none shrink-0">
                                    @
                                </span>
                                <input type="text" 
                                       id="instagram_username" 
                                       name="instagram_username" 
                                       value="{{ old('instagram_username') }}" 
                                       placeholder="akun_instagram_anda"
                                       class="w-full border-0 px-3.5 py-2.5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Dapat digunakan untuk kolaborasi konten edukasi Kesrawan di wilayah Anda.</p>
                            @error('instagram_username')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Wilayah Domisili & Auto Tagging Lokasi (Cascading Selects) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 flex items-center justify-center font-bold text-sm">
                                2
                            </span>
                            <div>
                                <h2 class="font-outfit font-bold text-lg text-slate-900">Wilayah Domisili &amp; Tagging Lokasi</h2>
                                <p class="text-xs text-slate-500">Pilih wilayah administrasi (Provinsi, Kota, Kecamatan, Desa) secara bertingkat</p>
                            </div>
                        </div>

                        <button type="button" 
                                @click="locateUser()" 
                                :disabled="isLocating || isGeocoding"
                                class="button-secondary text-xs px-3.5 py-2 rounded-xl inline-flex items-center gap-2 text-teal-800 border-teal-200 bg-teal-50/70 hover:bg-teal-100 font-semibold shadow-2xs transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-show="!isLocating">📍</span>
                            <span x-show="isLocating" x-cloak class="w-3.5 h-3.5 border-2 border-teal-700 border-t-transparent rounded-full animate-spin"></span>
                            <span x-text="isLocating ? 'Mencari Lokasi...' : 'Gunakan GPS Saya'"></span>
                        </button>
                    </div>

                    <!-- Auto-Fill Status Notification -->
                    <div x-show="autoFillStatus" 
                         x-cloak 
                         x-transition
                         class="p-3.5 bg-teal-50 border border-teal-200 text-teal-950 rounded-2xl text-xs flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base shrink-0">✨</span>
                            <span class="font-medium leading-relaxed" x-text="autoFillStatus"></span>
                        </div>
                        <button type="button" 
                                @click="autoFillStatus = ''" 
                                class="text-teal-700 hover:text-teal-950 p-1 rounded-lg hover:bg-teal-100 transition shrink-0" 
                                title="Tutup notifikasi">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        <!-- 1. Asal Provinsi (Dropdown) -->
                        <div>
                            <label for="province_select" class="form-label">1. Asal Provinsi <span class="text-rose-500">*</span></label>
                            <select id="province_select" 
                                    x-model="selectedProvinceId" 
                                    @change="onProvinceChange()" 
                                    required 
                                    class="form-input">
                                <option value="">-- Pilih Provinsi --</option>
                                <template x-for="p in provincesList" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                            <input type="hidden" name="province_name" :value="selectedProvinceName">
                            @error('province_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 2. Asal Kota/Kabupaten (Dropdown) -->
                        <div>
                            <label for="regency_select" class="form-label flex items-center justify-between">
                                <span>2. Asal Kota / Kabupaten <span class="text-rose-500">*</span></span>
                                <span x-show="isLoadingRegencies" x-cloak class="text-[10px] text-teal-700 font-semibold animate-pulse">
                                    Memuat kota...
                                </span>
                            </label>
                            <select id="regency_select" 
                                    x-model="selectedRegencyId" 
                                    @change="onRegencyChange()" 
                                    :disabled="!selectedProvinceId || isLoadingRegencies || regenciesList.length === 0"
                                    required 
                                    class="form-input disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">-- Pilih Kota / Kabupaten --</option>
                                <template x-for="r in regenciesList" :key="r.id">
                                    <option :value="r.id" x-text="r.name"></option>
                                </template>
                            </select>
                            <input type="hidden" name="city_name" :value="selectedRegencyName">
                            @error('city_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 3. Asal Kecamatan (Dropdown) -->
                        <div>
                            <label for="district_select" class="form-label flex items-center justify-between">
                                <span>3. Asal Kecamatan <span class="text-rose-500">*</span></span>
                                <span x-show="isLoadingDistricts" x-cloak class="text-[10px] text-teal-700 font-semibold animate-pulse">
                                    Memuat kecamatan...
                                </span>
                            </label>
                            <select id="district_select" 
                                    x-model="selectedDistrictId" 
                                    @change="onDistrictChange()" 
                                    :disabled="!selectedRegencyId || isLoadingDistricts || districtsList.length === 0"
                                    required 
                                    class="form-input disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">-- Pilih Kecamatan --</option>
                                <template x-for="d in districtsList" :key="d.id">
                                    <option :value="d.id" x-text="d.name"></option>
                                </template>
                            </select>
                            <input type="hidden" name="district_name" :value="selectedDistrictName">
                            @error('district_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 4. Asal Desa/Kalurahan (Dropdown) -->
                        <div>
                            <label for="village_select" class="form-label flex items-center justify-between">
                                <span>4. Asal Desa / Kelurahan / Kalurahan <span class="text-rose-500">*</span></span>
                                <span x-show="isLoadingVillages" x-cloak class="text-[10px] text-teal-700 font-semibold animate-pulse">
                                    Memuat desa/kelurahan...
                                </span>
                            </label>
                            <select id="village_select" 
                                    x-model="selectedVillageId" 
                                    @change="onVillageChange()" 
                                    :disabled="!selectedDistrictId || isLoadingVillages || villagesList.length === 0"
                                    required 
                                    class="form-input disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">-- Pilih Desa / Kelurahan --</option>
                                <template x-for="v in villagesList" :key="v.id">
                                    <option :value="v.id" x-text="v.name"></option>
                                </template>
                            </select>
                            <input type="hidden" name="village_name" :value="selectedVillageName">
                            @error('village_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Auto Tagging Lokasi Peta Interaktif (Leaflet) -->
                    <div class="space-y-3 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <label class="form-label mb-0">
                                Titik Koordinat Lokasi (Auto Tagging Peta)
                            </label>
                            <span class="text-[11px] text-slate-500">
                                Titik otomatis berpindah saat Anda memilih wilayah di atas, atau geser pin manual
                            </span>
                        </div>

                        <!-- Map Container -->
                        <div class="relative overflow-hidden rounded-2xl border border-slate-300 shadow-inner">
                            <div id="map-container"></div>
                            
                            <!-- Geocoding Status Badge -->
                            <div x-show="isGeocoding" 
                                 x-cloak
                                 class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-xl text-xs font-semibold text-teal-800 shadow-md flex items-center gap-2 border border-teal-200">
                                <span class="w-2 h-2 rounded-full bg-teal-600 animate-ping"></span>
                                Memperbarui koordinat...
                            </div>
                        </div>

                        <!-- Coordinates & Formatted Address Summary Box -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-slate-700">Latitude:</span>
                                    <span class="font-mono text-teal-800 font-semibold" x-text="latitude || '-'"></span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="font-bold text-slate-700">Longitude:</span>
                                    <span class="font-mono text-teal-800 font-semibold" x-text="longitude || '-'"></span>
                                </div>
                                <span class="text-[11px] text-slate-500" x-show="formattedAddress">
                                    📍 Alamat Terdeteksi
                                </span>
                            </div>
                            <div x-show="formattedAddress" class="text-slate-600 border-t border-slate-200/60 pt-1.5 leading-relaxed">
                                <span class="font-semibold text-slate-700">Alamat Peta:</span> <span x-text="formattedAddress"></span>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Form Submission -->
                        <input type="hidden" name="latitude" :value="latitude">
                        <input type="hidden" name="longitude" :value="longitude">
                        <input type="hidden" name="formatted_address" :value="formattedAddress">
                    </div>
                </div>

                <!-- Section 3: Keaktifan Muhammadiyah & Dokumen SK/KTAM -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 flex items-center justify-center font-bold text-sm">
                            3
                        </span>
                        <div>
                            <h2 class="font-outfit font-bold text-lg text-slate-900">Keaktifan Organisasi &amp; Dokumen Pendukung</h2>
                            <p class="text-xs text-slate-500">Unggah berkas resmi SK Pimpinan dan Kartu Tanda Anggota Muhammadiyah</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Pimpinan Muhammadiyah / Ortom yang Aktif Diikuti -->
                        <div>
                            <label for="muhammadiyah_active_leadership" class="form-label">
                                Pimpinan Muhammadiyah / Ortom yang Sedang Aktif Diikuti <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="muhammadiyah_active_leadership" 
                                   name="muhammadiyah_active_leadership" 
                                   value="{{ old('muhammadiyah_active_leadership') }}" 
                                   required 
                                   placeholder="Contoh: PRM Rejowinangun / PC IMM Sleman / PD Pemuda Muhammadiyah Bantul / dll."
                                   class="form-input">
                            <p class="text-[11px] text-slate-400 mt-1">
                                Minimal tingkat Ranting atau Komisariat (Muhammadiyah, Aisyiyah, Pemuda Muhammadiyah, Nasyiatul Aisyiyah, IMM, IPM, Hizbul Wathan, atau Tapak Suci).
                            </p>
                            @error('muhammadiyah_active_leadership')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload 1: SK Pimpinan Muhammadiyah Aktif (PDF) -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <label for="sk_pimpinan_document" class="font-bold text-sm text-slate-900 block">
                                        Surat Keputusan (SK) Pimpinan Aktif <span class="text-rose-500">*</span>
                                    </label>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Unggah berkas SK pengangkatan atau kepengurusan resmi yang masih berlaku (Format: <strong>PDF</strong>, maks. 10 MB).
                                    </p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-teal-100 text-teal-800">
                                    PDF Saja
                                </span>
                            </div>

                            <input type="file" 
                                   id="sk_pimpinan_document" 
                                   name="sk_pimpinan_document" 
                                   accept="application/pdf"
                                   @change="onFileSelected($event, 'sk')"
                                   required
                                   class="form-input file:mr-4 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-700 file:text-white hover:file:bg-teal-800 text-xs">

                            <div x-show="skFileName" x-cloak class="text-xs text-teal-800 font-semibold flex items-center gap-1.5">
                                <span>📄</span> <span x-text="skFileName"></span>
                            </div>
                            @error('sk_pimpinan_document')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload 2: Screenshot KTAM Fisik / Aplikasi MASA (Image / PDF) -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <label for="ktam_document" class="font-bold text-sm text-slate-900 block">
                                        Screenshot KTAM Fisik / Aplikasi MASA Muhammadiyah <span class="text-rose-500">*</span>
                                    </label>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Unggah foto kartu KTAM fisik atau tangkapan layar kartu digital dari aplikasi MASA (Format: <strong>JPG, PNG, WEBP, atau PDF</strong>, maks. 10 MB).
                                    </p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-teal-100 text-teal-800">
                                    Foto / PDF
                                </span>
                            </div>

                            <input type="file" 
                                   id="ktam_document" 
                                   name="ktam_document" 
                                   accept="image/jpeg,image/png,image/webp,application/pdf"
                                   @change="onFileSelected($event, 'ktam')"
                                   required
                                   class="form-input file:mr-4 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-700 file:text-white hover:file:bg-teal-800 text-xs">

                            <div x-show="ktamFileName" x-cloak class="text-xs text-teal-800 font-semibold flex items-center gap-1.5">
                                <span>🖼️</span> <span x-text="ktamFileName"></span>
                            </div>
                            @error('ktam_document')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Wawasan Kesrawan (Animal Welfare) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 flex items-center justify-center font-bold text-sm">
                            4
                        </span>
                        <div>
                            <h2 class="font-outfit font-bold text-lg text-slate-900">Wawasan Kesejahteraan Hewan (Kesrawan / Animal Welfare)</h2>
                            <p class="text-xs text-slate-500">Pandangan dan komitmen Anda mengenai perlindungan dan manajemen kucing lingkungan</p>
                        </div>
                    </div>

                    <div>
                        <label for="animal_welfare_essay" class="form-label">
                            Tuliskan wawasan tentang Kesrawan / Animal Welfare di bawah ini <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="animal_welfare_essay" 
                                  name="animal_welfare_essay" 
                                  rows="6" 
                                  x-model="essayText"
                                  required 
                                  placeholder="Ceritakan pemahaman Anda mengenai prinsip kesejahteraan hewan (5 Kebebasan Hewan / Animal Welfare), urgensi sterilisasi dan vaksinasi kucing lingkungan, edukasi warga persyarikatan, serta gagasan program kerja representatif di wilayah Anda..."
                                  class="form-input text-sm leading-relaxed"></textarea>
                        
                        <div class="flex items-center justify-between mt-2 text-xs text-slate-400">
                            <span>Isikan uraian yang jelas, lugas, dan mencerminkan komitmen pengabdian Anda.</span>
                            <span :class="essayText.length < 30 ? 'text-amber-600' : 'text-emerald-700'" class="font-semibold font-mono">
                                <span x-text="essayText.length"></span> karakter
                            </span>
                        </div>
                        @error('animal_welfare_essay')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section 5: Persetujuan Kebijakan Privasi & Ketentuan Data -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-5">
                    <div class="bg-teal-50/70 border border-teal-200 rounded-2xl p-4 sm:p-5 space-y-2">
                        <div class="flex items-center gap-2 text-teal-900 font-outfit font-bold text-sm">
                            <span>🛡️</span> Komitmen Pelindungan Data Organisasi
                        </div>
                        <p class="text-xs text-teal-950 leading-relaxed">
                            KucingMu dan KucingMu menjamin bahwa seluruh data identitas, NBM, nomor kontak, serta dokumen yang Anda lampirkan <strong>tidak akan dipublikasikan ke publik</strong>. Data hanya dipergunakan untuk keperluan administrasi organisasi, verifikasi kelayakan representatif wilayah, dan koordinasi program persyarikatan sesuai <a href="{{ route('privacy.policy') }}" target="_blank" class="font-bold underline text-teal-900 hover:text-teal-950">Kebijakan Privasi KucingMu</a> (UU PDP No. 27/2022).
                        </p>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="privacy_agreed" 
                                   value="1" 
                                   {{ old('privacy_agreed') ? 'checked' : '' }}
                                   required 
                                   class="mt-1 w-4 h-4 text-teal-700 border-slate-300 rounded focus:ring-teal-500">
                            <span class="text-xs text-slate-700 leading-relaxed">
                                Saya menyatakan bahwa data yang saya kirimkan adalah benar, sah, dan dapat dipertanggungjawabkan. Saya menyetujui pemrosesan data untuk seleksi Penjaringan Representatif KucingMu dan bersedia mematuhi pedoman etik persyarikatan. <span class="text-rose-500">*</span>
                            </span>
                        </label>
                        @error('privacy_agreed')
                            <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ url('/') }}" class="button-secondary text-sm px-6 py-3 rounded-2xl w-full sm:w-auto text-center font-semibold order-2 sm:order-1">
                        &larr; Batal &amp; Kembali
                    </a>

                    <button type="submit" 
                            class="button-primary text-sm px-8 py-3.5 rounded-2xl w-full sm:w-auto text-center font-extrabold bg-teal-700 hover:bg-teal-800 text-white shadow-md order-1 sm:order-2 flex items-center justify-center gap-2">
                        <span>🚀</span> Kirim Pendaftaran Representatif
                    </button>
                </div>

            </form>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-10 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="flex items-center gap-2">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="" aria-hidden="true" width="28" height="28" class="h-7 w-auto object-contain">
                @else
                    <span class="text-2xl" aria-hidden="true">🐱</span>
                @endif
                <span class="font-outfit font-extrabold text-white text-base tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </div>
            
            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('privacy.policy') }}" class="text-slate-400 hover:text-teal-300 transition">
                    Kebijakan Privasi
                </a>
                <span class="text-slate-700">&bull;</span>
                <a href="{{ route('contact.index') }}" class="text-slate-400 hover:text-teal-300 transition">
                    Hubungi Kami
                </a>
                <span class="text-slate-700">&bull;</span>
                <a href="{{ url('/') }}" class="text-slate-400 hover:text-teal-300 transition">
                    Beranda
                </a>
            </div>

            <p class="text-xs text-slate-400 footer-text">
                {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. KucingMu.' !!}
            </p>
        </div>
    </footer>

    @include('partials.accessibility-widget')

    <!-- Alpine.js & Cascading Wilayah + Map Controller -->
    <script>
        function representativeForm() {
            return {
                provincesList: [],
                regenciesList: [],
                districtsList: [],
                villagesList: [],

                selectedProvinceId: '',
                selectedProvinceName: '{{ old('province_name', '') }}',
                selectedRegencyId: '',
                selectedRegencyName: '{{ old('city_name', '') }}',
                selectedDistrictId: '',
                selectedDistrictName: '{{ old('district_name', '') }}',
                selectedVillageId: '',
                selectedVillageName: '{{ old('village_name', '') }}',

                isLoadingRegencies: false,
                isLoadingDistricts: false,
                isLoadingVillages: false,

                latitude: '{{ old('latitude', '-7.801389') }}',
                longitude: '{{ old('longitude', '110.364444') }}',
                formattedAddress: '{{ old('formatted_address', '') }}',
                isGeocoding: false,
                isLocating: false,
                autoFillStatus: '',

                skFileName: '',
                ktamFileName: '',
                essayText: '{{ old('animal_welfare_essay', '') }}',

                map: null,
                marker: null,

                initComponent() {
                    this.loadProvinces();
                    this.initMap();
                },

                // String cleaner for accurate regional name matching
                cleanName(name) {
                    if (!name || typeof name !== 'string') return '';
                    return name
                        .toLowerCase()
                        .replace(/\b(provinsi|prov\.|daerah istimewa|daerah khusus ibukota|d\.i\.|di|dki|special region of|kabupaten|kab\.|kota|regency|city|kecamatan|kec\.|district|subdistrict|kelurahan|kel\.|desa|village)\b/gi, '')
                        .replace(/[^a-z0-9]/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                },

                // Match finder among options
                findBestMatch(list, candidates) {
                    if (!list || !list.length || !candidates || !candidates.length) return null;

                    const validCandidates = candidates.filter(c => typeof c === 'string' && c.trim().length > 0);
                    if (validCandidates.length === 0) return null;

                    // 1. Exact cleaned name match
                    for (const cand of validCandidates) {
                        const cleanedCand = this.cleanName(cand);
                        if (!cleanedCand) continue;
                        const match = list.find(item => this.cleanName(item.name) === cleanedCand);
                        if (match) return match;
                    }

                    // 2. Substring inclusion on cleaned name
                    for (const cand of validCandidates) {
                        const cleanedCand = this.cleanName(cand);
                        if (!cleanedCand || cleanedCand.length < 3) continue;
                        const match = list.find(item => {
                            const cleanedItem = this.cleanName(item.name);
                            return cleanedItem.length >= 3 && (cleanedItem.includes(cleanedCand) || cleanedCand.includes(cleanedItem));
                        });
                        if (match) return match;
                    }

                    // 3. Fallback raw case-insensitive search
                    for (const cand of validCandidates) {
                        const lowerCand = cand.toLowerCase().trim();
                        const match = list.find(item => item.name.toLowerCase().includes(lowerCand) || lowerCand.includes(item.name.toLowerCase()));
                        if (match) return match;
                    }

                    return null;
                },

                // Auto-fill all 4 regional select dropdowns from GPS coordinates
                async autoFillFromCoordinates(lat, lng) {
                    this.isGeocoding = true;
                    this.autoFillStatus = '';
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                        const data = await response.json();
                        
                        if (!data || !data.address) {
                            return;
                        }

                        const addr = data.address;
                        if (data.display_name) {
                            this.formattedAddress = data.display_name;
                        }

                        // Pastikan daftar provinsi sudah termuat
                        if (!this.provincesList || this.provincesList.length === 0) {
                            await this.loadProvinces();
                        }

                        // 1. Cocokkan Provinsi
                        const provCandidates = [
                            addr.state,
                            addr.province,
                            addr.region,
                            addr.state_district
                        ].filter(Boolean);

                        const matchedProvince = this.findBestMatch(this.provincesList, provCandidates);
                        if (matchedProvince) {
                            this.selectedProvinceId = matchedProvince.id;
                            this.selectedProvinceName = matchedProvince.name;

                            // Muat data Kota/Kabupaten
                            await this.loadRegencies(matchedProvince.id);

                            // 2. Cocokkan Kota/Kabupaten
                            const regCandidates = [
                                addr.city,
                                addr.county,
                                addr.city_district,
                                addr.town,
                                addr.municipality
                            ].filter(Boolean);

                            const matchedRegency = this.findBestMatch(this.regenciesList, regCandidates);
                            if (matchedRegency) {
                                this.selectedRegencyId = matchedRegency.id;
                                this.selectedRegencyName = matchedRegency.name;

                                // Muat data Kecamatan
                                await this.loadDistricts(matchedRegency.id);

                                // 3. Cocokkan Kecamatan
                                const distCandidates = [
                                    addr.municipality,
                                    addr.city_district,
                                    addr.district,
                                    addr.suburb,
                                    addr.town
                                ].filter(Boolean);

                                const matchedDistrict = this.findBestMatch(this.districtsList, distCandidates);
                                if (matchedDistrict) {
                                    this.selectedDistrictId = matchedDistrict.id;
                                    this.selectedDistrictName = matchedDistrict.name;

                                    // Muat data Desa/Kelurahan
                                    await this.loadVillages(matchedDistrict.id);

                                    // 4. Cocokkan Desa/Kelurahan
                                    const vilCandidates = [
                                        addr.village,
                                        addr.quarter,
                                        addr.suburb,
                                        addr.neighbourhood,
                                        addr.hamlet,
                                        addr.residential
                                    ].filter(Boolean);

                                    const matchedVillage = this.findBestMatch(this.villagesList, vilCandidates);
                                    if (matchedVillage) {
                                        this.selectedVillageId = matchedVillage.id;
                                        this.selectedVillageName = matchedVillage.name;
                                    }
                                }
                            }
                        }

                        // Ringkasan hasil deteksi wilayah
                        const matchedHierarchy = [
                            this.selectedVillageName ? 'Desa/Kel. ' + this.selectedVillageName : null,
                            this.selectedDistrictName ? 'Kec. ' + this.selectedDistrictName : null,
                            this.selectedRegencyName,
                            this.selectedProvinceName
                        ].filter(Boolean);

                        if (matchedHierarchy.length > 0) {
                            this.autoFillStatus = 'Wilayah berhasil terisi otomatis: ' + matchedHierarchy.join(', ');
                        }
                    } catch (err) {
                        console.error('Gagal memproses auto-fill wilayah dari koordinat GPS:', err);
                    } finally {
                        this.isGeocoding = false;
                    }
                },

                // 1. Fetch Provinces
                async loadProvinces() {
                    try {
                        let res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                        if (!res.ok) {
                            res = await fetch('{{ route('api.representatives.provinces') }}');
                        }
                        this.provincesList = await res.json();

                        // If old province exists, preselect
                        if (this.selectedProvinceName) {
                            const found = this.provincesList.find(p => p.name.toLowerCase() === this.selectedProvinceName.toLowerCase());
                            if (found) {
                                this.selectedProvinceId = found.id;
                                this.selectedProvinceName = found.name;
                                await this.loadRegencies(found.id, true);
                            }
                        }
                    } catch (e) {
                        console.error('Gagal memuat provinsi:', e);
                    }
                },

                // 2. Province change handler
                async onProvinceChange() {
                    const p = this.provincesList.find(item => String(item.id) === String(this.selectedProvinceId));
                    this.selectedProvinceName = p ? p.name : '';
                    
                    this.regenciesList = [];
                    this.districtsList = [];
                    this.villagesList = [];
                    this.selectedRegencyId = '';
                    this.selectedRegencyName = '';
                    this.selectedDistrictId = '';
                    this.selectedDistrictName = '';
                    this.selectedVillageId = '';
                    this.selectedVillageName = '';

                    if (this.selectedProvinceId) {
                        await this.loadRegencies(this.selectedProvinceId);
                        this.searchLocationByAddress();
                    }
                },

                // 3. Fetch Regencies
                async loadRegencies(provinceId, isPreload = false) {
                    this.isLoadingRegencies = true;
                    try {
                        let res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`);
                        if (!res.ok) {
                            res = await fetch(`{{ route('api.representatives.regencies') }}?province_id=${provinceId}`);
                        }
                        this.regenciesList = await res.json();

                        if (isPreload && this.selectedRegencyName) {
                            const found = this.regenciesList.find(r => r.name.toLowerCase() === this.selectedRegencyName.toLowerCase());
                            if (found) {
                                this.selectedRegencyId = found.id;
                                this.selectedRegencyName = found.name;
                                await this.loadDistricts(found.id, true);
                            }
                        }
                    } catch (e) {
                        console.error('Gagal memuat kota/kabupaten:', e);
                    } finally {
                        this.isLoadingRegencies = false;
                    }
                },

                // 4. Regency change handler
                async onRegencyChange() {
                    const r = this.regenciesList.find(item => String(item.id) === String(this.selectedRegencyId));
                    this.selectedRegencyName = r ? r.name : '';
                    
                    this.districtsList = [];
                    this.villagesList = [];
                    this.selectedDistrictId = '';
                    this.selectedDistrictName = '';
                    this.selectedVillageId = '';
                    this.selectedVillageName = '';

                    if (this.selectedRegencyId) {
                        await this.loadDistricts(this.selectedRegencyId);
                        this.searchLocationByAddress();
                    }
                },

                // 5. Fetch Districts
                async loadDistricts(regencyId, isPreload = false) {
                    this.isLoadingDistricts = true;
                    try {
                        let res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`);
                        if (!res.ok) {
                            res = await fetch(`{{ route('api.representatives.districts') }}?regency_id=${regencyId}`);
                        }
                        this.districtsList = await res.json();

                        if (isPreload && this.selectedDistrictName) {
                            const found = this.districtsList.find(d => d.name.toLowerCase() === this.selectedDistrictName.toLowerCase());
                            if (found) {
                                this.selectedDistrictId = found.id;
                                this.selectedDistrictName = found.name;
                                await this.loadVillages(found.id, true);
                            }
                        }
                    } catch (e) {
                        console.error('Gagal memuat kecamatan:', e);
                    } finally {
                        this.isLoadingDistricts = false;
                    }
                },

                // 6. District change handler
                async onDistrictChange() {
                    const d = this.districtsList.find(item => String(item.id) === String(this.selectedDistrictId));
                    this.selectedDistrictName = d ? d.name : '';
                    
                    this.villagesList = [];
                    this.selectedVillageId = '';
                    this.selectedVillageName = '';

                    if (this.selectedDistrictId) {
                        await this.loadVillages(this.selectedDistrictId);
                        this.searchLocationByAddress();
                    }
                },

                // 7. Fetch Villages
                async loadVillages(districtId, isPreload = false) {
                    this.isLoadingVillages = true;
                    try {
                        let res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`);
                        if (!res.ok) {
                            res = await fetch(`{{ route('api.representatives.villages') }}?district_id=${districtId}`);
                        }
                        this.villagesList = await res.json();

                        if (isPreload && this.selectedVillageName) {
                            const found = this.villagesList.find(v => v.name.toLowerCase() === this.selectedVillageName.toLowerCase());
                            if (found) {
                                this.selectedVillageId = found.id;
                                this.selectedVillageName = found.name;
                            }
                        }
                    } catch (e) {
                        console.error('Gagal memuat desa/kelurahan:', e);
                    } finally {
                        this.isLoadingVillages = false;
                    }
                },

                // 8. Village change handler
                onVillageChange() {
                    const v = this.villagesList.find(item => String(item.id) === String(this.selectedVillageId));
                    this.selectedVillageName = v ? v.name : '';
                    if (this.selectedVillageName) {
                        this.searchLocationByAddress();
                    }
                },

                // 9. File upload indicators
                onFileSelected(event, type) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (type === 'sk') {
                        this.skFileName = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
                    } else if (type === 'ktam') {
                        this.ktamFileName = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
                    }
                },

                // 10. Map initialization & Geocoding
                initMap() {
                    const defaultLat = parseFloat(this.latitude) || -7.801389;
                    const defaultLng = parseFloat(this.longitude) || 110.364444;

                    this.map = L.map('map-container').setView([defaultLat, defaultLng], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap kontributor'
                    }).addTo(this.map);

                    this.marker = L.marker([defaultLat, defaultLng], {
                        draggable: true
                    }).addTo(this.map);

                    this.marker.on('dragend', async (e) => {
                        const position = this.marker.getLatLng();
                        this.latitude = position.lat.toFixed(7);
                        this.longitude = position.lng.toFixed(7);
                        await this.autoFillFromCoordinates(position.lat, position.lng);
                    });

                    this.map.on('click', async (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.latitude = e.latlng.lat.toFixed(7);
                        this.longitude = e.latlng.lng.toFixed(7);
                        await this.autoFillFromCoordinates(e.latlng.lat, e.latlng.lng);
                    });

                    if (!this.formattedAddress && this.latitude && this.longitude) {
                        this.reverseGeocode(defaultLat, defaultLng);
                    }
                },

                reverseGeocode(lat, lng) {
                    this.isGeocoding = true;
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                        .then(res => res.json())
                        .then(data => {
                            this.isGeocoding = false;
                            if (data && data.display_name) {
                                this.formattedAddress = data.display_name;
                            }
                        })
                        .catch(() => {
                            this.isGeocoding = false;
                        });
                },

                searchLocationByAddress() {
                    const parts = [];
                    if (this.selectedVillageName) parts.push(this.selectedVillageName);
                    if (this.selectedDistrictName) parts.push(this.selectedDistrictName);
                    if (this.selectedRegencyName) parts.push(this.selectedRegencyName);
                    if (this.selectedProvinceName) parts.push(this.selectedProvinceName);
                    parts.push('Indonesia');

                    if (parts.length <= 1) return;

                    const query = parts.join(', ');
                    this.isGeocoding = true;

                    fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1`)
                        .then(res => res.json())
                        .then(results => {
                            this.isGeocoding = false;
                            if (results && results.length > 0) {
                                const lat = parseFloat(results[0].lat);
                                const lon = parseFloat(results[0].lon);
                                this.map.setView([lat, lon], 14);
                                this.marker.setLatLng([lat, lon]);
                                this.latitude = lat.toFixed(7);
                                this.longitude = lon.toFixed(7);
                                this.formattedAddress = results[0].display_name;
                            }
                        })
                        .catch(() => {
                            this.isGeocoding = false;
                        });
                },

                locateUser() {
                    if (!navigator.geolocation) {
                        alert('Fitur Geolocation tidak didukung oleh peramban Anda.');
                        return;
                    }

                    this.isLocating = true;
                    this.autoFillStatus = '';

                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            this.map.setView([lat, lng], 16);
                            this.marker.setLatLng([lat, lng]);
                            this.latitude = lat.toFixed(7);
                            this.longitude = lng.toFixed(7);

                            await this.autoFillFromCoordinates(lat, lng);
                            this.isLocating = false;
                        },
                        (error) => {
                            this.isLocating = false;
                            let msg = 'Gagal mendeteksi lokasi GPS.';
                            if (error.code === 1) {
                                msg = 'Izin akses lokasi GPS ditolak oleh peramban Anda. Silakan izinkan akses lokasi (GPS) di pengaturan browser Anda.';
                            } else if (error.code === 2) {
                                msg = 'Sinyal atau posisi GPS tidak tersedia pada perangkat Anda saat ini.';
                            } else if (error.code === 3) {
                                msg = 'Waktu permintaan deteksi GPS habis (timeout). Silakan coba kembali.';
                            }
                            alert(msg);
                        },
                        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                    );
                }
            };
        }
    </script>
</body>
</html>
