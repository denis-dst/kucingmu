<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Penjaringan Representatif KucingMu Seluruh Indonesia | {{ $app_settings['app_name'] ?? 'KucingMu' }}</title>
    <meta name="description" content="Formulir resmi pendaftaran Penjaringan Representatif KucingMu di seluruh wilayah Indonesia di bawah naungan Majelis Lingkungan Hidup PP Muhammadiyah.">

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
                            Majelis Lingkungan Hidup PP Muhammadiyah
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

    <!-- Header Hero Banner -->
    <section class="bg-gradient-to-br from-teal-900 via-teal-800 to-sky-800 text-white py-12 sm:py-16 border-b border-teal-950 relative overflow-hidden">
        <!-- Subtle ambient backdrop accents -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-teal-400 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-sky-400 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-teal-100 border border-white/20 text-xs font-semibold backdrop-blur">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Program Inisiasi Nasional
                </div>

                <h1 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Penjaringan Representatif KucingMu di Seluruh Wilayah Indonesia
                </h1>

                <p class="text-sm sm:text-base text-teal-100/90 leading-relaxed font-normal">
                    Panggilan pengabdian bagi kader dan warga persyarikatan untuk menjadi simpul penggerak, edukator kesrawan (kesejahteraan hewan), serta duta relawan KucingMu di tingkat Pimpinan Wilayah (PWM), Daerah (PDM), dan Cabang (PCM).
                </p>

                <!-- Syarat Utama Cards -->
                <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
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
                  x-init="initMap()"
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

                        <!-- Nomor WhatsApp Aktif -->
                        <div>
                            <label for="whatsapp_number" class="form-label">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs text-slate-400 font-semibold select-none">
                                    🇮🇩 +62
                                </span>
                                <input type="tel" 
                                       id="whatsapp_number" 
                                       name="whatsapp_number" 
                                       value="{{ old('whatsapp_number', $user->phone ?? '') }}" 
                                       required 
                                       placeholder="81234567890"
                                       class="form-input pl-16">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Untuk verifikasi cepat dan grup koordinasi relawan.</p>
                            @error('whatsapp_number')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username Instagram -->
                        <div class="sm:col-span-2">
                            <label for="instagram_username" class="form-label">Username Instagram <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs text-slate-400 font-semibold select-none">
                                    @
                                </span>
                                <input type="text" 
                                       id="instagram_username" 
                                       name="instagram_username" 
                                       value="{{ old('instagram_username') }}" 
                                       placeholder="akun_instagram_anda"
                                       class="form-input pl-8">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Dapat digunakan untuk kolaborasi konten edukasi Kesrawan di wilayah Anda.</p>
                            @error('instagram_username')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Wilayah Domisili & Auto Tagging Lokasi -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-800 border border-teal-200/80 flex items-center justify-center font-bold text-sm">
                                2
                            </span>
                            <div>
                                <h2 class="font-outfit font-bold text-lg text-slate-900">Wilayah Domisili &amp; Tagging Lokasi</h2>
                                <p class="text-xs text-slate-500">Pilih wilayah administrasi dan tentukan titik koordinat penugasan</p>
                            </div>
                        </div>

                        <button type="button" 
                                @click="locateUser()" 
                                class="button-secondary text-xs px-3 py-1.5 rounded-xl inline-flex items-center gap-1.5 text-teal-800 border-teal-200 bg-teal-50/50 hover:bg-teal-100/70 font-semibold">
                            <span>📍</span> <span class="hidden sm:inline">Gunakan</span> GPS Saya
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Asal Provinsi -->
                        <div>
                            <label for="province_name" class="form-label">Asal Provinsi <span class="text-rose-500">*</span></label>
                            <select id="province_name" 
                                    name="province_name" 
                                    x-model="selectedProvince" 
                                    @change="onProvinceChange()" 
                                    required 
                                    class="form-input">
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}">{{ $prov }}</option>
                                @endforeach
                            </select>
                            @error('province_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Asal Kota/Kabupaten -->
                        <div>
                            <label for="city_name" class="form-label">Asal Kota / Kabupaten <span class="text-rose-500">*</span></label>
                            <select id="city_name" 
                                    name="city_name" 
                                    x-model="selectedCity" 
                                    @change="onCityChange()" 
                                    :disabled="!selectedProvince || availableCities.length === 0"
                                    required 
                                    class="form-input disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">Pilih Kota / Kabupaten</option>
                                <template x-for="city in availableCities" :key="city">
                                    <option :value="city" x-text="city"></option>
                                </template>
                            </select>
                            @error('city_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Asal Kecamatan -->
                        <div>
                            <label for="district_name" class="form-label">Asal Kecamatan <span class="text-rose-500">*</span></label>
                            <input type="text" 
                                   id="district_name" 
                                   name="district_name" 
                                   x-model="district"
                                   @input.debounce.800ms="searchLocationByAddress()"
                                   required 
                                   placeholder="Contoh: Kotagede"
                                   class="form-input">
                            @error('district_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Asal Desa/Kalurahan -->
                        <div>
                            <label for="village_name" class="form-label">Asal Desa / Kelurahan / Kalurahan <span class="text-rose-500">*</span></label>
                            <input type="text" 
                                   id="village_name" 
                                   name="village_name" 
                                   x-model="village"
                                   @input.debounce.800ms="searchLocationByAddress()"
                                   required 
                                   placeholder="Contoh: Rejowinangun"
                                   class="form-input">
                            @error('village_name')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Auto Tagging Lokasi Peta Interaktif (Leaflet) -->
                    <div class="space-y-3 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <label class="form-label mb-0">
                                Titik Koordinat Lokasi (Auto Tagging)
                            </label>
                            <span class="text-[11px] text-slate-500">
                                Geser atau klik peta untuk memperbarui titik koordinat secara presisi
                            </span>
                        </div>

                        <!-- Map Container -->
                        <div class="relative overflow-hidden rounded-2xl border border-slate-300 shadow-inner">
                            <div id="map-container"></div>
                            
                            <!-- Geocoding Status Badge -->
                            <div x-show="isGeocoding" 
                                 x-cloak
                                 class="absolute top-3 right-3 z-[1000] bg-white/90 backdrop-blur px-3 py-1.5 rounded-xl text-xs font-semibold text-teal-800 shadow-md flex items-center gap-2 border border-teal-200">
                                <span class="w-2 h-2 rounded-full bg-teal-600 animate-ping"></span>
                                Memperbarui lokasi...
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
                                    📍 Terdeteksi otomatis
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
                            KucingMu dan Majelis Lingkungan Hidup PP Muhammadiyah menjamin bahwa seluruh data identitas, NBM, nomor kontak, serta dokumen yang Anda lampirkan <strong>tidak akan dipublikasikan ke publik</strong>. Data hanya dipergunakan untuk keperluan administrasi organisasi, verifikasi kelayakan representatif wilayah, dan koordinasi program persyarikatan sesuai <a href="{{ route('privacy.policy') }}" target="_blank" class="font-bold underline text-teal-900 hover:text-teal-950">Kebijakan Privasi KucingMu</a> (UU PDP No. 27/2022).
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
                {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah.' !!}
            </p>
        </div>
    </footer>

    @include('partials.accessibility-widget')

    <!-- Alpine.js & Map Controller -->
    <script>
        const regenciesMapData = @json($regenciesMap);

        function representativeForm() {
            return {
                selectedProvince: '{{ old('province_name', '') }}',
                selectedCity: '{{ old('city_name', '') }}',
                district: '{{ old('district_name', '') }}',
                village: '{{ old('village_name', '') }}',
                availableCities: [],
                
                latitude: '{{ old('latitude', '-7.801389') }}',
                longitude: '{{ old('longitude', '110.364444') }}',
                formattedAddress: '{{ old('formatted_address', '') }}',
                isGeocoding: false,

                skFileName: '',
                ktamFileName: '',
                essayText: '{{ old('animal_welfare_essay', '') }}',

                map: null,
                marker: null,

                init() {
                    if (this.selectedProvince && regenciesMapData[this.selectedProvince]) {
                        this.availableCities = regenciesMapData[this.selectedProvince];
                    }
                },

                onProvinceChange() {
                    if (this.selectedProvince && regenciesMapData[this.selectedProvince]) {
                        this.availableCities = regenciesMapData[this.selectedProvince];
                        this.selectedCity = '';
                        this.searchLocationByAddress();
                    } else {
                        this.availableCities = [];
                        this.selectedCity = '';
                    }
                },

                onCityChange() {
                    if (this.selectedCity) {
                        this.searchLocationByAddress();
                    }
                },

                onFileSelected(event, type) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (type === 'sk') {
                        this.skFileName = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
                    } else if (type === 'ktam') {
                        this.ktamFileName = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
                    }
                },

                initMap() {
                    this.init();
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

                    this.marker.on('dragend', (e) => {
                        const position = this.marker.getLatLng();
                        this.updateCoordinates(position.lat, position.lng, true);
                    });

                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.updateCoordinates(e.latlng.lat, e.latlng.lng, true);
                    });

                    // Initial address reverse geocode if empty
                    if (!this.formattedAddress && this.latitude && this.longitude) {
                        this.reverseGeocode(defaultLat, defaultLng);
                    }
                },

                updateCoordinates(lat, lng, shouldReverseGeocode = false) {
                    this.latitude = lat.toFixed(7);
                    this.longitude = lng.toFixed(7);
                    if (shouldReverseGeocode) {
                        this.reverseGeocode(lat, lng);
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
                    if (this.village) parts.push(this.village);
                    if (this.district) parts.push(this.district);
                    if (this.selectedCity) parts.push(this.selectedCity);
                    if (this.selectedProvince) parts.push(this.selectedProvince);
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

                    this.isGeocoding = true;
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            this.map.setView([lat, lng], 16);
                            this.marker.setLatLng([lat, lng]);
                            this.updateCoordinates(lat, lng, true);
                        },
                        (error) => {
                            this.isGeocoding = false;
                            alert('Gagal mendeteksi lokasi: ' + error.message);
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                }
            };
        }
    </script>
</body>
</html>
