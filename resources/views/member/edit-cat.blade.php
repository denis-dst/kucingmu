<x-app-layout>
    <div class="py-12">
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
                    <p class="text-sm text-slate-500 mt-1">Perbarui data detail kucing Anda, kelola banyak foto (tampak depan, samping, atas), pilih 1 foto utama untuk KTAM, serta sampel biometrik.</p>
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
                        <div>
                            <label for="cat_breed" class="form-label font-semibold text-slate-700">Ras / Breed</label>
                            <input type="text" id="cat_breed" name="breed" value="{{ old('breed', $cat->breed) }}" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Persia / Domestik">
                            <x-input-error :messages="$errors->get('breed')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
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
                    </div>

                    <!-- Multi-Photo Gallery Section -->
                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                📷 Galeri Foto Kucing (Tampak Depan, Samping, Atas)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Satu kucing dapat memiliki lebih dari 1 foto. Pilih 1 foto utama sebagai foto yang akan tercetak pada kartu KTAM.</p>
                        </div>

                        <!-- Existing Photo Grid -->
                        @if($cat->photos->count() > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach($cat->photos as $photo)
                                    <div class="relative bg-slate-50 border {{ $photo->is_primary ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-200' }} rounded-xl p-2 flex flex-col justify-between space-y-2">
                                        <div class="relative h-28 w-full rounded-lg overflow-hidden bg-slate-200">
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
                        @else
                            <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-4 text-center text-xs text-slate-400">
                                Belum ada foto di galeri. Silakan unggah foto di bawah ini.
                            </div>
                        @endif

                        <!-- Input Upload Foto Baru -->
                        <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200 space-y-3">
                            <label class="form-label font-semibold text-slate-700 block text-xs">Tambah Foto Baru ke Galeri</label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <input type="file" name="photos[]" class="form-input w-full py-1 text-xs border-slate-300 rounded-lg">
                                </div>
                                <div>
                                    <select name="photo_labels[]" class="form-input w-full py-1 text-xs border-slate-300 rounded-lg">
                                        <option value="Tampak Depan">Tampak Depan</option>
                                        <option value="Tampak Samping">Tampak Samping</option>
                                        <option value="Tampak Atas">Tampak Atas</option>
                                        <option value="Foto Hidung/Wajah">Foto Hidung/Wajah</option>
                                        <option value="Foto Telapak Kaki (Paw)">Foto Telapak Kaki (Paw)</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biometrics Section -->
                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                🐾 Data Biometrik Kucing (Paw Print / Nose Print)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sistem mendukung penyimpanan identifikasi biometrik telapak kaki (*paw*) atau hidung (*nose print*).</p>
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
                                <label for="biometric_photo" class="form-label font-semibold text-slate-700">Unggah Sampel Biometrik</label>
                                <input type="file" id="biometric_photo" name="biometric_photo" class="form-input mt-1 block w-full text-xs border-slate-300 rounded-xl">
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
</x-app-layout>
