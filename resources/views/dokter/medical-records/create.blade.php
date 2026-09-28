<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">
        
        <!-- Breadcrumb & Nav -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('dokter.medical-records.index') }}" class="hover:text-teal-700 font-medium">Rekam Medis</a>
            <span>&bull;</span>
            <span class="text-slate-800 font-semibold">Mulai Pemeriksaan Baru</span>
        </div>

        <!-- Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 flex items-center justify-center text-2xl shrink-0">
                    🩺
                </div>
                <div>
                    <h1 class="font-outfit text-xl md:text-2xl font-bold text-slate-900">
                        Inisiasi Pemeriksaan Kucing Pasien
                    </h1>
                    <p class="text-xs md:text-sm text-slate-500 mt-1">
                        Pilih kucing yang akan diperiksa, tentukan model layanan (klinik langsung atau telekonsultasi), dan catat keluhan awal sebelum masuk ke workspace SOAP.
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

                <!-- Section: Patient Selection -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pilih Pasien Kucing <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-teal-800 bg-teal-50 px-2 py-0.5 rounded font-semibold border border-teal-200">
                            Hanya KTAKuMu Terverifikasi
                        </span>
                    </div>

                    @if($selectedCat)
                        <input type="hidden" name="cat_id" value="{{ $selectedCat->id }}">
                        <div class="p-4 rounded-xl border border-teal-200 bg-teal-50/50 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @if($selectedCat->primary_photo_url)
                                    <img src="{{ $selectedCat->primary_photo_url }}" alt="{{ $selectedCat->name }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-teal-700 text-white font-bold text-xl flex items-center justify-center">
                                        {{ substr($selectedCat->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-outfit font-bold text-slate-900 text-base">{{ $selectedCat->name }}</h3>
                                        @if($selectedCat->unique_code)
                                            <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-[10px] font-mono font-bold">{{ $selectedCat->unique_code }}</span>
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">✓ KTAKuMu Resmi</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold">⚠️ Belum Terbit KTAKuMu</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600 mt-0.5">Ras: {{ $selectedCat->breed }} &bull; Kelamin: {{ $selectedCat->gender === 'male' ? 'Jantan' : 'Betina' }} &bull; Lahir: {{ $selectedCat->date_of_birth ? \Carbon\Carbon::parse($selectedCat->date_of_birth)->format('d M Y') : '-' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Pemilik: <span class="font-semibold text-slate-800">{{ $selectedCat->owner->name ?? '-' }}</span> (NBM: {{ $selectedCat->owner->formatted_nbm ?? '-' }})</p>
                                </div>
                            </div>
                            <a href="{{ route('dokter.medical-records.create') }}" class="text-xs font-semibold text-teal-800 hover:underline">Ganti Kucing</a>
                        </div>
                    @else
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
                            <div>
                                <select name="cat_id" required class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600">
                                    <option value="">-- Pilih Kucing Terverifikasi (KTAKuMu Resmi) --</option>
                                    @foreach($availableCats as $cat)
                                        <option value="{{ $cat->id }}" {{ old('cat_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }} [KTAKuMu: {{ $cat->unique_code }}] &bull; Pemilik: {{ $cat->owner->name ?? '-' }} ({{ $cat->breed }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">Hanya menampilkan kucing yang telah disetujui/diverifikasi oleh Admin dan memiliki Nomor KTAKuMu resmi.</p>
                            </div>
                        @endif
                        @error('cat_id')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <!-- Section: Service Type & Clinic -->
                <div class="grid md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Jenis Layanan <span class="text-rose-500">*</span>
                        </label>
                        <select name="service_type" required class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600">
                            <option value="clinic" {{ old('service_type') === 'clinic' ? 'selected' : '' }}>Pemeriksaan Fisik Langsung (Klinik)</option>
                            <option value="online" {{ old('service_type') === 'online' ? 'selected' : '' }}>Telekonsultasi Jarak Jauh (Online)</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Sesuai PRD, struktur SOAP sama namun sumber pemeriksaan fisik disesuaikan.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Fasilitas / Klinik
                        </label>
                        <input type="text" name="clinic_name" value="{{ old('clinic_name', 'Klinik Hewan KucingMu') }}" class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600" placeholder="e.g. Klinik Hewan KucingMu">
                    </div>
                </div>

                <!-- Section: Chief Complaint -->
                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Keluhan Utama Member / Pasien (Chief Complaint) <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="chief_complaint" rows="3" required class="w-full text-sm rounded-xl border-slate-200 p-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600" placeholder="Tuliskan keluhan utama kucing, misalnya: Kucing muntah-muntah sejak kemarin, nafsu makan hilang, mata berair..."></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Keluhan asli member disimpan sebagai data dasar dan dapat diklarifikasi lebih detail pada Step 1 (Subjective).</p>
                </div>

                <!-- Section: Onset Gejala -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kapan Gejala Pertama Kali Muncul (Onset)
                    </label>
                    <input type="text" name="symptom_onset" value="{{ old('symptom_onset', '1-2 hari yang lalu') }}" class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600" placeholder="e.g. Sejak 3 hari lalu, tadi pagi, 1 minggu terakhir...">
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('dokter.medical-records.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition shadow-xs">
                        <span>Lanjut ke Workspace SOAP Stepper &rarr;</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
