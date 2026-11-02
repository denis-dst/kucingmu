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
                                    <span class="font-bold text-sm text-slate-900">Kucing Hilang</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Melaporkan anabul yang lepas atau hilang dari rumah / lingkungan (milik sendiri atau membantu orang lain).
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

                    <!-- Sub-Pilihan Sumber Data (Khusus Laporan Kucing Hilang) -->
                    <div x-show="reportType === 'lost'" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-1.5">
                                <span>🐾</span> Pilihan Sumber Data Kucing yang Hilang:
                            </label>
                            <span class="text-[11px] text-slate-500 font-medium">Pilih salah satu metode</span>
                        </div>

                        <!-- 2 Opsi: Kucing Terdaftar vs Belum Terdaftar / Bantu Orang Lain -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Opsi 1: Kucing Saya yang Sudah Terdaftar -->
                            <label class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition select-none"
                                :class="lostSource === 'registered' ? 'border-teal-700 bg-teal-50/70 ring-2 ring-teal-600/20 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <input type="radio" name="lost_source" value="registered" x-model="lostSource" @change="onSourceChanged('registered')" class="mt-1 text-teal-700 focus:ring-teal-600">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">🏠</span>
                                        <span class="font-bold text-xs sm:text-sm text-slate-900">Kucing Saya yang Sudah Terdaftar</span>
                                    </div>
                                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-relaxed">
                                        Ambil langsung dari profil anabul di akun KucingMu Anda. Foto dan data identitas terisi otomatis.
                                    </p>
                                    <div class="mt-2 flex items-center gap-1.5 text-[10px] font-semibold text-teal-800">
                                        <span class="bg-teal-100/90 px-2 py-0.5 rounded-md">Tersinkronisasi KTAKuMu</span>
                                        <span>{{ $myCats->count() }} anabul di profil</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Opsi 2: Kucing Belum Terdaftar / Membantu Orang Lain -->
                            <label class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition select-none"
                                :class="lostSource === 'unregistered' ? 'border-amber-600 bg-amber-50/70 ring-2 ring-amber-600/20 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <input type="radio" name="lost_source" value="unregistered" x-model="lostSource" @change="onSourceChanged('unregistered')" class="mt-1 text-amber-600 focus:ring-amber-500">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">🤝</span>
                                        <span class="font-bold text-xs sm:text-sm text-slate-900">Kucing Belum Terdaftar / Bantu Orang Lain</span>
                                    </div>
                                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-relaxed">
                                        Kucing belum ada di profil, atau membantu tetangga, teman, dan keluarga yang kehilangan anabul.
                                    </p>
                                    <div class="mt-2 flex items-center gap-1.5 text-[10px] font-semibold text-amber-800">
                                        <span class="bg-amber-100/90 px-2 py-0.5 rounded-md">Input Bebas</span>
                                        <span>Dapat sesuaikan kontak pemilik</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- KONTEN UNTUK PILIHAN KUCING TERDAFTAR -->
                        <div x-show="lostSource === 'registered'" x-cloak class="pt-2 space-y-3">
                            @if($myCats->isEmpty())
                                <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-3">
                                    <span class="text-3xl">🐱</span>
                                    <div class="font-bold text-sm text-slate-800">Belum Ada Kucing Terdaftar di Akun Anda</div>
                                    <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed">
                                        Anda belum memiliki data anabul di profil akun Anda. Anda dapat mendaftarkannya terlebih dahulu di dashboard atau memilih opsi Kucing Belum Terdaftar untuk langsung melaporkan.
                                    </p>
                                    <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                                        <a href="{{ route('dashboard') }}" class="button-primary min-h-[44px] px-4 text-xs font-bold inline-flex items-center gap-1.5">
                                            <span>+</span>
                                            <span>Daftarkan Kucing di Dashboard Dulu</span>
                                        </a>
                                        <button type="button" @click="onSourceChanged('unregistered')" class="button-secondary min-h-[44px] px-4 text-xs font-semibold">
                                            Lapor Kucing Belum Terdaftar / Bantu Orang Lain
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="form-label font-bold text-slate-800 mb-2 block">
                                        Pilih Kucing Peliharaan Anda yang Hilang <span class="text-rose-600">*</span>
                                    </label>
                                    <input type="hidden" name="cat_id" :value="selectedCatId">

                                    <!-- Grid Pilihan Kucing -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($myCats as $cat)
                                            <div @click="selectCat({{ $cat->id }})"
                                                 class="p-3.5 rounded-2xl border-2 cursor-pointer transition flex items-center gap-3 select-none"
                                                 :class="selectedCatId == {{ $cat->id }} ? 'border-teal-700 bg-teal-50/80 shadow-xs ring-2 ring-teal-600/30' : 'border-slate-200 hover:border-teal-400 bg-white'">
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

                                <!-- Card Konfirmasi Sinkronisasi Identitas -->
                                <div x-show="selectedCatData" x-cloak
                                     class="p-4 rounded-2xl bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <img :src="selectedCatData?.photo_url" class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-xs shrink-0">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 bg-teal-100/90 px-2 py-0.5 rounded-full">Kucing Terdaftar</span>
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
                                        <span>Identitas & Foto Profil Terhubung</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- KONTEN UNTUK PILIHAN KUCING BELUM TERDAFTAR / BANTU ORANG LAIN -->
                        <div x-show="lostSource === 'unregistered'" x-cloak class="pt-2">
                            <input type="hidden" name="cat_id" value="">
                            <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-950 flex items-start gap-3">
                                <span class="text-xl shrink-0">🤝</span>
                                <div class="text-xs space-y-1">
                                    <div class="font-bold text-slate-800">Mode: Kucing Belum Terdaftar / Membantu Orang Lain</div>
                                    <p class="text-slate-600 leading-relaxed">
                                        Silakan lengkapi identitas dan foto anabul secara manual pada formulir di bawah. Bila Anda sedang membantu teman, tetangga, atau keluarga, Anda juga dapat menyesuaikan nama dan kontak nomor WhatsApp pemilik aslinya pada bagian <strong>Kontak Pelapor</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Foto Anabul -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📸</span>
                                <span x-show="reportType === 'lost' && lostSource === 'registered' && selectedCatData">Foto Anabul Terbaru (Tambahan / Kondisi Terakhir)</span>
                                <span x-show="!(reportType === 'lost' && lostSource === 'registered' && selectedCatData)">Foto Anabul Terbaru</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span x-show="reportType === 'lost' && lostSource === 'registered' && selectedCatData">
                                    Foto utama otomatis menggunakan foto profil anabul terdaftar. Anda dapat menambahkan foto kondisi terakhir (opsional).
                                </span>
                                <span x-show="!(reportType === 'lost' && lostSource === 'registered' && selectedCatData)">
                                    Unggah foto yang jelas menampilkan wajah, warna bulu, dan tanda khas anabul agar mudah dikenali.
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Sync preview badge when registered cat has photo -->
                    <div x-show="reportType === 'lost' && lostSource === 'registered' && selectedCatData" x-cloak
                         class="p-3.5 rounded-xl bg-teal-50 border border-teal-200 flex items-center gap-3">
                        <img :src="selectedCatData?.photo_url" class="w-12 h-12 rounded-xl object-cover border border-teal-300 shadow-2xs shrink-0">
                        <div class="text-xs text-teal-900 min-w-0">
                            <div class="font-bold flex items-center gap-1.5">
                                <span>✓</span>
                                <span>Foto Profil Terdaftar Aktif</span>
                            </div>
                            <div class="text-teal-700 text-[11px] leading-relaxed">
                                Foto di samping otomatis dijadikan foto utama laporan jika tidak ada foto baru yang diunggah.
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 hover:border-teal-600 bg-slate-50/60 p-6 text-center transition">
                            <input type="file" name="photos[]" multiple accept="image/*" @change="previewFiles($event)" class="sr-only">
                            <div class="space-y-2">
                                <span class="text-3xl">📷</span>
                                <div class="text-xs sm:text-sm font-bold text-slate-700">
                                    <span x-show="reportType === 'lost' && lostSource === 'registered' && selectedCatData">Unggah foto tambahan dari galeri (opsional)</span>
                                    <span x-show="!(reportType === 'lost' && lostSource === 'registered' && selectedCatData)">Pilih foto anabul dari galeri perangkat Anda</span>
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
                                    Foto Utama Baru
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
                            <input type="text" name="title" x-model="title" required
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
                            <input type="text" name="coat_pattern" x-model="coatPattern"
                                placeholder="Contoh: Tabby, Tuxedo, Calico, Polos"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Perkiraan Usia -->
                        <div>
                            <label class="form-label">Perkiraan Usia</label>
                            <input type="text" name="age_estimate" x-model="ageEstimate"
                                placeholder="Contoh: 1 tahun / Dewasa / Anak-anak"
                                class="form-input text-xs sm:text-sm">
                        </div>

                        <!-- Kondisi Saat Kejadian -->
                        <div>
                            <label class="form-label">Kondisi Fisik Saat Dilihat / Ditemukan <span class="text-rose-600">*</span></label>
                            <select name="condition" x-model="condition" required class="form-input text-xs sm:text-sm">
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
                            <textarea name="distinctive_features" x-model="distinctiveFeatures" rows="3" required
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
                            Kontak ini digunakan oleh penemu atau komunitas untuk menghubungi pihak pelapor / pemilik anabul.
                        </p>
                    </div>

                    <!-- Callout for helping someone else -->
                    <div x-show="reportType === 'lost' && lostSource === 'unregistered'" x-cloak
                         class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900 flex items-start gap-2.5">
                        <span class="text-base shrink-0">💡</span>
                        <span>
                            <strong>Sedang membantu orang lain?</strong> Anda dapat mengganti nama dan nomor telepon di bawah ini dengan nama dan nomor WhatsApp pemilik kucing yang sebenarnya agar pihak penemu dapat langsung menghubungi pemilik asli anabul.
                        </span>
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
            const catsData = @json($myCatsFormatted ?? []);
            const hasCats = catsData.length > 0;
            const requestedSource = '{{ request('source') }}';
            const initialLostSource = '{{ old('lost_source', '') }}' || (requestedSource === 'unregistered' || !hasCats ? 'unregistered' : 'registered');
            const initialSelectedCatId = '{{ old('cat_id', isset($selectedCat) ? $selectedCat->id : '') }}' || (initialLostSource === 'registered' && hasCats ? catsData[0].id : '');

            return {
                reportType: '{{ old('type', $initialType ?? 'lost') }}',
                lostSource: initialLostSource,
                catsList: catsData,
                selectedCatId: initialLostSource === 'registered' ? initialSelectedCatId : '',
                selectedCatData: null,
                title: @json(old('title', '')),
                catName: @json(old('cat_name', '')),
                coatColor: @json(old('coat_color', '')),
                coatPattern: @json(old('coat_pattern', '')),
                catGender: @json(old('gender', 'tidak_diketahui')),
                ageEstimate: @json(old('age_estimate', '')),
                condition: @json(old('condition', 'sehat')),
                distinctiveFeatures: @json(old('distinctive_features', '')),
                searchRadiusKm: {{ old('search_radius_km', 3.0) }},
                coords: {
                    lat: '{{ old('latitude', -7.7956) }}',
                    lng: '{{ old('longitude', 110.3695) }}',
                },
                formattedAddress: @json(old('formatted_address', '')),
                selectedProvince: @json(old('province', '')),
                cityName: @json(old('city', '')),
                districtName: @json(old('district', '')),
                villageName: @json(old('village', '')),
                previewUrls: [],
                map: null,
                marker: null,
                radiusCircle: null,
                isGeocoding: false,

                init() {
                    if (this.reportType === 'lost' && this.lostSource === 'registered' && this.selectedCatId) {
                        this.selectCat(this.selectedCatId, false);
                    }
                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                selectCat(catId, overwrite = true) {
                    this.selectedCatId = catId;
                    const cat = this.catsList.find(c => c.id == catId);
                    if (cat) {
                        this.selectedCatData = cat;
                        if (overwrite || !this.catName) {
                            this.catName = cat.name || '';
                        }
                        if (overwrite || !this.coatColor) {
                            this.coatColor = cat.color || '';
                        }
                        if (overwrite || !this.coatPattern) {
                            this.coatPattern = cat.breed || '';
                        }
                        if (overwrite || !this.catGender || this.catGender === 'tidak_diketahui') {
                            this.catGender = cat.gender || 'tidak_diketahui';
                        }
                        if (overwrite || !this.ageEstimate) {
                            this.ageEstimate = (cat.age_text && cat.age_text !== '-') ? cat.age_text : '';
                        }
                        if (overwrite || !this.title || this.title.startsWith('Dicari: Kucing ')) {
                            this.title = 'Dicari: Kucing ' + cat.name + ' Hilang';
                        }
                        if (overwrite || !this.distinctiveFeatures) {
                            this.distinctiveFeatures = cat.notes ? cat.notes : ('Kucing peliharaan terdaftar KTAKuMu: ' + (cat.unique_code || cat.name));
                        }
                    }
                },

                onSourceChanged(source) {
                    this.lostSource = source;
                    if (source === 'unregistered') {
                        this.selectedCatId = '';
                        this.selectedCatData = null;
                    } else if (source === 'registered') {
                        if (this.catsList.length > 0) {
                            const targetId = this.selectedCatId || this.catsList[0].id;
                            this.selectCat(targetId, true);
                        }
                    }
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
                }
            };
        }
    </script>
</x-app-layout>

