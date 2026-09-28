<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

        <!-- Breadcrumb & Nav -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('dokter.medical-records.index') }}" class="hover:text-teal-700 font-medium">Rekam
                Medis</a>
            <span>&bull;</span>
            <span class="text-slate-800 font-semibold">Mulai Pemeriksaan Baru</span>
        </div>

        <!-- Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
            <div class="flex items-start gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 flex items-center justify-center text-2xl shrink-0">
                    🩺
                </div>
                <div>
                    <h1 class="font-outfit text-xl md:text-2xl font-bold text-slate-900">
                        Inisiasi Pemeriksaan Kucing Pasien
                    </h1>
                    <p class="text-xs md:text-sm text-slate-500 mt-1">
                        Pilih kucing yang akan diperiksa, tentukan model layanan (klinik langsung atau telekonsultasi),
                        dan catat keluhan awal sebelum masuk ke workspace SOAP.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
            <form method="POST" action="{{ route('dokter.medical-records.store') }}" class="space-y-6">
                @csrf

                @if($appointment)
                    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                @endif

                <!-- Section: Patient Selection (Searchable Combobox) -->
                <div class="space-y-3" x-data="{
                    searchQuery: '',
                    isOpen: false,
                    selectedCat: {{ $selectedCat ? json_encode([
                        'id' => $selectedCat->id,
                        'name' => $selectedCat->name,
                        'unique_code' => $selectedCat->unique_code,
                        'breed' => $selectedCat->breed,
                        'gender' => $selectedCat->gender === 'male' ? 'Jantan' : 'Betina',
                        'date_of_birth' => $selectedCat->date_of_birth ? \Carbon\Carbon::parse($selectedCat->date_of_birth)->format('d M Y') : '-',
                        'owner_name' => $selectedCat->owner->name ?? '-',
                        'owner_nbm' => $selectedCat->owner->formatted_nbm ?? '-',
                        'photo_url' => $selectedCat->primary_photo_url,
                    ]) : 'null' }},
                    catsList: {{ json_encode($availableCatsData ?? []) }},
                    get filteredCats() {
                        if (!this.searchQuery.trim()) {
                            return this.catsList;
                        }
                        const q = this.searchQuery.toLowerCase().trim();
                        return this.catsList.filter(c => c.search_text && c.search_text.includes(q));
                    },
                    selectCat(cat) {
                        this.selectedCat = cat;
                        this.searchQuery = '';
                        this.isOpen = false;
                    },
                    clearSelection() {
                        this.selectedCat = null;
                        this.searchQuery = '';
                        this.$nextTick(() => {
                            this.$refs.searchInput?.focus();
                        });
                    }
                }">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pilih Pasien Kucing <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-teal-800 bg-teal-50 px-2 py-0.5 rounded font-semibold border border-teal-200">
                            Hanya KTAKuMu Terverifikasi
                        </span>
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="cat_id" :value="selectedCat ? selectedCat.id : ''" required>

                    <!-- Selected Cat Card (Shows when a cat is chosen) -->
                    <div x-show="selectedCat" x-transition class="p-4 rounded-xl border border-teal-200 bg-teal-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <template x-if="selectedCat && selectedCat.photo_url">
                                <img :src="selectedCat.photo_url" :alt="selectedCat.name" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                            </template>
                            <template x-if="selectedCat && !selectedCat.photo_url">
                                <div class="w-14 h-14 rounded-xl bg-teal-700 text-white font-bold text-xl flex items-center justify-center shrink-0" x-text="selectedCat.name ? selectedCat.name.charAt(0) : '🐱'"></div>
                            </template>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-outfit font-bold text-slate-900 text-base" x-text="selectedCat ? selectedCat.name : ''"></h3>
                                    <template x-if="selectedCat && selectedCat.unique_code">
                                        <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-[10px] font-mono font-bold" x-text="selectedCat.unique_code"></span>
                                    </template>
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">✓ KTAKuMu Resmi</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-0.5">
                                    Ras: <span class="font-semibold text-slate-700" x-text="selectedCat ? selectedCat.breed : ''"></span> &bull;
                                    Kelamin: <span class="text-slate-700" x-text="selectedCat ? selectedCat.gender : ''"></span>
                                    <span x-show="selectedCat && selectedCat.date_of_birth && selectedCat.date_of_birth !== '-'"> &bull; Lahir: <span x-text="selectedCat.date_of_birth"></span></span>
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Pemilik: <span class="font-semibold text-slate-800" x-text="selectedCat ? selectedCat.owner_name : '-'"></span>
                                    <span x-show="selectedCat && selectedCat.owner_nbm && selectedCat.owner_nbm !== '-'" class="text-slate-500" x-text="` (NBM: ${selectedCat.owner_nbm})`"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="clearSelection()" class="text-xs font-semibold text-teal-800 hover:text-teal-950 hover:underline flex items-center gap-1 shrink-0 self-start sm:self-center">
                            <span>🔄 Ganti Kucing</span>
                        </button>
                    </div>

                    <!-- Searchable Input & Dropdown (When no cat is selected) -->
                    <div x-show="!selectedCat" class="space-y-2">
                        @if($availableCats->isEmpty())
                            <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/70 text-xs text-amber-900 space-y-1">
                                <div class="font-bold flex items-center gap-1.5">
                                    <span>⚠️</span>
                                    <span>Belum ada kucing yang terverifikasi dan memiliki KTAKuMu resmi.</span>
                                </div>
                                <p class="text-[11px] text-amber-800">
                                    Rekam medis dokter hanya dapat dibuat untuk kucing yang telah disetujui / diverifikasi oleh Admin dan memiliki Nomor KTAKuMu resmi. Silakan hubungi Admin untuk verifikasi kucing peserta terlebih dahulu.
                                </p>
                            </div>
                        @else
                            <div class="relative" @click.outside="isOpen = false">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text"
                                           x-ref="searchInput"
                                           x-model="searchQuery"
                                           @focus="isOpen = true"
                                           @input="isOpen = true"
                                           @keydown.escape="isOpen = false"
                                           placeholder="Ketik untuk mencari nama kucing, nomor KTAKuMu, pemilik, atau ras..."
                                           class="w-full text-sm pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-100 transition shadow-2xs">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1">
                                        <button type="button" x-show="searchQuery" @click="searchQuery = ''; $refs.searchInput.focus()" class="text-slate-400 hover:text-slate-600 p-1 text-xs">✕</button>
                                        <button type="button" @click="isOpen = !isOpen" class="text-slate-400 hover:text-slate-600 p-1">
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Dropdown Filter Results -->
                                <div x-show="isOpen"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="absolute z-30 mt-1.5 w-full bg-white rounded-xl border border-slate-200 shadow-xl max-h-72 overflow-y-auto divide-y divide-slate-100"
                                     style="display: none;">
                                    
                                    <template x-for="cat in filteredCats" :key="cat.id">
                                        <div @click="selectCat(cat)"
                                             class="p-3 hover:bg-teal-50/80 cursor-pointer transition flex items-center justify-between gap-3 text-left">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <template x-if="cat.photo_url">
                                                    <img :src="cat.photo_url" :alt="cat.name" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shrink-0">
                                                </template>
                                                <template x-if="!cat.photo_url">
                                                    <div class="w-10 h-10 rounded-lg bg-teal-700 text-white font-bold flex items-center justify-center shrink-0 text-sm" x-text="cat.name.charAt(0)"></div>
                                                </template>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-bold text-slate-900 text-sm" x-text="cat.name"></span>
                                                        <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-[10px] font-mono font-bold" x-text="cat.unique_code"></span>
                                                        <span class="text-[10px] text-slate-500 font-medium" x-text="`(${cat.breed} • ${cat.gender})`"></span>
                                                    </div>
                                                    <div class="text-xs text-slate-600 mt-0.5">
                                                        <span>Pemilik: </span>
                                                        <strong class="text-slate-800" x-text="cat.owner_name"></strong>
                                                        <span x-show="cat.owner_nbm && cat.owner_nbm !== '-'" class="text-slate-500" x-text="` (NBM: ${cat.owner_nbm})`"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-teal-50 text-teal-800 border border-teal-200 shrink-0 hover:bg-teal-700 hover:text-white transition">
                                                Pilih Pasien &rarr;
                                            </span>
                                        </div>
                                    </template>

                                    <div x-show="filteredCats.length === 0" class="p-6 text-center text-xs text-slate-500 space-y-1">
                                        <div class="text-lg">🔍</div>
                                        <div class="font-semibold text-slate-700">Tidak ada pasien yang cocok</div>
                                        <p class="text-[11px] text-slate-400">Tidak ditemukan kucing ber-KTAKuMu yang sesuai dengan kata kunci pencarian Anda.</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Ketik nama kucing, kode KTAKuMu, ras, atau nama pemilik untuk mencari pasien secara cepat.</p>
                        @endif
                    </div>

                    @error('cat_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Section: Service Type & Clinic -->
                <div class="grid md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Jenis Layanan <span class="text-rose-500">*</span>
                        </label>
                        <select name="service_type" required
                            class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600">
                            <option value="clinic" {{ old('service_type') === 'clinic' ? 'selected' : '' }}>Pemeriksaan
                                Fisik Langsung (Klinik)</option>
                            <option value="online" {{ old('service_type') === 'online' ? 'selected' : '' }}>Telekonsultasi
                                Jarak Jauh (Online)</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Fasilitas / Klinik
                        </label>
                        <input type="text" name="clinic_name" value="{{ old('clinic_name', 'Klinik Hewan KucingMu') }}"
                            class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600"
                            placeholder="e.g. Klinik Hewan KucingMu">
                    </div>
                </div>

                <!-- Section: Chief Complaint -->
                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Keluhan Utama Member / Pasien (Chief Complaint) <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="chief_complaint" rows="3" required
                        class="w-full text-sm rounded-xl border-slate-200 p-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600"
                        placeholder="Tuliskan keluhan utama kucing, misalnya: Kucing muntah-muntah sejak kemarin, nafsu makan hilang, mata berair..."></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Keluhan asli member disimpan sebagai data dasar dan dapat
                        diklarifikasi lebih detail pada Step 1 (Subjective).</p>
                </div>

                <!-- Section: Onset Gejala -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kapan Gejala Pertama Kali Muncul (Onset)
                    </label>
                    <input type="text" name="symptom_onset" value="{{ old('symptom_onset', '1-2 hari yang lalu') }}"
                        class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600"
                        placeholder="e.g. Sejak 3 hari lalu, tadi pagi, 1 minggu terakhir...">
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('dokter.medical-records.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition shadow-xs">
                        <span>Lanjut ke Workspace SOAP Stepper &rarr;</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>