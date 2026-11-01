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
                    Tambahkan lokasi anabul ke Peta Spasial GIS KucingMU.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="petaAnabulForm(@js($myCatsFormatted))">
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
                            <span>🏷️</span> Pilih Kategori Anabul
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pilihlah apakah ini kucing peliharaan milik Anda yang sudah terdaftar atau kucing liar di lingkungan sekitar.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <!-- Pet Cat Card -->
                        <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                            :class="ownershipType === 'pet' ? 'border-teal-700 bg-teal-50/60 shadow-xs ring-2 ring-teal-600/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="ownership_type" value="pet" x-model="ownershipType" class="mt-1 text-teal-700 focus:ring-teal-600">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🏠</span>
                                    <span class="font-bold text-sm text-slate-800">Kucing Peliharaan Saya</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Kucing peliharaan Anda yang terdaftar pada akun KucingMU (Pin Hijau Kehijauan / Teal). Identitas & foto otomatis terhubung.
                                </p>
                            </div>
                        </label>

                        <!-- Stray Cat Card -->
                        <label class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition select-none"
                            :class="ownershipType === 'stray' ? 'border-orange-600 bg-orange-50/60 shadow-xs ring-2 ring-orange-600/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="radio" name="ownership_type" value="stray" x-model="ownershipType" class="mt-1 text-orange-600 focus:ring-orange-500">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🐾</span>
                                    <span class="font-bold text-sm text-slate-800">Kucing Liar di Sekitar</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Kucing jalanan atau kucing komunitas tanpa pemilik di sekitar lingkungan Anda (Pin Oranye). Masukkan foto dan ciri fisiknya.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- SUB-SECTION KHUSUS KUCING PELIHARAAN: PILIH DARI KUCING SAYA -->
                    <div x-show="ownershipType === 'pet'" x-cloak class="space-y-4 pt-2">
                        @if($myCats->isEmpty())
                            <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200/90 text-center space-y-3">
                                <span class="text-3xl">🐱</span>
                                <div class="font-bold text-sm text-slate-800">Belum Ada Kucing Peliharaan Terdaftar</div>
                                <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed">
                                    Anda belum memiliki data kucing peliharaan pada akun Anda. Daftarkan profil anabul Anda terlebih dahulu, atau beralih ke Kucing Liar di Sekitar.
                                </p>
                                <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                                    <a href="{{ route('dashboard') }}" class="button-primary min-h-[44px] px-4 text-xs font-bold inline-flex items-center gap-1.5">
                                        <span>+</span>
                                        <span>Daftarkan Kucing di Dashboard Dulu</span>
                                    </a>
                                    <button type="button" @click="ownershipType = 'stray'" class="button-secondary min-h-[44px] px-4 text-xs font-semibold">
                                        Petakan Kucing Liar di Sekitar
                                    </button>
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="form-label font-bold text-slate-800 mb-2 block">
                                    Pilih Kucing Peliharaan Anda yang Ingin Dipetakan <span class="text-rose-600">*</span>
                                </label>
                                <input type="hidden" name="cat_id" :value="selectedCatId">

                                <!-- Grid Pilihan Kucing -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($myCats as $cat)
                                        <div @click="selectCat({{ $cat->id }})"
                                             class="p-3.5 rounded-2xl border-2 cursor-pointer transition flex items-center gap-3.5 select-none"
                                             :class="selectedCatId == {{ $cat->id }} ? 'border-teal-700 bg-teal-50/70 shadow-xs ring-2 ring-teal-600/30' : 'border-slate-200 hover:border-teal-400 bg-white'">
                                            <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span class="font-bold text-sm text-slate-800 truncate">{{ $cat->name }}</span>
                                                    <span x-show="selectedCatId == {{ $cat->id }}" class="text-teal-700 text-xs font-black">✓</span>
                                                </div>
                                                <div class="text-[11px] text-slate-500 truncate">{{ $cat->breed ?: 'Domestik' }} &bull; {{ $cat->color ?: 'Warna -' }}</div>
                                                <div class="text-[10px] text-teal-700 font-semibold flex items-center gap-1.5 mt-0.5">
                                                    <span>{{ in_array(strtolower($cat->gender ?? ''), ['male', 'jantan']) ? '♂ Jantan' : '♀ Betina' }}</span>
                                                    @if($cat->unique_code)
                                                        <span class="bg-teal-100 text-teal-800 px-1 py-0.2 rounded font-mono">{{ $cat->unique_code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Cat Identity Confirmation Card (Sync Notice) -->
                            <div x-show="selectedCatData" x-cloak
                                 class="p-4 rounded-2xl bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <img :src="selectedCatData?.photo_url" class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-xs shrink-0">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 bg-teal-100/90 px-2 py-0.5 rounded-full">Profil Kucing Terdaftar</span>
                                            <span x-show="selectedCatData?.unique_code" class="text-xs font-mono font-bold text-teal-900" x-text="selectedCatData?.unique_code"></span>
                                        </div>
                                        <div class="text-base font-outfit font-black text-slate-900 mt-0.5 truncate" x-text="selectedCatData?.name"></div>
                                        <div class="text-xs text-slate-600 flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                            <span x-text="selectedCatData?.breed"></span>
                                            <span>&bull;</span>
                                            <span x-text="selectedCatData?.color"></span>
                                            <span>&bull;</span>
                                            <span x-text="selectedCatData?.gender_label"></span>
                                            <span x-show="selectedCatData?.age_text && selectedCatData?.age_text !== '-'">&bull;</span>
                                            <span x-show="selectedCatData?.age_text && selectedCatData?.age_text !== '-'" x-text="selectedCatData?.age_text"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-teal-800 font-semibold bg-white px-3 py-1.5 rounded-xl border border-teal-200/80 shadow-2xs flex items-center gap-1.5 self-stretch sm:self-auto justify-center shrink-0">
                                    <span>✓</span>
                                    <span>Identitas & Foto Tersinkronisasi</span>
                                </div>
                            </div>

                            <!-- Notification banner explaining why manual form is omitted -->
                            <div class="p-3.5 rounded-xl bg-teal-50/70 border border-teal-200/70 text-xs text-teal-800 flex items-center gap-2.5">
                                <span class="text-base">💡</span>
                                <span>
                                    <strong>Tidak perlu mengisi ulang identitas:</strong> Nama, ras, warna bulu, jenis kelamin, usia, dan foto anabul diambil langsung dari profil terdaftar. Anda hanya perlu menentukan titik tagging GIS pada peta di bawah ini.
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SECTION 2: FOTO ANABUL (HANYA UNTUK KUCING LIAR) -->
                <div x-show="ownershipType === 'stray'" x-cloak class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📸</span> Foto Anabul Liar
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Unggah foto anabul yang Anda jumpai agar dapat dikenali oleh komunitas. Foto pertama akan menjadi foto utama (thumbnail).
                            </p>
                        </div>
                    </div>

                    <!-- File input -->
                    <div>
                        <label class="block w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 hover:border-orange-500 bg-slate-50/60 p-6 text-center transition">
                            <input type="file" name="photos[]" multiple accept="image/*" @change="previewFiles($event)" class="sr-only">
                            <div class="space-y-2">
                                <span class="text-3xl">📷</span>
                                <div class="text-xs sm:text-sm font-bold text-slate-700">
                                    Klik di sini untuk memilih foto anabul liar
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
                                <span x-show="index === 0" class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-md bg-orange-600 text-white text-[9px] font-bold">
                                    Foto Utama
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SECTION 3: IDENTITAS & CIRI FISIK (HANYA UNTUK KUCING LIAR) -->
                <div x-show="ownershipType === 'stray'" x-cloak class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>🐱</span> Identitas & Ciri Fisik Kucing Liar
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Catat ciri visual anabul agar mudah diidentifikasi oleh relawan dan komunitas.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama / Kode Panggilan -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Nama Panggilan atau Kode Identifikasi <span class="text-rose-600">*</span></label>
                            <input type="text" name="name" x-model="catName"
                                placeholder="Contoh: Si Belang Pasar, Oyen Parkiran, Manis"
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
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" x-model="catGender" class="form-input text-xs sm:text-sm">
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
                                placeholder="Jelaskan bentuk ekor (lurus/bengkok/bundel), warna mata, bentuk telinga (ear tip jika ada), bekas luka atau ciri khusus lainnya..."
                                class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: KONDISI KESEHATAN (DITAMPILKAN LENGKAP UNTUK KUCING LIAR, OPSIONAL/SEDERHANA UNTUK PELIHARAAN) -->
                <div x-show="ownershipType === 'stray'" x-cloak class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                            <span>🩺</span> Kondisi Kesehatan & Waktu Terlihat
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kondisi Anabul -->
                        <div>
                            <label class="form-label">Kondisi Anabul Saat Ini</label>
                            <select name="condition" class="form-input text-xs sm:text-sm">
                                <option value="sehat">Sehat & Aktif</option>
                                <option value="sakit">Sakit (Flu, Diare, Kulit, dll)</option>
                                <option value="terluka">Terluka (Patah Tulang, Robek)</option>
                                <option value="butuh_pertolongan">Butuh Pertolongan Segera (Darurat)</option>
                                <option value="tidak_diketahui">Belum Dapat Dipastikan</option>
                            </select>
                        </div>

                        <!-- Status Steril -->
                        <div>
                            <label class="form-label">Status Sterilisasi</label>
                            <select name="sterilization_status" class="form-input text-xs sm:text-sm">
                                <option value="tidak_diketahui">Belum Diketahui</option>
                                <option value="sudah">Sudah Disteril (Ada Ear Tip)</option>
                                <option value="belum">Belum Disteril</option>
                            </select>
                        </div>

                        <!-- Waktu Terakhir Terlihat -->
                        <div class="sm:col-span-2">
                            <label class="form-label">Waktu Terakhir Terlihat</label>
                            <input type="datetime-local" name="last_seen_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                                class="form-input text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: TITIK LOKASI SPASIAL GIS (INTERACTIVE LEAFLET MAP & GPS) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📍</span> Titik Tagging Lokasi Peta (GIS) <span class="text-rose-600">*</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="ownershipType === 'pet' ? 'Tentukan titik lokasi rumah atau tempat tinggal anabul Anda di peta.' : 'Tentukan titik lokasi di mana Anda menjumpai kucing liar ini.'">
                            </p>
                        </div>
                        <button type="button" @click="useCurrentGps()"
                            class="button-secondary min-h-[44px] px-3.5 py-1.5 text-xs font-bold inline-flex items-center gap-1.5 self-start sm:self-auto">
                            <span>📡</span>
                            <span>Gunakan GPS Saya</span>
                        </button>
                    </div>

                    <!-- Map Container -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-300 shadow-inner">
                        <div id="location-picker-map" style="height: 360px; width: 100%;"></div>

                        <div x-show="isGeocoding" x-cloak
                             class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur px-3 py-1.5 rounded-xl text-xs font-semibold text-teal-800 shadow-md border border-teal-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-600 animate-ping"></span>
                            <span>Mendeteksi alamat...</span>
                        </div>
                    </div>

                    <!-- Coordinates Display Box -->
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div><span class="text-slate-400">Lat:</span> <span class="font-mono font-bold text-slate-800" x-text="coords.lat || '-'"></span></div>
                                <div><span class="text-slate-400">Lng:</span> <span class="font-mono font-bold text-slate-800" x-text="coords.lng || '-'"></span></div>
                            </div>
                            <span class="text-teal-700 font-semibold text-[11px] flex items-center gap-1" x-show="coords.lat">
                                <span>👆</span> Pin peta dapat digeser langsung ke titik yang tepat
                            </span>
                        </div>
                        <div x-show="formattedAddress" class="text-slate-600 border-t border-slate-200/60 pt-1.5 text-xs leading-relaxed">
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

                    <!-- Catatan Lokasi Tambahan -->
                    <div class="pt-2">
                        <label class="form-label">Catatan Tambahan Lokasi (Opsional)</label>
                        <textarea name="description" rows="2"
                            :placeholder="ownershipType === 'pet' ? 'Contoh: Rumah pemilik di dalam perumahan, kucing indoor / bermain di halaman' : 'Contoh: Sering berteduh di teras ruko, sumber pakan dari warung sekitar'"
                            class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('peta-anabul.index') }}" class="button-secondary min-h-[44px] px-5 text-sm">
                        Batal
                    </a>
                    <button type="submit"
                        :disabled="ownershipType === 'pet' && !selectedCatId"
                        class="button-primary min-h-[44px] px-7 text-sm font-bold shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="ownershipType === 'pet' ? 'Simpan Tagging GIS Kucing ke Peta' : 'Simpan Data ke Peta Anabul'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function petaAnabulForm(registeredCats = []) {
            return {
                ownershipType: '{{ old('ownership_type', isset($selectedCat) ? 'pet' : ($myCats->isNotEmpty() ? 'pet' : 'stray')) }}',
                registeredCats: registeredCats,
                selectedCatId: '{{ old('cat_id', isset($selectedCat) ? $selectedCat->id : ($myCats->first()?->id ?? '')) }}',
                selectedCatData: null,
                catName: '{{ old('name', '') }}',
                coatColor: '{{ old('coat_color', '') }}',
                catGender: '{{ old('gender', 'tidak_diketahui') }}',
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
                    if (this.selectedCatId) {
                        this.selectCat(this.selectedCatId);
                    }
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                selectCat(catId) {
                    this.selectedCatId = catId;
                    const found = this.registeredCats.find(c => String(c.id) === String(catId));
                    if (found) {
                        this.selectedCatData = found;
                        this.catName = found.name;
                        this.coatColor = found.color;
                        this.catGender = (found.gender === 'male' || found.gender === 'jantan') ? 'jantan' : ((found.gender === 'female' || found.gender === 'betina') ? 'betina' : 'tidak_diketahui');
                    } else {
                        this.selectedCatData = null;
                    }
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
                }
            };
        }
    </script>
</x-app-layout>
