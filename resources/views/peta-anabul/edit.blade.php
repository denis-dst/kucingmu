<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('peta-anabul.show', $petaAnabul->id) }}"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                title="Batal dan kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-800 tracking-tight">Edit Data: {{ $petaAnabul->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Perbarui informasi, status keberadaan, atau posisi koordinat anabul.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="petaAnabulEditForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>⚠️</span> Terdapat kesalahan input:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-2 text-xs">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('peta-anabul.update', $petaAnabul->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section 1: Kategori Kepemilikan -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>🏷️</span> Kategori & Status Anabul
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kategori Kepemilikan <span class="text-rose-600">*</span></label>
                            <select name="ownership_type" required class="form-input text-xs sm:text-sm">
                                <option value="stray" {{ old('ownership_type', $petaAnabul->ownership_type) === 'stray' ? 'selected' : '' }}>🐾 Kucing Liar / Sekitar (Pin Oranye)</option>
                                <option value="pet" {{ old('ownership_type', $petaAnabul->ownership_type) === 'pet' ? 'selected' : '' }}>🏠 Kucing Peliharaan Member (Pin Teal)</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Status Keberadaan <span class="text-rose-600">*</span></label>
                            <select name="status" required class="form-input text-xs sm:text-sm">
                                <option value="aktif" {{ old('status', $petaAnabul->status) === 'aktif' ? 'selected' : '' }}>Aktif di Lokasi</option>
                                <option value="sudah_ditangani" {{ old('status', $petaAnabul->status) === 'sudah_ditangani' ? 'selected' : '' }}>Sudah Ditangani / Dirawat</option>
                                <option value="tidak_terlihat_lagi" {{ old('status', $petaAnabul->status) === 'tidak_terlihat_lagi' ? 'selected' : '' }}>Tidak Terlihat Lagi</option>
                                <option value="arsip" {{ old('status', $petaAnabul->status) === 'arsip' ? 'selected' : '' }}>Diarsipkan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Identitas & Ciri Fisik -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>🐱</span> Identitas & Ciri Fisik
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="form-label">Nama Panggilan atau Kode <span class="text-rose-600">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $petaAnabul->name) }}" required class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Warna Bulu</label>
                            <input type="text" name="coat_color" value="{{ old('coat_color', $petaAnabul->coat_color) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Pola Bulu</label>
                            <input type="text" name="coat_pattern" value="{{ old('coat_pattern', $petaAnabul->coat_pattern) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Jenis Kelamin <span class="text-rose-600">*</span></label>
                            <select name="gender" required class="form-input text-xs sm:text-sm">
                                <option value="tidak_diketahui" {{ old('gender', $petaAnabul->gender) === 'tidak_diketahui' ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="jantan" {{ old('gender', $petaAnabul->gender) === 'jantan' ? 'selected' : '' }}>Jantan (♂)</option>
                                <option value="betina" {{ old('gender', $petaAnabul->gender) === 'betina' ? 'selected' : '' }}>Betina (♀)</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Perkiraan Usia</label>
                            <input type="text" name="age_estimate" value="{{ old('age_estimate', $petaAnabul->age_estimate) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kondisi Kesehatan <span class="text-rose-600">*</span></label>
                            <select name="condition" required class="form-input text-xs sm:text-sm">
                                <option value="sehat" {{ old('condition', $petaAnabul->condition) === 'sehat' ? 'selected' : '' }}>Sehat & Aktif</option>
                                <option value="sakit" {{ old('condition', $petaAnabul->condition) === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="terluka" {{ old('condition', $petaAnabul->condition) === 'terluka' ? 'selected' : '' }}>Terluka</option>
                                <option value="butuh_pertolongan" {{ old('condition', $petaAnabul->condition) === 'butuh_pertolongan' ? 'selected' : '' }}>Butuh Pertolongan Segera</option>
                                <option value="tidak_diketahui" {{ old('condition', $petaAnabul->condition) === 'tidak_diketahui' ? 'selected' : '' }}>Belum Dipastikan</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Status Sterilisasi <span class="text-rose-600">*</span></label>
                            <select name="sterilization_status" required class="form-input text-xs sm:text-sm">
                                <option value="tidak_diketahui" {{ old('sterilization_status', $petaAnabul->sterilization_status) === 'tidak_diketahui' ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="sudah" {{ old('sterilization_status', $petaAnabul->sterilization_status) === 'sudah' ? 'selected' : '' }}>Sudah Disteril</option>
                                <option value="belum" {{ old('sterilization_status', $petaAnabul->sterilization_status) === 'belum' ? 'selected' : '' }}>Belum Disteril</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Ciri Fisik Khas</label>
                            <textarea name="characteristics" rows="3" class="form-input text-xs sm:text-sm leading-relaxed">{{ old('characteristics', $petaAnabul->characteristics) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Deskripsi Tambahan</label>
                            <textarea name="description" rows="2" class="form-input text-xs sm:text-sm leading-relaxed">{{ old('description', $petaAnabul->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Foto Tambahan -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>📸</span> Foto Anabul
                    </h2>

                    <!-- Existing photos -->
                    @if($petaAnabul->photos->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($petaAnabul->photos as $p)
                                <div class="relative aspect-square rounded-xl overflow-hidden border border-slate-200">
                                    <img src="{{ $p->url }}" class="w-full h-full object-cover">
                                    @if($p->is_primary)
                                        <span class="absolute top-1 left-1 bg-teal-700 text-white text-[9px] px-1.5 py-0.5 rounded font-bold">
                                            Utama
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2">
                        <label class="form-label">Unggah Foto Tambahan</label>
                        <input type="file" name="new_photos[]" multiple accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    </div>
                </div>

                <!-- Section 4: Lokasi Spasial GIS -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>📍</span> Lokasi Peta (GIS)
                    </h2>

                    <div class="relative overflow-hidden rounded-2xl border border-slate-300 shadow-inner">
                        <div id="location-edit-map" style="height: 300px; width: 100%;"></div>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1 text-xs">
                        <div class="flex items-center gap-3">
                            <div><span class="text-slate-400">Lat:</span> <span class="font-mono font-bold" x-text="coords.lat"></span></div>
                            <div><span class="text-slate-400">Lng:</span> <span class="font-mono font-bold" x-text="coords.lng"></span></div>
                        </div>
                        <div class="text-slate-500 text-[11px]">*Geser pin di peta untuk memperbarui koordinat.</div>
                    </div>

                    <input type="hidden" name="latitude" :value="coords.lat">
                    <input type="hidden" name="longitude" :value="coords.lng">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Provinsi</label>
                            <select name="province" class="form-input text-xs sm:text-sm">
                                <option value="">- Pilih Provinsi -</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}" {{ old('province', $petaAnabul->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Kabupaten / Kota</label>
                            <input type="text" name="city" value="{{ old('city', $petaAnabul->city) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="district" value="{{ old('district', $petaAnabul->district) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kelurahan / Desa</label>
                            <input type="text" name="village" value="{{ old('village', $petaAnabul->village) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Alamat Lengkap</label>
                            <input type="text" name="formatted_address" value="{{ old('formatted_address', $petaAnabul->formatted_address) }}" class="form-input text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Submit & Delete Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <form action="{{ route('peta-anabul.destroy', $petaAnabul->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data anabul ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-danger min-h-[44px]">
                            Hapus Data Anabul
                        </button>
                    </form>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('peta-anabul.show', $petaAnabul->id) }}" class="button-secondary min-h-[44px] px-5 text-sm">
                            Batal
                        </a>
                        <button type="submit" class="button-primary min-h-[44px] px-7 text-sm font-bold shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function petaAnabulEditForm() {
            return {
                coords: {
                    lat: '{{ old('latitude', $petaAnabul->latitude) }}',
                    lng: '{{ old('longitude', $petaAnabul->longitude) }}',
                },
                map: null,
                marker: null,

                init() {
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                initMap() {
                    const lat = parseFloat(this.coords.lat);
                    const lng = parseFloat(this.coords.lng);

                    this.map = L.map('location-edit-map').setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap | KucingMU'
                    }).addTo(this.map);

                    this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                    this.marker.on('dragend', (e) => {
                        const newPos = e.target.getLatLng();
                        this.coords.lat = newPos.lat.toFixed(7);
                        this.coords.lng = newPos.lng.toFixed(7);
                    });

                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.coords.lat = e.latlng.lat.toFixed(7);
                        this.coords.lng = e.latlng.lng.toFixed(7);
                    });
                }
            };
        }
    </script>
</x-app-layout>

