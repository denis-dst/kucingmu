<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('temu-anabul.index') }}"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                title="Kembali ke Temu Anabul">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-900 tracking-tight">Buat Laporan Temu Anabul</h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Bantu mempertemukan anabul dengan pemiliknya melalui pelaporan yang akurat.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="temuAnabulCreateForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>⚠️</span> Mohon lengkapi isian berikut:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-2 text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('temu-anabul.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section 1: Kategori Laporan (Hilang vs Ditemukan) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>📢</span> Kategori Laporan
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Lost Option -->
                        <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                            :class="reportType === 'lost' ? 'border-rose-600 bg-rose-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="type" value="lost" x-model="reportType" class="mt-1 text-rose-600 focus:ring-rose-500">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🚨</span>
                                    <span class="font-bold text-sm text-slate-900">Kucing Saya Hilang</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Saya adalah pemilik yang mencari anabul yang lepas atau hilang dari rumah / lingkungan.
                                </p>
                            </div>
                        </label>

                        <!-- Found Option -->
                        <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                            :class="reportType === 'found' ? 'border-indigo-600 bg-indigo-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="type" value="found" x-model="reportType" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🐱</span>
                                    <span class="font-bold text-sm text-slate-900">Saya Menemukan Kucing</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Saya menemukan anabul yang tersesat atau disorientasi dan ingin membantu menemukan pemilik aslinya.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Auto-fill from My Cats (for Lost type) -->
                    <div x-show="reportType === 'lost'" x-cloak class="p-4 rounded-xl bg-teal-50/60 border border-teal-200/80 space-y-2">
                        <label class="form-label text-teal-900">Pilih dari Kucing Saya yang Terdaftar (Otomatis Isi Data)</label>
                        @if($myCats->isEmpty())
                            <p class="text-xs text-slate-600">
                                Anda belum memiliki data kucing terdaftar di profil. Anda dapat mengisi formulir secara manual di bawah.
                            </p>
                        @else
                            <select name="cat_id" x-model="selectedCatId" @change="onCatSelected($event.target.value)"
                                class="form-input bg-white text-xs sm:text-sm">
                                <option value="">- Pilih Kucing Saya atau Isi Manual -</option>
                                @foreach($myCats as $cat)
                                    <option value="{{ $cat->id }}"
                                        data-name="{{ $cat->name }}"
                                        data-color="{{ $cat->color }}"
                                        data-gender="{{ $cat->gender }}"
                                        data-breed="{{ $cat->breed }}"
                                        {{ (isset($selectedCat) && $selectedCat->id === $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->breed ?: 'Kucing' }} - {{ $cat->color ?: 'Warna tidak tercatat' }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                <!-- Section 2: Foto Anabul -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>📸</span> Foto Anabul Terbaru
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Unggah foto yang jelas menampilkan wajah, warna bulu, dan tanda khas anabul.
                        </p>
                    </div>

                    <div>
                        <label class="block w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 hover:border-teal-600 bg-slate-50/60 p-6 text-center transition">
                            <input type="file" name="photos[]" multiple accept="image/*" @change="previewFiles($event)" class="sr-only">
                            <div class="space-y-2">
                                <span class="text-3xl">📷</span>
                                <div class="text-xs sm:text-sm font-bold text-slate-700">
                                    Pilih foto anabul dari galeri perangkat Anda
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    Format: JPG, PNG, WEBP (Maksimal 10 MB per foto, otomatis dikompresi)
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Previews -->
                    <div x-show="previewUrls.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        <template x-for="(url, index) in previewUrls" :key="index">
                            <div class="relative aspect-square rounded-xl overflow-hidden border border-slate-200 shadow-2xs group">
                                <img :src="url" class="w-full h-full object-cover">
                                <span x-show="index === 0" class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-teal-700 text-white text-[9px] font-bold">
                                    Foto Utama
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Section 3: Informasi & Ciri Fisik -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>📝</span> Informasi & Ciri Anabul
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Judul Laporan -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Judul Laporan Singkat <span class="text-rose-600">*</span></label>
                            <input type="text" name="title" required
                                placeholder="Contoh: Dicari Kucing Persia Abu-abu Lepas di Area UMY / Ditemukan Kucing Berkalung di Gondomanan"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Nama Anabul -->
                        <div>
                            <label class="form-label">Nama Kucing / Panggilan Sementara <span class="text-rose-600">*</span></label>
                            <input type="text" name="cat_name" x-model="catName" required
                                placeholder="Contoh: Milo / Si Belang"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="form-label">Jenis Kelamin <span class="text-rose-600">*</span></label>
                            <select name="gender" x-model="catGender" required class="form-input text-xs sm:text-sm">
                                <option value="tidak_diketahui">Belum Diketahui</option>
                                <option value="jantan">Jantan (♂)</option>
                                <option value="betina">Betina (♀)</option>
                            </select>
                        </div>

                        <!-- Warna Bulu -->
                        <div>
                            <label class="form-label">Warna Bulu</label>
                            <input type="text" name="coat_color" x-model="coatColor"
                                placeholder="Contoh: Oranye belang putih, Belang tiga"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Pola Bulu -->
                        <div>
                            <label class="form-label">Pola Bulu</label>
                            <input type="text" name="coat_pattern"
                                placeholder="Contoh: Tabby, Tuxedo, Calico, Polos"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Perkiraan Usia -->
                        <div>
                            <label class="form-label">Perkiraan Usia</label>
                            <input type="text" name="age_estimate"
                                placeholder="Contoh: 1 tahun / Dewasa / Anak-anak"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Kondisi Saat Kejadian -->
                        <div>
                            <label class="form-label">Kondisi Fisik Saat Dilihat / Ditemukan <span class="text-rose-600">*</span></label>
                            <select name="condition" required class="form-input text-xs sm:text-sm">
                                <option value="sehat">Sehat & Bersih</option>
                                <option value="terawat">Terawat (Tampak Kucing Rumahan)</option>
                                <option value="lemas_kelaparan">Lemas / Tampak Kelaparan</option>
                                <option value="sakit">Sakit</option>
                                <option value="terluka">Terluka / Perlu Perawatan Medis</option>
                                <option value="tidak_diketahui">Belum Dapat Dipastikan</option>
                            </select>
                        </div>

                        <!-- Ciri Fisik Khas (Wajib) -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Ciri-Ciri Khas Fisik <span class="text-rose-600">*</span></label>
                            <textarea name="distinctive_features" rows="3" required
                                placeholder="Tuliskan ciri fisik yang spesifik: warna dan bentuk kalung/lonceng, bentuk ekor (lurus/bengkok/pendek), bekas luka atau operasi steril, tanda khusus di bulu atau telinga..."
                                class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                        </div>

                        <!-- Status Pengamanan (Jika Temuan) -->
                        <div class="sm:col-span-2" x-show="reportType === 'found'" x-cloak>
                            <label class="form-label">Status Keberadaan Anabul Saat Ini</label>
                            <select name="holding_status" class="form-input text-xs sm:text-sm">
                                <option value="kept_by_finder">Saat ini saya amankan di rumah sementara</option>
                                <option value="at_clinic_shelter">Dititipkan di klinik hewan / shelter</option>
                                <option value="left_at_location">Masih berada / berkeliaran di lokasi penemuan</option>
                                <option value="unknown">Lainnya / Belum ditentukan</option>
                            </select>
                        </div>

                        <!-- Imbalan (Jika Hilang) -->
                        <div class="sm:col-span-2" x-show="reportType === 'lost'" x-cloak>
                            <label class="form-label">Informasi Imbalan / Reward (Opsional)</label>
                            <input type="text" name="reward_info"
                                placeholder="Contoh: Ada imbalan bagi yang menemukan dengan selamat (opsional)"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Tanggal & Waktu Kejadian -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Waktu Kejadian (Terakhir Terlihat / Ditemukan) <span class="text-rose-600">*</span></label>
                            <input type="datetime-local" name="incident_date" value="{{ now()->format('Y-m-d\TH:i') }}" required
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Catatan Deskripsi -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Kronologi / Catatan Tambahan</label>
                            <textarea name="notes" rows="2"
                                placeholder="Ceritakan bagaimana anabul bisa lepas atau situasi saat pertama kali ditemukan..."
                                class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Lokasi Spasial Kejadian & Radius Pencarian (GIS) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📍</span> Lokasi Kejadian di Peta (GIS)
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Tandai lokasi persis anabul terakhir terlihat atau tempat Anda menemukannya.
                            </p>
                        </div>
                        <button type="button" @click="useCurrentGps()"
                            class="button-secondary min-h-[38px] px-3.5 py-1.5 text-xs font-bold inline-flex items-center gap-1.5 self-start sm:self-auto">
                            <span>📡</span>
                            <span>Gunakan GPS Saya</span>
                        </button>
                    </div>

                    <!-- Map Container -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-300 shadow-inner">
                        <div id="temu-location-map" style="height: 320px; width: 100%;"></div>

                        <div x-show="isGeocoding" x-cloak
                             class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-xl text-xs font-semibold text-teal-800 shadow-md border border-teal-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-600 animate-ping"></span>
                            <span>Mendeteksi alamat...</span>
                        </div>
                    </div>

                    <!-- Radius Slider for Lost Cats -->
                    <div x-show="reportType === 'lost'" class="p-3.5 bg-rose-50/60 border border-rose-200/80 rounded-xl space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-rose-900">Perkiraan Radius Wilayah Pencarian:</span>
                            <span class="font-mono font-bold text-rose-700" x-text="searchRadiusKm + ' km'"></span>
                        </div>
                        <input type="range" name="search_radius_km" min="1" max="20" step="0.5" x-model="searchRadiusKm"
                            @input="updateRadiusCircle()" class="w-full accent-rose-600">
                        <p class="text-[11px] text-rose-700">
                            Lingkaran putus-putus di peta menunjukkan perkiraan jangkauan kucing menjelajah.
                        </p>
                    </div>

                    <!-- Hidden inputs -->
                    <input type="hidden" name="latitude" :value="coords.lat">
                    <input type="hidden" name="longitude" :value="coords.lng">
                    <input type="hidden" name="formatted_address" :value="formattedAddress">

                    <!-- Administrative fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="form-label">Provinsi</label>
                            <select name="province" x-model="selectedProvince" class="form-input text-xs sm:text-sm">
                                <option value="">- Pilih Provinsi -</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}">{{ $prov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Kabupaten / Kota</label>
                            <input type="text" name="city" x-model="cityName" placeholder="Contoh: Kota Yogyakarta" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="district" x-model="districtName" placeholder="Contoh: Gondomanan" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kelurahan / Desa</label>
                            <input type="text" name="village" x-model="villageName" placeholder="Contoh: Ngupasan" class="form-input text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Section 5: Kontak Pelapor -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>📞</span> Kontak Pelapor
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kontak ini digunakan oleh penemu atau pemilik anabul untuk menghubungi Anda.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Kontak <span class="text-rose-600">*</span></label>
                            <input type="text" name="contact_name" value="{{ old('contact_name', Auth::user()->name) }}" required class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Nomor WhatsApp / Telepon <span class="text-rose-600">*</span></label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', Auth::user()->phone) }}" required
                                placeholder="Contoh: 08123456789"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-slate-700">
                                <input type="checkbox" name="is_contact_public" value="1" checked class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Tampilkan tombol WhatsApp langsung pada halaman laporan agar mudah dihubungi</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('temu-anabul.index') }}" class="button-secondary min-h-[44px] px-5 text-sm">
                        Batal
                    </a>
                    <button type="submit" class="button-primary min-h-[44px] px-7 text-sm font-bold shadow-md">
                        Publikasikan Laporan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function temuAnabulCreateForm() {
            return {
                reportType: '{{ old('type', $initialType ?? 'lost') }}',
                selectedCatId: '{{ old('cat_id', isset($selectedCat) ? $selectedCat->id : '') }}',
                catName: '{{ old('cat_name', isset($selectedCat) ? $selectedCat->name : '') }}',
                coatColor: '{{ old('coat_color', isset($selectedCat) ? $selectedCat->color : '') }}',
                catGender: '{{ old('gender', isset($selectedCat) ? $selectedCat->gender : 'tidak_diketahui') }}',
                searchRadiusKm: {{ old('search_radius_km', 3.0) }},
                coords: {
                    lat: '{{ old('latitude', -7.7956) }}',
                    lng: '{{ old('longitude', 110.3695) }}',
                },
                formattedAddress: '{{ old('formatted_address', '') }}',
                selectedProvince: '{{ old('province', '') }}',
                cityName: '{{ old('city', '') }}',
                districtName: '{{ old('district', '') }}',
                villageName: '{{ old('village', '') }}',
                previewUrls: [],
                map: null,
                marker: null,
                radiusCircle: null,
                isGeocoding: false,

                init() {
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                initMap() {
                    const defaultLat = parseFloat(this.coords.lat) || -7.7956;
                    const defaultLng = parseFloat(this.coords.lng) || 110.3695;

                    this.map = L.map('temu-location-map').setView([defaultLat, defaultLng], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap | KucingMU'
                    }).addTo(this.map);

                    this.marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.map);

                    this.updateRadiusCircle();

                    this.marker.on('dragend', (e) => {
                        const newPos = e.target.getLatLng();
                        this.updateCoordinates(newPos.lat, newPos.lng);
                    });

                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.updateCoordinates(e.latlng.lat, e.latlng.lng);
                    });
                },

                updateCoordinates(lat, lng) {
                    this.coords.lat = lat.toFixed(7);
                    this.coords.lng = lng.toFixed(7);
                    this.updateRadiusCircle();
                    this.reverseGeocode(lat, lng);
                },

                updateRadiusCircle() {
                    if (!this.map || !this.coords.lat || !this.coords.lng) return;
                    if (this.radiusCircle) {
                        this.map.removeLayer(this.radiusCircle);
                    }

                    if (this.reportType === 'lost' && this.searchRadiusKm) {
                        this.radiusCircle = L.circle([parseFloat(this.coords.lat), parseFloat(this.coords.lng)], {
                            radius: parseFloat(this.searchRadiusKm) * 1000,
                            color: '#e11d48',
                            fillColor: '#e11d48',
                            fillOpacity: 0.1,
                            dashArray: '4, 4',
                            weight: 2
                        }).addTo(this.map);
                    }
                },

                useCurrentGps() {
                    if (!navigator.geolocation) {
                        alert('Peramban tidak mendukung GPS.');
                        return;
                    }

                    this.isGeocoding = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            this.coords.lat = lat.toFixed(7);
                            this.coords.lng = lng.toFixed(7);
                            this.map.setView([lat, lng], 15);
                            this.marker.setLatLng([lat, lng]);
                            this.updateRadiusCircle();
                            this.reverseGeocode(lat, lng);
                        },
                        (err) => {
                            this.isGeocoding = false;
                            alert('Gagal mendeteksi lokasi GPS: ' + err.message);
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                },

                reverseGeocode(lat, lng) {
                    this.isGeocoding = true;
                    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;

                    fetch(url, { headers: { 'Accept-Language': 'id' } })
                        .then(res => res.json())
                        .then(data => {
                            this.isGeocoding = false;
                            if (data && data.address) {
                                this.formattedAddress = data.display_name || '';
                                const addr = data.address;
                                if (addr.state) this.selectedProvince = addr.state;
                                if (addr.city || addr.town || addr.municipality || addr.county) {
                                    this.cityName = addr.city || addr.town || addr.municipality || addr.county;
                                }
                                if (addr.suburb || addr.district) {
                                    this.districtName = addr.suburb || addr.district;
                                }
                                if (addr.village || addr.quarter) {
                                    this.villageName = addr.village || addr.quarter;
                                }
                            }
                        })
                        .catch(() => {
                            this.isGeocoding = false;
                        });
                },

                previewFiles(e) {
                    const files = e.target.files;
                    this.previewUrls = [];
                    if (!files) return;

                    Array.from(files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            this.previewUrls.push(event.target.result);
                        };
                        reader.readAsDataURL(file);
                    });
                },

                onCatSelected(catId) {
                    if (!catId) return;
                    const opt = document.querySelector(`select[name="cat_id"] option[value="${catId}"]`);
                    if (opt) {
                        this.catName = opt.dataset.name || '';
                        this.coatColor = opt.dataset.color || '';
                        this.catGender = opt.dataset.gender || 'tidak_diketahui';
                    }
                }
            };
        }
    </script>
</x-app-layout>

