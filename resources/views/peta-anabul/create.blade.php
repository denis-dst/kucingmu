<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('peta-anabul.index') }}"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                title="Kembali ke Peta">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-800 tracking-tight">Petakan Anabul Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Catat persebaran kucing peliharaan atau kucing liar di sekitar lingkungan Anda.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="petaAnabulForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>⚠️</span> Mohon lengkapi data berikut:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-2 text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('peta-anabul.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section 1: Kategori Kepemilikan (Pembeda Pin Peta) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>🏷️</span> Kategori Anabul
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pilihlah apakah ini kucing peliharaan Anda sendiri atau kucing liar yang Anda jumpai di lingkungan sekitar.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <!-- Stray Cat Card -->
                        <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                            :class="ownershipType === 'stray' ? 'border-orange-600 bg-orange-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="ownership_type" value="stray" x-model="ownershipType" class="mt-1 text-orange-600 focus:ring-orange-500">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🐾</span>
                                    <span class="font-bold text-sm text-slate-800">Kucing Liar di Sekitar</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Kucing jalanan, kucing komunitas, atau kucing tanpa pemilik tetap di lingkungan sekitar (Pin Oranye).
                                </p>
                            </div>
                        </label>

                        <!-- Pet Cat Card -->
                        <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                            :class="ownershipType === 'pet' ? 'border-teal-700 bg-teal-50/50 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="ownership_type" value="pet" x-model="ownershipType" class="mt-1 text-teal-700 focus:ring-teal-600">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🏠</span>
                                    <span class="font-bold text-sm text-slate-800">Kucing Peliharaan Saya</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Kucing peliharaan milik Anda yang terdaftar pada akun KucingMU (Pin Hijau Kehijauan / Teal).
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Dropdown Pilih Dari Kucing Saya (Jika Kucing Peliharaan) -->
                    <div x-show="ownershipType === 'pet'" x-cloak class="p-4 rounded-xl bg-teal-50/60 border border-teal-200/80 space-y-2">
                        <label class="form-label text-teal-900">Pilih dari Kucing Saya yang Terdaftar</label>
                        @if($myCats->isEmpty())
                            <p class="text-xs text-slate-600">
                                Anda belum memiliki data kucing peliharaan di profil. Anda dapat mengisi nama dan ciri anabul secara langsung di bawah ini.
                            </p>
                        @else
                            <select name="cat_id" x-model="selectedCatId" @change="onCatSelected($event.target.value)"
                                class="form-input bg-white text-xs sm:text-sm">
                                <option value="">- Isi Data Kucing Baru atau Pilih di Bawah -</option>
                                @foreach($myCats as $cat)
                                    <option value="{{ $cat->id }}"
                                        data-name="{{ $cat->name }}"
                                        data-breed="{{ $cat->breed }}"
                                        data-color="{{ $cat->color }}"
                                        data-gender="{{ $cat->gender }}"
                                        {{ (isset($selectedCat) && $selectedCat->id === $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->breed ?: 'Kucing' }} - {{ $cat->color ?: 'Warna tidak tercatat' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-teal-700">
                                Memilih anabul Anda akan otomatis mengisi nama, jenis kelamin, dan foto anabul.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Section 2: Foto Anabul (Multiple Photos) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📸</span> Foto Anabul
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Unggah foto yang jelas agar anabul dapat dikenali oleh komunitas. Foto pertama akan menjadi foto utama (thumbnail).
                            </p>
                        </div>
                    </div>

                    <!-- File input -->
                    <div>
                        <label class="block w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 hover:border-teal-600 bg-slate-50/60 p-6 text-center transition">
                            <input type="file" name="photos[]" multiple accept="image/*" @change="previewFiles($event)" class="sr-only">
                            <div class="space-y-2">
                                <span class="text-3xl">📷</span>
                                <div class="text-xs sm:text-sm font-bold text-slate-700">
                                    Klik di sini untuk memilih foto dari galeri
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    Format: JPG, PNG, WEBP (Maksimal 10 MB per foto, otomatis dikompresi)
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Photos Preview Grid -->
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

                <!-- Section 3: Identitas & Ciri Fisik -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>🐱</span> Identitas & Ciri Fisik Anabul
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama / Kode Panggilan -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Nama Panggilan atau Kode Identifikasi <span class="text-rose-600">*</span></label>
                            <input type="text" name="name" x-model="catName" required
                                placeholder="Contoh: Si Belang, Oyen Pasar, Si Manis"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Warna Bulu -->
                        <div>
                            <label class="form-label">Warna Bulu Dominan</label>
                            <input type="text" name="coat_color" x-model="coatColor"
                                placeholder="Contoh: Oranye, Hitam Putih, Belang Tiga"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Pola Bulu -->
                        <div>
                            <label class="form-label">Pola Bulu</label>
                            <select name="coat_pattern" class="form-input text-xs sm:text-sm">
                                <option value="">- Pilih Pola Bulu -</option>
                                <option value="Tabby / Loreng">Tabby / Loreng</option>
                                <option value="Tuxedo">Tuxedo</option>
                                <option value="Calico / Kembang Telon">Calico / Kembang Telon</option>
                                <option value="Tortoiseshell (Tortie)">Tortoiseshell (Tortie)</option>
                                <option value="Solid / Polos Satu Warna">Solid / Polos Satu Warna</option>
                                <option value="Bicolor / Dua Warna">Bicolor / Dua Warna</option>
                                <option value="Colorpoint">Colorpoint</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
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

                        <!-- Perkiraan Usia -->
                        <div>
                            <label class="form-label">Perkiraan Usia</label>
                            <select name="age_estimate" class="form-input text-xs sm:text-sm">
                                <option value="">- Pilih Perkiraan Usia -</option>
                                <option value="Anak (Kitten 0 - 6 bulan)">Anak (Kitten 0 - 6 bulan)</option>
                                <option value="Remaja (Junior 7 - 12 bulan)">Remaja (Junior 7 - 12 bulan)</option>
                                <option value="Dewasa (Adult 1 - 7 tahun)">Dewasa (Adult 1 - 7 tahun)</option>
                                <option value="Senior (Diatas 7 tahun)">Senior (Diatas 7 tahun)</option>
                            </select>
                        </div>

                        <!-- Ciri Fisik Khas -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Ciri Fisik Khas</label>
                            <textarea name="characteristics" rows="3"
                                placeholder="Jelaskan bentuk ekor (lurus/bengkok/bundel), warna mata, bentuk telinga (ear tip jika ada), bekas luka atau kalung..."
                                class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Kondisi Kesehatan & Sterilisasi -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>🩺</span> Kondisi Kesehatan & Penanganan
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kondisi Anabul -->
                        <div>
                            <label class="form-label">Kondisi Saat Ini <span class="text-rose-600">*</span></label>
                            <select name="condition" required class="form-input text-xs sm:text-sm">
                                <option value="sehat">Sehat & Aktif</option>
                                <option value="sakit">Sakit (Flu, Diare, Kulit, dll)</option>
                                <option value="terluka">Terluka (Patah Tulang, Robek)</option>
                                <option value="butuh_pertolongan">Butuh Pertolongan Segera (Darurat)</option>
                                <option value="tidak_diketahui">Belum Dapat Dipastikan</option>
                            </select>
                        </div>

                        <!-- Status Steril -->
                        <div>
                            <label class="form-label">Status Sterilisasi <span class="text-rose-600">*</span></label>
                            <select name="sterilization_status" required class="form-input text-xs sm:text-sm">
                                <option value="tidak_diketahui">Belum Diketahui</option>
                                <option value="sudah">Sudah Disteril (Ada Ear Tip / Kartu)</option>
                                <option value="belum">Belum Disteril</option>
                            </select>
                        </div>

                        <!-- Waktu Terakhir Terlihat -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Waktu Terakhir Terlihat</label>
                            <input type="datetime-local" name="last_seen_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Catatan Deskripsi -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Catatan & Deskripsi Tambahan</label>
                            <textarea name="description" rows="2"
                                placeholder="Informasi tambahan mengenai kebiasaan anabul, sumber pakan di lokasi, atau penanganan sebelumnya..."
                                class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Lokasi Spasial GIS (Interactive Leaflet Map & GPS) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📍</span> Titik Lokasi Peta (GIS)
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Tentukan lokasi anabul dengan tombol GPS atau geser pin langsung pada peta.
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
                        <div id="location-picker-map" style="height: 340px; width: 100%;"></div>

                        <div x-show="isGeocoding" x-cloak
                             class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-xl text-xs font-semibold text-teal-800 shadow-md border border-teal-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-600 animate-ping"></span>
                            <span>Mendeteksi alamat...</span>
                        </div>
                    </div>

                    <!-- Coordinates Display Box -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div><span class="text-slate-400">Lat:</span> <span class="font-mono font-bold text-slate-700" x-text="coords.lat || '-'"></span></div>
                                <div><span class="text-slate-400">Lng:</span> <span class="font-mono font-bold text-slate-700" x-text="coords.lng || '-'"></span></div>
                            </div>
                            <span class="text-slate-400 text-[11px]" x-show="coords.lat">
                                *Pin dapat digeser secara manual
                            </span>
                        </div>
                        <div x-show="formattedAddress" class="text-slate-600 border-t border-slate-200/60 pt-1.5">
                            <span class="font-semibold text-slate-700">Alamat Terdeteksi:</span> <span x-text="formattedAddress"></span>
                        </div>
                    </div>

                    <!-- Hidden Inputs for Coordinates & Address -->
                    <input type="hidden" name="latitude" :value="coords.lat">
                    <input type="hidden" name="longitude" :value="coords.lng">
                    <input type="hidden" name="formatted_address" :value="formattedAddress">

                    <!-- Administrative Selectors -->
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
                            <input type="text" name="village" x-model="villageName" placeholder="Contoh: Prawirodirjan" class="form-input text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('peta-anabul.index') }}" class="button-secondary min-h-[44px] px-5 text-sm">
                        Batal
                    </a>
                    <button type="submit" class="button-primary min-h-[44px] px-7 text-sm font-bold shadow-md">
                        Simpan Data ke Peta Anabul
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function petaAnabulForm() {
            return {
                ownershipType: '{{ old('ownership_type', 'stray') }}',
                selectedCatId: '{{ old('cat_id', isset($selectedCat) ? $selectedCat->id : '') }}',
                catName: '{{ old('name', isset($selectedCat) ? $selectedCat->name : '') }}',
                coatColor: '{{ old('coat_color', isset($selectedCat) ? $selectedCat->color : '') }}',
                catGender: '{{ old('gender', isset($selectedCat) ? $selectedCat->gender : 'tidak_diketahui') }}',
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
                isGeocoding: false,

                init() {
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                initMap() {
                    const defaultLat = parseFloat(this.coords.lat) || -7.7956;
                    const defaultLng = parseFloat(this.coords.lng) || 110.3695;

                    this.map = L.map('location-picker-map').setView([defaultLat, defaultLng], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors | KucingMU'
                    }).addTo(this.map);

                    this.marker = L.marker([defaultLat, defaultLng], {
                        draggable: true
                    }).addTo(this.map);

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
                    this.reverseGeocode(lat, lng);
                },

                useCurrentGps() {
                    if (!navigator.geolocation) {
                        alert('Peramban Anda tidak mendukung deteksi GPS.');
                        return;
                    }

                    this.isGeocoding = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            this.coords.lat = lat.toFixed(7);
                            this.coords.lng = lng.toFixed(7);
                            this.map.setView([lat, lng], 16);
                            this.marker.setLatLng([lat, lng]);
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
                    const select = document.querySelector(`select[name="cat_id"] option[value="${catId}"]`);
                    if (select) {
                        this.catName = select.dataset.name || '';
                        this.coatColor = select.dataset.color || '';
                        this.catGender = select.dataset.gender || 'tidak_diketahui';
                    }
                }
            };
        }
    </script>
</x-app-layout>

