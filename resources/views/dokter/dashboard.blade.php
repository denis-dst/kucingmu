<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8" x-data="{ activeAppointment: null }">
        
        <!-- Hero Panel -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold">
                    <span>🩺</span>
                    <span>Ruang Kerja Dokter Hewan</span>
                </div>
                <h1 class="font-outfit text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
                    Selamat Bertugas, {{ Auth::user()->name }}!
                </h1>
                <p class="text-sm text-slate-600 max-w-2xl leading-relaxed">
                    Akses rekam medis pasien terpadu, kelola antrian pemeriksaan kucing hari ini, dokumentasikan temuan klinis dengan standar <strong>SOAP</strong>, dan terbitkan resep elektronik terstruktur.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('dokter.medical-records.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition shadow-2xs">
                    📁 Semua Rekam Medis
                </a>
                <a href="{{ route('dokter.medical-records.create') }}" class="px-4 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition shadow-xs flex items-center gap-1.5">
                    <span>+ Periksa Pasien Baru</span>
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center gap-2">
                <span>⚠</span> {{ session('error') }}
            </div>
        @endif

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Antrian Hari Ini</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['today_queue'] ?? $queue->count() }}</span>
                    <span class="text-xs text-slate-500">pasien</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Draft Pemeriksaan</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-amber-600">{{ $stats['in_progress'] ?? ($inProgressDrafts->count() ?? 0) }}</span>
                    <span class="text-xs text-slate-500">berjalan</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Selesai</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-emerald-700">{{ $stats['completed'] ?? 0 }}</span>
                    <span class="text-xs text-slate-500">rekam medis</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Resep Diterbitkan</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-indigo-700">{{ $stats['total_prescriptions'] ?? 0 }}</span>
                    <span class="text-xs text-slate-500">e-Prescription</span>
                </div>
            </div>
        </div>

        <!-- In-Progress Drafts Alert Banner (if any) -->
        @if(isset($inProgressDrafts) && $inProgressDrafts->isNotEmpty())
            <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⏳</span>
                        <h3 class="font-outfit font-bold text-amber-900 text-sm">Pemeriksaan Berjalan (Draft Belum Selesai)</h3>
                    </div>
                    <span class="text-xs font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">{{ $inProgressDrafts->count() }} Draft</span>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($inProgressDrafts as $draft)
                        <div class="p-3 bg-white rounded-xl border border-amber-200/80 flex items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="font-bold text-slate-900">{{ $draft->cat->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $draft->record_number }} &bull; {{ $draft->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('dokter.medical-records.examine', $draft) }}" class="px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white font-semibold text-[11px] transition shrink-0 shadow-2xs">
                                Lanjutkan SOAP &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Dashboard Content Grid -->
        <div class="grid gap-8 lg:grid-cols-3">
            
            <!-- Left Queue Column -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Today's Examination Queue -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                        <div>
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Antrian Pasien Hari Ini</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Kucing yang dijadwalkan periksa atau telah di-check in.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-bold border border-teal-200">{{ $queue->count() }} Antrian</span>
                    </div>

                    @if($queue->isEmpty())
                        <div class="text-center py-12 text-slate-500">
                            <span class="text-3xl">📭</span>
                            <p class="mt-4 text-sm font-semibold text-slate-700">Tidak ada antrian pemeriksaan untuk hari ini.</p>
                            <p class="text-xs text-slate-400 mt-1">Kucing akan masuk ke daftar ini setelah relawan melakukan check-in atau membuat janji temu.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($queue as $app)
                                <div class="rounded-xl border border-slate-200 p-5 bg-slate-50/50 hover:bg-white hover:border-teal-300 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        @if($app->cat->primary_photo_url)
                                            <img src="{{ $app->cat->primary_photo_url }}" alt="{{ $app->cat->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-teal-50 text-teal-800 text-xl font-bold flex items-center justify-center shrink-0">
                                                {{ substr($app->cat->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $app->cat->name }}</h3>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $app->status == 'checked_in' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                                    {{ $app->status == 'checked_in' ? 'Siap Periksa' : 'Direncanakan' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-0.5">Ras: {{ $app->cat->breed }} &bull; Kelamin: {{ $app->cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">Pemilik: <span class="font-semibold text-slate-700">{{ $app->cat->owner->name ?? '-' }}</span> (NBM: <span class="font-mono font-medium">{{ $app->cat->owner->formatted_nbm ?? '-' }}</span>)</p>
                                            @if($app->notes)
                                                <p class="text-xs text-slate-500 mt-2 bg-white p-2 rounded border border-slate-100 italic">"{{ $app->notes }}"</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-row md:flex-col items-end gap-2 shrink-0">
                                        <!-- Primary action: Full SOAP Stepper -->
                                        <div class="flex items-center gap-1.5">
                                            <form method="POST" action="{{ route('dokter.medical-records.store') }}">
                                                @csrf
                                                <input type="hidden" name="cat_id" value="{{ $app->cat_id }}">
                                                <input type="hidden" name="appointment_id" value="{{ $app->id }}">
                                                <input type="hidden" name="service_type" value="clinic">
                                                <input type="hidden" name="chief_complaint" value="{{ $app->notes ?: 'Pemeriksaan antrian klinik' }}">
                                                <button type="submit" class="px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition shadow-xs flex items-center gap-1.5">
                                                    <span>🩺 Mulai SOAP</span>
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('dokter.appointments.cancel', $app) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan antrian pasien {{ $app->cat->name }} dari daftar antrian hari ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Batalkan / Hapus Antrian" aria-label="Batalkan dan hapus antrian pasien {{ $app->cat->name }}" class="p-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 text-xs font-semibold flex items-center justify-center transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Secondary action: Quick inline checkup -->
                                        <button type="button" @click="activeAppointment = {{ $app }}" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 underline">
                                            Form Cepat
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Recent Records Section -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Riwayat Pemeriksaan Terakhir Anda</h2>
                            <p class="text-xs text-slate-500">10 pasien terakhir yang Anda tangani.</p>
                        </div>
                        <a href="{{ route('dokter.medical-records.index') }}" class="text-xs font-bold text-teal-700 hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    @if($recentRecords->isEmpty())
                        <p class="text-sm text-slate-500 py-6 text-center">Belum ada pemeriksaan medis yang tersimpan.</p>
                    @else
                        <!-- Desktop / Tablet Table View (horizontal scrollable with min-w, no button wrapping) -->
                        <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-100">
                            <table class="w-full min-w-[660px] text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                                        <th scope="col" class="py-3 px-3.5 whitespace-nowrap">No. RM / Tanggal</th>
                                        <th scope="col" class="py-3 px-3.5 min-w-[130px]">Kucing</th>
                                        <th scope="col" class="py-3 px-3.5 min-w-[130px]">Pemilik</th>
                                        <th scope="col" class="py-3 px-3.5 min-w-[150px]">Diagnosis / Kondisi</th>
                                        <th scope="col" class="py-3 px-3.5 whitespace-nowrap min-w-[110px]">Berat / Suhu</th>
                                        <th scope="col" class="py-3 px-3.5 text-right whitespace-nowrap min-w-[110px]">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                                    @foreach($recentRecords as $rec)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3 px-3.5 whitespace-nowrap">
                                                <div class="font-mono font-bold text-slate-900">{{ $rec->record_number }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $rec->created_at->format('d M Y, H:i') }}</div>
                                            </td>
                                            <td class="py-3 px-3.5 font-bold text-slate-900">
                                                {{ $rec->cat->name ?? '-' }}
                                                @if(isset($rec->cat->breed))
                                                    <span class="block font-normal text-[10px] text-slate-500">{{ $rec->cat->breed }}</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5 text-slate-600">{{ $rec->cat->owner->name ?? ($rec->member->name ?? '-') }}</td>
                                            <td class="py-3 px-3.5">
                                                @if($rec->primaryDiagnosis)
                                                    <span class="font-semibold text-slate-800 block">{{ $rec->primaryDiagnosis->diagnosis_name }}</span>
                                                @elseif($rec->general_condition)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 border border-teal-100 text-teal-800 inline-block">
                                                        {{ $rec->general_condition }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic">Pemeriksaan</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5 font-mono text-[11px] whitespace-nowrap text-slate-800">
                                                {{ $rec->weight }} kg &bull; {{ $rec->temperature }} °C
                                            </td>
                                            <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center justify-end gap-1.5">
                                                    <a href="{{ route('dokter.medical-records.show', $rec) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition" title="Lihat Detail Rekam Medis">
                                                        Detail
                                                    </a>
                                                    <a href="{{ route('dokter.medical-records.print', $rec) }}" target="_blank" class="inline-flex items-center px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition" title="Cetak Ringkasan Medis">
                                                        🖨️
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Responsive Card View (< 640px) -->
                        <div class="block sm:hidden space-y-3">
                            @foreach($recentRecords as $rec)
                                <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/50 hover:bg-white transition space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <div class="font-mono font-bold text-xs text-slate-900">{{ $rec->record_number }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $rec->created_at->format('d M Y, H:i') }}</div>
                                        </div>
                                        @if($rec->primaryDiagnosis)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 border border-teal-200 text-teal-800 max-w-[160px] truncate">
                                                {{ $rec->primaryDiagnosis->diagnosis_name }}
                                            </span>
                                        @elseif($rec->general_condition)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                {{ $rec->general_condition }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between gap-3 text-xs pt-2 border-t border-slate-100">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $rec->cat->name ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-500">Pemilik: {{ $rec->cat->owner->name ?? ($rec->member->name ?? '-') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-mono text-slate-800 text-[11px] font-semibold">{{ $rec->weight }} kg</div>
                                            <div class="font-mono text-slate-500 text-[10px]">{{ $rec->temperature }} °C</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                        <a href="{{ route('dokter.medical-records.show', $rec) }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition min-h-[44px] flex items-center justify-center">
                                            Lihat Detail &rarr;
                                        </a>
                                        <a href="{{ route('dokter.medical-records.print', $rec) }}" target="_blank" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition min-h-[44px] flex items-center justify-center gap-1 shrink-0" title="Cetak Ringkasan Medis">
                                            <span>🖨️</span>
                                            <span>Cetak</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right Quick Checkup Column -->
            <div class="space-y-6">
                
                <!-- Quick Inline Checkup Form -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs" x-show="activeAppointment" x-transition>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <h2 class="font-outfit text-base font-bold text-slate-900">Form Pemeriksaan Cepat</h2>
                        <button type="button" @click="activeAppointment = null" class="text-slate-400 hover:text-slate-600 font-bold text-xs">✕ Tutup</button>
                    </div>

                    <!-- Info Header -->
                    <div class="mb-4 bg-teal-50/50 p-3 rounded-xl border border-teal-100">
                        <div class="text-[10px] text-teal-700 uppercase tracking-wider font-bold">Pasien:</div>
                        <div class="font-outfit text-base font-bold text-slate-900 mt-0.5" x-text="activeAppointment ? activeAppointment.cat.name : ''"></div>
                        <div class="text-xs text-slate-500 mt-0.5" x-text="activeAppointment ? activeAppointment.cat.breed + ' (' + (activeAppointment.cat.gender == 'male' ? 'Jantan' : 'Betina') + ')' : ''"></div>
                    </div>

                    <form method="POST" :action="activeAppointment ? `/checkup/${activeAppointment.id}` : '#'" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Berat (Kg) <span class="text-rose-500">*</span></label>
                                <input type="number" step="0.01" name="weight" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2.5 text-slate-900" placeholder="e.g. 3.5">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Suhu (°C) <span class="text-rose-500">*</span></label>
                                <input type="number" step="0.1" name="temperature" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2.5 text-slate-900" placeholder="e.g. 38.5">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Umum <span class="text-rose-500">*</span></label>
                            <select name="general_condition" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2.5">
                                <option value="Sehat">Sehat</option>
                                <option value="Lemas / Dehidrasi">Lemas / Dehidrasi</option>
                                <option value="Sakit / Demam">Sakit / Demam</option>
                                <option value="Flu Kucing / Bersin">Flu Kucing / Bersin</option>
                                <option value="Gangguan Kulit / Jamur">Gangguan Kulit / Jamur</option>
                            </select>
                        </div>

                        <!-- Treatment Checkboxes -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                            <span class="block text-[11px] font-bold text-slate-700">Tindakan Rutin:</span>
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" name="deworming_given" value="1" class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Pemberian Obat Cacing</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" name="anti_flea_given" value="1" class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Pengobatan Kutu (Anti-Flea)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" name="supplement_given" value="1" class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Pemberian Vitamin / Suplemen</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tindakan / Resep</label>
                            <textarea name="treatment_notes" rows="2" class="w-full text-xs rounded-lg border-slate-200 p-2 text-slate-800" placeholder="Tindakan medis yang diberikan..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Rekomendasi Perawatan</label>
                            <textarea name="recommendation" rows="2" class="w-full text-xs rounded-lg border-slate-200 p-2 text-slate-800" placeholder="Istirahat cukup, bersihkan rutin..."></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition">
                            Simpan Rekam Medis Cepat
                        </button>
                    </form>
                </div>

                <!-- Guidance Box -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="text-base">💡</span>
                        <h3 class="font-outfit font-bold text-slate-900 text-sm">Standar Catatan Klinis SOAP</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Untuk pemeriksaan yang membutuhkan riwayat anamnesa terperinci, resep elektronik terstruktur, dan penegakan diagnosis diferensial, gunakan tombol <strong>"Mulai SOAP"</strong> pada kartu antrian pasien.
                    </p>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Versi PRD: 1.0</span>
                        <a href="{{ route('dokter.medical-records.index') }}" class="font-bold text-teal-700 hover:underline">Kelola Arsip &rarr;</a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
