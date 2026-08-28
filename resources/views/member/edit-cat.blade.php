<x-app-layout>
    <div class="py-12" x-data="webcamCapture()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Back button -->
            <div class="mb-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-teal-700 transition">
                    ← Kembali ke Dashboard
                </a>
            </div>

            <!-- Edit Cat Form Card -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900">Ubah Profil & Galeri Kucing</h2>
                    <p class="text-sm text-slate-500 mt-1">Perbarui data detail kucing Anda, kelola banyak foto (tampak depan, samping, atas), foto langsung dari kamera, serta sampel biometrik.</p>
                </div>

                <form method="POST" action="{{ route('cat.update', $cat->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Nama Kucing -->
                        <div>
                            <label for="cat_name" class="form-label font-semibold text-slate-700">Nama Kucing</label>
                            <input type="text" id="cat_name" name="name" value="{{ old('name', $cat->name) }}" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Mochi">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Ras / Breed -->
                        @php
                            $breedList = $masterBreeds ?? \App\Models\MasterBreed::getAllBreedNames();
                            $currentBreed = old('breed', $cat->breed);
                            $isKnownBreed = in_array($currentBreed, $breedList);
                            $initialSelection = $isKnownBreed ? $currentBreed : ($currentBreed ? 'Lainnya' : 'Domestik');
                            $initialCustom = !$isKnownBreed ? $currentBreed : old('breed_custom', '');
                        @endphp
                        <div x-data="{ selectedBreed: '{{ $initialSelection }}' }" class="space-y-2">
                            <label for="cat_breed" class="form-label font-semibold text-slate-700">Ras / Jenis Kucing <span class="text-rose-500">*</span></label>
                            <select id="cat_breed" name="breed" x-model="selectedBreed" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2">
                                @foreach($breedList as $b)
                                    <option value="{{ $b }}" {{ $initialSelection === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                                <option value="Lainnya" {{ $initialSelection === 'Lainnya' ? 'selected' : '' }}>➕ Lainnya (Input Sendiri)</option>
                            </select>

                            <!-- Input Kustom jika pilih Lainnya -->
                            <div x-show="selectedBreed === 'Lainnya'" x-transition class="bg-amber-50/80 p-2.5 rounded-xl border border-amber-200">
                                <label for="breed_custom" class="form-label text-xs text-amber-900 mb-1">
                                    Tuliskan Nama Ras Kucing Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="breed_custom" name="breed_custom" value="{{ $initialCustom }}" placeholder="Contoh: Munchkin / Balinese" class="form-input text-xs bg-white w-full rounded-lg">
                                <p class="text-[11px] text-amber-700 mt-1">Ras baru ini otomatis tersimpan ke master dan menjadi pilihan ke depannya.</p>
                            </div>
                            <x-input-error :messages="$errors->get('breed')" class="mt-1" />
                            <x-input-error :messages="$errors->get('breed_custom')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Warna / Pola Bulu -->
                        <div>
                            <label for="cat_color" class="form-label font-semibold text-slate-700">Warna / Pola Bulu</label>
                            <input type="text" id="cat_color" name="color" value="{{ old('color', $cat->color) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Calico / Tabby / Hitam Putih">
                            <x-input-error :messages="$errors->get('color')" class="mt-1" />
                        </div>

                        <!-- Wilayah Muhammadiyah (Master Wilayah) -->
                        <div>
                            <label for="cat_wilayah" class="form-label font-semibold text-slate-700">Wilayah Muhammadiyah (Master Wilayah)</label>
                            <select id="cat_wilayah" name="wilayah_code" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2">
                                @if(isset($masterWilayahs))
                                    @foreach($masterWilayahs as $wil)
                                        <option value="{{ $wil->kode }}" {{ old('wilayah_code', $cat->wilayah_code) == $wil->kode ? 'selected' : '' }}>
                                            {{ $wil->kode }} - {{ $wil->nama }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="34" selected>34 - D.I. Yogyakarta (PWM DIY)</option>
                                @endif
                            </select>
                            <span class="text-[11px] text-slate-500 mt-1 block">
                                Kode Unik KTAM: <strong class="font-mono text-teal-800">{{ $cat->formatted_unique_code }}</strong>
                            </span>
                            <x-input-error :messages="$errors->get('wilayah_code')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="cat_gender" class="form-label font-semibold text-slate-700">Jenis Kelamin</label>
                            <select id="cat_gender" name="gender" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2">
                                <option value="male" {{ old('gender', $cat->gender) == 'male' ? 'selected' : '' }}>Jantan</option>
                                <option value="female" {{ old('gender', $cat->gender) == 'female' ? 'selected' : '' }}>Betina</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="cat_dob" class="form-label font-semibold text-slate-700">Tanggal Lahir</label>
                            <input type="date" id="cat_dob" name="date_of_birth" value="{{ old('date_of_birth', $cat->date_of_birth ? $cat->date_of_birth->format('Y-m-d') : '') }}" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
                        </div>

                        <!-- Status Kucing (Hidup / Mati) -->
                        <div>
                            <label for="cat_status" class="form-label font-semibold text-slate-700">Status Kehidupan Kucing</label>
                            @php
                                $currStatus = old('status', $cat->status ?: 'alive');
                            @endphp
                            <select id="cat_status" name="status" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2 font-medium {{ $currStatus === 'deceased' ? 'bg-slate-100 text-slate-700 border-slate-300' : 'bg-teal-50/50 text-teal-900 border-teal-200' }}">
                                <option value="alive" {{ in_array($currStatus, ['alive', 'hidup']) ? 'selected' : '' }}>🟢 Hidup (Aktif)</option>
                                <option value="deceased" {{ in_array($currStatus, ['deceased', 'mati']) ? 'selected' : '' }}>⚪ Mati (Meninggal)</option>
                            </select>
                            <span class="text-[11px] text-slate-500 mt-1 block">Status hidup/mati untuk pencatatan KTAM & data anggota.</span>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Multi-Photo Gallery Section with Direct Position Upload Buttons -->
                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                    📷 Galeri Foto Kucing Berdasarkan Posisi
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Unggah langsung foto untuk masing-masing posisi kucing (Tampak Depan, Samping, Atas, Hidung/Wajah, Telapak Kaki). Format <strong>JPG & PNG</strong>, maksimal <strong>1 MB</strong> per foto.</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-teal-50 text-teal-800 border border-teal-200 shrink-0">
                                <span>⚡</span> Maks. 1 MB / Foto (JPG/PNG)
                            </span>
                        </div>

                        @php
                            $photoPositions = [
                                [
                                    'key' => 'depan',
                                    'label' => 'Tampak Depan',
                                    'subtitle' => 'Wajah / Pose Depan (Utama KTAM)',
                                    'icon' => '🐱',
                                    'is_primary_candidate' => true,
                                ],
                                [
                                    'key' => 'samping',
                                    'label' => 'Tampak Samping',
                                    'subtitle' => 'Profil Samping Tubuh (Kiri/Kanan)',
                                    'icon' => '🐈',
                                    'is_primary_candidate' => false,
                                ],
                                [
                                    'key' => 'atas',
                                    'label' => 'Tampak Atas',
                                    'subtitle' => 'Pola Punggung / Dorsal Tubuh',
                                    'icon' => '🐾',
                                    'is_primary_candidate' => false,
                                ],
                                [
                                    'key' => 'wajah',
                                    'label' => 'Foto Hidung/Wajah',
                                    'subtitle' => 'Detail Hidung & Muka Close-up',
                                    'icon' => '👃',
                                    'is_primary_candidate' => false,
                                ],
                                [
                                    'key' => 'paw',
                                    'label' => 'Foto Telapak Kaki (Paw)',
                                    'subtitle' => 'Detail Bantalan Paw Print',
                                    'icon' => '🐾',
                                    'is_primary_candidate' => false,
                                ],
                            ];

                            // Track mapped photos so any other unmatched photos can still be viewed & managed
                            $mappedPhotoIds = [];
                        @endphp

                        <!-- Grid 5 Posisi Foto -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($photoPositions as $pos)
                                @php
                                    $existingPhoto = $cat->photos->first(function($p) use ($pos) {
                                        return strtolower(trim($p->label)) === strtolower(trim($pos['label']));
                                    });
                                    if ($existingPhoto) {
                                        $mappedPhotoIds[] = $existingPhoto->id;
                                    }
                                @endphp

                                <div class="relative bg-slate-50/90 border {{ $existingPhoto && $existingPhoto->is_primary ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-200' }} rounded-2xl p-4 flex flex-col justify-between space-y-3 hover:border-slate-300 transition shadow-xs">
                                    <!-- Header Posisi -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-base">{{ $pos['icon'] }}</span>
                                                <h4 class="text-xs font-bold text-slate-800">{{ $pos['label'] }}</h4>
                                            </div>
                                            <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">{{ $pos['subtitle'] }}</p>
                                        </div>

                                        @if($existingPhoto && $existingPhoto->is_primary)
                                            <span class="bg-teal-600 text-white text-[9px] font-bold px-2 py-0.5 rounded shadow-xs whitespace-nowrap">
                                                ★ UTAMA KTAM
                                            </span>
                                        @elseif($existingPhoto)
                                            <span class="bg-emerald-100 text-emerald-800 text-[9px] font-bold px-2 py-0.5 rounded border border-emerald-200 whitespace-nowrap">
                                                ✓ Tersimpan
                                            </span>
                                        @else
                                            <span class="bg-slate-200/80 text-slate-600 text-[9px] font-bold px-2 py-0.5 rounded whitespace-nowrap">
                                                Kosong
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Preview Area -->
                                    <div class="relative aspect-4/3 sm:aspect-square w-full rounded-xl overflow-hidden bg-white border border-slate-200/80 flex items-center justify-center shadow-inner">
                                        <!-- Live Selected Preview -->
                                        <template x-if="previews['{{ $pos['key'] }}']">
                                            <div class="relative w-full h-full">
                                                <img :src="previews['{{ $pos['key'] }}']" alt="{{ $pos['label'] }}" class="w-full h-full object-cover">
                                                <span class="absolute top-2 left-2 bg-indigo-600 text-white text-[9px] font-bold px-2 py-0.5 rounded shadow-sm">
                                                    📷 Baru Dipilih
                                                </span>
                                                <button type="button" @click="clearSelectedPhoto('{{ $pos['key'] }}', 'photo_input_{{ $pos['key'] }}')" class="absolute top-2 right-2 bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm hover:bg-rose-700">
                                                    ✕ Batal
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Existing Stored Photo -->
                                        <template x-if="!previews['{{ $pos['key'] }}']">
                                            @if($existingPhoto)
                                                <img src="{{ asset('storage/' . $existingPhoto->photo_path) }}" alt="{{ $pos['label'] }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="text-center p-3 text-slate-400 space-y-1">
                                                    <span class="text-3xl block">{{ $pos['icon'] }}</span>
                                                    <span class="text-[11px] font-medium block">Belum ada foto</span>
                                                    <span class="text-[10px] text-slate-400 block">Klik tombol di bawah</span>
                                                </div>
                                            @endif
                                        </template>
                                    </div>

                                    <!-- Existing Photo Action (Set Utama & Hapus) if available and no new preview -->
                                    @if($existingPhoto)
                                        <div x-show="!previews['{{ $pos['key'] }}']" class="flex items-center justify-between text-xs pt-1 border-t border-slate-200/60">
                                            @if(!$existingPhoto->is_primary)
                                                <button type="submit" form="set-primary-form-{{ $existingPhoto->id }}" class="text-[11px] font-bold text-teal-700 hover:text-teal-900 hover:underline">
                                                    ★ Jadikan Utama KTAM
                                                </button>
                                            @else
                                                <span class="text-[11px] text-teal-700 font-bold">Foto Utama Saat Ini</span>
                                            @endif

                                            <button type="submit" form="delete-photo-form-{{ $existingPhoto->id }}" onclick="return confirm('Hapus foto {{ $pos['label'] }} ini?')" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline">
                                                Hapus
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Direct Upload & Camera Buttons -->
                                    <div class="space-y-1.5 pt-1">
                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Tombol Unggah File / Galeri -->
                                            <label class="button-secondary text-xs font-semibold py-2 px-2 flex items-center justify-center gap-1.5 cursor-pointer bg-white hover:bg-slate-100 shadow-xs border-slate-300">
                                                <span>📁</span>
                                                <span class="truncate">{{ $existingPhoto ? 'Ganti File' : 'Unggah File' }}</span>
                                                <input type="file"
                                                       id="photo_input_{{ $pos['key'] }}"
                                                       name="photos[{{ $pos['key'] }}]"
                                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                                       class="hidden"
                                                       @change="validateAndPreview($event, '{{ $pos['key'] }}')">
                                            </label>

                                            <!-- Tombol Kamera -->
                                            <button type="button"
                                                    @click="openCamera('photo_input_{{ $pos['key'] }}', '{{ $pos['key'] }}')"
                                                    class="button-secondary text-xs font-semibold py-2 px-2 flex items-center justify-center gap-1.5 bg-white hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 shadow-xs border-slate-300">
                                                <span>📷</span>
                                                <span class="truncate">{{ $existingPhoto ? 'Ganti Kamera' : 'Kamera' }}</span>
                                            </button>
                                        </div>

                                        <input type="hidden" name="photo_labels[{{ $pos['key'] }}]" value="{{ $pos['label'] }}">
                                        
                                        <p class="text-[10px] text-slate-400 text-center">Format: JPG/PNG (Maks 1 MB)</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Unmapped / Extra Photos Section if exists -->
                        @php
                            $extraPhotos = $cat->photos->whereNotIn('id', $mappedPhotoIds);
                        @endphp
                        @if($extraPhotos->count() > 0)
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 mt-4">
                                <h4 class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <span>🖼</span> Foto Tambahan Lainnya yang Tersimpan ({{ $extraPhotos->count() }})
                                </h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($extraPhotos as $photo)
                                        <div class="relative bg-white border {{ $photo->is_primary ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-200' }} rounded-xl p-2 flex flex-col justify-between space-y-2">
                                            <div class="relative h-24 w-full rounded-lg overflow-hidden bg-slate-200">
                                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $photo->label }}" class="h-full w-full object-cover">
                                                @if($photo->is_primary)
                                                    <span class="absolute top-1 left-1 bg-teal-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-sm">
                                                        ★ UTAMA KTAM
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="space-y-1 text-center">
                                                <span class="text-xs font-semibold text-slate-700 block truncate">{{ $photo->label }}</span>
                                                <div class="flex items-center justify-center gap-2 pt-1 border-t border-slate-200/60">
                                                    @if(!$photo->is_primary)
                                                        <button type="submit" form="set-primary-form-{{ $photo->id }}" class="text-[10px] font-bold text-teal-700 hover:underline">
                                                            Set Utama
                                                        </button>
                                                        <span class="text-slate-300">|</span>
                                                    @endif
                                                    <button type="submit" form="delete-photo-form-{{ $photo->id }}" onclick="return confirm('Hapus foto ini?')" class="text-[10px] font-bold text-red-600 hover:underline">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Biometrics Section -->
                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                🐾 Data Biometrik Kucing (Paw Print / Nose Print)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sistem mendukung penyimpanan identifikasi biometrik telapak kaki (*paw*) atau hidung (*nose print*). Maks. 1 MB (JPG/PNG).</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="biometric_type" class="form-label font-semibold text-slate-700">Jenis Biometrik</label>
                                <select id="biometric_type" name="biometric_type" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-xs py-2">
                                    <option value="none" {{ old('biometric_type', $cat->biometric_type) == 'none' ? 'selected' : '' }}>Belum Ada Biometrik</option>
                                    <option value="paw" {{ old('biometric_type', $cat->biometric_type) == 'paw' ? 'selected' : '' }}>Paw Print (Telapak Kaki)</option>
                                    <option value="nose" {{ old('biometric_type', $cat->biometric_type) == 'nose' ? 'selected' : '' }}>Nose Print (Pola Hidung)</option>
                                    <option value="both" {{ old('biometric_type', $cat->biometric_type) == 'both' ? 'selected' : '' }}>Paw & Nose Print (Lengkap)</option>
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="biometric_photo" class="form-label font-semibold text-slate-700">Unggah Sampel Biometrik</label>
                                    <button type="button" @click="openCamera('biometric_photo', 'biometric', 'biometric_preview')" class="text-[10px] font-bold text-teal-700 hover:underline">
                                        📷 Kamera
                                    </button>
                                </div>
                                <input type="file"
                                       id="biometric_photo"
                                       name="biometric_photo"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       capture="environment"
                                       @change="validateAndPreview($event, 'biometric', 'biometric_preview')"
                                       class="form-input mt-1 block w-full text-xs border-slate-300 rounded-xl">
                                <img id="biometric_preview" class="hidden w-20 h-20 object-cover rounded-lg border border-teal-500 mt-2 shadow-xs">
                            </div>

                            <div>
                                <label for="biometric_code" class="form-label font-semibold text-slate-700">Kode/Pola Biometrik</label>
                                <input type="text" id="biometric_code" name="biometric_code" value="{{ old('biometric_code', $cat->biometric_code) }}" placeholder="e.g. PAW-8823-XYZ" class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs">
                            </div>
                        </div>

                        @if($cat->biometric_photo_path)
                            <div class="flex items-center gap-3 bg-teal-50/60 p-3 rounded-xl border border-teal-100">
                                <img src="{{ asset('storage/' . $cat->biometric_photo_path) }}" alt="Biometrik" class="w-12 h-12 object-cover rounded-lg border border-teal-200">
                                <div class="text-xs text-teal-900">
                                    <span class="font-bold">Foto Biometrik Tersimpan</span>
                                    <p class="text-[11px] text-teal-700 mt-0.5">Jenis: {{ strtoupper($cat->biometric_type) }} | Kode: {{ $cat->biometric_code ?? '-' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Alergi & Vaksin -->
                    <div class="border-t border-slate-100 pt-6 grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="cat_allergies" class="form-label font-semibold text-slate-700">Alergi Kucing <span class="text-xs text-slate-400">(Opsional)</span></label>
                            <input type="text" id="cat_allergies" name="allergies" value="{{ old('allergies', $cat->allergies) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 shadow-sm" placeholder="e.g. Alergi ayam">
                        </div>

                        <div>
                            <label for="cat_vaccine" class="form-label font-semibold text-slate-700">Riwayat Vaksin <span class="text-xs text-slate-400">(Opsional)</span></label>
                            <input type="text" id="cat_vaccine" name="vaccine_history" value="{{ old('vaccine_history', $cat->vaccine_history) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 shadow-sm" placeholder="e.g. Tricat, Rabies">
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('dashboard') }}" class="button-secondary px-5 py-2.5">
                            Batal
                        </a>
                        <button type="submit" class="button-primary px-6 py-2.5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Live Camera Modal Overlay -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="font-outfit font-bold text-slate-900 text-base flex items-center gap-2">
                        <span>📷</span> Tangkap Foto dari Kamera
                    </h3>
                    <button type="button" @click="closeCamera()" class="text-slate-400 hover:text-slate-700 font-bold text-xl">&times;</button>
                </div>

                <!-- Video Stream Preview -->
                <div class="relative bg-black rounded-2xl overflow-hidden aspect-square flex items-center justify-center shadow-inner">
                    <video x-ref="videoElement" autoplay playsinline class="w-full h-full object-cover" x-show="!capturedImage"></video>
                    <img :src="capturedImage" x-show="capturedImage" class="w-full h-full object-cover">
                    <canvas x-ref="canvasElement" class="hidden"></canvas>
                </div>

                <!-- Controls -->
                <div class="pt-2">
                    <template x-if="!capturedImage">
                        <button type="button" @click="takeSnap()" class="w-full button-primary py-3 text-sm font-bold flex justify-center items-center gap-2">
                            <span>🔴</span> Tangkap Foto
                        </button>
                    </template>
                    
                    <template x-if="capturedImage">
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="retakeSnap()" class="button-secondary py-2.5 text-xs font-bold">
                                🔄 Ulangi Foto
                            </button>
                            <button type="button" @click="usePhoto()" class="button-primary py-2.5 text-xs font-bold">
                                ✅ Gunakan Foto Ini
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- Hidden forms for set-primary and delete photo actions -->
    @foreach($cat->photos as $photo)
        @if(!$photo->is_primary)
            <form id="set-primary-form-{{ $photo->id }}" action="{{ route('photos.set-primary', $photo->id) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endif
        <form id="delete-photo-form-{{ $photo->id }}" action="{{ route('photos.destroy', $photo->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <script>
        function webcamCapture() {
            return {
                showModal: false,
                stream: null,
                capturedImage: null,
                targetInputId: null,
                previewKey: null,
                previewImageId: null,
                previews: {},

                validateAndPreview(event, key, directPreviewId = null) {
                    const input = event.target;
                    const file = input.files && input.files[0];
                    if (!file) return;

                    // Validate file type (JPG/PNG only)
                    const validMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                    const isValidType = validMimes.includes(file.type) || file.name.match(/\.(jpe?g|png)$/i);
                    if (!isValidType) {
                        alert('Format file tidak didukung! Harap unggah foto dengan format JPG atau PNG.');
                        input.value = '';
                        if (key) this.previews[key] = null;
                        if (directPreviewId) {
                            const prev = document.getElementById(directPreviewId);
                            if (prev) prev.classList.add('hidden');
                        }
                        return;
                    }

                    // Validate file size: Max 1 MB (1024 * 1024 bytes)
                    const maxSizeBytes = 1024 * 1024;
                    if (file.size > maxSizeBytes) {
                        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                        alert(`Ukuran file terlalu besar (${sizeMb} MB)! Maksimal ukuran 1 foto adalah 1 MB (JPG/PNG).`);
                        input.value = '';
                        if (key) this.previews[key] = null;
                        if (directPreviewId) {
                            const prev = document.getElementById(directPreviewId);
                            if (prev) prev.classList.add('hidden');
                        }
                        return;
                    }

                    // Generate live preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (key) {
                            this.previews[key] = e.target.result;
                        }
                        if (directPreviewId) {
                            const prev = document.getElementById(directPreviewId);
                            if (prev) {
                                prev.src = e.target.result;
                                prev.classList.remove('hidden');
                            }
                        }
                    };
                    reader.readAsDataURL(file);
                },

                clearSelectedPhoto(key, inputId) {
                    this.previews[key] = null;
                    const input = document.getElementById(inputId);
                    if (input) input.value = '';
                },

                openCamera(inputId, previewKey = null, directPreviewId = null) {
                    this.targetInputId = inputId;
                    this.previewKey = previewKey;
                    this.previewImageId = directPreviewId;
                    this.capturedImage = null;
                    this.showModal = true;

                    this.$nextTick(() => {
                        navigator.mediaDevices.getUserMedia({
                            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }
                        }).then(s => {
                            this.stream = s;
                            if (this.$refs.videoElement) {
                                this.$refs.videoElement.srcObject = s;
                            }
                        }).catch(err => {
                            alert('Tidak dapat mengoperasikan kamera: ' + err.message + '\nPastikan izin akses kamera telah diberikan di browser.');
                            this.showModal = false;
                        });
                    });
                },

                takeSnap() {
                    const video = this.$refs.videoElement;
                    const canvas = this.$refs.canvasElement;
                    if (!video || !canvas) return;

                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    this.capturedImage = canvas.toDataURL('image/jpeg', 0.85);
                },

                retakeSnap() {
                    this.capturedImage = null;
                },

                usePhoto() {
                    if (!this.capturedImage || !this.targetInputId) return;

                    const arr = this.capturedImage.split(',');
                    const mime = arr[0].match(/:(.*?);/)[1];
                    const bstr = atob(arr[1]);
                    let n = bstr.length;
                    const u8arr = new Uint8Array(n);
                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }
                    const file = new File([u8arr], 'camera_photo_' + Date.now() + '.jpg', { type: mime });

                    if (file.size > 1024 * 1024) {
                        alert('Ukuran foto kamera melebihi 1 MB. Silakan ulangi pemotretan.');
                        return;
                    }

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    const inputElem = document.getElementById(this.targetInputId);
                    if (inputElem) {
                        inputElem.files = dataTransfer.files;
                    }

                    if (this.previewKey) {
                        this.previews[this.previewKey] = this.capturedImage;
                    }

                    if (this.previewImageId) {
                        const prevElem = document.getElementById(this.previewImageId);
                        if (prevElem) {
                            prevElem.src = this.capturedImage;
                            prevElem.classList.remove('hidden');
                        }
                    }

                    this.closeCamera();
                },

                closeCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    this.showModal = false;
                    this.capturedImage = null;
                }
            }
        }
    </script>
</x-app-layout>
