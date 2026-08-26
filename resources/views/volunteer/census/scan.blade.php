<x-app-layout>
    <div class="py-8" x-data="catScannerApp()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Biometrik Visual Lapangan</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Pindai & Deteksi Kucing Sensus
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Arahkan kamera ke wajah atau pola tubuh kucing liar untuk memeriksa apakah kucing tersebut sudah pernah terdata dalam database sensus kampus PTMA.
                    </p>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <a href="{{ route('volunteer.census.index') }}" class="button-secondary px-4 py-2 inline-flex items-center gap-2 text-xs font-semibold bg-white">
                            <span>←</span> Kembali ke Daftar Sensus
                        </a>
                        <a href="{{ route('volunteer.census.create') }}" class="button-secondary px-4 py-2 inline-flex items-center gap-2 text-xs font-semibold bg-white">
                            <span>➕</span> Input Sensus Manual
                        </a>
                    </div>
                </div>

                <!-- Stats / Engine Badge -->
                <div class="hidden sm:flex flex-col items-end justify-center text-right space-y-2">
                    <div class="bg-white/80 backdrop-blur-sm border border-teal-100 rounded-xl px-4 py-2 shadow-sm">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Basis Data Master</span>
                        <span class="font-outfit text-xl font-bold text-teal-800">{{ $totalRegistered }} Ekor Terdata</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold"
                         :class="aiReady ? 'bg-emerald-100 text-emerald-800' : (aiLoading ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')">
                        <span class="w-2 h-2 rounded-full" :class="aiReady ? 'bg-emerald-500 animate-pulse' : (aiLoading ? 'bg-amber-500 animate-spin' : 'bg-slate-400')"></span>
                        <span x-text="aiStatusText">Menyiapkan Engine AI...</span>
                    </div>
                </div>
            </div>

            <!-- Scanner Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Left Column: Camera Viewfinder & Image Input (5 Cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="content-card space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="font-outfit text-base font-bold text-slate-900 flex items-center gap-2">
                                <span>📷</span> Tangkap Foto Kucing
                            </h2>
                            <!-- Switch mode button -->
                            <div class="flex rounded-lg bg-slate-100 p-0.5 text-xs font-semibold">
                                <button type="button" @click="setMode('camera')" 
                                        :class="inputMode === 'camera' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                        class="px-2.5 py-1 rounded-md transition-all">
                                    Kamera
                                </button>
                                <button type="button" @click="setMode('upload')" 
                                        :class="inputMode === 'upload' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                        class="px-2.5 py-1 rounded-md transition-all">
                                    Galeri
                                </button>
                            </div>
                        </div>

                        <!-- 1. Mode Kamera -->
                        <div x-show="inputMode === 'camera'" class="space-y-3">
                            <div class="relative aspect-4/3 w-full rounded-2xl overflow-hidden bg-slate-900 border border-slate-700 flex items-center justify-center shadow-inner">
                                <video id="scannerVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                                <canvas id="scannerCanvas" class="hidden"></canvas>

                                <!-- Viewfinder Reticle Overlay -->
                                <div class="absolute inset-0 pointer-events-none flex items-center justify-center p-6">
                                    <div class="w-48 h-48 sm:w-56 sm:h-56 border-2 border-dashed border-teal-300/80 rounded-2xl relative flex items-center justify-center">
                                        <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-teal-400"></div>
                                        <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-teal-400"></div>
                                        <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-teal-400"></div>
                                        <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-teal-400"></div>
                                        <span class="text-[10px] tracking-wide text-teal-200 bg-slate-900/60 px-2 py-0.5 rounded font-mono">
                                            Fokus Wajah Kucing
                                        </span>
                                    </div>
                                </div>

                                <!-- Camera loading state -->
                                <div x-show="cameraStarting" class="absolute inset-0 bg-slate-900/80 flex flex-col items-center justify-center text-white space-y-2">
                                    <span class="animate-spin text-2xl">⟳</span>
                                    <span class="text-xs font-semibold">Mengaktifkan Kamera...</span>
                                </div>

                                <!-- Camera error state -->
                                <div x-show="cameraError" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-white p-4 text-center space-y-2">
                                    <span class="text-2xl">⚠️</span>
                                    <span class="text-xs text-rose-300 font-semibold" x-text="cameraError"></span>
                                    <button type="button" @click="startCamera()" class="mt-2 text-xs bg-teal-700 text-white px-3 py-1.5 rounded-lg">
                                        Coba Lagi
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="switchCameraFacing()" :disabled="!cameraActive" class="button-secondary text-xs font-semibold py-2 px-3 bg-white" title="Ganti Kamera Depan/Belakang">
                                    🔄 Ganti Kamera
                                </button>
                                <button type="button" @click="captureAndScan()" :disabled="!cameraActive || isScanning" class="button-primary flex-1 py-2.5 text-xs font-bold shadow flex items-center justify-center gap-2">
                                    <span x-show="!isScanning">📸 Ambil & Pindai Sekarang</span>
                                    <span x-show="isScanning" class="inline-flex items-center gap-1.5">
                                        <span class="animate-spin">⟳</span> Memindai...
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Mode Unggah Galeri -->
                        <div x-show="inputMode === 'upload'" class="space-y-3">
                            <label class="relative aspect-4/3 w-full rounded-2xl border-2 border-dashed border-slate-300 hover:border-teal-500 bg-slate-50 hover:bg-teal-50/40 transition-colors flex flex-col items-center justify-center p-4 cursor-pointer overflow-hidden group">
                                <template x-if="scannedImagePreview">
                                    <img :src="scannedImagePreview" alt="Preview Foto" class="w-full h-full object-contain rounded-xl">
                                </template>
                                <template x-if="!scannedImagePreview">
                                    <div class="text-center space-y-2">
                                        <div class="w-12 h-12 mx-auto rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">
                                            📁
                                        </div>
                                        <p class="text-xs font-bold text-slate-800">Klik untuk Pilih Foto dari Galeri</p>
                                        <p class="text-[11px] text-slate-500">Mendukung format JPG, PNG, atau WEBP</p>
                                    </div>
                                </template>
                                <input type="file" accept="image/*" class="hidden" @change="handleFileUpload($event)">
                            </label>

                            <template x-if="scannedImagePreview">
                                <button type="button" @click="runScanOnPreview()" :disabled="isScanning" class="w-full button-primary py-2.5 text-xs font-bold shadow flex items-center justify-center gap-2">
                                    <span x-show="!isScanning">🔍 Pindai Ulang Foto Ini</span>
                                    <span x-show="isScanning" class="inline-flex items-center gap-1.5">
                                        <span class="animate-spin">⟳</span> Memindai Foto...
                                    </span>
                                </button>
                            </template>
                        </div>

                        <!-- Filter Options Accordion -->
                        <div class="border-t border-slate-100 pt-3 space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Filter Pencocokan (Opsional)</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-600 block mb-0.5">Filter Kampus</label>
                                    <select x-model="filterKampus" class="form-input text-xs py-1 px-2.5 w-full bg-slate-50">
                                        <option value="Semua">Semua Kampus</option>
                                        @foreach($campuses as $camp)
                                            <option value="{{ $camp }}">{{ $camp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-600 block mb-0.5">Pola Warna</label>
                                    <select x-model="filterWarna" class="form-input text-xs py-1 px-2.5 w-full bg-slate-50">
                                        <option value="">Semua Warna</option>
                                        <option value="Tabby">Tabby</option>
                                        <option value="Calico">Calico / Tortie</option>
                                        <option value="Black/White">Black / White</option>
                                        <option value="Ginger">Ginger / Orange</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Column: Scan Results & Side-by-Side Comparison (7 Cols) -->
                <div class="lg:col-span-7 space-y-4">

                    <!-- State A: Idle / Belum Ada Pemindaian -->
                    <div x-show="!hasScanned && !isScanning" class="content-card p-8 text-center space-y-3 bg-white">
                        <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 text-teal-700 mx-auto flex items-center justify-center text-3xl">
                            🔎
                        </div>
                        <h3 class="font-outfit text-base font-bold text-slate-900">Siap Memindai Kucing</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Arahkan kamera atau unggah foto kucing di panel sebelah kiri. Sistem akan mencocokkan kemiripan biometrik visual dengan arsip sensus PTMA.
                        </p>
                    </div>

                    <!-- State B: Sedang Scanning -->
                    <div x-show="isScanning" class="content-card p-8 text-center space-y-4 bg-white" x-cloak>
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-50 text-teal-700 text-2xl animate-pulse">
                            ⚡
                        </div>
                        <div>
                            <h3 class="font-outfit text-base font-bold text-slate-900" x-text="scanProgressMessage">Menganalisis Ciri Visual Kucing...</h3>
                            <p class="text-xs text-slate-500 mt-1">Mengekstrak ciri wajah & membandingkan dengan master data...</p>
                        </div>
                        <div class="w-48 h-1.5 bg-slate-100 rounded-full mx-auto overflow-hidden">
                            <div class="h-full bg-teal-600 rounded-full animate-pulse w-3/4"></div>
                        </div>
                    </div>

                    <!-- State C: Hasil Pemindaian Selesai -->
                    <div x-show="hasScanned && !isScanning" class="space-y-4" x-cloak>

                        <!-- Case 1: Kucing Ditemukan Cocok (Likely Match / High Similarity) -->
                        <template x-if="matchResult && matchResult.best_match && matchResult.is_likely_match">
                            <div class="content-card border-2 border-emerald-300 bg-emerald-50/30 space-y-5 p-5">
                                <div class="flex items-start justify-between gap-3 border-b border-emerald-100 pb-3">
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                            <span>✓</span> Kucing Sudah Terdata
                                        </span>
                                        <h3 class="font-outfit text-lg font-bold text-slate-900 mt-1">
                                            Ditemukan Kemiripan Sangat Tinggi
                                        </h3>
                                        <p class="text-xs text-slate-600">
                                            Kucing ini terdeteksi memiliki kemiripan kuat dengan rekaman sensus <strong class="text-teal-900" x-text="matchResult.best_match.id_kucing"></strong>.
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Skor Kemiripan</span>
                                        <span class="font-outfit text-2xl font-bold text-emerald-700" x-text="`${matchResult.best_match.similarity_percent}%`"></span>
                                    </div>
                                </div>

                                <!-- Side-by-Side Comparison -->
                                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                    <!-- Foto Baru -->
                                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 space-y-1.5">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Foto Hasil Scan</span>
                                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                                            <img :src="scannedImagePreview" alt="Foto Scan" class="w-full h-full object-cover">
                                        </div>
                                    </div>

                                    <!-- Foto Master Database -->
                                    <div class="bg-white p-2.5 rounded-xl border border-emerald-200 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Foto Master Sensus</span>
                                            <span class="text-[10px] font-mono font-bold text-teal-800" x-text="matchResult.best_match.id_kucing"></span>
                                        </div>
                                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                                            <img :src="matchResult.best_match.foto_wajah_url || matchResult.best_match.foto_atas_url || '/images/cat-placeholder.png'" 
                                                 alt="Foto Master" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                </div>

                                <!-- Identity & Morphometry Info -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 bg-white p-3 rounded-xl border border-slate-200 text-xs">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block">Kampus / Lokasi</span>
                                        <span class="font-bold text-slate-800" x-text="matchResult.best_match.display_kampus"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block">Zona Lapangan</span>
                                        <span class="font-bold text-slate-800 truncate block" x-text="matchResult.best_match.zona"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block">Warna & Usia</span>
                                        <span class="font-bold text-slate-800" x-text="`${matchResult.best_match.display_warna} (${matchResult.best_match.usia})`"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block">Tanggal Sensus</span>
                                        <span class="font-semibold text-slate-600" x-text="matchResult.best_match.created_at_formatted"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-1">
                                    <a :href="`/sensus-kucing/${matchResult.best_match.id}`" target="_blank" 
                                       class="w-full sm:w-auto button-primary px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow">
                                        <span>📋</span> Buka Detail Sensus Kucing Ini ↗
                                    </a>
                                    <button type="button" @click="proceedToCreateWithPhoto()" 
                                            class="w-full sm:w-auto button-secondary px-4 py-2 text-xs font-semibold inline-flex items-center justify-center gap-1.5 bg-white">
                                        <span>➕</span> Bukan Kucing Ini? Daftarkan Sebagai Kucing Baru
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Case 2: Kucing Baru / Tidak Ada Kemiripan Kuat (< 72%) -->
                        <template x-if="!matchResult || !matchResult.best_match || !matchResult.is_likely_match">
                            <div class="content-card border border-slate-200 bg-white space-y-5 p-5">
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-teal-800 bg-teal-100 px-2.5 py-0.5 rounded-full">
                                            <span>✨</span> Kucing Belum Pernah Terdata
                                        </span>
                                        <h3 class="font-outfit text-lg font-bold text-slate-900 mt-1">
                                            Kandidat Kucing Baru Terkonfirmasi
                                        </h3>
                                        <p class="text-xs text-slate-600">
                                            Tidak ditemukan arsip sensus yang memiliki pola visual serupa di database. Anda dapat langsung mendaftarkannya sebagai data sensus baru.
                                        </p>
                                    </div>
                                    <template x-if="matchResult && matchResult.best_match">
                                        <div class="text-right">
                                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Kemiripan Tertinggi</span>
                                            <span class="font-outfit text-lg font-bold text-slate-600" x-text="`${matchResult.best_match.similarity_percent}%`"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Preview Scanned Photo & Registration Action -->
                                <div class="flex flex-col sm:flex-row items-center gap-4 bg-teal-50/50 p-4 rounded-xl border border-teal-100">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 shrink-0 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-sm">
                                        <img :src="scannedImagePreview" alt="Foto Scan" class="w-full h-full object-cover">
                                    </div>
                                    <div class="space-y-2 text-center sm:text-left flex-1">
                                        <h4 class="font-outfit text-sm font-bold text-teal-950">Gunakan Foto Ini untuk Sensus Baru</h4>
                                        <p class="text-xs text-slate-600">
                                            Foto wajah yang baru saja dipindai akan otomatis dipasang di Slot 1 (Foto Wajah) formulir sensus tanpa perlu foto ulang.
                                        </p>
                                        <button type="button" @click="proceedToCreateWithPhoto()" 
                                                class="button-primary px-5 py-2.5 text-xs font-bold shadow-md inline-flex items-center gap-2">
                                            <span>➕</span> Daftarkan Kucing Ini Sekarang ➔
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Alternative Candidates List (Jika ada alternatif yang diproses) -->
                        <template x-if="matchResult && matchResult.matches && matchResult.matches.length > 1">
                            <div class="content-card space-y-3 bg-white p-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <h4 class="font-outfit text-xs font-bold uppercase tracking-wider text-slate-600">
                                        Kandidat Pembanding Lainnya (<span x-text="matchResult.matches.length - 1"></span>)
                                    </h4>
                                    <span class="text-[11px] text-slate-400">Diurutkan berdasarkan kemiripan</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <template x-for="(cand, idx) in matchResult.matches.slice(1, 5)" :key="cand.id">
                                        <div class="flex items-center gap-2.5 p-2 rounded-lg border border-slate-100 hover:border-teal-200 bg-slate-50/60 hover:bg-teal-50/30 transition-colors">
                                            <div class="w-12 h-12 shrink-0 rounded-lg overflow-hidden bg-white border border-slate-200">
                                                <img :src="cand.foto_wajah_url || '/images/cat-placeholder.png'" alt="Foto Wajah" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0 flex-1 text-xs">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-slate-900 truncate" x-text="cand.id_kucing"></span>
                                                    <span class="font-semibold text-[11px] text-teal-700 font-mono" x-text="`${cand.similarity_percent}%`"></span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 truncate" x-text="`${cand.display_kampus} • ${cand.display_warna}`"></p>
                                                <a :href="`/sensus-kucing/${cand.id}`" target="_blank" class="text-[10px] text-teal-700 hover:underline font-semibold block mt-0.5">
                                                    Lihat Data ↗
                                                </a>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Load TensorFlow.js and MobileNet -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.17.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@2.1.1/dist/mobilenet.min.js"></script>

    <script>
        function catScannerApp() {
            return {
                inputMode: 'camera', // 'camera' or 'upload'
                cameraActive: false,
                cameraStarting: false,
                cameraError: null,
                facingMode: 'environment', // 'user' or 'environment'
                mediaStream: null,

                aiLoading: true,
                aiReady: false,
                aiStatusText: 'Memuat Model AI...',
                netModel: null,

                isScanning: false,
                hasScanned: false,
                scanProgressMessage: '',
                scannedImagePreview: null,
                currentEmbedding: null,

                filterKampus: 'Semua',
                filterWarna: '',

                matchResult: null,

                async init() {
                    await this.loadAIModel();
                    this.startCamera();
                    this.backgroundCheckMissingEmbeddings();
                },

                // Load MobileNet Feature Extractor
                async loadAIModel() {
                    this.aiLoading = true;
                    try {
                        if (window.mobilenet) {
                            this.netModel = await mobilenet.load({ version: 2, alpha: 1.0 });
                            this.aiReady = true;
                            this.aiStatusText = 'Engine AI Siap';
                        } else {
                            this.aiReady = false;
                            this.aiStatusText = 'Mode Pencocokan Warna Aktif';
                        }
                    } catch (e) {
                        console.warn('Gagal memuat MobileNet CDN, beralih ke Color Fingerprint PHP:', e);
                        this.aiReady = false;
                        this.aiStatusText = 'Mode Warna Aktif';
                    } finally {
                        this.aiLoading = false;
                    }
                },

                // Mode Selector
                setMode(mode) {
                    this.inputMode = mode;
                    if (mode === 'camera') {
                        this.startCamera();
                    } else {
                        this.stopCamera();
                    }
                },

                // Start Live Camera
                async startCamera() {
                    this.stopCamera();
                    this.cameraStarting = true;
                    this.cameraError = null;

                    try {
                        this.mediaStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: { ideal: this.facingMode },
                                width: { ideal: 1280 },
                                height: { ideal: 720 }
                            },
                            audio: false
                        });

                        const video = document.getElementById('scannerVideo');
                        if (video) {
                            video.srcObject = this.mediaStream;
                            video.onloadedmetadata = () => {
                                video.play();
                                this.cameraActive = true;
                                this.cameraStarting = false;
                            };
                        }
                    } catch (err) {
                        this.cameraStarting = false;
                        this.cameraActive = false;
                        this.cameraError = 'Izin kamera tidak diberikan atau perangkat kamera tidak ditemukan.';
                        console.error(err);
                    }
                },

                // Switch Camera (Front / Back)
                switchCameraFacing() {
                    this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
                    this.startCamera();
                },

                // Stop Live Camera
                stopCamera() {
                    if (this.mediaStream) {
                        this.mediaStream.getTracks().forEach(track => track.stop());
                        this.mediaStream = null;
                    }
                    this.cameraActive = false;
                },

                // Handle File Upload from Gallery
                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.scannedImagePreview = e.target.result;
                            this.runScanOnPreview();
                        };
                        reader.readAsDataURL(file);
                    }
                },

                // Capture Frame from Camera and Scan
                async captureAndScan() {
                    const video = document.getElementById('scannerVideo');
                    const canvas = document.getElementById('scannerCanvas');
                    if (!video || !canvas) return;

                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    this.scannedImagePreview = canvas.toDataURL('image/jpeg', 0.88);
                    await this.runScanOnPreview();
                },

                // Extract Embedding & Send Match Request
                async runScanOnPreview() {
                    if (!this.scannedImagePreview) return;

                    this.isScanning = true;
                    this.scanProgressMessage = 'Mengekstrak Ciri Visual Kucing...';

                    let embeddingVector = null;

                    // 1. Run MobileNet Inference if loaded
                    if (this.netModel) {
                        try {
                            const img = new Image();
                            img.src = this.scannedImagePreview;
                            await img.decode();

                            const activation = this.netModel.infer(img, true); // Extract 1024 or 1000 embedding vector
                            const arrayData = await activation.data();
                            embeddingVector = Array.from(arrayData);
                            activation.dispose();
                        } catch (e) {
                            console.warn('Ekstraksi embedding browser error:', e);
                        }
                    }

                    this.currentEmbedding = embeddingVector;
                    this.scanProgressMessage = 'Membandingkan dengan Database Sensus PTMA...';

                    // 2. Send to Laravel Backend
                    try {
                        const payload = {
                            embedding: embeddingVector,
                            image_base64: this.scannedImagePreview,
                            kampus: this.filterKampus !== 'Semua' ? this.filterKampus : null,
                            warna: this.filterWarna || null,
                            threshold: 0.45
                        };

                        const res = await fetch(`{{ route('volunteer.census.match') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.matchResult = data;
                            this.hasScanned = true;
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat memproses pemindaian.');
                        }
                    } catch (err) {
                        console.error('Match request error:', err);
                        alert('Gagal menghubungi server untuk pencocokan data.');
                    } finally {
                        this.isScanning = false;
                    }
                },

                // Proceed to Registration with the Scanned Photo
                proceedToCreateWithPhoto() {
                    if (this.scannedImagePreview) {
                        // Store to sessionStorage so create.blade.php can auto-load it
                        sessionStorage.setItem('kucingmu_scanned_photo_wajah', this.scannedImagePreview);
                        if (this.currentEmbedding) {
                            sessionStorage.setItem('kucingmu_scanned_embedding', JSON.stringify(this.currentEmbedding));
                        }
                        if (this.filterKampus && this.filterKampus !== 'Semua') {
                            sessionStorage.setItem('kucingmu_scanned_kampus', this.filterKampus);
                        }
                        if (this.filterWarna) {
                            sessionStorage.setItem('kucingmu_scanned_warna', this.filterWarna);
                        }
                    }
                    window.location.href = `{{ route('volunteer.census.create') }}?from_scan=1`;
                },

                // Silently sync missing embeddings for older records in background
                async backgroundCheckMissingEmbeddings() {
                    if (!this.netModel) return;
                    try {
                        const res = await fetch(`{{ route('volunteer.census.missing-embeddings') }}`);
                        const data = await res.json();
                        if (data.success && data.records && data.records.length > 0) {
                            const syncItems = [];
                            for (const rec of data.records) {
                                if (!rec.foto_wajah_url) continue;
                                try {
                                    const img = new Image();
                                    img.crossOrigin = 'anonymous';
                                    img.src = rec.foto_wajah_url;
                                    await img.decode();
                                    const act = this.netModel.infer(img, true);
                                    const arr = await act.data();
                                    syncItems.push({ id: rec.id, embedding: Array.from(arr) });
                                    act.dispose();
                                } catch (err) {
                                    // Ignore single image load error
                                }
                            }

                            if (syncItems.length > 0) {
                                await fetch(`{{ route('volunteer.census.sync-embeddings') }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ items: syncItems })
                                });
                            }
                        }
                    } catch (e) {
                        // Background silent fail
                    }
                }
            };
        }
    </script>
</x-app-layout>
