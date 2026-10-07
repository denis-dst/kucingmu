<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.adoptions.applications') }}" class="p-2 rounded-xl text-slate-500 hover:text-teal-800 hover:bg-slate-100 transition" title="Kembali ke Daftar Permohonan">
                    ←
                </a>
                <div>
                    <span class="eyebrow">Tinjauan & Mediasi Adopsi</span>
                    <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 flex items-center gap-2">
                        <span>Permohonan #{{ $application->application_code }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $application->status_badge_class }}">
                            {{ $application->status_label }}
                        </span>
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.adoptions.applications') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>←</span> Semua Permohonan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @php
                $cat = $application->cat;
                $survey = $application->strayCatSurvey;
                $catName = $cat ? $cat->name : ($survey ? $survey->physical_cat_name : 'Anabul');
                $photoUrl = $cat ? $cat->primary_photo_url : ($survey ? $survey->photo_url : asset('images/default-cat.png'));
                $owner = $cat ? $cat->owner : null;
                $volunteer = $survey ? $survey->volunteer : null;

                // WhatsApp Helper for Adopter
                $cleanAdopterPhone = preg_replace('/[^0-9]/', '', $application->applicant_phone);
                if (str_starts_with($cleanAdopterPhone, '0')) {
                    $cleanAdopterPhone = '62' . substr($cleanAdopterPhone, 1);
                }
                $waAdopterMsg = "Halo kak {$application->applicant_name}, kami dari Tim Administrator KucingMu menindaklanjuti permohonan adopsi Anda [{$application->application_code}] untuk anabul '{$catName}'. Kami ingin melakukan konfirmasi singkat...";
                $waAdopterUrl = "https://wa.me/{$cleanAdopterPhone}?text=" . urlencode($waAdopterMsg);

                // WhatsApp Helper for Owner
                $ownerPhone = $owner ? $owner->phone : ($volunteer ? $volunteer->phone : null);
                $cleanOwnerPhone = $ownerPhone ? preg_replace('/[^0-9]/', '', $ownerPhone) : null;
                if ($cleanOwnerPhone && str_starts_with($cleanOwnerPhone, '0')) {
                    $cleanOwnerPhone = '62' . substr($cleanOwnerPhone, 1);
                }
                $guardianName = $owner ? $owner->name : ($volunteer ? $volunteer->name : 'Pemilik/Relawan');
                $waOwnerMsg = "Halo kak {$guardianName}, kami dari Tim Administrator KucingMu menginformasikan bahwa ada peminat adopsi yang telah mengajukan komitmen untuk anabul '{$catName}' (Kode: {$application->application_code}).";
                $waOwnerUrl = $cleanOwnerPhone ? "https://wa.me/{$cleanOwnerPhone}?text=" . urlencode($waOwnerMsg) : null;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Details & Update Form (8 cols) -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Card 1: Profil Calon Adopter (Pemohon) -->
                    <div class="content-card bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="font-outfit text-base font-bold text-slate-900 flex items-center gap-2">
                                <span>👤</span> Data Calon Adopter (Pemohon)
                            </h2>
                            <span class="text-xs text-slate-400 font-mono">Dibuat: {{ $application->created_at->format('d M Y H:i') }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap:</span>
                                <div class="font-bold text-slate-900 text-sm">{{ $application->applicant_name }}</div>
                                @if($application->applicant)
                                    <div class="text-[10px] text-teal-700 font-semibold">Akun Terdaftar (ID: {{ $application->applicant->id }})</div>
                                @else
                                    <div class="text-[10px] text-slate-500">Peminat Publik</div>
                                @endif
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kontak WhatsApp / HP:</span>
                                <div class="font-bold font-mono text-slate-900 text-sm">{{ $application->applicant_phone }}</div>
                                @if($cleanAdopterPhone)
                                    <a href="{{ $waAdopterUrl }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-100/70 hover:bg-emerald-200 px-2.5 py-1 rounded-lg transition mt-1">
                                        <span>💬</span> Hubungi Calon Adopter via WA
                                    </a>
                                @endif
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email:</span>
                                <div class="font-bold font-mono text-slate-900">{{ $application->applicant_email }}</div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Domisili Kota / Kabupaten:</span>
                                <div class="font-bold text-slate-900">{{ $application->applicant_city ?: '-' }}</div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100 sm:col-span-2">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Alamat Lengkap:</span>
                                <div class="font-medium text-slate-800 leading-relaxed">{{ $application->applicant_address ?: '-' }}</div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tipe Hunian:</span>
                                <div class="font-semibold text-slate-900">{{ $application->housing_type }}</div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl space-y-1 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Hewan Lain di Rumah:</span>
                                <div class="font-semibold text-slate-900">{{ $application->has_other_pets ?: 'Tidak Ada' }}</div>
                            </div>
                        </div>

                        <!-- Pernyataan Komitmen -->
                        <div class="p-4 bg-teal-50/60 rounded-2xl border border-teal-100 space-y-2">
                            <span class="text-[11px] font-bold text-teal-900 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📝</span> Pernyataan & Komitmen Calon Adopter:
                            </span>
                            <p class="text-xs text-slate-800 leading-relaxed italic bg-white p-3 rounded-xl border border-teal-100/80">
                                "{{ $application->commitment_notes }}"
                            </p>
                            <div class="flex items-center gap-2 text-xs text-teal-800 font-semibold pt-1">
                                <span>{{ $application->has_family_consent ? '✅' : '⚠️' }}</span>
                                <span>Persetujuan Anggota Keluarga: {{ $application->has_family_consent ? 'Sudah Disetujui Bersama' : 'Belum Terkonfirmasi' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Anabul & Pemilik Asal (Unmasked untuk Admin) -->
                    <div class="content-card bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="font-outfit text-base font-bold text-slate-900 flex items-center gap-2">
                                <span>🐱</span> Data Anabul & Wali Asal (Pemilik / Relawan)
                            </h2>
                            @if($cat)
                                <a href="{{ route('adoption.show', ['member', $cat->id]) }}" target="_blank" class="text-xs font-bold text-teal-700 hover:text-teal-900">
                                    Lihat Etalase Publik ↗
                                </a>
                            @elseif($survey)
                                <a href="{{ route('adoption.show', ['rescue', $survey->id]) }}" target="_blank" class="text-xs font-bold text-indigo-700 hover:text-indigo-900">
                                    Lihat Etalase Publik ↗
                                </a>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row items-start gap-4">
                            <div class="w-24 h-24 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200 shadow-inner">
                                <img src="{{ $photoUrl }}" alt="{{ $catName }}" class="w-full h-full object-cover">
                            </div>
                            
                            <div class="space-y-1 text-xs flex-1">
                                <h3 class="font-outfit text-lg font-extrabold text-slate-900">{{ $catName }}</h3>
                                <div class="text-slate-500">
                                    @if($cat)
                                        <span>{{ $cat->breed ?? 'Domestik' }}</span> • 
                                        <span>{{ $cat->gender === 'male' ? 'Jantan ♂' : 'Betina ♀' }}</span> • 
                                        <span>{{ $cat->age_text }}</span> • 
                                        <span class="font-mono text-teal-700 font-semibold">{{ $cat->unique_code ?? 'Tanpa KTA' }}</span>
                                    @elseif($survey)
                                        <span>Kucing Rescue Sensus Relawan</span> • 
                                        <span>Lokasi: {{ $survey->campus_location ?: 'Kampus/Lingkungan' }}</span>
                                    @endif
                                </div>

                                <!-- Info Wali / Owner Asal (Unmasked untuk Admin) -->
                                <div class="mt-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-1.5">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kontak Wali / Pemilik Asal (Rahasia untuk Sesama Member):</div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $guardianName }}</div>
                                            @if($owner)
                                                <div class="text-[11px] text-slate-500 font-mono">{{ $owner->email }} • {{ $owner->phone ?? 'Tidak ada no HP' }}</div>
                                            @elseif($volunteer)
                                                <div class="text-[11px] text-slate-500 font-mono">{{ $volunteer->email }} • {{ $volunteer->phone ?? 'Tidak ada no HP' }}</div>
                                            @endif
                                        </div>

                                        @if($waOwnerUrl)
                                            <a href="{{ $waOwnerUrl }}" target="_blank" class="button-secondary text-[11px] font-bold px-2.5 py-1.5 rounded-lg inline-flex items-center gap-1 text-emerald-800 bg-emerald-50 border-emerald-200 hover:bg-emerald-100">
                                                <span>💬</span> Hubungi Wali via WA
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Form Update Status Mediasi & Catatan Admin -->
                    <div class="content-card bg-white rounded-3xl border border-teal-200 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="font-outfit text-base font-bold text-slate-900 flex items-center gap-2">
                                <span>⚙️</span> Perbarui Status Mediasi & Catatan Admin
                            </h2>
                            @if($application->reviewer)
                                <span class="text-xs text-slate-500">
                                    Terakhir ditinjau oleh: <strong>{{ $application->reviewer->name }}</strong>
                                </span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.adoptions.update-status', $application->id) }}" class="space-y-4">
                            @csrf
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label text-xs font-bold text-slate-700">Status Permohonan Saat Ini:</label>
                                    <select name="status" class="form-input text-xs py-2 font-semibold">
                                        <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>🔴 Menunggu Review (Pending)</option>
                                        <option value="reviewing" {{ $application->status === 'reviewing' ? 'selected' : '' }}>🟡 Sedang Ditinjau Admin (Reviewing)</option>
                                        <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>🟢 Disetujui (Tahap Wawancara / Siap Adopsi)</option>
                                        <option value="completed" {{ $application->status === 'completed' ? 'selected' : '' }}>🏠 Selesai Serah Terima (Completed)</option>
                                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>⚪ Ditolak (Rejected)</option>
                                        <option value="cancelled" {{ $application->status === 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan Pemohon (Cancelled)</option>
                                    </select>
                                    <p class="text-[11px] text-slate-400 mt-1">
                                        * Memilih status <strong>Completed</strong> akan otomatis memperbarui status kucing menjadi <em>Adopted</em>.
                                    </p>
                                </div>

                                <div>
                                    <label class="form-label text-xs font-bold text-slate-700">Catatan Internal Admin / Hasil Wawancara:</label>
                                    <textarea name="admin_notes" rows="4" class="form-input text-xs leading-relaxed" placeholder="Tulis hasil konfirmasi WhatsApp, jadwal serah terima, kondisi kandang, dll...">{{ $application->admin_notes }}</textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="submit" class="button-primary text-xs font-bold px-6 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-1.5">
                                    <span>💾</span> Simpan Status & Catatan Mediasi
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- Right Column: Mediation Guide & Danger Zone (4 cols) -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- Panduan Mediasi & Keamanan Privasi -->
                    <div class="content-card bg-gradient-to-br from-teal-900 to-emerald-950 text-white rounded-3xl p-6 shadow-md border border-teal-700/60 space-y-4">
                        <div class="flex items-center gap-2 text-teal-300 font-bold text-xs uppercase tracking-wider">
                            <span>🛡️</span> Protokol Privasi & Mediasi
                        </div>
                        
                        <h3 class="font-outfit text-base font-bold text-white">
                            Mengapa Mediasi Admin Diperlukan?
                        </h3>

                        <div class="text-xs text-teal-100/90 space-y-3 leading-relaxed">
                            <p>
                                1. <strong>Perlindungan Privasi:</strong> Kontak member dan calon adopter dirahasiakan satu sama lain untuk mencegah penyalahgunaan data atau kontak tak diinginkan.
                            </p>
                            <p>
                                2. <strong>Background Check:</strong> Admin memastikan calon adopter memiliki kesiapan tempat tinggal, izin keluarga, serta komitmen bebas penelantaran hewan.
                            </p>
                            <p>
                                3. <strong>Fasilitasi Serah Terima:</strong> Setelah disetujui, admin membuatkan grup WhatsApp bersama atau memfasilitasi titik temu aman untuk serah terima anabul.
                            </p>
                        </div>

                        <div class="p-3 rounded-2xl bg-teal-800/60 border border-teal-600/40 text-[11px] text-teal-200">
                            💡 <em>Tip: Gunakan tombol WhatsApp langsung di atas untuk memulai obrolan dengan template siap pakai.</em>
                        </div>
                    </div>

                    <!-- Danger Zone: Delete Application -->
                    <div class="content-card bg-rose-50/50 rounded-3xl p-6 border border-rose-200 space-y-3">
                        <h3 class="font-outfit text-sm font-bold text-rose-900 flex items-center gap-1.5">
                            <span>⚠️</span> Hapus Permohonan
                        </h3>
                        <p class="text-xs text-rose-800/80 leading-relaxed">
                            Menghapus data permohonan ini secara permanen dari database. Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <form method="POST" action="{{ route('admin.adoptions.destroy-application', $application->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permohonan adopsi ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 px-4 rounded-xl text-xs font-bold text-rose-700 bg-white border border-rose-300 hover:bg-rose-100 transition shadow-2xs">
                                🗑️ Hapus Permohonan Adopsi
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
