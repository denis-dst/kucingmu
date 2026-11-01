<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('temu-anabul.show', $temuAnabul->id) }}"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                title="Batal dan kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-800 tracking-tight">Edit Laporan: {{ $temuAnabul->cat_name }}</h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Perbarui informasi atau status laporan anabul.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="temuAnabulEditForm()">
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

            <form action="{{ route('temu-anabul.update', $temuAnabul->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section 1: Informasi Status Laporan -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>📢</span> Status Laporan
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Status Laporan <span class="text-rose-600">*</span></label>
                            <select name="status" required class="form-input text-xs sm:text-sm">
                                <option value="active" {{ old('status', $temuAnabul->status) === 'active' ? 'selected' : '' }}>Aktif (Dalam Pencarian / Menunggu Pemilik)</option>
                                <option value="reunited" {{ old('status', $temuAnabul->status) === 'reunited' ? 'selected' : '' }}>✨ Selesai / Berhasil Reuni</option>
                                <option value="closed" {{ old('status', $temuAnabul->status) === 'closed' ? 'selected' : '' }}>Laporan Ditutup</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Kondisi Fisik <span class="text-rose-600">*</span></label>
                            <select name="condition" required class="form-input text-xs sm:text-sm">
                                <option value="sehat" {{ old('condition', $temuAnabul->condition) === 'sehat' ? 'selected' : '' }}>Sehat & Terawat</option>
                                <option value="terawat" {{ old('condition', $temuAnabul->condition) === 'terawat' ? 'selected' : '' }}>Terawat</option>
                                <option value="lemas_kelaparan" {{ old('condition', $temuAnabul->condition) === 'lemas_kelaparan' ? 'selected' : '' }}>Lemas / Kelaparan</option>
                                <option value="sakit" {{ old('condition', $temuAnabul->condition) === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="terluka" {{ old('condition', $temuAnabul->condition) === 'terluka' ? 'selected' : '' }}>Terluka</option>
                                <option value="tidak_diketahui" {{ old('condition', $temuAnabul->condition) === 'tidak_diketahui' ? 'selected' : '' }}>Belum Dipastikan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Identitas Anabul -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>🐱</span> Identitas & Ciri Fisik
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="form-label">Judul Laporan <span class="text-rose-600">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $temuAnabul->title) }}" required class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Nama Kucing <span class="text-rose-600">*</span></label>
                            <input type="text" name="cat_name" value="{{ old('cat_name', $temuAnabul->cat_name) }}" required class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Jenis Kelamin <span class="text-rose-600">*</span></label>
                            <select name="gender" required class="form-input text-xs sm:text-sm">
                                <option value="tidak_diketahui" {{ old('gender', $temuAnabul->gender) === 'tidak_diketahui' ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="jantan" {{ old('gender', $temuAnabul->gender) === 'jantan' ? 'selected' : '' }}>Jantan (♂)</option>
                                <option value="betina" {{ old('gender', $temuAnabul->gender) === 'betina' ? 'selected' : '' }}>Betina (♀)</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Warna Bulu</label>
                            <input type="text" name="coat_color" value="{{ old('coat_color', $temuAnabul->coat_color) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Pola Bulu</label>
                            <input type="text" name="coat_pattern" value="{{ old('coat_pattern', $temuAnabul->coat_pattern) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Perkiraan Usia</label>
                            <input type="text" name="age_estimate" value="{{ old('age_estimate', $temuAnabul->age_estimate) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Info Imbalan / Reward</label>
                            <input type="text" name="reward_info" value="{{ old('reward_info', $temuAnabul->reward_info) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Ciri Fisik Khas <span class="text-rose-600">*</span></label>
                            <textarea name="distinctive_features" rows="3" required class="form-input text-xs sm:text-sm leading-relaxed">{{ old('distinctive_features', $temuAnabul->distinctive_features) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Kronologi / Catatan</label>
                            <textarea name="notes" rows="2" class="form-input text-xs sm:text-sm leading-relaxed">{{ old('notes', $temuAnabul->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kontak Pelapor -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>📞</span> Kontak Pelapor
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Kontak <span class="text-rose-600">*</span></label>
                            <input type="text" name="contact_name" value="{{ old('contact_name', $temuAnabul->contact_name) }}" required class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Nomor WhatsApp / Telepon <span class="text-rose-600">*</span></label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $temuAnabul->contact_phone) }}" required class="form-input text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Foto Tambahan -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>📸</span> Foto Anabul
                    </h2>

                    @if($temuAnabul->photos->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($temuAnabul->photos as $p)
                                <div class="relative aspect-square rounded-xl overflow-hidden border border-slate-200">
                                    <img src="{{ $p->url }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2">
                        <label class="form-label">Unggah Foto Baru Tambahan</label>
                        <input type="file" name="new_photos[]" multiple accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    </div>
                </div>

                <!-- Section 5: Lokasi Kejadian (GIS) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>📍</span> Lokasi Kejadian (GIS)
                    </h2>

                    <div class="relative overflow-hidden rounded-2xl border border-slate-300 shadow-inner">
                        <div id="temu-edit-map" style="height: 300px; width: 100%;"></div>
                    </div>

                    <input type="hidden" name="latitude" :value="coords.lat">
                    <input type="hidden" name="longitude" :value="coords.lng">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Provinsi</label>
                            <select name="province" class="form-input text-xs sm:text-sm">
                                <option value="">- Pilih Provinsi -</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}" {{ old('province', $temuAnabul->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Kabupaten / Kota</label>
                            <input type="text" name="city" value="{{ old('city', $temuAnabul->city) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="district" value="{{ old('district', $temuAnabul->district) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Kelurahan / Desa</label>
                            <input type="text" name="village" value="{{ old('village', $temuAnabul->village) }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Alamat Lengkap</label>
                            <input type="text" name="formatted_address" value="{{ old('formatted_address', $temuAnabul->formatted_address) }}" class="form-input text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Submit & Delete Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <form action="{{ route('temu-anabul.destroy', $temuAnabul->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-danger min-h-[44px]">
                            Hapus Laporan
                        </button>
                    </form>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('temu-anabul.show', $temuAnabul->id) }}" class="button-secondary min-h-[44px] px-5 text-sm">
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
        function temuAnabulEditForm() {
            return {
                coords: {
                    lat: '{{ old('latitude', $temuAnabul->latitude) }}',
                    lng: '{{ old('longitude', $temuAnabul->longitude) }}',
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

                    this.map = L.map('temu-edit-map').setView([lat, lng], 15);

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

