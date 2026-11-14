<x-app-layout>
    <!-- Executive Dashboard Dependencies: Leaflet & Chart.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-8" x-data="{ searchKtam: '', showModal: false, activeCat: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Administrator Utama</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Selamat Datang, Admin KucingMu!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Verifikasi pendaftaran dan pemeriksaan kesehatan kucing, tinjau sampel biometrik dan galeri foto, lalu terbitkan Kartu Tanda Anggota KucingMu (KTAKuMu).
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('admin.users.index') }}" class="button-primary text-xs font-bold px-4 py-2.5 shadow-sm bg-teal-800 hover:bg-teal-900">
                            <span>👥</span> Kelola & Rekrut Pengguna
                        </a>
                        <a href="{{ route('admin.mail.inbox') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📬</span> Kotak Masuk
                        </a>
                        <a href="{{ route('admin.mail.outbox') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📤</span> Kotak Keluar
                        </a>
                        <a href="{{ route('export-data') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📊</span> Ekspor Data (CSV)
                        </a>
                        <a href="{{ route('superadmin.wilayah.index') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>🏛️</span> Master Wilayah
                        </a>
                        <a href="{{ route('superadmin.albums.index') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📸</span> Album Kegiatan
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    🏢
                </div>
            </div>

            <!-- User Stats Widgets (Sesuai /admin/users) -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Statistik Pengguna & Peran Komunitas</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-teal-700 hover:text-teal-900 hover:underline">
                        Kelola Semua Pengguna &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <a href="{{ route('admin.users.index', ['role' => 'all']) }}" 
                       class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <span>Semua Pengguna</span>
                            <span>👥</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['users_total'] }}</span>
                            <span class="text-xs font-semibold text-slate-500">Akun</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['role' => 'member']) }}" 
                       class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-teal-700 uppercase tracking-wider">
                            <span>Member Kucing</span>
                            <span>🐱</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-teal-900">{{ $stats['users_member'] }}</span>
                            <span class="text-xs font-semibold text-teal-700">Orang</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['role' => 'verifikator']) }}" 
                       class="content-card p-4 transition hover:border-sky-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-sky-700 uppercase tracking-wider">
                            <span>Verifikator</span>
                            <span>✅</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-sky-900">{{ $stats['users_verifikator'] ?? 0 }}</span>
                            <span class="text-xs font-semibold text-sky-700">Staf</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['role' => 'volunteer']) }}" 
                       class="content-card p-4 transition hover:border-indigo-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            <span>Relawan (Volunteer)</span>
                            <span>📋</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-indigo-900">{{ $stats['users_volunteer'] }}</span>
                            <span class="text-xs font-semibold text-indigo-700">Orang</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['role' => 'dokter']) }}" 
                       class="content-card p-4 transition hover:border-emerald-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-emerald-700 uppercase tracking-wider">
                            <span>Dokter Hewan (Vet)</span>
                            <span>🩺</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-emerald-900">{{ $stats['users_dokter'] }}</span>
                            <span class="text-xs font-semibold text-emerald-700">Dokter</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" 
                       class="content-card p-4 transition hover:border-amber-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-amber-700 uppercase tracking-wider">
                            <span>Pengelola (Admin)</span>
                            <span>🛡️</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-amber-900">{{ $stats['users_admin'] }}</span>
                            <span class="text-xs font-semibold text-amber-700">Admin</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Stats Widgets: Kucing & Status Penerbitan KTAKuMu -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Statistik Data Kucing & Status Penerbitan KTAKuMu</h2>
                    <span class="text-[11px] text-slate-400">💡 Klik kartu statistik untuk memfilter data di tabel</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    <!-- Total Kucing -->
                    <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'all'])) }}#cat-registry-table" 
                       class="content-card bg-white border border-slate-200 p-4 transition hover:border-teal-400 hover:shadow-md block {{ ($ktamStatusFilter ?? 'all') === 'all' && !request('status') ? 'ring-2 ring-teal-500/30' : '' }}"
                       title="Lihat semua data kucing">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kucing</span>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                <span class="text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200">🟢 {{ $stats['cats_alive_count'] }}</span>
                                <span class="text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">⚪ {{ $stats['cats_deceased_count'] }}</span>
                            </div>
                        </div>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['cats_count'] }}</span>
                            <span class="text-xs font-semibold text-teal-700">Ekor</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Semua data terdaftar &rarr;</p>
                    </a>

                    <!-- Antrian Verifikasi (Belum Terbit KTAM) -->
                    <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'pending'])) }}#cat-registry-table" 
                       class="content-card bg-amber-50/70 border border-amber-300 p-4 transition hover:border-amber-500 hover:shadow-md block {{ ($ktamStatusFilter ?? '') === 'pending' ? 'ring-2 ring-amber-500 bg-amber-100/70' : '' }}"
                       title="Filter Kucing Menunggu Verifikasi KTAKuMu">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Antrian Verifikasi</span>
                            <span class="text-xs font-bold text-amber-800">⏳ Belum Verif</span>
                        </div>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-amber-900">{{ $stats['pending_verification_count'] }}</span>
                            <span class="text-xs font-semibold text-amber-700">Kucing</span>
                        </div>
                        <p class="text-[10px] text-amber-800 font-medium mt-1">Belum diterbitkan KTAM &rarr;</p>
                    </a>

                    <!-- KTAKuMu Terbit -->
                    <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'issued'])) }}#cat-registry-table" 
                       class="content-card bg-teal-50/40 border border-teal-200 p-4 transition hover:border-teal-500 hover:shadow-md block {{ ($ktamStatusFilter ?? '') === 'issued' ? 'ring-2 ring-teal-600 bg-teal-50' : '' }}"
                       title="Filter Kucing dengan KTAKuMu Resmi Terbit">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-800 uppercase tracking-wider">KTAKuMu Terbit</span>
                            <span class="text-xs font-bold text-teal-700">✓ Resmi</span>
                        </div>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-teal-800">{{ $stats['ktam_count'] }}</span>
                            <span class="text-xs font-semibold text-teal-700">Kartu</span>
                        </div>
                        <p class="text-[10px] text-teal-700 font-medium mt-1">Sudah terverifikasi & terbit &rarr;</p>
                    </a>

                </div>
            </div>

            <!-- Executive Visual Analytics Dashboard (Line, Bar, GIS Map & Pie/Donut) -->
            <div class="space-y-6" x-data="executiveDashboardAnalytics()">
                <!-- Analytics Header with Filter Switcher -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-100 text-teal-800">
                                📊 Executive Analytics
                            </span>
                            <span class="text-xs text-slate-400 font-medium">Monitoring Real-Time Nasional</span>
                        </div>
                        <h2 class="font-outfit text-xl font-bold text-slate-900 mt-1">
                            Tren Pendaftaran & Sebaran Wilayah Anabul
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Analisis strategis lonjakan registrasi, konsentrasi populasi daerah, varian ras, dan status penerbitan KTAKuMu.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-bold">
                            <button type="button" 
                                    @click="switchTrendMode('daily')" 
                                    :class="trendMode === 'daily' ? 'bg-white text-teal-800 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-1.5 rounded-lg transition">
                                📅 30 Hari Terakhir
                            </button>
                            <button type="button" 
                                    @click="switchTrendMode('monthly')" 
                                    :class="trendMode === 'monthly' ? 'bg-white text-teal-800 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                    class="px-3 py-1.5 rounded-lg transition">
                                🗓️ 12 Bulan Terakhir
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 1: Line Chart (Trend Pendaftaran) & Bar Chart (Top Wilayah) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- 1. Line Chart: Tren Kapan Daftar -->
                    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-teal-700">Line Chart</span>
                                    <h3 class="font-outfit text-base font-bold text-slate-900">Tren Pendaftaran Anabul & Anggota</h3>
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold">
                                    <span class="flex items-center gap-1.5 text-teal-700">
                                        <span class="w-3 h-1 bg-teal-600 rounded-full inline-block"></span> Kucing
                                    </span>
                                    <span class="flex items-center gap-1.5 text-sky-600">
                                        <span class="w-3 h-1 bg-sky-500 rounded-full inline-block"></span> Member
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 relative h-64 sm:h-72 w-full">
                                <canvas id="chartTrendRegistration"></canvas>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>📈 Menunjukkan dinamika adopsi platform dan kapan waktu puncak registrasi publik terjadi.</span>
                        </div>
                    </div>

                    <!-- 2. Bar Chart: Daerah Mana Terbanyak -->
                    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700">Bar Chart</span>
                                    <h3 class="font-outfit text-base font-bold text-slate-900">Top Daerah Sebaran Terbanyak</h3>
                                </div>
                                <span class="text-xs font-semibold px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md">
                                    PWM / Provinsi
                                </span>
                            </div>

                            <div class="mt-4 relative h-64 sm:h-72 w-full">
                                <canvas id="chartBarWilayah"></canvas>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>📍 Pemetaan konsentrasi tertinggi anabul per PWM untuk alokasi program kesehatan.</span>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Peta Sebaran GIS Interaktif & Donut/Pie Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- 3. Peta Sebaran Nasional (GIS Map) -->
                    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700">Geospatial GIS Map</span>
                                    <h3 class="font-outfit text-base font-bold text-slate-900">Peta Sebaran Daerah Nasional KucingMu</h3>
                                </div>
                                <button type="button" @click="resetMapCenter()" class="button-secondary text-[11px] px-2.5 py-1">
                                    🎯 Center Indonesia
                                </button>
                            </div>

                            <!-- Leaflet Map Container -->
                            <div class="mt-4 relative h-80 sm:h-96 w-full rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                                <div id="executiveMap" class="w-full h-full" style="min-height: 320px; z-index: 1;"></div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-teal-600 inline-block"></span> Populasi Aktif</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-300 inline-block"></span> Sentra Wilayah</span>
                            </div>
                            <span>💡 Klik lingkaran pada peta untuk melihat rincian anabul & KTAKuMu di daerah terkait.</span>
                        </div>
                    </div>

                    <!-- 4. Pie / Donut Chart: Komposisi Karakteristik Anabul -->
                    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-700">Pie / Donut Chart</span>
                                    <h3 class="font-outfit text-base font-bold text-slate-900">Komposisi Karakteristik Anabul</h3>
                                </div>
                                
                                <!-- Donut view toggle tabs -->
                                <div class="inline-flex p-0.5 bg-slate-100 rounded-lg text-[10px] font-bold">
                                    <button type="button" 
                                            @click="switchDonut('breeds')" 
                                            :class="donutView === 'breeds' ? 'bg-white text-teal-800 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                            class="px-2 py-1 rounded transition">
                                        Ras
                                    </button>
                                    <button type="button" 
                                            @click="switchDonut('ktam')" 
                                            :class="donutView === 'ktam' ? 'bg-white text-teal-800 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                            class="px-2 py-1 rounded transition">
                                        KTAM
                                    </button>
                                    <button type="button" 
                                            @click="switchDonut('gender')" 
                                            :class="donutView === 'gender' ? 'bg-white text-teal-800 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                            class="px-2 py-1 rounded transition">
                                        Gender
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 relative h-64 sm:h-72 w-full flex items-center justify-center">
                                <canvas id="chartDonutExecutive"></canvas>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                            <span x-show="donutView === 'breeds'">Proporsi varian ras anabul terdata (Persia, Domestik, Campuran, dll).</span>
                            <span x-show="donutView === 'ktam'">Tingkat kepatuhan verifikasi dan penerbitan identitas resmi KTAKuMu.</span>
                            <span x-show="donutView === 'gender'">Perbandingan jenis kelamin (Jantan vs Betina) populasi anabul terdaftar.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Script Initializer for Executive Analytics -->
            <script>
                function executiveDashboardAnalytics() {
                    return {
                        trendMode: 'daily',
                        donutView: 'breeds',
                        analyticsData: @json($analytics ?? []),
                        trendChart: null,
                        barChart: null,
                        donutChart: null,
                        mapInstance: null,

                        init() {
                            this.$nextTick(() => {
                                this.initTrendChart();
                                this.initBarChart();
                                this.initDonutChart();
                                this.initLeafletMap();
                            });
                        },

                        switchTrendMode(mode) {
                            this.trendMode = mode;
                            if (!this.trendChart) return;

                            const source = mode === 'daily' ? this.analyticsData.daily : this.analyticsData.monthly;
                            if (!source) return;

                            this.trendChart.data.labels = source.labels || [];
                            this.trendChart.data.datasets[0].data = source.cats || [];
                            this.trendChart.data.datasets[1].data = source.users || [];
                            this.trendChart.update();
                        },

                        switchDonut(view) {
                            this.donutView = view;
                            if (!this.donutChart) return;

                            let labels = [];
                            let data = [];
                            let colors = [];

                            if (view === 'breeds') {
                                labels = this.analyticsData.breeds?.labels || ['Belum Ada Data'];
                                data = this.analyticsData.breeds?.data || [1];
                                colors = ['#0f766e', '#0284c7', '#8b5cf6', '#f59e0b', '#ec4899', '#94a3b8'];
                            } else if (view === 'ktam') {
                                labels = this.analyticsData.ktamPie?.labels || ['KTAKuMu Terbit', 'Perlu Verifikasi', 'Belum Verifikasi'];
                                data = this.analyticsData.ktamPie?.data || [0, 0, 0];
                                colors = ['#10b981', '#f59e0b', '#94a3b8'];
                            } else if (view === 'gender') {
                                labels = this.analyticsData.genderPie?.labels || ['Jantan (♂)', 'Betina (♀)', 'Belum Diketahui'];
                                data = this.analyticsData.genderPie?.data || [0, 0, 0];
                                colors = ['#3b82f6', '#ec4899', '#94a3b8'];
                            }

                            this.donutChart.data.labels = labels;
                            this.donutChart.data.datasets[0].data = data;
                            this.donutChart.data.datasets[0].backgroundColor = colors;
                            this.donutChart.update();
                        },

                        initTrendChart() {
                            const ctx = document.getElementById('chartTrendRegistration');
                            if (!ctx) return;

                            const initial = this.analyticsData.daily || { labels: [], cats: [], users: [] };

                            this.trendChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: initial.labels || [],
                                    datasets: [
                                        {
                                            label: 'Pendaftaran Kucing',
                                            data: initial.cats || [],
                                            borderColor: '#0f766e',
                                            backgroundColor: 'rgba(15, 118, 110, 0.1)',
                                            fill: true,
                                            tension: 0.35,
                                            borderWidth: 2.5,
                                            pointRadius: 3,
                                            pointBackgroundColor: '#0f766e',
                                        },
                                        {
                                            label: 'Pendaftaran Member',
                                            data: initial.users || [],
                                            borderColor: '#0284c7',
                                            backgroundColor: 'rgba(2, 132, 199, 0.08)',
                                            fill: true,
                                            tension: 0.35,
                                            borderWidth: 2,
                                            pointRadius: 2.5,
                                            pointBackgroundColor: '#0284c7',
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                            padding: 10,
                                            cornerRadius: 8,
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { color: '#f8fafc' },
                                            ticks: { font: { size: 10 }, maxRotation: 45 }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: '#f1f5f9' },
                                            ticks: { precision: 0, font: { size: 10 } }
                                        }
                                    }
                                }
                            });
                        },

                        initBarChart() {
                            const ctx = document.getElementById('chartBarWilayah');
                            if (!ctx) return;

                            const barData = this.analyticsData.barWilayah || { labels: [], data: [] };

                            this.barChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: barData.labels && barData.labels.length > 0 ? barData.labels : ['Belum ada wilayah'],
                                    datasets: [{
                                        label: 'Populasi Anabul',
                                        data: barData.data && barData.data.length > 0 ? barData.data : [0],
                                        backgroundColor: '#6366f1',
                                        borderRadius: 6,
                                        borderSkipped: false,
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return ` ${context.raw} ekor anabul terdaftar`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            grid: { color: '#f1f5f9' },
                                            ticks: { precision: 0, font: { size: 10 } }
                                        },
                                        y: {
                                            grid: { display: false },
                                            ticks: { font: { size: 10, weight: 'bold' } }
                                        }
                                    }
                                }
                            });
                        },

                        initDonutChart() {
                            const ctx = document.getElementById('chartDonutExecutive');
                            if (!ctx) return;

                            const breedsData = this.analyticsData.breeds || { labels: [], data: [] };
                            const labels = breedsData.labels && breedsData.labels.length > 0 ? breedsData.labels : ['Belum Ada Data'];
                            const data = breedsData.data && breedsData.data.length > 0 ? breedsData.data : [1];

                            this.donutChart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: data,
                                        backgroundColor: ['#0f766e', '#0284c7', '#8b5cf6', '#f59e0b', '#ec4899', '#94a3b8'],
                                        borderWidth: 2,
                                        borderColor: '#ffffff',
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '65%',
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                boxWidth: 12,
                                                padding: 12,
                                                font: { size: 11 }
                                            }
                                        }
                                    }
                                }
                            });
                        },

                        initLeafletMap() {
                            const mapEl = document.getElementById('executiveMap');
                            if (!mapEl || typeof L === 'undefined') return;

                            // Default center Indonesia
                            const defaultCenter = [-2.5, 118.0];
                            const defaultZoom = 5;

                            this.mapInstance = L.map(mapEl, {
                                center: defaultCenter,
                                zoom: defaultZoom,
                                scrollWheelZoom: false,
                            });

                            // Standard OpenStreetMap tiles (sama seperti Peta Anabul & Sebaran Representatif)
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | KucingMu GIS',
                                maxZoom: 19,
                            }).addTo(this.mapInstance);

                            // Invalidate size once visible
                            setTimeout(() => {
                                if (this.mapInstance) this.mapInstance.invalidateSize();
                            }, 300);

                            const locations = this.analyticsData.mapLocations || [];
                            const activeMarkers = [];

                            locations.forEach(loc => {
                                if (!loc.lat || !loc.lng) return;

                                const hasPopulation = loc.total_cats > 0;
                                const radius = hasPopulation 
                                    ? Math.max(12, Math.min(28, 9 + Math.sqrt(loc.total_cats) * 5))
                                    : 6;

                                const marker = L.circleMarker([loc.lat, loc.lng], {
                                    radius: radius,
                                    color: hasPopulation ? '#0f766e' : '#94a3b8',
                                    fillColor: hasPopulation ? '#14b8a6' : '#cbd5e1',
                                    fillOpacity: hasPopulation ? 0.75 : 0.4,
                                    weight: hasPopulation ? 2 : 1,
                                });

                                marker.bindTooltip(`<b>${loc.short_name || loc.name}</b>: ${loc.total_cats} anabul`, {
                                    direction: 'top',
                                    offset: [0, -radius]
                                });

                                const popupHtml = `
                                    <div class="p-2.5 font-sans text-slate-800 text-xs min-w-[200px]">
                                        <div class="font-bold text-sm text-teal-900 border-b border-slate-100 pb-1.5 flex items-center justify-between">
                                            <span>${loc.name}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-teal-50 text-teal-800 font-bold">${loc.short_name}</span>
                                        </div>
                                        <div class="mt-2 space-y-1.5">
                                            <div class="flex justify-between items-center text-slate-600">
                                                <span>🐱 Populasi Anabul:</span>
                                                <span class="font-bold text-teal-800">${loc.total_cats} ekor</span>
                                            </div>
                                            <div class="flex justify-between items-center text-slate-600">
                                                <span>💳 KTAKuMu Terbit:</span>
                                                <span class="font-bold text-emerald-700">${loc.ktam_issued} kartu</span>
                                            </div>
                                        </div>
                                        <div class="mt-2.5 pt-1.5 border-t border-slate-100 text-[10px] text-slate-400 flex items-center justify-between">
                                            <span>Sentra PWM Wilayah</span>
                                            <span class="font-semibold text-teal-700">KucingMu GIS</span>
                                        </div>
                                    </div>
                                `;

                                marker.bindPopup(popupHtml);
                                marker.addTo(this.mapInstance);

                                if (hasPopulation) {
                                    activeMarkers.push([loc.lat, loc.lng]);
                                }
                            });

                            if (activeMarkers.length > 0 && this.mapInstance) {
                                try {
                                    const bounds = L.latLngBounds(activeMarkers);
                                    if (bounds.isValid()) {
                                        this.mapInstance.fitBounds(bounds, { padding: [50, 50], maxZoom: 8 });
                                    }
                                } catch (e) {}
                            }
                        },

                        resetMapCenter() {
                            if (this.mapInstance) {
                                this.mapInstance.setView([-2.5, 118.0], 5, { animate: true });
                            }
                        }
                    };
                }
            </script>

            <!-- Main Full-Width Cat Registry Datatable Card -->
            <div id="cat-registry-table" class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-mt-6">
                @php
                    $currentSort = $sort ?? 'created_at';
                    $currentDir = $direction ?? 'desc';
                    $currentStatus = $statusFilter ?? 'all';
                    $currentKtamStatus = $ktamStatusFilter ?? 'all';
                    
                    $makeSortUrl = function($col) use ($currentSort, $currentDir) {
                        $newDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                        return route('dashboard', array_merge(request()->except(['page']), [
                            'sort' => $col,
                            'direction' => $newDir,
                        ])) . '#cat-registry-table';
                    };

                    $getSortIndicator = function($col) use ($currentSort, $currentDir) {
                        if ($currentSort === $col) {
                            return $currentDir === 'asc' ? ' ↑' : ' ↓';
                        }
                        return '';
                    };
                @endphp

                <!-- Toolbar Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h2 class="font-outfit text-xl font-bold text-slate-900 leading-tight">Database Anggota KucingMu</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                    {{ $cats->total() }} Kucing Terdaftar
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Kelola data identitas, status kehidupan, kartu KTAKuMu, serta aksi verifikasi & penghapusan.</p>
                        </div>

                        @if(request('search') || (request('status') && request('status') !== 'all') || (request('ktam_status') && request('ktam_status') !== 'all') || request('sort'))
                            <a href="{{ route('dashboard') }}#cat-registry-table" class="button-secondary text-xs px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 self-start sm:self-auto shrink-0 inline-flex items-center gap-1 font-semibold">
                                <span>✕</span> Reset Semua Filter
                            </a>
                        @endif
                    </div>

                    <!-- Filter, Search, & Sort Bar -->
                    <form method="GET" action="{{ route('dashboard') }}#cat-registry-table" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-2">
                        <!-- Search Input (Span 4 on LG) -->
                        <div class="sm:col-span-2 lg:col-span-4 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   @input.debounce.500ms="$el.form.submit()"
                                   placeholder="Cari nama kucing, pemilik, NIAKuMu, ras, NBM..." 
                                   class="w-full text-xs pl-10 pr-8 py-2.5 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition placeholder:text-slate-400">
                            @if(request('search'))
                                <a href="{{ route('dashboard', array_merge(request()->except(['search', 'page']))) }}#cat-registry-table" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold" title="Hapus pencarian">✕</a>
                            @endif
                        </div>

                        <!-- Filter Status Kehidupan (Span 2 on LG) -->
                        <div class="lg:col-span-2">
                            <select id="admin_filter_status" name="status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition font-medium text-slate-700">
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>Semua Status Hidup</option>
                                <option value="alive" {{ $currentStatus === 'alive' ? 'selected' : '' }}>🟢 Hidup (Aktif)</option>
                                <option value="deceased" {{ $currentStatus === 'deceased' ? 'selected' : '' }}>⚪ Mati (Meninggal)</option>
                            </select>
                        </div>

                        <!-- Filter Status KTAKuMu (Span 3 on LG) -->
                        <div class="lg:col-span-3">
                            <select id="admin_filter_ktam_status" name="ktam_status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition font-medium text-slate-700">
                                <option value="all" {{ ($currentKtamStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status KTAKuMu</option>
                                <option value="pending" {{ ($currentKtamStatus ?? '') === 'pending' ? 'selected' : '' }}>⏳ Antrian Verifikasi ({{ $stats['pending_verification_count'] }})</option>
                                <option value="issued" {{ ($currentKtamStatus ?? '') === 'issued' ? 'selected' : '' }}>✓ KTAKuMu Terbit ({{ $stats['ktam_count'] }})</option>
                            </select>
                        </div>

                        <!-- Quick Sort Dropdown (Span 2 on LG) -->
                        <div class="lg:col-span-2">
                            <select id="admin_sort_select" name="sort_direction" onchange="
                                const val = this.value.split(':');
                                const sortInput = this.form.querySelector('input[name=sort]');
                                const dirInput = this.form.querySelector('input[name=direction]');
                                if (sortInput && dirInput) {
                                    sortInput.value = val[0];
                                    dirInput.value = val[1];
                                    this.form.submit();
                                }
                            " class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition font-medium text-slate-700">
                                <option value="created_at:desc" {{ ($currentSort == 'created_at' && $currentDir == 'desc') ? 'selected' : '' }}>Urutan: Terbaru</option>
                                <option value="created_at:asc" {{ ($currentSort == 'created_at' && $currentDir == 'asc') ? 'selected' : '' }}>Urutan: Terlama</option>
                                <option value="name:asc" {{ ($currentSort == 'name' && $currentDir == 'asc') ? 'selected' : '' }}>Nama Kucing (A - Z)</option>
                                <option value="name:desc" {{ ($currentSort == 'name' && $currentDir == 'desc') ? 'selected' : '' }}>Nama Kucing (Z - A)</option>
                                <option value="owner:asc" {{ ($currentSort == 'owner' && $currentDir == 'asc') ? 'selected' : '' }}>Pemilik (A - Z)</option>
                                <option value="owner:desc" {{ ($currentSort == 'owner' && $currentDir == 'desc') ? 'selected' : '' }}>Pemilik (Z - A)</option>
                                <option value="breed:asc" {{ ($currentSort == 'breed' && $currentDir == 'asc') ? 'selected' : '' }}>Ras Kucing (A - Z)</option>
                                <option value="date_of_birth:asc" {{ ($currentSort == 'date_of_birth' && $currentDir == 'asc') ? 'selected' : '' }}>Umur (Paling Tua)</option>
                                <option value="date_of_birth:desc" {{ ($currentSort == 'date_of_birth' && $currentDir == 'desc') ? 'selected' : '' }}>Umur (Paling Muda)</option>
                                <option value="unique_code:asc" {{ ($currentSort == 'unique_code' && $currentDir == 'asc') ? 'selected' : '' }}>Nomor NIAKuMu</option>
                                <option value="status:asc" {{ ($currentSort == 'status' && $currentDir == 'asc') ? 'selected' : '' }}>Status Hidup/Mati</option>
                            </select>
                            <input type="hidden" name="sort" value="{{ $currentSort }}">
                            <input type="hidden" name="direction" value="{{ $currentDir }}">
                        </div>

                        <!-- Apply Button (Span 1 on LG) -->
                        <div class="lg:col-span-1">
                            <button type="submit" class="w-full text-xs font-bold py-2.5 px-3 rounded-xl shadow-xs inline-flex items-center justify-center gap-1.5 bg-teal-700 hover:bg-teal-800 text-white transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>Cari</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Datatable Content -->
                @if($cats->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">🐱</div>
                        <h3 class="text-sm font-bold text-slate-800">Tidak ada data kucing yang sesuai</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan coba ubah kata kunci pencarian atau ubah filter status di atas.</p>
                        <a href="{{ route('dashboard') }}#cat-registry-table" class="inline-flex items-center gap-1.5 mt-4 text-xs font-bold text-teal-700 hover:text-teal-900 underline">
                            Tampilkan Semua Data Kucing
                        </a>
                    </div>
                @else
                    <!-- DESKTOP / TABLET VIEW: Modern Datatable -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse" aria-label="Database Anggota Kucing">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-600 font-bold bg-slate-50/80 select-none uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan Nama">
                                            Identitas Kucing
                                             <span class="text-teal-700 font-bold">{{ $getSortIndicator('name') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('owner') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan Pemilik">
                                            Pemilik & NBM
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('owner') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('breed') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan Ras">
                                            Ras & Biometrik
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('breed') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('unique_code') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan NIAKuMu">
                                            Nomor NIAKuMu
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('unique_code') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan Status Hidup/Mati">
                                            Status Hidup
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('status') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <div class="flex items-center gap-1">
                                            <span>Status KTAKuMu</span>
                                            @if(request('ktam_status') && request('ktam_status') !== 'all')
                                                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse" title="Filter KTAKuMu aktif"></span>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi / Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-normal">
                                @foreach($cats as $cat)
                                    <tr class="hover:bg-teal-50/30 transition-colors {{ $cat->isDeceased() ? 'bg-slate-50/60 text-slate-500' : '' }}">
                                        <!-- Foto & Nama Kucing -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-11 h-11 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs">
                                                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover {{ $cat->isDeceased() ? 'grayscale opacity-75' : '' }}">
                                                    @if($cat->isDeceased())
                                                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                                            <span class="text-[8px] font-bold text-white uppercase bg-black/60 px-1 rounded">Mati</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 text-sm leading-snug flex items-center gap-1.5 truncate">
                                                        <span>{{ $cat->name }}</span>
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                                        {{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }} &bull; <span class="font-medium text-slate-600">{{ $cat->age_text }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Pemilik & NBM -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-1.5 justify-between">
                                                <div class="font-semibold text-slate-900 text-xs">{{ $cat->owner ? $cat->owner->name : '-' }}</div>
                                                @if($cat->owner && Auth::id() !== $cat->owner->id)
                                                    <form action="{{ route('admin.users.impersonate', $cat->owner->id) }}" method="POST" class="inline" onsubmit="return confirm('Masuk sebagai pemilik {{ $cat->owner->name }}?')">
                                                        @csrf
                                                        <button type="submit" class="text-[10px] bg-slate-100 hover:bg-teal-100 hover:text-teal-900 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200 font-bold transition flex items-center gap-0.5" title="Login Sebagai Pemilik (Impersonate)">
                                                            <span>🎭</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                NBM: <span class="text-slate-700 font-medium">{{ $cat->owner ? ($cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? '-')) : '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- Ras & Biometrik -->
                                        <td class="py-3.5 px-4">
                                            <div class="text-xs font-semibold text-slate-800">{{ $cat->breed }}</div>
                                            @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-teal-50 text-teal-800 border border-teal-200 uppercase whitespace-nowrap mt-1 inline-flex items-center gap-1">
                                                    <span>🐾</span> {{ $cat->biometric_type }}
                                                </span>
                                            @else
                                                <span class="text-[11px] text-slate-400">Standar Foto</span>
                                            @endif
                                        </td>

                                        <!-- Nomor KTAM -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($cat->unique_code)
                                                <div class="font-mono text-xs font-bold text-teal-900">{{ $cat->unique_code }}</div>
                                                <div class="text-[10px] text-slate-500 font-sans mt-0.5">
                                                    {{ $cat->wilayah ? $cat->wilayah->nama : 'PWM DIY' }}
                                                </div>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Belum Verif</span>
                                            @endif
                                        </td>

                                        <!-- Status Hidup / Mati -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                @if($cat->isAlive())
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1" title="Kucing Hidup (Aktif)">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hidup
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300 inline-flex items-center gap-1" title="Kucing Mati / Meninggal">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Mati
                                                    </span>
                                                @endif

                                                <!-- Quick Toggle Status Button -->
                                                <form action="{{ route('cat.toggle-status', $cat->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Ubah status kucing {{ $cat->name }} menjadi {{ $cat->isAlive() ? 'MATI (Meninggal)' : 'HIDUP (Aktif)' }}?')"
                                                            class="text-xs text-slate-400 hover:text-teal-700 hover:bg-slate-100 p-1.5 rounded-lg transition" 
                                                            title="Ubah status hidup/mati">
                                                        🔄
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                        <!-- Status Penerbitan KTAKuMu (Clickable Filters) -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($cat->unique_code && $cat->ktamCard)
                                                @php
                                                    $verifiedAt = $cat->verified_at ?? $cat->ktamCard->verified_at;
                                                    $verifier = $cat->verifier ?? $cat->ktamCard->verifier;
                                                @endphp
                                                <div class="space-y-0.5">
                                                    <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'issued'])) }}#cat-registry-table" 
                                                       title="Klik untuk filter semua KTAKuMu Terbit" 
                                                       class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 inline-flex items-center gap-1 transition cursor-pointer">
                                                        <span>✓</span> KTAKuMu Terbit
                                                    </a>
                                                    @if($verifiedAt)
                                                        <div class="text-[10px] text-slate-500 font-mono" title="Waktu Verifikasi">
                                                            {{ $verifiedAt->format('d M Y, H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($verifier)
                                                        <div class="text-[9px] text-slate-400 truncate max-w-[120px]" title="Diverifikasi oleh {{ $verifier->name }}">
                                                            Oleh: {{ $verifier->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'pending'])) }}#cat-registry-table" 
                                                   title="Klik untuk filter Antrian Verifikasi" 
                                                   class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1 transition cursor-pointer">
                                                    <span>⏳</span> Antrian Verifikasi
                                                </a>
                                            @endif
                                        </td>

                                        <!-- Aksi / Tindakan -->
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                <a href="{{ route('cat.edit', $cat->id) }}" title="Update Foto, Identitas & Biometrik" class="btn-action-secondary py-1.5 px-2.5 text-xs font-semibold">
                                                    <span>✏️</span> Ubah
                                                </a>
                                                
                                                @if($cat->unique_code && $cat->ktamCard)
                                                    <a href="{{ route('ktam.download', $cat->id) }}" class="btn-action-success py-1.5 px-2.5 text-xs font-bold" title="Unduh Kartu PDF">
                                                        <span>📄</span> PDF
                                                    </a>
                                                @else
                                                    <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Verifikasi & terbitkan kartu KTAKuMu untuk {{ $cat->name }}?')" class="btn-action-primary py-1.5 px-2.5 text-xs font-bold" title="Terbitkan Kartu KTAKuMu">
                                                            <span>✓</span> Verifikasi
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs font-semibold" title="Hapus Kucing">
                                                        <span>🗑</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- MOBILE VIEW: Interactive Responsive Cards -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($cats as $cat)
                            <div class="p-4 space-y-3.5 {{ $cat->isDeceased() ? 'bg-slate-50/50' : 'bg-white' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs shrink-0 {{ $cat->isDeceased() ? 'grayscale opacity-75' : '' }}">
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $cat->name }}</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                            <p class="text-[11px] text-slate-400">Umur: {{ $cat->age_text }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-1 shrink-0">
                                        @if($cat->isAlive())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                🟢 Hidup
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                                ⚪ Mati
                                            </span>
                                        @endif

                                        @if($cat->unique_code && $cat->ktamCard)
                                            <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'issued'])) }}#cat-registry-table" class="text-[10px] font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 px-2 py-0.5 rounded border border-teal-200 transition">
                                                KTAKuMu Terbit
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard', array_merge(request()->except(['page']), ['ktam_status' => 'pending'])) }}#cat-registry-table" class="text-[10px] font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 px-2 py-0.5 rounded border border-amber-200 transition">
                                                Antrian Verifikasi
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-3 text-xs space-y-1.5 border border-slate-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500">Pemilik:</span>
                                        <span class="font-semibold text-slate-800">{{ $cat->owner ? $cat->owner->name : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500">NBM:</span>
                                        <span class="font-mono text-slate-700">{{ $cat->owner ? ($cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? '-')) : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500">Nomor NIAKuMu:</span>
                                        @if($cat->unique_code)
                                            <span class="font-mono font-bold text-teal-800">{{ $cat->unique_code }}</span>
                                        @else
                                            <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded text-[10px] font-bold border border-amber-200">Belum Verif</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons on Mobile -->
                                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                    <div class="flex items-center gap-1.5">
                                        <!-- Toggle Status -->
                                        <form action="{{ route('cat.toggle-status', $cat->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Ubah status kucing {{ $cat->name }} menjadi {{ $cat->isAlive() ? 'MATI' : 'HIDUP' }}?')"
                                                    class="button-secondary text-xs py-1.5 px-2.5 rounded-lg text-slate-600" 
                                                    title="Ubah Status Hidup/Mati">
                                                🔄 Ubah Status
                                            </button>
                                        </form>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('cat.edit', $cat->id) }}" class="btn-action-secondary py-1.5 px-2.5 text-xs font-semibold">
                                            ✏️ Ubah
                                        </a>

                                        @if($cat->unique_code && $cat->ktamCard)
                                            <a href="{{ route('ktam.download', $cat->id) }}" class="btn-action-success py-1.5 px-2.5 text-xs font-bold">
                                                📄 PDF
                                            </a>
                                        @else
                                            <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Verifikasi KTAKuMu untuk {{ $cat->name }}?')" class="btn-action-primary py-1.5 px-2.5 text-xs font-bold">
                                                    ✓ Verifikasi
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs font-semibold" title="Hapus Kucing">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50">
                        {{ $cats->fragment('cat-registry-table')->links() }}
                    </div>
                @endif
            </div>

            <!-- Bottom Utilities Grid: KTAKuMu Verification Simulator & Quick Appointments -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- KTAKuMu Verification Card -->
                <div class="content-card bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div>
                            <h2 class="font-outfit text-base font-bold text-slate-900">Periksa Keaslian KTAKuMu</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Uji verifikasi QR atau lookup kartu KTAKuMu publik.</p>
                        </div>
                        <span class="text-2xl">🔍</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="search-ktam-input" class="form-label text-xs font-semibold text-slate-700">Nomor Kartu KTAKuMu / NIAKuMu</label>
                            <input id="search-ktam-input" type="text" x-model="searchKtam" placeholder="Contoh: 34.kcg.0001" class="form-input text-xs font-mono font-bold">
                        </div>
                        
                        <button type="button" @click="if(searchKtam.trim()) { window.location.href = `/verify/${searchKtam.trim()}` } else { alert('Silakan masukkan nomor NIAKuMu terlebih dahulu.') }" class="w-full button-primary flex justify-center py-2.5 text-xs font-bold shadow-xs">
                            Cek Status Validitas KTAKuMu
                        </button>
                    </div>

                    @if($stats['ktam_count'] > 0)
                        <div class="mt-4 border-t border-slate-100 pt-3">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilihan Cepat Nomor NIAKuMu:</span>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($cats->filter(fn($c) => $c->ktamCard)->take(4) as $c)
                                    <button type="button" @click="searchKtam = '{{ $c->ktamCard->ktam_number }}'" class="text-xs font-mono bg-slate-50 hover:bg-teal-50 hover:text-teal-800 px-2.5 py-1 rounded-lg border border-slate-200 font-bold transition">
                                        {{ $c->ktamCard->ktam_number }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Recent Appointments & Quick Actions -->
                <div class="content-card bg-white border border-slate-200 p-6 rounded-2xl shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="font-outfit text-base font-bold text-slate-900">Janji Temu Terkini</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Antrian jadwal pemeriksaan kucing.</p>
                        </div>
                        <span class="text-2xl">📅</span>
                    </div>

                    @if($appointments->isEmpty())
                        <div class="text-center py-6 text-slate-500 text-xs bg-slate-50 rounded-xl border border-slate-100">
                            Belum ada jadwal janji temu terbaru.
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach($appointments as $appt)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-900 font-bold flex items-center justify-center text-xs shrink-0">
                                            🐱
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $appt->cat ? $appt->cat->name : 'Kucing' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $appt->cat && $appt->cat->owner ? $appt->cat->owner->name : '-' }} &bull; {{ $appt->time_slot }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono text-[11px] font-bold text-slate-700 block">{{ $appt->date ? $appt->date->format('d M Y') : '-' }}</span>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $appt->status === 'completed' ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $appt->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @if(request()->has('search') || request()->has('page') || request()->has('status') || request()->has('ktam_status') || request()->has('sort'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tableEl = document.getElementById('cat-registry-table');
                if (tableEl && window.location.hash === '#cat-registry-table') {
                    tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput && "{{ request('search') }}") {
                    searchInput.focus();
                    const val = searchInput.value;
                    searchInput.setSelectionRange(val.length, val.length);
                }
            });
        </script>
    @endif
</x-app-layout>
