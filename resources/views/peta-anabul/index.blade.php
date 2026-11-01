<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl" aria-hidden="true">🗺️</span>
                    <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-800 tracking-tight">Peta Anabul</h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Basis data spasial kucing komunitas berbasis GIS untuk pemantauan populasi, kesehatan, dan penanganan anabul.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('peta-anabul.create') }}"
                    class="button-primary min-h-[44px] px-4 py-2.5 text-xs sm:text-sm font-bold flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Petakan Anabul</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet & MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>

    <style>
        .peta-container {
            height: calc(100vh - 270px);
            min-height: 520px;
            width: 100%;
        }
        @media (max-width: 640px) {
            .peta-container {
                height: 480px;
                min-height: 420px;
            }
        }
        /* Custom Marker Styles */
        .cat-marker-pin {
            position: relative;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .cat-marker-pin:hover {
            transform: scale(1.15) translateY(-4px);
            z-index: 1000 !important;
        }
        .cat-marker-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
            background-color: #f1f5f9;
        }
        .cat-marker-pet .cat-marker-avatar {
            border-color: #0f766e; /* Teal for member pet cats */
            box-shadow: 0 0 0 2px #fff, 0 4px 12px rgba(15, 118, 110, 0.45);
        }
        .cat-marker-stray .cat-marker-avatar {
            border-color: #ea580c; /* Orange for stray cats */
            box-shadow: 0 0 0 2px #fff, 0 4px 12px rgba(234, 88, 12, 0.45);
        }
        .cat-marker-badge {
            position: absolute;
            bottom: -3px;
            right: -3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            border: 1.5px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .cat-marker-pet .cat-marker-badge {
            background-color: #0f766e;
            color: #fff;
        }
        .cat-marker-stray .cat-marker-badge {
            background-color: #ea580c;
            color: #fff;
        }
        .cat-marker-urgent .cat-marker-avatar {
            animation: pulse-ring 1.8s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(225, 29, 72, 0); }
            100% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
        }

        /* Leaflet popup styling */
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 0;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .leaflet-popup-content {
            margin: 0;
            line-height: 1.5;
            min-width: 250px;
            max-width: 300px;
        }

        /* Fix Leaflet default blue link overriding button text */
        .leaflet-container a.popup-btn,
        .leaflet-popup-content a.popup-btn,
        .leaflet-popup-content-wrapper a.popup-btn {
            color: #ffffff !important;
            text-decoration: none !important;
        }
        .leaflet-container a.popup-btn *,
        .leaflet-popup-content a.popup-btn *,
        .leaflet-popup-content-wrapper a.popup-btn * {
            color: #ffffff !important;
        }
    </style>

    <div class="py-4 sm:py-6" x-data="petaAnabulApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 text-xs sm:text-sm font-semibold flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-teal-600 hover:text-teal-900 text-base leading-none">&times;</button>
                </div>
            @endif

            <!-- Guest Callout Banner (Akses Publik vs Member) -->
            @guest
                <div class="p-3.5 sm:p-4 rounded-2xl bg-teal-50/80 border border-teal-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-700 text-white flex items-center justify-center text-lg shrink-0">
                            🗺️
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-teal-950">Peta Anabul Terbuka untuk Umum</div>
                            <div class="text-[11px] sm:text-xs text-teal-800 mt-0.5">
                                Anda dapat melihat dan mencari data kucing tanpa harus login. Untuk menambahkan data kucing baru di lingkungan sekitar, silakan masuk dengan akun member KucingMU.
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('login') }}" class="button-secondary min-h-[38px] px-3.5 text-xs font-semibold">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="button-primary min-h-[38px] px-3.5 text-xs font-bold">
                            Daftar Member
                        </a>
                    </div>
                </div>
            @endguest

            <!-- Statistics Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg sm:text-xl font-bold shrink-0">
                        🗺️
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Terdata</div>
                        <div class="text-lg sm:text-xl font-outfit font-black text-slate-800 leading-tight">
                            {{ number_format($stats['total']) }}
                        </div>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg sm:text-xl font-bold shrink-0">
                        🏠
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-teal-600 uppercase tracking-wider">Kucing Peliharaan</div>
                        <div class="text-lg sm:text-xl font-outfit font-black text-slate-800 leading-tight">
                            {{ number_format($stats['pets']) }}
                        </div>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg sm:text-xl font-bold shrink-0">
                        🐾
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-orange-600 uppercase tracking-wider">Kucing Liar Sekitar</div>
                        <div class="text-lg sm:text-xl font-outfit font-black text-slate-800 leading-tight">
                            {{ number_format($stats['strays']) }}
                        </div>
                    </div>
                </div>

                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg sm:text-xl font-bold shrink-0">
                        ⚠️
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Butuh Pertolongan</div>
                        <div class="text-lg sm:text-xl font-outfit font-black text-slate-800 leading-tight">
                            {{ number_format($stats['needs_help']) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar & Mode Switcher -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3.5">
                <form method="GET" action="{{ route('peta-anabul.index') }}" class="space-y-3" id="filter-form">
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <input type="hidden" name="lat" :value="userCoords.lat">
                    <input type="hidden" name="lng" :value="userCoords.lng">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <!-- Search text -->
                        <div class="lg:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Kucing</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>
                                <input type="text" name="q" value="{{ request('q') }}"
                                    placeholder="Nama, kode, ciri fisik, atau alamat..."
                                    class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-100 outline-none">
                            </div>
                        </div>

                        <!-- Tipe Kategori Kucing (Pin Distinction) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori Anabul</label>
                            <select name="ownership_type" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs sm:text-sm bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 outline-none">
                                <option value="">Semua (Peliharaan & Liar)</option>
                                <option value="pet" {{ request('ownership_type') === 'pet' ? 'selected' : '' }}>🏠 Kucing Peliharaan Member</option>
                                <option value="stray" {{ request('ownership_type') === 'stray' ? 'selected' : '' }}>🐾 Kucing Liar / Sekitar</option>
                            </select>
                        </div>

                        <!-- Kondisi -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kondisi</label>
                            <select name="condition" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs sm:text-sm bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 outline-none">
                                <option value="all">Semua Kondisi</option>
                                <option value="sehat" {{ request('condition') === 'sehat' ? 'selected' : '' }}>Sehat</option>
                                <option value="sakit" {{ request('condition') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="terluka" {{ request('condition') === 'terluka' ? 'selected' : '' }}>Terluka</option>
                                <option value="butuh_pertolongan" {{ request('condition') === 'butuh_pertolongan' ? 'selected' : '' }}>Butuh Pertolongan</option>
                            </select>
                        </div>

                        <!-- Wilayah Provinsi -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Provinsi</label>
                            <select name="province" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs sm:text-sm bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 outline-none">
                                <option value="">Semua Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Radius, Buttons & View Switcher -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                        <!-- Radius selection & Geolocation -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-slate-600">Radius Lokasi:</span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="detectUserLocation(3)"
                                    :class="radiusKm == 3 ? 'bg-teal-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="px-2.5 py-1.5 rounded-lg text-xs transition min-h-[36px]">
                                    3 km
                                </button>
                                <button type="button" @click="detectUserLocation(5)"
                                    :class="radiusKm == 5 ? 'bg-teal-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="px-2.5 py-1.5 rounded-lg text-xs transition min-h-[36px]">
                                    5 km
                                </button>
                                <button type="button" @click="detectUserLocation(10)"
                                    :class="radiusKm == 10 ? 'bg-teal-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="px-2.5 py-1.5 rounded-lg text-xs transition min-h-[36px]">
                                    10 km
                                </button>
                                <button type="button" @click="clearRadius()" x-show="radiusKm"
                                    class="px-2 py-1 text-slate-400 hover:text-slate-600 text-xs">
                                    Reset Radius
                                </button>
                            </div>
                            <input type="hidden" name="radius" :value="radiusKm">
                        </div>

                        <!-- Action Buttons & Switcher -->
                        <div class="flex items-center gap-2">
                            <button type="submit" class="button-primary min-h-[38px] px-3.5 py-1.5 text-xs font-bold">
                                Terapkan Filter
                            </button>
                            <a href="{{ route('peta-anabul.index') }}" class="button-secondary min-h-[38px] px-3 py-1.5 text-xs">
                                Reset
                            </a>

                            <!-- View Mode Switcher -->
                            <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200 ml-2">
                                <a href="{{ request()->fullUrlWithQuery(['view' => 'map']) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewMode === 'map' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                    <span>🗺️</span>
                                    <span>Peta</span>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $viewMode === 'list' ? 'bg-white text-teal-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                    <span>📋</span>
                                    <span>Daftar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- PIN LEGEND INDICATOR (Pemenuhan Perintah User) -->
            <div class="flex flex-wrap items-center justify-between gap-3 px-3 py-2 bg-white rounded-xl border border-slate-200 text-xs shadow-2xs">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Legenda Pin Peta:</span>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-teal-700 ring-2 ring-teal-200 inline-block"></span>
                        <span class="font-semibold text-slate-700">Kucing Peliharaan Member (🏠)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-orange-600 ring-2 ring-orange-200 inline-block"></span>
                        <span class="font-semibold text-slate-700">Kucing Liar di Sekitar (🐾)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-600 animate-ping inline-block"></span>
                        <span class="font-semibold text-rose-700">Perlu Pertolongan Segera</span>
                    </div>
                </div>
                <div class="text-[11px] text-slate-500">
                    Klik marker untuk melihat thumbnail dan informasi lengkap anabul.
                </div>
            </div>

            @if($viewMode === 'map')
                <!-- MAP VIEW CONTAINER -->
                <div class="relative bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div id="peta-anabul-map" class="peta-container"></div>

                    <!-- Geolocation status overlay -->
                    <div x-show="isLocating" x-cloak
                         class="absolute top-4 right-4 z-[1000] bg-white/95 backdrop-blur px-3 py-2 rounded-xl text-xs font-semibold text-teal-800 shadow-md border border-teal-200 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-600 animate-ping"></span>
                        <span>Mendeteksi lokasi Anda...</span>
                    </div>
                </div>
            @else
                <!-- LIST VIEW CONTAINER (Alternatif Ramah Mobile & Ringan) -->
                <div class="space-y-4">
                    @if($cats->isEmpty())
                        <div class="p-8 sm:p-12 text-center bg-white rounded-2xl border border-slate-200 shadow-xs space-y-3">
                            <span class="text-4xl">🐱</span>
                            <h3 class="text-base sm:text-lg font-bold text-slate-800">Belum Ada Anabul Terdata</h3>
                            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                                Belum ada laporan kucing di kriteria atau wilayah ini. Jadilah yang pertama memetakan anabul di lingkungan Anda.
                            </p>
                            <a href="{{ route('peta-anabul.create') }}" class="button-primary inline-flex mt-2">
                                + Petakan Anabul Sekarang
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($cats as $cat)
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                                    <div>
                                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                            <img src="{{ $cat->thumbnail_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                            <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1.5">
                                                @if($cat->isPet())
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-teal-700 text-white shadow-xs">
                                                        🏠 Peliharaan
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-orange-600 text-white shadow-xs">
                                                        🐾 Kucing Liar
                                                    </span>
                                                @endif
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $cat->condition_badge_class }}">
                                                    {{ $cat->condition_label }}
                                                </span>
                                            </div>
                                            <div class="absolute bottom-2.5 right-2.5 bg-black/60 backdrop-blur-xs text-white px-2 py-0.5 rounded-lg text-[10px] font-mono">
                                                {{ $cat->unique_code }}
                                            </div>
                                        </div>

                                        <div class="p-4 space-y-2">
                                            <div class="flex items-start justify-between gap-2">
                                                <h3 class="font-outfit font-bold text-slate-900 text-base leading-tight">
                                                    {{ $cat->name }}
                                                </h3>
                                                <span class="text-xs text-slate-500 capitalize">
                                                    {{ $cat->gender === 'jantan' ? '♂ Jantan' : ($cat->gender === 'betina' ? '♀ Betina' : '? Belum Tahu') }}
                                                </span>
                                            </div>

                                            <div class="text-xs text-slate-600 space-y-1">
                                                <div><span class="text-slate-400">Pola/Warna:</span> {{ $cat->coat_pattern ?: '-' }} ({{ $cat->coat_color ?: '-' }})</div>
                                                <div><span class="text-slate-400">Lokasi:</span> {{ $cat->district ? $cat->district . ', ' : '' }}{{ $cat->city ?: '-' }}</div>
                                                <div class="text-[11px] text-slate-400">Terakhir: {{ $cat->last_seen_at ? $cat->last_seen_at->diffForHumans() : '-' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 pt-0">
                                        <a href="{{ route('peta-anabul.show', $cat->id) }}"
                                            class="w-full button-secondary min-h-[40px] text-xs font-bold flex items-center justify-center">
                                            Lihat Detail Anabul
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4">
                            {{ $cats->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <!-- Leaflet & Clustering Script -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        function petaAnabulApp() {
            return {
                map: null,
                markerCluster: null,
                userCircle: null,
                userMarker: null,
                isLocating: false,
                radiusKm: '{{ request('radius') }}' || null,
                userCoords: {
                    lat: '{{ request('lat') }}' || null,
                    lng: '{{ request('lng') }}' || null,
                },

                init() {
                    @if($viewMode === 'map')
                        this.$nextTick(() => {
                            this.initMap();
                        });
                    @endif
                },

                initMap() {
                    const container = document.getElementById('peta-anabul-map');
                    if (!container) return;

                    // Indonesia center fallback
                    const centerLat = this.userCoords.lat ? parseFloat(this.userCoords.lat) : -2.5489;
                    const centerLng = this.userCoords.lng ? parseFloat(this.userCoords.lng) : 118.0149;
                    const zoom = this.userCoords.lat ? 12 : 5;

                    this.map = L.map('peta-anabul-map', {
                        zoomControl: true,
                        scrollWheelZoom: true
                    }).setView([centerLat, centerLng], zoom);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | KucingMU'
                    }).addTo(this.map);

                    this.markerCluster = L.markerClusterGroup({
                        showCoverageOnHover: false,
                        maxClusterRadius: 45,
                        spiderfyOnMaxZoom: true,
                    });
                    this.map.addLayer(this.markerCluster);

                    this.loadMarkers();

                    // If radius is active, draw circle
                    if (this.userCoords.lat && this.userCoords.lng && this.radiusKm) {
                        this.drawRadiusCircle(parseFloat(this.userCoords.lat), parseFloat(this.userCoords.lng), parseFloat(this.radiusKm));
                    }
                },

                loadMarkers() {
                    // Extract query parameters from URL to apply current filters to markers API
                    const urlParams = new URLSearchParams(window.location.search);
                    const markersUrl = '{{ route('peta-anabul.markers') }}?' + urlParams.toString();

                    fetch(markersUrl)
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success' && res.data) {
                                this.renderMarkers(res.data);
                            }
                        })
                        .catch(err => console.error('Gagal memuat marker:', err));
                },

                renderMarkers(cats) {
                    this.markerCluster.clearLayers();
                    const bounds = [];

                    cats.forEach(cat => {
                        if (!cat.lat || !cat.lng) return;

                        const isPet = cat.ownership_type === 'pet';
                        const isUrgent = ['sakit', 'terluka', 'butuh_pertolongan'].includes(cat.condition);
                        const markerClass = isPet ? 'cat-marker-pet' : 'cat-marker-stray';
                        const badgeEmoji = isPet ? '🏠' : '🐾';
                        const urgentClass = isUrgent ? 'cat-marker-urgent' : '';

                        const iconHtml = `
                            <div class="cat-marker-pin ${markerClass} ${urgentClass}">
                                <img src="${cat.thumbnail_url}" alt="${cat.name}" class="cat-marker-avatar" loading="lazy" />
                                <span class="cat-marker-badge">${badgeEmoji}</span>
                            </div>
                        `;

                        const customIcon = L.divIcon({
                            className: 'custom-cat-icon-container',
                            html: iconHtml,
                            iconSize: [38, 38],
                            iconAnchor: [19, 19],
                            popupAnchor: [0, -22],
                        });

                        const popupHtml = `
                            <div class="font-sans text-slate-800">
                                <div class="relative w-full h-32 bg-slate-100 overflow-hidden">
                                    <img src="${cat.thumbnail_url}" alt="${cat.name}" class="w-full h-full object-cover">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold ${isPet ? 'bg-teal-700 text-white' : 'bg-orange-600 text-white'}" style="color: #ffffff !important;">
                                        ${cat.ownership_label}
                                    </span>
                                </div>
                                <div class="p-3.5 space-y-2">
                                    <div class="flex items-start justify-between gap-1">
                                        <h4 class="font-outfit font-black text-slate-900 text-sm leading-tight">${cat.name}</h4>
                                        <span class="font-mono text-[10px] text-slate-400 font-semibold">${cat.unique_code}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1 text-[10px]">
                                        <span class="px-2 py-0.5 rounded-full font-bold border ${cat.condition_badge_class}">${cat.condition_label}</span>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold">${cat.coat_pattern}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 line-clamp-2">
                                        📍 ${cat.formatted_address || cat.city || 'Lokasi tercatat'}
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-100">
                                        <a href="${cat.detail_url}" class="popup-btn w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold shadow-xs transition" style="color: #ffffff !important; text-decoration: none !important; background-color: #0f766e !important;">
                                            <span style="color: #ffffff !important; font-weight: 700;">Lihat Detail Anabul</span>
                                            <span style="color: #ffffff !important; font-weight: 700;">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;

                        const marker = L.marker([cat.lat, cat.lng], { icon: customIcon }).bindPopup(popupHtml);
                        this.markerCluster.addLayer(marker);
                        bounds.push([cat.lat, cat.lng]);
                    });

                    // Auto-zoom if markers exist and no manual radius filter
                    if (bounds.length > 0 && !this.radiusKm) {
                        this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                    }
                },

                detectUserLocation(km) {
                    if (!navigator.geolocation) {
                        alert('Peramban Anda tidak mendukung deteksi lokasi (Geolocation).');
                        return;
                    }

                    this.isLocating = true;
                    this.radiusKm = km;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.isLocating = false;
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            this.userCoords.lat = lat;
                            this.userCoords.lng = lng;

                            // Update form inputs and submit
                            const form = document.getElementById('filter-form');
                            if (form) {
                                form.submit();
                            }
                        },
                        (error) => {
                            this.isLocating = false;
                            alert('Gagal mendeteksi lokasi: ' + error.message);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
                    );
                },

                drawRadiusCircle(lat, lng, km) {
                    if (this.userCircle) this.map.removeLayer(this.userCircle);
                    if (this.userMarker) this.map.removeLayer(this.userMarker);

                    this.userMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'user-location-pin',
                            html: '<div style="background:#2563eb; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 0 0 2px #2563eb;"></div>',
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        })
                    }).addTo(this.map).bindPopup('<b>Lokasi Anda</b>');

                    this.userCircle = L.circle([lat, lng], {
                        radius: km * 1000,
                        color: '#0f766e',
                        fillColor: '#0f766e',
                        fillOpacity: 0.1,
                        weight: 2,
                    }).addTo(this.map);

                    this.map.fitBounds(this.userCircle.getBounds(), { padding: [30, 30] });
                },

                clearRadius() {
                    this.radiusKm = null;
                    this.userCoords.lat = null;
                    this.userCoords.lng = null;
                    const form = document.getElementById('filter-form');
                    if (form) form.submit();
                }
            };
        }
    </script>
</x-app-layout>

