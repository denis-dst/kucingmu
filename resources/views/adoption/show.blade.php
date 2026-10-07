<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('adoption.index') }}" class="p-2 rounded-xl text-slate-500 hover:text-teal-800 hover:bg-slate-100 transition" title="Kembali ke Katalog">
                    ←
                </a>
                <div>
                    <span class="eyebrow">Detail Profil Adopsi Anabul</span>
                    <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">
                        {{ $catData->name }}
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('adoption.index') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>←</span> Katalog Adopsi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        showApplyModal: false, 
        activePhoto: '{{ $catData->photo_url }}',
        applicantName: '{{ Auth::check() ? Auth::user()->name : '' }}',
        applicantEmail: '{{ Auth::check() ? Auth::user()->email : '' }}',
        applicantPhone: '{{ Auth::check() ? (Auth::user()->phone ?? '') : '' }}',
        applicantCity: '',
        applicantAddress: '',
        housingType: 'Rumah Sendiri / Keluarga',
        hasOtherPets: 'Tidak Ada',
        familyConsent: true,
        commitmentNotes: ''
    }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Success Alert after Submission -->
            @if(session('adoption_success'))
                @php $succ = session('adoption_success'); @endphp
                <div class="p-6 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-3xl shadow-lg space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-3xl">🎉</span>
                        <div>
                            <h2 class="font-outfit text-lg sm:text-xl font-bold">
                                Alhamdulillah! Permohonan Adopsi Berhasil Dikirim
                            </h2>
                            <p class="text-xs sm:text-sm text-teal-100 mt-1 leading-relaxed">
                                Terima kasih <strong>{{ $succ['name'] }}</strong>, permohonan adopsi Anda untuk anabul <strong>{{ $succ['cat_name'] }}</strong> telah tercatat dengan Kode Permohonan:
                            </p>
                            <div class="inline-block mt-2 px-3 py-1.5 rounded-xl bg-white text-teal-900 font-mono text-sm font-extrabold shadow-xs">
                                🔖 {{ $succ['code'] }}
                            </div>
                            <p class="text-xs text-teal-100 mt-3">
                                Tim Administrator KucingMu akan meninjau komitmen Anda dan menghubungi Anda via WhatsApp/Email untuk tahap selanjutnya. Anda juga dapat konfirmasi langsung ke admin melalui tombol WhatsApp di bawah.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Cat Profile Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Gallery & Quick Facts (5 cols) -->
                <div class="lg:col-span-5 space-y-5">
                    
                    <!-- Main Active Photo -->
                    <div class="content-card bg-white rounded-3xl p-3 border border-slate-200 shadow-sm overflow-hidden">
                        <div class="relative aspect-square w-full rounded-2xl overflow-hidden bg-slate-100 shadow-inner">
                            <img :src="activePhoto" alt="{{ $catData->name }}" class="w-full h-full object-cover">
                            
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs border border-slate-200">
                                    {{ $catData->source_label }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border shadow-xs backdrop-blur-xs {{ $catData->badge_class }}">
                                    {{ $catData->status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Thumbnail Gallery if multiple photos -->
                        @if(isset($catData->all_photos) && $catData->all_photos->count() > 1)
                            <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1">
                                @foreach($catData->all_photos as $photo)
                                    @php $photoUrl = asset('storage/' . $photo->photo_path); @endphp
                                    <button type="button" 
                                            @click="activePhoto = '{{ $photoUrl }}'"
                                            class="w-16 h-16 rounded-xl overflow-hidden border-2 transition shrink-0 cursor-pointer"
                                            :class="activePhoto === '{{ $photoUrl }}' ? 'border-teal-600 ring-2 ring-teal-500/30' : 'border-slate-200 opacity-70 hover:opacity-100'">
                                        <img src="{{ $photoUrl }}" alt="Thumb" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Quick Identitas Card -->
                    <div class="content-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ringkasan Identitas</h3>
                        
                        <div class="divide-y divide-slate-100 text-xs text-slate-700">
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Nomor Registrasi</span>
                                <span class="font-mono font-bold text-teal-800">{{ $catData->unique_code }}</span>
                            </div>
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Ras / Jenis</span>
                                <span class="font-semibold text-slate-900">{{ $catData->breed }}</span>
                            </div>
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Jenis Kelamin</span>
                                <span class="font-semibold text-slate-900">{{ $catData->gender_label }}</span>
                            </div>
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Perkiraan Usia</span>
                                <span class="font-semibold text-slate-900">{{ $catData->age_text }}</span>
                            </div>
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Warna / Corak</span>
                                <span class="font-semibold text-slate-900">{{ $catData->color ?: 'Domestik' }}</span>
                            </div>
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Lokasi Anabul</span>
                                <span class="font-semibold text-slate-900">📍 {{ $catData->location }}</span>
                            </div>
                            <div class="py-2 flex items-center justify-between">
                                <span class="text-slate-500">Biaya Adopsi</span>
                                <span class="font-bold text-emerald-700">🎁 {{ $catData->fee_type }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Protected Guardian Information (Privasi Terjaga) -->
                    <div class="content-card bg-gradient-to-r from-teal-50 to-emerald-50 rounded-2xl border border-teal-200 p-5 shadow-sm space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🛡️</span>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-teal-900">Wali / Pemilik Asli</h3>
                                <p class="text-[11px] text-teal-700">Privasi nomor kontak terlindungi</p>
                            </div>
                        </div>

                        <div class="p-3 bg-white/90 rounded-xl border border-teal-100 text-xs space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Identitas Wali:</span>
                                <strong class="text-slate-900">{{ $catData->guardian_name }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Wilayah / Kota:</span>
                                <span class="font-medium text-slate-800">{{ $catData->location }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Kontak Langsung:</span>
                                <span class="text-[10px] bg-teal-100 text-teal-800 px-2 py-0.5 rounded font-bold">
                                    Dimediasi Admin
                                </span>
                            </div>
                        </div>

                        <p class="text-[11px] text-teal-800 leading-relaxed">
                            Demi keamanan dan kenyamanan kedua belah pihak, nomor telepon dan alamat pemilik dimediasi melalui Admin KucingMu. Ajukan permohonan untuk proses temu & adopsi.
                        </p>
                    </div>

                </div>

                <!-- Right Column: Story, Health & CTA (7 cols) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Bio & Adoption Notes Card -->
                    <div class="content-card bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-5">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tentang Anabul</span>
                            <h2 class="font-outfit text-2xl font-extrabold text-slate-900 mt-1">
                                Mengenal Lebih Dekat {{ $catData->name }}
                            </h2>
                        </div>

                        <!-- Notes / Story -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs sm:text-sm text-slate-700 leading-relaxed space-y-2">
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span>📝</span> Cerita, Karakter & Syarat Adopsi:
                            </div>
                            @if(!empty($catData->notes))
                                <p class="whitespace-pre-line text-slate-800 font-normal">
                                    {{ $catData->notes }}
                                </p>
                            @else
                                <p class="text-slate-500 italic">
                                    Belum ada catatan khusus. Kucing dalam kondisi sehat, terawat, dan siap menyambut keluarga baru yang amanah dan penuh kasih sayang.
                                </p>
                            @endif
                        </div>

                        <!-- Health & Medical Details -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Riwayat Kesehatan & Perawatan</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                                        <span>💉</span> Riwayat Vaksinasi
                                    </div>
                                    <div class="text-xs font-bold text-slate-900">
                                        {{ $catData->vaccine_history ?: 'Belum Tercatat / Standar Pemeriksaan' }}
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                                        <span>⚠️</span> Alergi / Pantangan
                                    </div>
                                    <div class="text-xs font-bold text-slate-900">
                                        {{ $catData->allergies ?: 'Tidak Ada Alergi Diketahui' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Records History if any -->
                            @if(isset($catData->medical_records) && $catData->medical_records->isNotEmpty())
                                <div class="p-3.5 rounded-xl bg-teal-50/60 border border-teal-100 text-xs space-y-2">
                                    <div class="font-bold text-teal-900 flex items-center gap-1.5">
                                        <span>🩺</span> Riwayat Pemeriksaan Dokter Hewan:
                                    </div>
                                    <ul class="space-y-1.5 text-[11px] text-teal-800">
                                        @foreach($catData->medical_records->take(3) as $med)
                                            <li class="flex items-start gap-1.5">
                                                <span>✓</span>
                                                <div>
                                                    <strong>{{ $med->created_at->format('d/m/Y') }}:</strong> {{ $med->diagnosis ?: 'Pemeriksaan Rutin' }} 
                                                    @if($med->vet)
                                                        &bull; Oleh: <em>drh. {{ $med->vet->name }}</em>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Adoption Requirements Guide -->
                        <div class="p-4 bg-amber-50/70 rounded-2xl border border-amber-200 text-xs text-amber-900 space-y-2">
                            <div class="font-bold flex items-center gap-1.5 text-amber-950">
                                <span>📋</span> Kriteria Calon Pengadopsi Amanah:
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-[11px] text-amber-800">
                                <li>Mendapat persetujuan dari seluruh anggota keluarga / penghuni rumah.</li>
                                <li>Berkomitmen memelihara anabul secara bertanggung jawab (kebutuhan pakan, kebersihan, dan kesehatan).</li>
                                <li>Tidak untuk diperjualbelikan kembali atau ditelantarkan.</li>
                                <li>Bersedia memberikan kabar perkembangan anabul secara berkala ke admin / pemilik sebelumnya.</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                            <button type="button" 
                                    @click="showApplyModal = true"
                                    class="w-full sm:flex-1 button-primary text-xs sm:text-sm font-bold py-3.5 px-6 rounded-2xl shadow-md inline-flex items-center justify-center gap-2">
                                <span>💖</span> Ajukan Adopsi Anabul Ini
                            </button>

                            <a href="{{ $adminWaUrl }}" target="_blank"
                               class="w-full sm:w-auto px-5 py-3.5 rounded-2xl text-xs sm:text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md inline-flex items-center justify-center gap-2 transition">
                                <span>💬</span> WhatsApp Admin
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Modal Formulir Pengajuan Adopsi -->
        <div x-show="showApplyModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-200 my-8" @click.away="showApplyModal = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">💖</span>
                        <div>
                            <h3 class="font-outfit text-base sm:text-lg font-bold text-slate-900">Formulir Pengajuan Adopsi</h3>
                            <p class="text-xs text-slate-500">Anabul: <strong>{{ $catData->name }}</strong> ({{ $catData->unique_code }})</p>
                        </div>
                    </div>
                    <button type="button" @click="showApplyModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-base">✕</button>
                </div>

                <form method="POST" action="{{ route('adoption.apply', [$catData->source_type, $catData->id]) }}" class="space-y-4">
                    @csrf

                    <!-- Nama & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Nama Lengkap Pemohon <span class="text-rose-500">*</span></label>
                            <input type="text" name="applicant_name" x-model="applicantName" required class="form-input text-xs" placeholder="Nama Lengkap Anda">
                        </div>

                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="applicant_email" x-model="applicantEmail" required class="form-input text-xs font-mono" placeholder="email@contoh.com">
                        </div>
                    </div>

                    <!-- No WhatsApp & Kota -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span></label>
                            <input type="tel" name="applicant_phone" x-model="applicantPhone" required class="form-input text-xs font-mono" placeholder="08123456789">
                        </div>

                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Kota / Kabupaten Domisili <span class="text-rose-500">*</span></label>
                            <input type="text" name="applicant_city" x-model="applicantCity" required class="form-input text-xs" placeholder="e.g. Sleman / Yogyakarta">
                        </div>
                    </div>

                    <!-- Tipe Tempat Tinggal & Hewan Lain -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Tipe Tempat Tinggal <span class="text-rose-500">*</span></label>
                            <select name="housing_type" x-model="housingType" required class="form-input text-xs py-2">
                                <option value="Rumah Sendiri / Keluarga">Rumah Sendiri / Keluarga</option>
                                <option value="Rumah Kontrakan">Rumah Kontrakan</option>
                                <option value="Kost Ramah Hewan">Kost Ramah Hewan</option>
                                <option value="Apartemen">Apartemen</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Hewan Lain di Rumah</label>
                            <input type="text" name="has_other_pets" x-model="hasOtherPets" class="form-input text-xs" placeholder="e.g. 1 Kucing / Tidak Ada">
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Alamat Lengkap Domisili</label>
                        <textarea name="applicant_address" x-model="applicantAddress" rows="2" class="form-input text-xs leading-relaxed" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."></textarea>
                    </div>

                    <!-- Komitmen & Alasan -->
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Alasan & Komitmen Merawat Anabul <span class="text-rose-500">*</span></label>
                        <textarea name="commitment_notes" x-model="commitmentNotes" rows="3" required class="form-input text-xs leading-relaxed" placeholder="Ceritakan pengalaman Anda memelihara kucing, kesiapan fasilitas, dan komitmen menjaga kesejahteraan {{ $catData->name }}..."></textarea>
                    </div>

                    <!-- Persetujuan Keluarga -->
                    <div class="p-3 bg-teal-50/70 rounded-xl border border-teal-100 flex items-start gap-2.5">
                        <input type="checkbox" name="has_family_consent" value="1" id="family_consent" x-model="familyConsent" required class="mt-0.5 rounded text-teal-600 focus:ring-teal-500">
                        <label for="family_consent" class="text-xs text-teal-950 leading-snug cursor-pointer">
                            <strong>Persetujuan Penuh:</strong> Saya menyatakan bahwa seluruh anggota keluarga / penghuni rumah telah setuju untuk mengadopsi anabul ini secara bertanggung jawab.
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showApplyModal = false" class="button-secondary text-xs px-4 py-2.5 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="button-primary text-xs font-bold px-6 py-2.5 rounded-xl shadow-sm inline-flex items-center gap-1.5">
                            <span>🚀</span> Kirim Pengajuan Adopsi
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</x-app-layout>
