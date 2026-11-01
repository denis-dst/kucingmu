<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('temu-anabul.index') }}"
                    class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                    title="Kembali ke Daftar Laporan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-900 tracking-tight">Peta Temu Anabul</h1>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Visualisasi spasial sebaran kucing hilang dan ditemukan di seluruh Indonesia.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('temu-anabul.index') }}"
                    class="button-secondary min-h-[40px] px-3.5 text-xs font-bold flex items-center gap-1.5">
                    <span>📋</span>
                    <span>Tampilan Daftar</span>
                </a>
                <a href="{{ route('temu-anabul.create', ['type' => 'lost']) }}"
                    class="button-primary min-h-[40px] px-4 text-xs font-bold shadow-xs">
                    + Lapor Kucing Hilang
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet & Clustering CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>

    <style>
        .peta-temu-container {
            height: calc(100vh - 270px);
            min-height: 520px;
            width: 100%;
        }
        @media (max-width: 640px) {
            .peta-temu-container {
                height: 480px;
                min-height: 420px;
            }
        }
        .temu-pin {
            position: relative;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .temu-pin:hover {
            transform: scale(1.15) translateY(-4px);
            z-index: 1000 !important;
        }
        .temu-pin-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
            background-color: #f1f5f9;
        }
        .temu-pin-lost .temu-pin-img {
            border-color: #e11d48; /* Red for lost */
            box-shadow: 0 0 0 2px #fff, 0 4px 12px rgba(225, 29, 72, 0.5);
            animation: pulse-lost 2s infinite;
        }
        .temu-pin-found .temu-pin-img {
            border-color: #7c3aed; /* Violet for found */
            box-shadow: 0 0 0 2px #fff, 0 4px 12px rgba(124, 58, 237, 0.5);
        }
        .temu-pin-reunited .temu-pin-img {
            border-color: #059669; /* Emerald for reunited */
            box-shadow: 0 0 0 2px #fff, 0 4px 12px rgba(5, 150, 105, 0.5);
        }
        .temu-pin-badge {
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
            color: #fff;
        }
        .temu-pin-lost .temu-pin-badge { background-color: #e11d48; }
        .temu-pin-found .temu-pin-badge { background-color: #7c3aed; }
        .temu-pin-reunited .temu-pin-badge { background-color: #059669; }

        @keyframes pulse-lost {
            0% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(225, 29, 72, 0); }
            100% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
        }
    </style>

    <div class="py-4 sm:py-6" x-data="petaTemuApp()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <!-- Filter & Legend Bar -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <!-- Category Buttons -->
                    <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                        <button type="button" @click="filterType = ''; loadMarkers()"
                            :class="filterType === '' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-3.5 py-2 rounded-xl transition min-h-[38px]">
                            Semua Laporan
                        </button>
                        <button type="button" @click="filterType = 'lost'; loadMarkers()"
                            :class="filterType === 'lost' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                            class="px-3.5 py-2 rounded-xl transition min-h-[38px] flex items-center gap-1.5">
                            <span>🚨</span>
                            <span>Kucing Hilang ({{ $stats['lost'] }})</span>
                        </button>
                        <button type="button" @click="filterType = 'found'; loadMarkers()"
                            :class="filterType === 'found' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'"
                            class="px-3.5 py-2 rounded-xl transition min-h-[38px] flex items-center gap-1.5">
                            <span>🐱</span>
                            <span>Kucing Ditemukan ({{ $stats['found'] }})</span>
                        </button>
                        <button type="button" @click="filterType = 'reunited'; loadMarkers()"
                            :class="filterType === 'reunited' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                            class="px-3.5 py-2 rounded-xl transition min-h-[38px] flex items-center gap-1.5">
                            <span>✨</span>
                            <span>Berhasil Reuni ({{ $stats['reunited'] }})</span>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="text" x-model="searchQuery" @input.debounce.400ms="loadMarkers()"
                            placeholder="Cari anabul atau kota..."
                            class="py-1.5 px-3 rounded-xl border border-slate-300 text-xs w-full sm:w-60 focus:border-teal-600 outline-none">
                    </div>
                </div>

                <!-- Legend Indicator -->
                <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-slate-100 text-xs text-slate-600">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Legenda:</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-600 inline-block"></span>
                        <span class="font-semibold text-rose-700">Kucing Hilang (🚨)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-violet-600 inline-block"></span>
                        <span class="font-semibold text-violet-700">Kucing Ditemukan (🐱)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                        <span class="font-semibold text-emerald-700">Berhasil Reuni (✨)</span>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="relative bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div id="peta-temu-map" class="peta-temu-container"></div>
            </div>

        </div>
    </div>

    <!-- Leaflet & Clustering Script -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        function petaTemuApp() {
            return {
                map: null,
                markerCluster: null,
                filterType: '',
                searchQuery: '',
                radiusCircles: [],

                init() {
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                initMap() {
                    const container = document.getElementById('peta-temu-map');
                    if (!container) return;

                    this.map = L.map('peta-temu-map', {
                        zoomControl: true,
                        scrollWheelZoom: true
                    }).setView([-2.5489, 118.0149], 5);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap | KucingMU'
                    }).addTo(this.map);

                    this.markerCluster = L.markerClusterGroup({
                        showCoverageOnHover: false,
                        maxClusterRadius: 40,
                    });
                    this.map.addLayer(this.markerCluster);

                    this.loadMarkers();
                },

                loadMarkers() {
                    let url = '{{ route('temu-anabul.markers') }}?';
                    const params = new URLSearchParams();

                    if (this.filterType === 'lost' || this.filterType === 'found') {
                        params.append('type', this.filterType);
                    } else if (this.filterType === 'reunited') {
                        params.append('status', 'reunited');
                    }

                    if (this.searchQuery) {
                        params.append('q', this.searchQuery);
                    }

                    fetch(url + params.toString())
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success' && res.data) {
                                this.renderMarkers(res.data);
                            }
                        })
                        .catch(err => console.error('Gagal memuat marker:', err));
                },

                renderMarkers(reports) {
                    this.markerCluster.clearLayers();
                    this.clearCircles();
                    const bounds = [];

                    reports.forEach(r => {
                        if (!r.lat || !r.lng) return;

                        const isReunited = r.status === 'reunited';
                        const isLost = r.type === 'lost' && !isReunited;
                        const markerClass = isReunited ? 'temu-pin-reunited' : (isLost ? 'temu-pin-lost' : 'temu-pin-found');
                        const badgeEmoji = isReunited ? '✨' : (isLost ? '🚨' : '🐱');

                        const iconHtml = `
                            <div class="temu-pin ${markerClass}">
                                <img src="${r.thumbnail_url}" alt="${r.cat_name}" class="temu-pin-img" loading="lazy" />
                                <span class="temu-pin-badge">${badgeEmoji}</span>
                            </div>
                        `;

                        const customIcon = L.divIcon({
                            className: 'custom-temu-icon',
                            html: iconHtml,
                            iconSize: [38, 38],
                            iconAnchor: [19, 19],
                            popupAnchor: [0, -22],
                        });

                        const popupHtml = `
                            <div class="font-sans text-slate-800">
                                <div class="relative w-full h-28 bg-slate-100 overflow-hidden">
                                    <img src="${r.thumbnail_url}" alt="${r.cat_name}" class="w-full h-full object-cover">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold ${isReunited ? 'bg-emerald-600 text-white' : (isLost ? 'bg-rose-600 text-white' : 'bg-indigo-600 text-white')}">
                                        ${r.type_label}
                                    </span>
                                </div>
                                <div class="p-3 space-y-2">
                                    <div>
                                        <h4 class="font-outfit font-black text-slate-900 text-sm leading-tight">${r.cat_name}</h4>
                                        <div class="text-[11px] text-slate-500 font-medium">${r.title}</div>
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        📍 ${r.formatted_address || r.city || 'Lokasi kejadian'}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Waktu: ${r.incident_date_human}
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-100 flex items-center gap-1.5">
                                        <a href="${r.detail_url}" class="flex-1 py-1.5 px-3 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold text-center transition">
                                            Lihat Laporan &rarr;
                                        </a>
                                        ${r.whatsapp_url ? `
                                            <a href="${r.whatsapp_url}" target="_blank" class="p-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition" title="WhatsApp">
                                                💬
                                            </a>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;

                        const marker = L.marker([r.lat, r.lng], { icon: customIcon }).bindPopup(popupHtml);
                        this.markerCluster.addLayer(marker);
                        bounds.push([r.lat, r.lng]);

                        // For active lost cat, draw search radius circle on click
                        if (isLost && r.search_radius_km) {
                            marker.on('popupopen', () => {
                                this.clearCircles();
                                const circle = L.circle([r.lat, r.lng], {
                                    radius: r.search_radius_km * 1000,
                                    color: '#e11d48',
                                    fillColor: '#e11d48',
                                    fillOpacity: 0.1,
                                    weight: 1.5,
                                    dashArray: '4, 4'
                                }).addTo(this.map);
                                this.radiusCircles.push(circle);
                            });
                        }
                    });

                    if (bounds.length > 0) {
                        this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
                    }
                },

                clearCircles() {
                    this.radiusCircles.forEach(c => this.map.removeLayer(c));
                    this.radiusCircles = [];
                }
            };
        }
    </script>
</x-app-layout>

