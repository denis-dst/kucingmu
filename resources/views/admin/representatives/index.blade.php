<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="eyebrow">Pengurus &amp; Relawan Persyarikatan</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                    Penjaringan Representatif Wilayah
                </h1>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                <a href="{{ route('admin.representatives.export', request()->query()) }}" 
                   class="button-secondary text-xs px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 font-semibold text-slate-700">
                    <span>📊</span> Unduh Data (CSV)
                </a>
                <a href="{{ route('representative.register') }}" 
                   target="_blank"
                   class="button-primary text-xs px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 font-bold bg-teal-700 hover:bg-teal-800 text-white">
                    <span>➕</span> Buka Formulir Publik ↗
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS & JS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        #representatives-map {
            height: 440px !important;
            min-height: 380px !important;
            width: 100% !important;
            display: block !important;
            position: relative !important;
            border-radius: 1rem;
            z-index: 10;
        }
        @media (max-width: 640px) {
            #representatives-map {
                height: 340px !important;
                min-height: 300px !important;
            }
        }
        .custom-rep-pin {
            background: transparent !important;
            border: none !important;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 2px;
        }
        .leaflet-popup-content {
            margin: 10px 12px;
            line-height: 1.4;
        }
        .leaflet-popup-content a.btn-popup-primary {
            color: #ffffff !important;
            background-color: #0f766e !important;
            text-decoration: none !important;
        }
        .leaflet-popup-content a.btn-popup-primary:hover {
            background-color: #115e59 !important;
            color: #ffffff !important;
        }
        .leaflet-popup-content a.btn-popup-wa {
            color: #065f46 !important;
            background-color: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            text-decoration: none !important;
        }
        .leaflet-popup-content a.btn-popup-wa:hover {
            background-color: #d1fae5 !important;
            color: #064e3b !important;
        }
    </style>

    <div class="py-6 sm:py-8" x-data="{
        isExpanded: false,
        activeMapFilter: 'all',
        toggleMapHeight() {
            this.isExpanded = !this.isExpanded;
            const el = document.getElementById('representatives-map');
            if (el) {
                if (this.isExpanded) {
                    el.style.setProperty('height', window.innerWidth < 640 ? '540px' : '660px', 'important');
                } else {
                    el.style.setProperty('height', window.innerWidth < 640 ? '340px' : '440px', 'important');
                }
            }
            setTimeout(() => {
                if (window.representativesMap) {
                    window.representativesMap.invalidateSize();
                }
            }, 150);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Success Flash Message -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-center justify-between gap-3 text-emerald-900 text-xs sm:text-sm shadow-xs">
                    <div class="flex items-center gap-2 font-medium">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Summary Statistics Cards (6 Balanced Responsive Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
                
                <!-- 1. Semua Pendaftar (Total) -->
                <a href="{{ route('admin.representatives.index', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}"
                   class="group p-4 rounded-2xl transition-all duration-200 border shadow-xs space-y-1 block relative {{ ($statusFilter === 'all' || !$statusFilter) ? 'bg-slate-900 text-white border-slate-900 ring-2 ring-slate-900' : 'bg-white text-slate-800 border-slate-200 hover:border-slate-300 hover:bg-slate-50/80' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider block {{ ($statusFilter === 'all' || !$statusFilter) ? 'text-slate-300' : 'text-slate-500' }}">
                            Total Pendaftar
                        </span>
                        @if($statusFilter === 'all' || !$statusFilter)
                            <span class="text-[9px] font-bold bg-white/20 text-white px-1.5 py-0.5 rounded">Aktif</span>
                        @endif
                    </div>
                    <div class="font-outfit text-2xl font-extrabold {{ ($statusFilter === 'all' || !$statusFilter) ? 'text-white' : 'text-slate-900' }}">
                        {{ number_format($stats['total']) }}
                    </div>
                    <span class="text-[10px] block {{ ($statusFilter === 'all' || !$statusFilter) ? 'text-slate-300' : 'text-slate-500' }}">
                        Semua pendaftaran
                    </span>
                </a>

                <!-- 2. Menunggu Review (Pending) -->
                <a href="{{ route('admin.representatives.index', array_merge(request()->except('page', 'status'), ['status' => 'pending'])) }}"
                   class="group p-4 rounded-2xl transition-all duration-200 border shadow-xs space-y-1 block relative {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white border-amber-500 ring-2 ring-amber-500' : 'bg-amber-50/80 text-amber-900 border-amber-200/90 hover:bg-amber-100/80 hover:border-amber-300' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider block {{ $statusFilter === 'pending' ? 'text-amber-100' : 'text-amber-800' }}">
                            Menunggu Review
                        </span>
                        @if($statusFilter === 'pending')
                            <span class="text-[9px] font-bold bg-white/20 text-white px-1.5 py-0.5 rounded">Aktif</span>
                        @endif
                    </div>
                    <div class="font-outfit text-2xl font-extrabold {{ $statusFilter === 'pending' ? 'text-white' : 'text-amber-950' }}">
                        {{ number_format($stats['pending']) }}
                    </div>
                    <span class="text-[10px] block {{ $statusFilter === 'pending' ? 'text-amber-100' : 'text-amber-700 font-semibold' }}">
                        Perlu diproses
                    </span>
                </a>

                <!-- 3. Sedang Direview (Reviewed) -->
                <a href="{{ route('admin.representatives.index', array_merge(request()->except('page', 'status'), ['status' => 'reviewed'])) }}"
                   class="group p-4 rounded-2xl transition-all duration-200 border shadow-xs space-y-1 block relative {{ $statusFilter === 'reviewed' ? 'bg-blue-600 text-white border-blue-600 ring-2 ring-blue-600' : 'bg-blue-50/80 text-blue-900 border-blue-200/90 hover:bg-blue-100/80 hover:border-blue-300' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider block {{ $statusFilter === 'reviewed' ? 'text-blue-100' : 'text-blue-800' }}">
                            Sedang Direview
                        </span>
                        @if($statusFilter === 'reviewed')
                            <span class="text-[9px] font-bold bg-white/20 text-white px-1.5 py-0.5 rounded">Aktif</span>
                        @endif
                    </div>
                    <div class="font-outfit text-2xl font-extrabold {{ $statusFilter === 'reviewed' ? 'text-white' : 'text-blue-950' }}">
                        {{ number_format($stats['reviewed']) }}
                    </div>
                    <span class="text-[10px] block {{ $statusFilter === 'reviewed' ? 'text-blue-100' : 'text-blue-700 font-semibold' }}">
                        Tahap validasi
                    </span>
                </a>

                <!-- 4. Diterima / Sah (Approved) -->
                <a href="{{ route('admin.representatives.index', array_merge(request()->except('page', 'status'), ['status' => 'approved'])) }}"
                   class="group p-4 rounded-2xl transition-all duration-200 border shadow-xs space-y-1 block relative {{ $statusFilter === 'approved' ? 'bg-teal-700 text-white border-teal-700 ring-2 ring-teal-700' : 'bg-teal-50/80 text-teal-900 border-teal-200/90 hover:bg-teal-100/80 hover:border-teal-300' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider block {{ $statusFilter === 'approved' ? 'text-teal-100' : 'text-teal-800' }}">
                            Diterima / Sah
                        </span>
                        @if($statusFilter === 'approved')
                            <span class="text-[9px] font-bold bg-white/20 text-white px-1.5 py-0.5 rounded">Aktif</span>
                        @endif
                    </div>
                    <div class="font-outfit text-2xl font-extrabold {{ $statusFilter === 'approved' ? 'text-white' : 'text-teal-950' }}">
                        {{ number_format($stats['approved']) }}
                    </div>
                    <span class="text-[10px] block {{ $statusFilter === 'approved' ? 'text-teal-100' : 'text-teal-700 font-semibold' }}">
                        Representatif resmi
                    </span>
                </a>

                <!-- 5. Ditolak (Rejected) -->
                <a href="{{ route('admin.representatives.index', array_merge(request()->except('page', 'status'), ['status' => 'rejected'])) }}"
                   class="group p-4 rounded-2xl transition-all duration-200 border shadow-xs space-y-1 block relative {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white border-rose-600 ring-2 ring-rose-600' : 'bg-rose-50/80 text-rose-900 border-rose-200/90 hover:bg-rose-100/80 hover:border-rose-300' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider block {{ $statusFilter === 'rejected' ? 'text-rose-100' : 'text-rose-800' }}">
                            Ditolak
                        </span>
                        @if($statusFilter === 'rejected')
                            <span class="text-[9px] font-bold bg-white/20 text-white px-1.5 py-0.5 rounded">Aktif</span>
                        @endif
                    </div>
                    <div class="font-outfit text-2xl font-extrabold {{ $statusFilter === 'rejected' ? 'text-white' : 'text-rose-950' }}">
                        {{ number_format($stats['rejected']) }}
                    </div>
                    <span class="text-[10px] block {{ $statusFilter === 'rejected' ? 'text-rose-100' : 'text-rose-700 font-semibold' }}">
                        Belum sesuai
                    </span>
                </a>

                <!-- 6. Sebaran Provinsi & Terpetakan -->
                <a href="#map-section"
                   class="group p-4 rounded-2xl transition-all duration-200 border shadow-xs space-y-1 block bg-slate-50 text-slate-800 border-slate-200 hover:border-slate-300 hover:bg-slate-100/80">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                            Provinsi Terwakili
                        </span>
                        <span class="text-xs">🗺️</span>
                    </div>
                    <div class="font-outfit text-2xl font-extrabold text-slate-900">
                        {{ $stats['total_provinces'] }} <span class="text-xs font-normal text-slate-500">/ 38</span>
                    </div>
                    <span class="text-[10px] text-slate-600 block">
                        {{ $totalOverallMapped }} titik GPS terpetakan
                    </span>
                </a>

            </div>

            <!-- Visualisasi Peta Sebaran Representatif Wilayah -->
            <div id="map-section" class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200 shadow-sm space-y-4">
                
                <!-- Map Header & Controls -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center text-sm font-bold">
                                📍
                            </span>
                            <h2 class="font-outfit font-bold text-base sm:text-lg text-slate-900">
                                Peta Sebaran Representatif Wilayah
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Titik koordinat lokasi geografis pendaftar berdasarkan inputan peta saat pengisian formulir.
                            <strong class="text-slate-700 font-semibold">({{ $totalMapped }} titik terpetakan</strong> dari {{ $representatives->total() }} data pada filter saat ini)
                        </p>
                    </div>

                    <!-- Map Action Toolbar -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Quick Status Filter Toggle for Map -->
                        <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs">
                            <button type="button" 
                                    @click="activeMapFilter = 'all'; filterMapMarkers('all')" 
                                    :class="activeMapFilter === 'all' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px]">
                                Semua (<span id="count-all">{{ $totalMapped }}</span>)
                            </button>
                            <button type="button" 
                                    @click="activeMapFilter = 'approved'; filterMapMarkers('approved')" 
                                    :class="activeMapFilter === 'approved' ? 'bg-teal-700 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-teal-800'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span> Diterima
                            </button>
                            <button type="button" 
                                    @click="activeMapFilter = 'pending'; filterMapMarkers('pending')" 
                                    :class="activeMapFilter === 'pending' ? 'bg-amber-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-amber-800'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                            </button>
                            <button type="button" 
                                    @click="activeMapFilter = 'reviewed'; filterMapMarkers('reviewed')" 
                                    :class="activeMapFilter === 'reviewed' ? 'bg-blue-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-blue-800'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Review
                            </button>
                            <button type="button" 
                                    @click="activeMapFilter = 'rejected'; filterMapMarkers('rejected')" 
                                    :class="activeMapFilter === 'rejected' ? 'bg-rose-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-rose-800'"
                                    class="px-2.5 py-1 rounded-lg transition text-[11px] flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                            </button>
                        </div>

                        <!-- Zoom Controls -->
                        <button type="button" 
                                onclick="fitMapToAllMarkers()" 
                                title="Fokuskan ke seluruh titik lokasi"
                                class="button-secondary text-xs px-2.5 py-1.5 rounded-xl inline-flex items-center gap-1 font-semibold text-slate-700">
                            <span>🎯</span> Pas kan Titik
                        </button>
                        <button type="button" 
                                onclick="resetMapToIndonesia()" 
                                title="Kembalikan tampilan peta ke wilayah Nusantara Indonesia"
                                class="button-secondary text-xs px-2.5 py-1.5 rounded-xl inline-flex items-center gap-1 font-semibold text-slate-700">
                            <span>🇮🇩</span> Indonesia
                        </button>
                        <button type="button" 
                                @click="toggleMapHeight()" 
                                :title="isExpanded ? 'Kecilkan Tampilan Peta' : 'Perluas Tampilan Peta'"
                                class="button-secondary text-xs px-2.5 py-1.5 rounded-xl inline-flex items-center gap-1 font-semibold text-slate-700">
                            <span x-text="isExpanded ? '⏬' : '⏫'"></span>
                            <span x-text="isExpanded ? 'Normal' : 'Perluas'"></span>
                        </button>
                    </div>
                </div>

                <!-- Map Canvas Container -->
                <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-inner bg-slate-100" style="min-height: 380px;">
                    <div id="representatives-map" 
                         style="height: 440px; min-height: 380px; width: 100%; display: block;"
                         class="w-full transition-all duration-300 z-10"></div>

                    @if($totalMapped === 0)
                        <div class="absolute inset-0 z-20 flex items-center justify-center bg-slate-50/90 backdrop-blur-xs p-6 text-center">
                            <div class="max-w-md space-y-2">
                                <span class="text-3xl block">📍</span>
                                <h3 class="font-outfit font-bold text-sm text-slate-800">Belum Ada Titik Lokasi Terpetakan</h3>
                                <p class="text-xs text-slate-500">
                                    Tidak ditemukan koordinat GPS pada data pendaftar dengan filter yang aktif saat ini.
                                </p>
                                @if(request()->hasAny(['search', 'status', 'province']))
                                    <div class="pt-2">
                                        <a href="{{ route('admin.representatives.index') }}" class="button-secondary text-xs px-3 py-1.5 rounded-xl inline-flex">
                                            Reset Filter Pencarian
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Map Footer Legend & Instructions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 text-xs text-slate-500">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="font-bold text-slate-700 text-[11px]">Keterangan Pin:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-teal-600 border border-white shadow-xs inline-block"></span>
                            <span>Diterima / Sah</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-amber-500 border border-white shadow-xs inline-block"></span>
                            <span>Menunggu Review</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-blue-600 border border-white shadow-xs inline-block"></span>
                            <span>Sedang Direview</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-600 border border-white shadow-xs inline-block"></span>
                            <span>Ditolak</span>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-400">
                        Klik pin pada peta untuk melihat detail ringkas representatif
                    </div>
                </div>

            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-4">
                <form method="GET" action="{{ route('admin.representatives.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    
                    <!-- Search Input -->
                    <div class="sm:col-span-5">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                🔍
                            </span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari nama, NBM, nomor registrasi, WA, kota..."
                                   class="form-input pl-9 text-xs py-2">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:col-span-3">
                        <select name="status" class="form-input text-xs py-2" onchange="this.form.submit()">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Sedang Direview</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <!-- Province Filter -->
                    <div class="sm:col-span-3">
                        <select name="province" class="form-input text-xs py-2" onchange="this.form.submit()">
                            <option value="">Semua Provinsi</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="sm:col-span-1 flex items-center gap-1.5">
                        <button type="submit" class="button-primary text-xs p-2 rounded-xl w-full justify-center" title="Terapkan Filter">
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'status', 'province']))
                            <a href="{{ route('admin.representatives.index') }}" class="button-secondary text-xs p-2 rounded-xl" title="Reset Filter">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Representatives Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                @if($representatives->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <span class="text-4xl">📭</span>
                        <h3 class="font-outfit font-bold text-base text-slate-800">Tidak Ada Data Pendaftaran</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Belum ada pendaftaran calon representatif yang sesuai dengan kriteria filter saat ini.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3.5 px-4">No. Registrasi &amp; Tanggal</th>
                                    <th class="py-3.5 px-4">Calon Representatif</th>
                                    <th class="py-3.5 px-4">Wilayah Penugasan</th>
                                    <th class="py-3.5 px-4">Pimpinan / Ortom</th>
                                    <th class="py-3.5 px-4">Dokumen</th>
                                    <th class="py-3.5 px-4 text-center">Status</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($representatives as $rep)
                                    @php $badge = $rep->status_badge; @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <!-- No. Registrasi & Tanggal -->
                                        <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                            <a href="{{ route('admin.representatives.show', $rep) }}" class="font-mono font-bold text-teal-800 hover:underline">
                                                {{ $rep->registration_number }}
                                            </a>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $rep->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>

                                        <!-- Calon Representatif -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900 text-sm">{{ $rep->name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                                                <span>NBM: <strong class="text-slate-700 font-mono">{{ $rep->nbm ?? '-' }}</strong></span>
                                                <span>&bull;</span>
                                                <span>Usia: {{ $rep->birth_date ? $rep->birth_date->age . ' th' : '-' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <a href="{{ $rep->whatsapp_link }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-700 hover:underline font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                    <span>💬</span> {{ $rep->whatsapp_number }}
                                                </a>
                                                @if($rep->instagram_username)
                                                    <a href="https://instagram.com/{{ $rep->instagram_username }}" target="_blank" class="text-[10px] text-slate-500 hover:text-slate-900 font-medium">
                                                        @<span>{{ $rep->instagram_username }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Wilayah Penugasan -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-800">{{ $rep->province_name }}</div>
                                            <div class="text-[11px] text-slate-600">{{ $rep->city_name }}</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                Kec. {{ $rep->district_name }}, {{ $rep->village_name }}
                                            </div>
                                            @if($rep->latitude && $rep->longitude)
                                                <div class="mt-1 flex items-center gap-2">
                                                    <button type="button" 
                                                            onclick="focusMapOnPoint({{ $rep->latitude }}, {{ $rep->longitude }}, {{ $rep->id }})" 
                                                            class="text-[10px] text-teal-700 hover:text-teal-900 hover:underline font-bold inline-flex items-center gap-0.5 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200">
                                                        <span>📍</span> Fokus di Peta
                                                    </button>
                                                    <a href="https://www.google.com/maps?q={{ $rep->latitude }},{{ $rep->longitude }}" target="_blank" class="text-[10px] text-slate-400 hover:text-slate-600">
                                                        GPS ↗
                                                    </a>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Pimpinan / Ortom -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-medium text-slate-800 text-[11px] max-w-xs leading-relaxed">
                                                {{ $rep->muhammadiyah_active_leadership }}
                                            </div>
                                        </td>

                                        <!-- Dokumen -->
                                        <td class="py-3.5 px-4 align-top space-y-1">
                                            @if($rep->sk_pimpinan_document_path)
                                                <a href="{{ asset('storage/' . $rep->sk_pimpinan_document_path) }}" target="_blank" class="block text-[10px] font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 px-2 py-1 rounded border border-teal-200 text-center">
                                                    📄 SK Pimpinan (PDF)
                                                </a>
                                            @endif
                                            @if($rep->ktam_document_path)
                                                <a href="{{ asset('storage/' . $rep->ktam_document_path) }}" target="_blank" class="block text-[10px] font-bold text-sky-800 bg-sky-50 hover:bg-sky-100 px-2 py-1 rounded border border-sky-200 text-center">
                                                    🖼️ KTAM / MASA
                                                </a>
                                            @endif
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-3.5 px-4 align-top text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                                {{ $badge['label'] }}
                                            </span>
                                            @if($rep->reviewer)
                                                <div class="text-[9px] text-slate-400 mt-1">
                                                    oleh {{ $rep->reviewer->name }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 align-top text-right space-y-1 whitespace-nowrap">
                                            <a href="{{ route('admin.representatives.show', $rep) }}" 
                                               class="inline-flex items-center justify-center w-full gap-1 px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 active:bg-teal-900 text-white font-bold text-xs shadow-xs hover:shadow transition text-center"
                                               style="color: #ffffff !important; text-decoration: none !important;">
                                                Detail &amp; Proses
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($representatives->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                            {{ $representatives->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    <!-- Leaflet Map Script & Custom Interactive Marker System -->
    <script>
        const representativeData = @json($mapRepresentatives ?? []);
        let mapInstance = null;
        let markerClusterGroup = null;
        const allMarkers = [];

        function getMarkerColor(status) {
            switch(status) {
                case 'approved': return { bg: '#0f766e', border: '#115e59', text: 'Sah / Diterima' };
                case 'pending': return { bg: '#d97706', border: '#b45309', text: 'Menunggu Review' };
                case 'reviewed': return { bg: '#2563eb', border: '#1d4ed8', text: 'Sedang Direview' };
                case 'rejected': return { bg: '#e11d48', border: '#be123c', text: 'Ditolak' };
                default: return { bg: '#475569', border: '#334155', text: status };
            }
        }

        function createCustomPin(status) {
            const color = getMarkerColor(status);
            return L.divIcon({
                className: 'custom-rep-pin',
                html: `
                    <div style="position: relative; width: 30px; height: 38px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <svg width="30" height="38" viewBox="0 0 30 38" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));">
                            <path d="M15 0C6.71573 0 0 6.71573 0 15C0 24.5 15 38 15 38C15 38 30 24.5 30 15C30 6.71573 23.2843 0 15 0Z" fill="${color.bg}"/>
                            <circle cx="15" cy="15" r="7" fill="white"/>
                            <circle cx="15" cy="15" r="4.5" fill="${color.border}"/>
                        </svg>
                    </div>
                `,
                iconSize: [30, 38],
                iconAnchor: [15, 38],
                popupAnchor: [0, -38]
            });
        }

        function initRepresentativesMap() {
            const mapContainer = document.getElementById('representatives-map');
            if (!mapContainer || mapInstance) return;

            // Default center of Indonesia
            const defaultCenter = [-1.5, 117.5];
            const defaultZoom = 5;

            mapInstance = L.map('representatives-map', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView(defaultCenter, defaultZoom);

            window.representativesMap = mapInstance;

            // OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapInstance);

            // Layer group for markers
            markerClusterGroup = L.layerGroup().addTo(mapInstance);

            // Populate markers
            representativeData.forEach(rep => {
                if (rep.latitude && rep.longitude) {
                    const icon = createCustomPin(rep.status);
                    const marker = L.marker([rep.latitude, rep.longitude], { icon: icon });

                    const statusBadgeClass = rep.badge_bg || 'bg-slate-100 text-slate-800';

                    const popupContent = `
                        <div class="font-sans text-xs space-y-2 p-1 max-w-[260px]">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5 gap-2">
                                <span class="font-mono text-[10px] font-bold text-slate-500">${rep.registration_number}</span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full border ${statusBadgeClass}">
                                    ${rep.status_label}
                                </span>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">${rep.name}</h4>
                                <div class="text-[11px] text-slate-500 mt-0.5">NBM: <strong class="font-mono text-slate-700">${rep.nbm}</strong></div>
                            </div>

                            <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 space-y-1 text-[11px]">
                                <div class="text-slate-800 font-semibold">${rep.city_name}, ${rep.province_name}</div>
                                <div class="text-slate-500 text-[10px]">Kec. ${rep.district_name}, ${rep.village_name}</div>
                                <div class="text-teal-900 font-medium text-[10px] pt-1 border-t border-slate-200/60">
                                    🏛️ ${rep.muhammadiyah_active_leadership}
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 pt-1">
                                <a href="${rep.show_url}" 
                                   class="btn-popup-primary flex-1 text-white font-bold py-1.5 px-2 rounded-lg text-center text-[10px] transition"
                                   style="color: #ffffff !important; background-color: #0f766e !important; text-decoration: none !important;">
                                    Detail &amp; Proses ↗
                                </a>
                                <a href="${rep.whatsapp_link}" 
                                   target="_blank"
                                   class="btn-popup-wa font-bold py-1.5 px-2 rounded-lg text-[10px] transition inline-flex items-center justify-center"
                                   style="color: #065f46 !important; background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; text-decoration: none !important;">
                                    💬 WA
                                </a>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent, { maxWidth: 280 });
                    marker.repData = rep;
                    allMarkers.push(marker);
                    markerClusterGroup.addLayer(marker);
                }
            });

            // If markers exist, fit map bounds nicely
            setTimeout(() => {
                if (mapInstance) {
                    mapInstance.invalidateSize();
                    if (allMarkers.length > 0) {
                        const group = L.featureGroup(allMarkers);
                        mapInstance.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
                    }
                }
            }, 250);
        }

        function filterMapMarkers(status) {
            if (!markerClusterGroup) return;
            markerClusterGroup.clearLayers();

            const filtered = allMarkers.filter(m => {
                if (status === 'all') return true;
                return m.repData.status === status;
            });

            filtered.forEach(m => markerClusterGroup.addLayer(m));

            if (filtered.length > 0) {
                const group = L.featureGroup(filtered);
                mapInstance.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
            }
        }

        function fitMapToAllMarkers() {
            if (!mapInstance || allMarkers.length === 0) return;
            const group = L.featureGroup(allMarkers);
            mapInstance.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 13 });
        }

        function resetMapToIndonesia() {
            if (!mapInstance) return;
            mapInstance.setView([-1.5, 117.5], 5);
        }

        function focusMapOnPoint(lat, lng, repId) {
            const mapSection = document.getElementById('map-section');
            if (mapSection) {
                mapSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            if (!mapInstance) return;

            setTimeout(() => {
                mapInstance.setView([lat, lng], 15, { animate: true });
                const targetMarker = allMarkers.find(m => m.repData.id === repId);
                if (targetMarker) {
                    targetMarker.openPopup();
                }
            }, 350);
        }

        function safeInitMap() {
            const mapContainer = document.getElementById('representatives-map');
            if (!mapContainer) return;
            if (typeof L === 'undefined') {
                setTimeout(safeInitMap, 100);
                return;
            }
            initRepresentativesMap();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', safeInitMap);
        } else {
            safeInitMap();
        }
    </script>
</x-app-layout>

