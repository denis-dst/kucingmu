<x-app-layout>
    <div class="py-8" x-data="censusForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-teal-700 uppercase tracking-wider">
                        <a href="{{ route('volunteer.census.index') }}" class="hover:underline">Sensus Kucing PTMA</a>
                        <span>/</span>
                        <span class="text-slate-500">Formulir Input Lapangan</span>
                    </div>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Sensus Stray Cat PTMA
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Formulir Pengambilan Data Lapangan Klaster 1: Identifikasi, Welfare, dan Mikro-Habitat.
                    </p>
                </div>

                <div class="flex-shrink-0">
                    <a href="{{ route('volunteer.census.index') }}" class="button-secondary text-xs font-semibold px-4 py-2.5 min-h-[40px] inline-flex items-center gap-1.5">
                        <span>←</span> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1" role="alert">
                    <strong class="font-bold block text-sm">Terdapat kesalahan pengisian formulir:</strong>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Scanner Shortcut Banner -->
            <div class="bg-gradient-to-r from-teal-50 to-sky-50 border border-teal-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center text-xl shrink-0">
                        🔍
                    </div>
                    <div>
                        <h3 class="font-outfit text-xs font-bold text-slate-900">Ingin Memastikan Kucing Ini Belum Pernah Didata?</h3>
                        <p class="text-[11px] text-slate-600">Gunakan fitur pemindai biometrik visual untuk mencocokkan kemiripan wajah kucing dengan arsip sensus.</p>
                    </div>
                </div>
                <a href="{{ route('volunteer.census.scan') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 min-h-[36px] inline-flex items-center gap-1.5 bg-white shrink-0 shadow-xs hover:border-teal-400">
                    <span>📸</span> Buka Scanner Kucing ➔
                </a>
            </div>

            <!-- Main Form Card -->
            <form action="{{ route('volunteer.census.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
                @csrf

                <!-- ══════════════════════════════════════════════════════════ -->
                <!-- A. INFORMASI UMUM & LOKASI -->
                <!-- ══════════════════════════════════════════════════════════ -->
                <div class="content-card space-y-5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Bagian A</span>
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Informasi Umum & Lokasi</h2>
                        </div>
                        <span class="text-2xl" aria-hidden="true">📍</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kampus PTMA -->
                        <div class="min-w-0">
                            <label for="kampus" class="form-label text-xs">
                                Kampus PTMA <span class="text-rose-500">*</span>
                            </label>
                            <select id="kampus" name="kampus" x-model="kampus" @change="fetchNextId()" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="UMY">UMY (Univ. Muhammadiyah Yogyakarta)</option>
                                <option value="UAD">UAD (Univ. Ahmad Dahlan)</option>
                                <option value="UMP">UMP (Univ. Muhammadiyah Purwokerto)</option>
                                <option value="UMS">UMS (Univ. Muhammadiyah Surakarta)</option>
                                <option value="Lainnya">Lainnya (PTMA Lain)</option>
                            </select>
                        </div>

                        <!-- ID Kucing Otomatis -->
                        <div class="min-w-0">
                            <div class="flex items-center justify-between">
                                <label for="id_kucing" class="form-label text-xs">
                                    ID Kucing (Otomatis 3 Digit) <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-teal-700 font-semibold" x-show="loadingId">Menghitung ID...</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" id="id_kucing" name="id_kucing" x-model="idKucing" required placeholder="Contoh: UMY-001" class="form-input text-xs font-mono font-bold text-teal-900 bg-teal-50/50 border-teal-200 flex-1 min-w-0">
                                <button type="button" @click="fetchNextId()" :disabled="loadingId" title="Generate ulang nomor ID" class="btn-action-secondary px-3 py-2.5 text-xs font-bold shrink-0 min-h-[42px] border-teal-200 text-teal-800 bg-teal-50 hover:bg-teal-100 shadow-xs inline-flex items-center gap-1">
                                    <span x-show="!loadingId">↻ Auto</span>
                                    <span x-show="loadingId" class="animate-spin text-teal-700">⟳</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">ID otomatis mengikuti kode kampus dan 3 digit nomor urut berikutnya.</p>
                        </div>

                        <!-- Isian Manual Kampus Lainnya -->
                        <div x-show="kampus === 'Lainnya'" x-transition class="col-span-full bg-amber-50/70 p-3.5 rounded-xl border border-amber-200">
                            <label for="kampus_custom" class="form-label text-xs text-amber-900">
                                Tuliskan Nama Kampus PTMA Lainnya <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="kampus_custom" name="kampus_custom" x-model="kampusCustom" @input.debounce.500ms="fetchNextId()" placeholder="Contoh: UNISA Yogyakarta / UM Magelang" class="form-input text-xs bg-white w-full">
                        </div>

                        <!-- Zona / Sektor Kampus (Autocomplete Select / Combobox) -->
                        <div class="col-span-full min-w-0" x-data="{
                            open: false,
                            search: '{{ old('zona') }}',
                            zones: {{ json_encode($zones ?? ['UMY - Selatan', 'UMY - Utara', 'UMY - Tengah (admisi, AR, maskam, boga)', 'Unires & E8']) }},
                            get filteredZones() {
                                if (!this.search) return this.zones;
                                return this.zones.filter(z => z.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            selectZone(val) {
                                this.search = val;
                                this.open = false;
                            }
                        }" @click.outside="open = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="zona" class="form-label text-xs mb-0">
                                    Zona / Sektor Kampus <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-500">Pilih dari daftar atau ketik zona baru</span>
                            </div>

                            <div class="relative">
                                <div class="relative flex items-center">
                                    <input type="text"
                                           id="zona"
                                           name="zona"
                                           x-model="search"
                                           @focus="open = true"
                                           @input="open = true"
                                           required
                                           autocomplete="off"
                                           placeholder="Pilih atau ketik zona baru (contoh: UMY - Selatan, UMY - Utara)..."
                                           class="form-input text-xs pr-10 w-full">
                                    
                                    <button type="button" @click="open = !open" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs p-1" title="Buka pilihan zona">
                                        <span :class="open ? 'rotate-180' : ''" class="inline-block transition-transform duration-150">▼</span>
                                    </button>
                                </div>

                                <!-- Autocomplete Dropdown Menu -->
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto divide-y divide-slate-100 text-xs"
                                     style="display: none;">
                                    
                                    <!-- Filtered Options List -->
                                    <template x-for="item in filteredZones" :key="item">
                                        <button type="button"
                                                @click="selectZone(item)"
                                                class="w-full text-left px-3.5 py-2.5 hover:bg-teal-50 hover:text-teal-900 transition flex items-center justify-between group">
                                            <span class="font-medium text-slate-800 group-hover:text-teal-900" x-text="item"></span>
                                            <span class="text-[10px] text-teal-700 font-semibold opacity-0 group-hover:opacity-100 transition">Pilih ✓</span>
                                        </button>
                                    </template>

                                    <!-- Custom New Option if not exact match -->
                                    <template x-if="search && !zones.some(z => z.toLowerCase() === search.toLowerCase().trim())">
                                        <button type="button"
                                                @click="open = false"
                                                class="w-full text-left px-3.5 py-2.5 bg-amber-50/70 hover:bg-amber-100 text-amber-900 transition flex items-center gap-2">
                                            <span class="font-bold text-teal-700">➕</span>
                                            <span>Gunakan zona baru: <strong class="underline" x-text="search"></strong></span>
                                        </button>
                                    </template>

                                    <div x-show="filteredZones.length === 0 && (!search || zones.some(z => z.toLowerCase() === search.toLowerCase().trim()))" class="px-3.5 py-2.5 text-slate-400 text-center">
                                        Tidak ada opsi yang cocok. Ketik untuk menambahkan zona baru.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Koordinat GPS (Auto Tagging) -->
                        <div class="col-span-full bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <label class="form-label text-xs mb-0">Koordinat GPS (Auto-Tagging)</label>
                                    <span class="text-[11px] text-slate-500">Otomatis mendeteksi titik koordinat saat formulir dibuka.</span>
                                </div>
                                <button type="button" @click="getGpsLocation(true)" :disabled="gettingGps" class="button-secondary text-xs font-semibold px-3.5 py-1.5 min-h-[36px] inline-flex items-center gap-1.5 bg-white">
                                    <span x-show="!gettingGps">📍</span>
                                    <span x-show="gettingGps" class="animate-spin text-teal-700">⟳</span>
                                    <span x-text="gettingGps ? 'Mencari Titik GPS...' : 'Dapatkan Ulang GPS'"></span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Latitude</span>
                                    <input type="text" id="latitude" name="latitude" x-model="latitude" readonly placeholder="-7.801234" class="form-input text-xs font-mono bg-white w-full">
                                </div>
                                <div>
                                    <span class="text-[11px] font-semibold text-slate-600 block mb-1">Longitude</span>
                                    <input type="text" id="longitude" name="longitude" x-model="longitude" readonly placeholder="110.365432" class="form-input text-xs font-mono bg-white w-full">
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span :class="gpsStatusClass" x-text="gpsStatusMessage" class="font-semibold"></span>
                                <template x-if="latitude && longitude">
                                    <a :href="`https://www.google.com/maps?q=${latitude},${longitude}`" target="_blank" class="text-teal-700 hover:underline font-bold text-[11px]">
                                        Buka di Google Maps ↗
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════ -->
                <!-- B. IDENTIFIKASI INDIVIDU & 4 FOTO -->
                <!-- ══════════════════════════════════════════════════════════ -->
                <div class="content-card space-y-5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Bagian B</span>
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Identifikasi Individu Kucing</h2>
                        </div>
                        <span class="text-2xl" aria-hidden="true">🐱</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Estimasi Usia -->
                        <div class="min-w-0">
                            <label for="usia" class="form-label text-xs">
                                Estimasi Usia <span class="text-rose-500">*</span>
                            </label>
                            <select id="usia" name="usia" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="Kitten">Kitten (&lt; 6 bulan)</option>
                                <option value="Juvenile">Juvenile (6 - 12 bulan)</option>
                                <option value="Adult" selected>Adult (&gt; 1 tahun)</option>
                            </select>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="min-w-0">
                            <label for="gender" class="form-label text-xs">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <select id="gender" name="gender" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="Jantan">Jantan</option>
                                <option value="Betina">Betina</option>
                                <option value="Tidak Teridentifikasi">Tidak Teridentifikasi</option>
                            </select>
                        </div>

                        <!-- Pola Warna Bulu -->
                        <div class="min-w-0 sm:col-span-2 lg:col-span-1">
                            <label for="warna" class="form-label text-xs">
                                Pola Warna Bulu <span class="text-rose-500">*</span>
                            </label>
                            <select id="warna" name="warna" x-model="warna" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="Tabby">Tabby (Garis / Coretan)</option>
                                <option value="Calico">Calico / Tortoiseshell (Belang Tiga)</option>
                                <option value="Black/White">Black / White / Bicolor</option>
                                <option value="Ginger">Ginger / Orange</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Isian Manual Warna Lainnya -->
                        <div x-show="warna === 'Lainnya'" x-transition class="col-span-full bg-amber-50/70 p-3.5 rounded-xl border border-amber-200">
                            <label for="warna_custom" class="form-label text-xs text-amber-900">
                                Deskripsikan Pola Warna Lainnya <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="warna_custom" name="warna_custom" placeholder="Contoh: Solid Grey (Abu-abu polos), Seal Point (Siamese pattern)" class="form-input text-xs bg-white w-full">
                        </div>
                    </div>

                    <!-- 4 Foto Kucing (Kamera & Galeri) -->
                    <div class="border-t border-slate-200 pt-4 space-y-4">
                        <div>
                            <h3 class="font-outfit text-sm font-bold text-slate-900">Dokumentasi Foto Kucing</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Ambil langsung menggunakan kamera perangkat atau unggah dari galeri.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Slot 1: Foto Wajah (Wajib) -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">1. Foto Wajah</span>
                                        <span class="text-[10px] bg-rose-100 text-rose-800 font-bold px-1.5 py-0.5 rounded">Wajib *</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Tampak depan muka kucing</p>
                                </div>

                                <div class="relative aspect-square w-full rounded-lg overflow-hidden bg-white border border-slate-200 flex items-center justify-center">
                                    <template x-if="photos.wajah.preview">
                                        <img :src="photos.wajah.preview" alt="Foto Wajah" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photos.wajah.preview">
                                        <div class="text-center p-3 text-slate-400">
                                            <span class="text-2xl block">😺</span>
                                            <span class="text-[10px]">Belum ada foto</span>
                                        </div>
                                    </template>
                                </div>

                                <div class="space-y-1.5">
                                    <button type="button" @click="openCamera('wajah', 'Foto Wajah Kucing')" class="w-full button-primary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1">
                                        <span>📷</span> Kamera
                                    </button>
                                    <label class="w-full button-secondary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1 cursor-pointer bg-white">
                                        <span>📁</span> Galeri
                                        <input type="file" name="foto_wajah" accept="image/*" class="hidden" @change="handleFileSelect($event, 'wajah')">
                                    </label>
                                    <input type="hidden" name="foto_wajah_cam" x-model="photos.wajah.base64">
                                    <input type="hidden" name="foto_wajah_embedding" x-model="fotoWajahEmbedding">
                                </div>
                            </div>

                            <!-- Slot 2: Foto Tampak Atas (Wajib) -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">2. Tampak Atas</span>
                                        <span class="text-[10px] bg-rose-100 text-rose-800 font-bold px-1.5 py-0.5 rounded">Wajib *</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Punggung / postur dorsal</p>
                                </div>

                                <div class="relative aspect-square w-full rounded-lg overflow-hidden bg-white border border-slate-200 flex items-center justify-center">
                                    <template x-if="photos.atas.preview">
                                        <img :src="photos.atas.preview" alt="Foto Tampak Atas" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photos.atas.preview">
                                        <div class="text-center p-3 text-slate-400">
                                            <span class="text-2xl block">🐈</span>
                                            <span class="text-[10px]">Belum ada foto</span>
                                        </div>
                                    </template>
                                </div>

                                <div class="space-y-1.5">
                                    <button type="button" @click="openCamera('atas', 'Foto Tampak Atas Badan')" class="w-full button-primary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1">
                                        <span>📷</span> Kamera
                                    </button>
                                    <label class="w-full button-secondary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1 cursor-pointer bg-white">
                                        <span>📁</span> Galeri
                                        <input type="file" name="foto_atas" accept="image/*" class="hidden" @change="handleFileSelect($event, 'atas')">
                                    </label>
                                    <input type="hidden" name="foto_atas_cam" x-model="photos.atas.base64">
                                </div>
                            </div>

                            <!-- Slot 3: Foto Samping Kiri (Wajib) -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">3. Samping Kiri</span>
                                        <span class="text-[10px] bg-rose-100 text-rose-800 font-bold px-1.5 py-0.5 rounded">Wajib *</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Profil lateral sisi kiri</p>
                                </div>

                                <div class="relative aspect-square w-full rounded-lg overflow-hidden bg-white border border-slate-200 flex items-center justify-center">
                                    <template x-if="photos.samping.preview">
                                        <img :src="photos.samping.preview" alt="Foto Samping Kiri" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photos.samping.preview">
                                        <div class="text-center p-3 text-slate-400">
                                            <span class="text-2xl block">🐾</span>
                                            <span class="text-[10px]">Belum ada foto</span>
                                        </div>
                                    </template>
                                </div>

                                <div class="space-y-1.5">
                                    <button type="button" @click="openCamera('samping', 'Foto Tampak Samping Kiri')" class="w-full button-primary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1">
                                        <span>📷</span> Kamera
                                    </button>
                                    <label class="w-full button-secondary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1 cursor-pointer bg-white">
                                        <span>📁</span> Galeri
                                        <input type="file" name="foto_samping_kiri" accept="image/*" class="hidden" @change="handleFileSelect($event, 'samping')">
                                    </label>
                                    <input type="hidden" name="foto_samping_kiri_cam" x-model="photos.samping.base64">
                                </div>
                            </div>

                            <!-- Slot 4: Foto Opsional -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">4. Foto Opsional</span>
                                        <span class="text-[10px] bg-slate-200 text-slate-700 font-semibold px-1.5 py-0.5 rounded">Opsional</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Samping kanan / tanda khusus</p>
                                </div>

                                <div class="relative aspect-square w-full rounded-lg overflow-hidden bg-white border border-slate-200 flex items-center justify-center">
                                    <template x-if="photos.opsional.preview">
                                        <img :src="photos.opsional.preview" alt="Foto Opsional" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photos.opsional.preview">
                                        <div class="text-center p-3 text-slate-400">
                                            <span class="text-2xl block">🖼️</span>
                                            <span class="text-[10px]">Belum ada foto</span>
                                        </div>
                                    </template>
                                </div>

                                <div class="space-y-1.5">
                                    <button type="button" @click="openCamera('opsional', 'Foto Tambahan / Opsional')" class="w-full button-primary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1">
                                        <span>📷</span> Kamera
                                    </button>
                                    <label class="w-full button-secondary text-xs font-semibold py-1.5 min-h-[36px] flex items-center justify-center gap-1 cursor-pointer bg-white">
                                        <span>📁</span> Galeri
                                        <input type="file" name="foto_opsional" accept="image/*" class="hidden" @change="handleFileSelect($event, 'opsional')">
                                    </label>
                                    <input type="hidden" name="foto_opsional_cam" x-model="photos.opsional.base64">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════ -->
                <!-- C. KESEJAHTERAAN FISIK (WELFARE) & MORFOMETRI -->
                <!-- ══════════════════════════════════════════════════════════ -->
                <div class="content-card space-y-5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Bagian C</span>
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Kesejahteraan Fisik & Morfometri</h2>
                        </div>
                        <span class="text-2xl" aria-hidden="true">⚖️</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- BCS Score -->
                        <div class="col-span-full min-w-0">
                            <label for="bcs" class="form-label text-xs">
                                Body Condition Score (BCS 1 - 9) <span class="text-rose-500">*</span>
                            </label>
                            <select id="bcs" name="bcs" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="1-3">1-3 (Kurus Kering / Emaciated)</option>
                                <option value="4-5">4-5 (Ramping / Underweight)</option>
                                <option value="6" selected>6 (Ideal / Proporsional)</option>
                                <option value="7-8">7-8 (Kelebihan Berat Badan / Overweight)</option>
                                <option value="9">9 (Obesitas Parah)</option>
                            </select>
                        </div>

                        <!-- Kondisi Klinis / Lesi (Checkboxes) -->
                        <div class="col-span-full">
                            <label class="form-label text-xs">Kondisi Fisik / Lesi (Bisa pilih lebih dari satu)</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                                <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="kondisi_klinis[]" value="Sehat" x-model="klinisSehat" @change="toggleKlinis('Sehat')" class="rounded text-teal-700 focus:ring-teal-700">
                                    Tampak Sehat
                                </label>
                                <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="kondisi_klinis[]" value="Luka Terbuka/Abses" x-model="klinisLuka" @change="toggleKlinis('Gejala')" class="rounded text-teal-700 focus:ring-teal-700">
                                    Luka Terbuka / Abses
                                </label>
                                <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="kondisi_klinis[]" value="Infeksi Mata/Beres" x-model="klinisMata" @change="toggleKlinis('Gejala')" class="rounded text-teal-700 focus:ring-teal-700">
                                    Infeksi Mata / Beres
                                </label>
                                <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="kondisi_klinis[]" value="Botak/Kudis (Alopecia)" x-model="klinisAlopecia" @change="toggleKlinis('Gejala')" class="rounded text-teal-700 focus:ring-teal-700">
                                    Botak / Kudis (Alopecia)
                                </label>
                                <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="kondisi_klinis[]" value="Patah Tulang/Pincang" x-model="klinisPatah" @change="toggleKlinis('Gejala')" class="rounded text-teal-700 focus:ring-teal-700">
                                    Patah Tulang / Pincang
                                </label>
                                <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="kondisi_klinis[]" value="Telinga Robek/Ear Tipping" x-model="klinisEarTip" @change="toggleKlinis('Gejala')" class="rounded text-teal-700 focus:ring-teal-700">
                                    Telinga Robek / Ear Tipping
                                </label>
                            </div>
                        </div>

                        <!-- Kondisi Fisik Lainnya -->
                        <div class="col-span-full">
                            <label for="kondisi_klinis_custom" class="form-label text-xs">
                                Kondisi Fisik Lainnya (Tuliskan jika ada)
                            </label>
                            <input type="text" id="kondisi_klinis_custom" name="kondisi_klinis_custom" value="{{ old('kondisi_klinis_custom') }}" placeholder="Contoh: Ekor bengkok, buta satu mata, kuku patah..." class="form-input text-xs w-full">
                        </div>

                        <!-- Panjang Badan Total -->
                        <div class="min-w-0">
                            <label for="panjang_badan_cm" class="form-label text-xs">
                                Panjang Badan Total (cm)
                            </label>
                            <input type="number" step="0.1" id="panjang_badan_cm" name="panjang_badan_cm" value="{{ old('panjang_badan_cm') }}" placeholder="Contoh: 45.0 (Ujung hidung ke pangkal ekor)" class="form-input text-xs w-full">
                        </div>

                        <!-- Panjang Ekor -->
                        <div class="min-w-0">
                            <label for="panjang_ekor_cm" class="form-label text-xs">
                                Panjang Ekor (cm)
                            </label>
                            <input type="number" step="0.1" id="panjang_ekor_cm" name="panjang_ekor_cm" value="{{ old('panjang_ekor_cm') }}" placeholder="Contoh: 20.5 (Pangkal ke ujung ekor)" class="form-input text-xs w-full">
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════ -->
                <!-- D. KUALITAS MIKRO-HABITAT -->
                <!-- ══════════════════════════════════════════════════════════ -->
                <div class="content-card space-y-5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Bagian D</span>
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Kualitas Mikro-Habitat</h2>
                        </div>
                        <span class="text-2xl" aria-hidden="true">🌱</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Jarak ke Sumber Pakan -->
                        <div class="min-w-0">
                            <label for="jarak_pakan" class="form-label text-xs">
                                Jarak ke Sumber Pakan (Meter)
                            </label>
                            <input type="number" min="0" id="jarak_pakan" name="jarak_pakan" value="{{ old('jarak_pakan') }}" placeholder="Contoh: 5" class="form-input text-xs w-full">
                        </div>

                        <!-- Jenis Sumber Pakan Utama -->
                        <div class="min-w-0">
                            <label for="jenis_pakan" class="form-label text-xs">
                                Sumber Pakan Utama <span class="text-rose-500">*</span>
                            </label>
                            <select id="jenis_pakan" name="jenis_pakan" x-model="jenisPakan" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="Tempat Sampah Terbuka">Tempat Sampah Terbuka / Bak Sampah</option>
                                <option value="Limbah Kantin/Dapur" selected>Limbah Kantin / Dapur Kampus</option>
                                <option value="Pemberian Civitas Akademika (Acak)">Pemberian Civitas Akademika (Acak/Sporadis)</option>
                                <option value="Feeding Station Komunitas">Feeding Station Komunitas (Terjadwal/Rutin)</option>
                                <option value="Mangsa Alami (Tikus/Burung/Serangga)">Mangsa Alami (Tikus/Burung/Serangga)</option>
                                <option value="Sisa Makanan Asrama/Kos">Sisa Makanan Area Asrama/Kos</option>
                                <option value="Tidak Diketahui / Tidak Terlihat">Tidak Diketahui / Tidak Terlihat</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Tingkat Ancaman / Bahaya Lingkungan -->
                        <div class="min-w-0 sm:col-span-2 lg:col-span-1">
                            <label for="ancaman" class="form-label text-xs">
                                Ancaman Lingkungan <span class="text-rose-500">*</span>
                            </label>
                            <select id="ancaman" name="ancaman" x-model="ancaman" required class="form-input text-xs w-full max-w-full truncate cursor-pointer">
                                <option value="Lalu Lintas Kendaraan Padat (Jalan Utama/Parkiran)">Lalu Lintas Kendaraan Padat (Jalan Utama/Parkiran)</option>
                                <option value="Ancaman Hewan Lain (Anjing Liar/Kucing Dominan)">Ancaman Hewan Lain (Anjing Liar/Kucing Dominan)</option>
                                <option value="Aktivitas Konstruksi / Pembangunan">Aktivitas Konstruksi / Pembangunan</option>
                                <option value="Potensi Kekerasan Manusia (Pengusiran Kasar)">Potensi Kekerasan Manusia (Pengusiran Kasar)</option>
                                <option value="Cuaca Ekstrem tanpa Shelter yang Layak">Cuaca Ekstrem tanpa Shelter yang Layak</option>
                                <option value="Area Pembuangan Limbah Kimia/Berbahaya">Area Pembuangan Limbah Kimia/Berbahaya</option>
                                <option value="Relatif Aman (Zona Minim Gangguan)" selected>Relatif Aman (Zona Minim Gangguan)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Isian Manual Jenis Pakan Lainnya -->
                        <div x-show="jenisPakan === 'Lainnya'" x-transition class="col-span-full bg-amber-50/70 p-3.5 rounded-xl border border-amber-200">
                            <label for="jenis_pakan_custom" class="form-label text-xs text-amber-900">
                                Deskripsikan Sumber Pakan Lainnya <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="jenis_pakan_custom" name="jenis_pakan_custom" placeholder="Contoh: Diberi rutin oleh staf kantor TU" class="form-input text-xs bg-white w-full">
                        </div>

                        <!-- Isian Manual Ancaman Lainnya -->
                        <div x-show="ancaman === 'Lainnya'" x-transition class="col-span-full bg-amber-50/70 p-3.5 rounded-xl border border-amber-200">
                            <label for="ancaman_custom" class="form-label text-xs text-amber-900">
                                Deskripsikan Ancaman Lingkungan Lainnya <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="ancaman_custom" name="ancaman_custom" placeholder="Contoh: Saluran drainase terbuka berarus deras saat hujan" class="form-input text-xs bg-white w-full">
                        </div>
                    </div>

                    <!-- Catatan Tambahan (Full Width Card) -->
                    <div class="w-full pt-2">
                        <label for="catatan" class="form-label text-xs">Catatan Tambahan Surveyor (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3" placeholder="Tuliskan catatan khusus perilaku, lokasi tersembunyi, atau kondisi lingkungan saat sensus..." class="form-input text-xs w-full">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
                    <a href="{{ route('volunteer.census.index') }}" class="w-full sm:w-auto button-secondary text-xs font-semibold px-6 py-3 min-h-[44px] inline-flex items-center justify-center text-center">
                        Batal
                    </a>
                    <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto button-primary text-xs font-bold px-8 py-3 min-h-[44px] inline-flex items-center justify-center gap-2 shadow-xs">
                        <span x-show="!isSubmitting">💾 Simpan Data Sensus Kucing</span>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                            <span class="animate-spin text-white">⟳</span> Menyimpan Data...
                        </span>
                    </button>
                </div>
            </form>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- LIVE WEBCAM MODAL -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div x-show="cameraModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white w-full max-w-lg rounded-2xl border border-slate-200 overflow-hidden shadow-2xl space-y-4 p-5" @click.outside="closeCamera()">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <div>
                            <h3 class="font-outfit text-base font-bold text-slate-900" x-text="activeCameraTitle">Ambil Foto</h3>
                            <p class="text-xs text-slate-500">Posisikan kamera dengan jelas ke objek kucing.</p>
                        </div>
                        <button type="button" @click="closeCamera()" class="text-slate-400 hover:text-slate-700 text-sm font-bold p-1">✕</button>
                    </div>

                    <!-- Video Viewfinder -->
                    <div class="relative aspect-video rounded-xl bg-slate-900 overflow-hidden flex items-center justify-center border border-slate-800">
                        <video id="censusWebcamVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                        <canvas id="censusWebcamCanvas" class="hidden"></canvas>
                        
                        <div x-show="cameraLoading" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-white text-xs space-y-2">
                            <span class="animate-spin text-2xl text-teal-400">⟳</span>
                            <span>Menghubungkan ke perangkat kamera...</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-2">
                        <button type="button" @click="closeCamera()" class="button-secondary text-xs font-semibold px-4 py-2.5 min-h-[40px]">
                            Batal
                        </button>
                        <button type="button" @click="capturePhoto()" class="button-primary text-xs font-bold px-6 py-2.5 min-h-[40px] inline-flex items-center gap-2">
                            <span>📸</span> Jepret Foto
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function censusForm() {
            return {
                kampus: 'UMY',
                kampusCustom: '',
                idKucing: 'UMY-001',
                loadingId: false,

                latitude: '',
                longitude: '',
                gettingGps: false,
                gpsStatusMessage: 'Menunggu inisialisasi GPS...',
                gpsStatusClass: 'text-slate-500',

                warna: 'Tabby',
                jenisPakan: 'Limbah Kantin/Dapur',
                ancaman: 'Relatif Aman (Zona Minim Gangguan)',

                klinisSehat: true,
                klinisLuka: false,
                klinisMata: false,
                klinisAlopecia: false,
                klinisPatah: false,
                klinisEarTip: false,

                isSubmitting: false,
                fotoWajahEmbedding: '',

                // Photos state
                photos: {
                    wajah: { preview: null, base64: '' },
                    atas: { preview: null, base64: '' },
                    samping: { preview: null, base64: '' },
                    opsional: { preview: null, base64: '' },
                },

                // Camera Modal
                cameraModalOpen: false,
                activeCameraSlot: null,
                activeCameraTitle: '',
                cameraLoading: false,
                mediaStream: null,

                init() {
                    this.fetchNextId();
                    this.getGpsLocation(false);
                    this.checkScannerPrefill();
                },

                // Check prefill data passed from scanner page
                checkScannerPrefill() {
                    try {
                        const scannedPhoto = sessionStorage.getItem('kucingmu_scanned_photo_wajah');
                        const scannedEmb = sessionStorage.getItem('kucingmu_scanned_embedding');
                        const scannedKampus = sessionStorage.getItem('kucingmu_scanned_kampus');
                        const scannedWarna = sessionStorage.getItem('kucingmu_scanned_warna');

                        if (scannedPhoto) {
                            this.photos.wajah.preview = scannedPhoto;
                            this.photos.wajah.base64 = scannedPhoto;
                            sessionStorage.removeItem('kucingmu_scanned_photo_wajah');
                        }

                        if (scannedEmb) {
                            this.fotoWajahEmbedding = scannedEmb;
                            sessionStorage.removeItem('kucingmu_scanned_embedding');
                        }

                        if (scannedKampus) {
                            this.kampus = scannedKampus;
                            this.fetchNextId();
                            sessionStorage.removeItem('kucingmu_scanned_kampus');
                        }

                        if (scannedWarna) {
                            this.warna = scannedWarna;
                            sessionStorage.removeItem('kucingmu_scanned_warna');
                        }
                    } catch (e) {
                        console.warn('Gagal membaca prefill scanner:', e);
                    }
                },

                // Auto Tagging GPS
                getGpsLocation(isManual = false) {
                    this.gettingGps = true;
                    this.gpsStatusMessage = isManual ? 'Sedang merefresh koordinat GPS...' : 'Auto-tagging GPS mendeteksi lokasi...';
                    this.gpsStatusClass = 'text-teal-700';

                    if (!navigator.geolocation) {
                        this.gettingGps = false;
                        this.gpsStatusMessage = 'Geolocation tidak didukung oleh peramban ini.';
                        this.gpsStatusClass = 'text-amber-700';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.latitude = position.coords.latitude.toFixed(7);
                            this.longitude = position.coords.longitude.toFixed(7);
                            const acc = Math.round(position.coords.accuracy);
                            this.gettingGps = false;
                            this.gpsStatusMessage = `Koordinat berhasil didapatkan (Akurasi: ±${acc}m)`;
                            this.gpsStatusClass = 'text-emerald-700';
                        },
                        (error) => {
                            this.gettingGps = false;
                            this.gpsStatusMessage = 'Izin GPS belum diberikan atau sinyal lemah. Klik Dapatkan Ulang GPS.';
                            this.gpsStatusClass = 'text-slate-500';
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                },

                // Fetch Next Sequential ID
                async fetchNextId() {
                    this.loadingId = true;
                    try {
                        const params = new URLSearchParams({
                            kampus: this.kampus,
                            kampus_custom: this.kampusCustom
                        });
                        const res = await fetch(`{{ route('volunteer.census.next-id') }}?${params.toString()}`);
                        const data = await res.json();
                        if (data.success && data.id_kucing) {
                            this.idKucing = data.id_kucing;
                        }
                    } catch (e) {
                        console.error('Failed to fetch next ID', e);
                    } finally {
                        this.loadingId = false;
                    }
                },

                // Toggle Clinical Checkboxes
                toggleKlinis(type) {
                    if (type === 'Sehat' && this.klinisSehat) {
                        this.klinisLuka = false;
                        this.klinisMata = false;
                        this.klinisAlopecia = false;
                        this.klinisPatah = false;
                        this.klinisEarTip = false;
                    } else if (type === 'Gejala') {
                        if (this.klinisLuka || this.klinisMata || this.klinisAlopecia || this.klinisPatah || this.klinisEarTip) {
                            this.klinisSehat = false;
                        } else {
                            this.klinisSehat = true;
                        }
                    }
                },

                // Handle File Input Selection
                handleFileSelect(event, slot) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.photos[slot].preview = e.target.result;
                            this.photos[slot].base64 = ''; // Cleared so file takes precedence
                        };
                        reader.readAsDataURL(file);
                    }
                },

                // Open Live Camera
                async openCamera(slot, title) {
                    this.activeCameraSlot = slot;
                    this.activeCameraTitle = title;
                    this.cameraModalOpen = true;
                    this.cameraLoading = true;

                    try {
                        this.mediaStream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
                        });
                        const video = document.getElementById('censusWebcamVideo');
                        video.srcObject = this.mediaStream;
                        video.onloadedmetadata = () => {
                            video.play();
                            this.cameraLoading = false;
                        };
                    } catch (err) {
                        this.cameraLoading = false;
                        alert('Tidak dapat mengakses kamera: ' + err.message + '. Silakan gunakan tombol Unggah Galeri.');
                        this.closeCamera();
                    }
                },

                // Capture Snapshot from Camera
                capturePhoto() {
                    const video = document.getElementById('censusWebcamVideo');
                    const canvas = document.getElementById('censusWebcamCanvas');
                    if (!video || !canvas) return;

                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    if (this.activeCameraSlot && this.photos[this.activeCameraSlot]) {
                        this.photos[this.activeCameraSlot].preview = dataUrl;
                        this.photos[this.activeCameraSlot].base64 = dataUrl;
                    }

                    this.closeCamera();
                },

                // Close Camera Modal
                closeCamera() {
                    if (this.mediaStream) {
                        this.mediaStream.getTracks().forEach(track => track.stop());
                        this.mediaStream = null;
                    }
                    this.cameraModalOpen = false;
                    this.activeCameraSlot = null;
                }
            };
        }
    </script>
</x-app-layout>
