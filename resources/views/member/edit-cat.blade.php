<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Back button -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-teal-700 transition">
                    ← Kembali ke Dashboard
                </a>
            </div>

            <!-- Edit Cat Form Card -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900">Ubah Profil Kucing</h2>
                    <p class="text-sm text-slate-500 mt-1">Perbarui data detail kucing Anda termasuk foto profil terbarunya.</p>
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

                    <!-- Foto Section -->
                    <div class="space-y-3">
                        <label for="cat_photo" class="form-label font-semibold text-slate-700 block">Foto Kucing</label>
                        <div class="flex items-center gap-4">
                            <!-- Current Photo Thumbnail -->
                            <div class="h-20 w-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                @if($cat->photo_path)
                                    <img src="{{ asset('storage/' . $cat->photo_path) }}" alt="{{ $cat->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="text-xs text-slate-400 text-center font-semibold uppercase p-1">No Photo</div>
                                @endif
                            </div>
                            <!-- Input File -->
                            <div class="flex-1">
                                <input type="file" id="cat_photo" name="photo" class="form-input w-full py-1 text-xs border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                                <p class="text-xs text-slate-400 mt-1">Unggah foto baru jika ingin menggantinya (Maks. 2MB, format gambar).</p>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                    </div>

                    <!-- Alergi -->
                    <div>
                        <label for="cat_allergies" class="form-label font-semibold text-slate-700">Alergi Kucing <span class="text-xs text-slate-400">(Opsional)</span></label>
                        <input type="text" id="cat_allergies" name="allergies" value="{{ old('allergies', $cat->allergies) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Alergi ayam">
                        <x-input-error :messages="$errors->get('allergies')" class="mt-1" />
                    </div>

                    <!-- Riwayat Vaksin -->
                    <div>
                        <label for="cat_vaccine" class="form-label font-semibold text-slate-700">Riwayat Vaksin <span class="text-xs text-slate-400">(Opsional)</span></label>
                        <input type="text" id="cat_vaccine" name="vaccine_history" value="{{ old('vaccine_history', $cat->vaccine_history) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Tricat, Rabies">
                        <x-input-error :messages="$errors->get('vaccine_history')" class="mt-1" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
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
</x-app-layout>
