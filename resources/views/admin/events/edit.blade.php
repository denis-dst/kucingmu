<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Back button -->
            <div class="mb-6">
                <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-teal-700 transition">
                    ← Kembali ke Daftar Event
                </a>
            </div>

            <!-- Edit Event Form Card -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900">Ubah Detail Kegiatan</h2>
                    <p class="text-sm text-slate-500 mt-1">Perbarui informasi sosialisasi kegiatan pemeriksaan kesehatan kucing dsb.</p>
                </div>

                <form method="POST" action="{{ route('admin.events.update', $event->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Kegiatan -->
                    <div>
                        <label for="event_title" class="form-label font-semibold text-slate-700">Nama Kegiatan</label>
                        <input type="text" id="event_title" name="title" value="{{ old('title', $event->title) }}" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Sosialisasi & Pemeriksaan Kesehatan Kucing Muhammadiyah">
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="event_description" class="form-label font-semibold text-slate-700">Deskripsi / Detail Kegiatan</label>
                        <textarea id="event_description" name="description" rows="4" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="Jelaskan secara rinci detail kegiatan, persyaratan pendaftaran, dan informasi penting lainnya...">{{ old('description', $event->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Tanggal Kegiatan -->
                        <div>
                            <label for="event_date" class="form-label font-semibold text-slate-700">Tanggal Pelaksanaan</label>
                            <input type="date" id="event_date" name="date" value="{{ old('date', $event->date ? $event->date->format('Y-m-d') : '') }}" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                            <x-input-error :messages="$errors->get('date')" class="mt-1" />
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="event_location" class="form-label font-semibold text-slate-700">Tempat / Lokasi</label>
                            <input type="text" id="event_location" name="location" value="{{ old('location', $event->location) }}" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. Balai Pertemuan Muhammadiyah Lampung">
                            <x-input-error :messages="$errors->get('location')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Link Pendaftaran -->
                        <div>
                            <label for="event_registration_link" class="form-label font-semibold text-slate-700">Link Pendaftaran <span class="text-xs text-slate-400">(Opsional)</span></label>
                            <input type="text" id="event_registration_link" name="registration_link" value="{{ old('registration_link', $event->registration_link) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" placeholder="e.g. gentix-apps.com">
                            <x-input-error :messages="$errors->get('registration_link')" class="mt-1" />
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="event_status" class="form-label font-semibold text-slate-700">Status Publikasi</label>
                            <select id="event_status" name="status" required class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2">
                                <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>Aktif (Tampilkan)</option>
                                <option value="draft" {{ old('status', $event->status) === 'draft' ? 'selected' : '' }}>Draft (Sembunyikan)</option>
                                <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Selesai (Selesai/Arsip)</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Banner Image -->
                    <div class="space-y-3">
                        <label for="event_banner" class="form-label font-semibold text-slate-700 block">Banner Kegiatan</label>
                        <div class="flex items-center gap-4">
                            <!-- Current Photo Thumbnail -->
                            <div class="h-16 w-28 bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center rounded-lg">
                                @if($event->banner_path)
                                    <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="text-[10px] text-slate-400 text-center font-bold uppercase p-1">No Banner</div>
                                @endif
                            </div>
                            <!-- Input File -->
                            <div class="flex-1">
                                <input type="file" id="event_banner" name="banner" class="form-input w-full py-1 text-xs border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                                <p class="text-xs text-slate-400 mt-1">Unggah banner baru jika ingin menggantinya (JPEG, PNG, JPG, maks. 2MB).</p>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('banner')" class="mt-1" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.events.index') }}" class="button-secondary px-5 py-2.5">
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
