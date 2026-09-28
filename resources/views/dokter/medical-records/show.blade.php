<x-app-layout>
    @php
        $subj = $record->subjective;
        $primaryDiag = $record->primaryDiagnosis;
        $objectivesMap = $record->objectives->keyBy('parameter_code');
        $advices = $record->advices->keyBy('category');
        $followup = $record->followups->first();
    @endphp

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6" x-data="{ showAmendModal: false }">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('dokter.medical-records.index') }}" class="hover:text-teal-700 font-medium">Rekam Medis</a>
                <span>&bull;</span>
                <span class="font-mono text-slate-800 font-semibold">{{ $record->record_number }}</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $record->status_badge_class }}">
                    {{ $record->status_label }}
                </span>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('dokter.medical-records.print', $record) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                    <span>🖨️</span>
                    <span>Cetak Lembar Medis</span>
                </a>

                @if($record->isCompleted())
                    <button type="button" @click="showAmendModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-xs transition">
                        <span>✏️</span>
                        <span>Buat Amendemen</span>
                    </button>
                @else
                    <a href="{{ route('dokter.medical-records.examine', $record) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold shadow-xs transition">
                        <span>Lanjutkan SOAP &rarr;</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-medium flex items-center gap-2">
                <span class="text-emerald-700 font-bold">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm font-medium flex items-center gap-2">
                <span class="text-rose-700 font-bold">⚠</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Clinical Record Document -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            
            <!-- Document Header -->
            <div class="p-6 md:p-8 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    @if(isset($app_settings['app_logo']))
                        <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo" class="w-12 h-12 object-contain">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-teal-800 text-white text-2xl font-bold flex items-center justify-center">
                            🩺
                        </div>
                    @endif
                    <div>
                        <div class="text-[11px] font-bold text-teal-800 uppercase tracking-wider">
                            KucingMu Veterinary Medical Record
                        </div>
                        <h1 class="font-outfit text-2xl font-bold text-slate-900">
                            Lembar Catatan Medis & SOAP
                        </h1>
                        <p class="text-xs text-slate-500">
                            Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah &bull; {{ $record->clinic_name ?: 'Klinik Hewan KucingMu' }}
                        </p>
                    </div>
                </div>

                <div class="text-left md:text-right text-xs text-slate-600 space-y-1 font-mono">
                    <div>No. RM: <strong class="text-slate-900 text-sm">{{ $record->record_number }}</strong></div>
                    <div>Tipe Layanan: <strong class="text-slate-800 font-sans">{{ $record->service_type_label }}</strong></div>
                    <div>Waktu: <span class="text-slate-700 font-sans">{{ $record->created_at->format('d M Y, H:i') }}</span></div>
                </div>
            </div>

            <div class="p-6 md:p-8 space-y-8">
                
                <!-- Patient & Owner Info Grid -->
                <div class="grid md:grid-cols-2 gap-6 p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                    <div class="space-y-1 text-xs">
                        <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Identitas Pasien Kucing</div>
                        <div class="font-outfit font-bold text-slate-900 text-base">{{ $record->cat->name }}</div>
                        <div>Ras: <span class="font-semibold text-slate-700">{{ $record->cat->breed }}</span></div>
                        <div>Jenis Kelamin: <span class="font-semibold text-slate-700">{{ $record->cat->gender === 'male' ? 'Jantan' : 'Betina' }}</span></div>
                        <div>Tgl Lahir / Usia: <span class="font-semibold text-slate-700">{{ $record->cat->date_of_birth ? \Carbon\Carbon::parse($record->cat->date_of_birth)->format('d M Y') : '-' }}</span></div>
                        @if($record->cat->unique_code)
                            <div>No. KTAM: <span class="font-mono font-bold text-teal-800">{{ $record->cat->unique_code }}</span></div>
                        @endif
                    </div>

                    <div class="space-y-1 text-xs">
                        <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Pemilik & Dokter Pemeriksa</div>
                        <div>Pemilik: <span class="font-semibold text-slate-900">{{ $record->cat->owner->name ?? ($record->member->name ?? '-') }}</span></div>
                        <div>NBM: <span class="font-mono text-slate-700">{{ $record->cat->owner->formatted_nbm ?? '-' }}</span></div>
                        <div>Telepon: <span class="text-slate-700">{{ $record->cat->owner->phone ?? '-' }}</span></div>
                        <div class="pt-2 border-t border-slate-200">
                            Dokter Pemeriksa: <span class="font-bold text-slate-900">{{ $record->doctor->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 1. SUBJECTIVE -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded-full bg-teal-800 text-white font-bold text-xs flex items-center justify-center">S</span>
                        <h2 class="font-outfit font-bold text-slate-900 text-base">Subjective (Anamnesa)</h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-1">
                            <span class="text-slate-400 font-semibold">Keluhan Asli Pemilik:</span>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-800 font-medium">
                                "{{ $subj?->member_complaint ?? $record->chief_complaint }}"
                            </div>
                        </div>

                        <div class="space-y-1">
                            <span class="text-slate-400 font-semibold">Klarifikasi Dokter:</span>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-slate-800">
                                {{ $subj?->doctor_clarification ?: 'Tidak ada catatan klarifikasi tambahan.' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Onset</span>
                            <strong class="text-slate-800">{{ $subj?->symptom_onset ?? '-' }}</strong>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Nafsu Makan</span>
                            <strong class="text-slate-800">{{ $subj?->appetite_history ?? 'Normal' }}</strong>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Pola Minum</span>
                            <strong class="text-slate-800">{{ $subj?->drinking_history ?? 'Normal' }}</strong>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Urin & Feses</span>
                            <strong class="text-slate-800">{{ $subj?->urination_history ?? 'Normal' }} / {{ $subj?->defecation_history ?? 'Normal' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- 2. OBJECTIVE -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded-full bg-teal-800 text-white font-bold text-xs flex items-center justify-center">O</span>
                        <h2 class="font-outfit font-bold text-slate-900 text-base">Objective (Pemeriksaan Fisik & Vital)</h2>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-teal-50/50 border border-teal-200">
                            <span class="text-slate-500 block text-[10px]">Berat Badan</span>
                            <strong class="text-teal-900 text-base">{{ $record->weight }} kg</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-teal-50/50 border border-teal-200">
                            <span class="text-slate-500 block text-[10px]">Suhu Tubuh</span>
                            <strong class="text-teal-900 text-base">{{ $record->temperature }} °C</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[10px]">Denyut Jantung (HR)</span>
                            <strong class="text-slate-900 text-sm">{{ $objectivesMap['heart_rate']->value_numeric ?? '-' }} bpm</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[10px]">Frekuensi Napas (RR)</span>
                            <strong class="text-slate-900 text-sm">{{ $objectivesMap['respiratory_rate']->value_numeric ?? '-' }} rpm</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Kondisi Umum</span>
                            <strong class="text-slate-800">{{ $record->general_condition }}</strong>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Body Condition (BCS)</span>
                            <strong class="text-slate-800">{{ $objectivesMap['bcs']->value_text ?? '5/9' }}</strong>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Mukosa</span>
                            <strong class="text-slate-800">{{ $objectivesMap['mucous_membrane']->value_text ?? '-' }}</strong>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">CRT</span>
                            <strong class="text-slate-800">{{ $objectivesMap['crt']->value_text ?? '< 2 detik' }}</strong>
                        </div>
                    </div>

                    <!-- Systematic Organ Systems Checked -->
                    <div class="space-y-1 text-xs">
                        <span class="text-slate-400 font-semibold block text-[11px]">Temuan Pemeriksaan Sistem Organ:</span>
                        <div class="grid md:grid-cols-2 gap-2">
                            @foreach($record->objectives->filter(fn($o) => str_starts_with($o->parameter_code, 'sys_')) as $sys)
                                <div class="p-2 rounded bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="font-medium text-slate-800">{{ $sys->parameter_name }}</span>
                                        @if($sys->notes)
                                            <div class="text-[11px] text-slate-500 italic">{{ $sys->notes }}</div>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $sys->finding === 'normal' ? 'bg-emerald-50 text-emerald-800' : ($sys->finding === 'abnormal' ? 'bg-rose-50 text-rose-800' : 'bg-slate-100 text-slate-600') }}">
                                        {{ ucfirst($sys->finding) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 3. ASSESSMENT -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded-full bg-teal-800 text-white font-bold text-xs flex items-center justify-center">A</span>
                        <h2 class="font-outfit font-bold text-slate-900 text-base">Assessment (Diagnosis & Penilaian Klinis)</h2>
                    </div>

                    @if($primaryDiag)
                        <div class="p-4 rounded-xl bg-teal-50/50 border border-teal-200 space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-teal-900 text-sm">{{ $primaryDiag->diagnosis_name }}</span>
                                <span class="px-2 py-0.5 rounded bg-teal-700 text-white font-bold text-[10px]">Diagnosis Utama</span>
                            </div>
                            <div class="text-slate-600">
                                Kepastian: <strong class="capitalize text-slate-800">{{ $primaryDiag->certainty }}</strong> &bull;
                                Keparahan: <strong class="capitalize text-slate-800">{{ $primaryDiag->severity }}</strong>
                            </div>
                            @if($primaryDiag->clinical_reasoning)
                                <div class="text-slate-600 pt-1 italic">
                                    "{{ $primaryDiag->clinical_reasoning }}"
                                </div>
                            @endif
                        </div>
                    @endif

                    @php
                        $secondaryDiags = $record->diagnoses()->where('is_primary', false)->get();
                    @endphp
                    @if($secondaryDiags->isNotEmpty())
                        <div class="space-y-1.5 text-xs">
                            <span class="text-slate-400 font-semibold block text-[11px]">Diagnosis Sekunder / Banding:</span>
                            @foreach($secondaryDiags as $sec)
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <span class="font-semibold text-slate-800">{{ $sec->diagnosis_name }}</span>
                                    <span class="text-[10px] text-slate-500 capitalize">({{ $sec->diagnosis_type }} - {{ $sec->certainty }})</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 4. PLAN & E-PRESCRIPTION -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded-full bg-teal-800 text-white font-bold text-xs flex items-center justify-center">P</span>
                        <h2 class="font-outfit font-bold text-slate-900 text-base">Plan, Terapi & Resep Elektronik</h2>
                    </div>

                    @if($record->treatment_notes)
                        <div class="space-y-1 text-xs">
                            <span class="text-slate-400 font-semibold block text-[11px]">Tindakan Medis di Klinik:</span>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-800">
                                {{ $record->treatment_notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Electronic Prescriptions -->
                    <div class="space-y-2">
                        <span class="text-slate-700 font-bold block text-xs">Resep Elektronik (e-Prescription):</span>
                        @forelse($record->prescriptions as $rx)
                            <div class="rounded-xl border border-teal-200 overflow-hidden">
                                <div class="bg-teal-50 px-4 py-2 border-b border-teal-100 flex items-center justify-between text-xs">
                                    <span class="font-mono font-bold text-teal-900">Rx No: {{ $rx->prescription_number }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $rx->isIssued() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $rx->isIssued() ? 'Diterbitkan (Issued)' : 'Draft Resep' }}
                                    </span>
                                </div>
                                <div class="p-4 divide-y divide-slate-100 bg-white">
                                    @forelse($rx->items as $item)
                                        <div class="py-2.5 first:pt-0 last:pb-0 text-xs space-y-1">
                                            <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                                <span>{{ $item->medicine_name_snapshot }}</span>
                                                <span class="text-[10px] font-semibold text-slate-500 uppercase">[{{ $item->dosage_form }}]</span>
                                            </div>
                                            <div class="text-slate-700">
                                                Aturan: <strong class="text-teal-900">{{ $item->dose_value }} {{ $item->dose_unit }}</strong> &bull;
                                                Frekuensi: <strong>{{ $item->frequency_value }} {{ $item->frequency_unit }}</strong> &bull;
                                                Durasi: <strong>{{ $item->duration_value }} {{ $item->duration_unit }}</strong> &bull;
                                                Rute: <strong>{{ $item->administration_route }}</strong>
                                            </div>
                                            <div class="text-slate-500 italic">
                                                "{{ $item->usage_instructions }}"
                                                @if($item->warnings)
                                                    <span class="text-rose-600 font-medium"> &bull; Catatan: {{ $item->warnings }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-xs text-slate-400 italic">Tidak ada item obat terdaftar.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-400 text-xs italic">
                                Tidak ada resep obat untuk pemeriksaan ini.
                            </div>
                        @endforelse
                    </div>

                    <!-- Home Care & Red Flags -->
                    <div class="grid md:grid-cols-2 gap-4 text-xs pt-2">
                        <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200 space-y-1">
                            <span class="font-bold text-emerald-900 block text-[11px]">🏠 Instruksi Perawatan di Rumah</span>
                            <div class="text-slate-700">
                                {{ $advices['home_care']->instruction ?? ($record->recommendation ?: 'Jaga kebersihan lingkungan kucing dan pantau asupan air minum.') }}
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-200 space-y-1">
                            <span class="font-bold text-rose-900 block text-[11px]">⚠️ Tanda Bahaya (Kapan Harus Kembali Segera)</span>
                            <div class="text-slate-700">
                                {{ $advices['warning_sign']->instruction ?? 'Jika kucing lemas hebat, kesulitan bernapas, atau muntah terus-menerus, segera hubungi dokter.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Follow up -->
                    @if($followup && $followup->scheduled_at)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Rencana Kontrol Lanjutan (Follow-up)</span>
                                <strong class="text-slate-900">{{ $followup->scheduled_at->format('d M Y') }}</strong>
                                <span class="text-slate-500"> &bull; {{ $followup->reason }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">Terjadwal</span>
                        </div>
                    @endif
                </div>

                <!-- 5. AUDIT TRAIL LOG -->
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <span class="font-bold text-slate-700 block text-xs uppercase tracking-wider">Jejak Audit & Riwayat Perubahan (Audit Trail)</span>
                    <div class="space-y-2">
                        @forelse($record->auditLogs as $log)
                            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 text-xs flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $log->action }} &bull; <span class="text-slate-500 font-normal">{{ $log->reason ?: 'Perubahan tercatat oleh sistem.' }}</span></div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">Aktor: {{ $log->actor->name ?? 'User #' . $log->actor_id }}</div>
                                </div>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $log->occurred_at->format('d M Y, H:i') }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 italic">Belum ada audit log tambahan.</div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Footer Signature Box -->
            <div class="p-6 md:p-8 bg-slate-50/80 border-t border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="text-xs text-slate-500 space-y-1">
                    <div>Dokumen medis ini sah dan tercatat pada basis data terenkripsi KucingMu.</div>
                    <div class="text-[10px] text-slate-400">ID Pemeriksaan: {{ $record->id }} &bull; Ref: {{ $record->record_number }}</div>
                </div>

                <div class="text-center md:text-right text-xs">
                    <div class="text-slate-500">Dokter Hewan yang Bertugas:</div>
                    <div class="font-outfit font-bold text-slate-900 text-sm mt-1">{{ $record->doctor->name ?? '-' }}</div>
                    <div class="text-[10px] text-slate-400">NBM: {{ $record->doctor->formatted_nbm ?? '-' }}</div>
                </div>
            </div>

        </div>

        <!-- ===================================================================
             MODAL: BUAT AMENDEMEN REKAM MEDIS (PRD 10.3)
             =================================================================== -->
        <div x-show="showAmendModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showAmendModal = false" class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"></div>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                    <form method="POST" action="{{ route('dokter.medical-records.amend', $record) }}">
                        @csrf
                        <div class="bg-white px-6 pt-5 pb-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">✏️</span>
                                <h3 class="font-outfit text-base font-bold text-slate-900">Buat Amendemen Rekam Medis</h3>
                            </div>
                            <button type="button" @click="showAmendModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-sm">✕</button>
                        </div>

                        <div class="p-6 space-y-4 text-xs">
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                                <strong>Ketentuan Amendemen:</strong> Rekam medis yang telah difinalisasi tidak boleh diubah diam-diam. Anda wajib mencantumkan alasan klinis koreksi. Versi sebelum dan sesudah perubahan akan dicatat permanen dalam audit trail.
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Alasan Koreksi / Amendemen <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="amendment_reason" rows="2" required class="w-full text-xs rounded-xl border-slate-200 p-3 text-slate-800 focus:ring-teal-600" placeholder="e.g. Koreksi catatan terapi: pasien dilaporkan muntah setelah pemberian obat pertama, terapi diganti..."></textarea>
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Catatan Terapi / Prosedur Yang Diperbarui <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="amended_notes" rows="3" required class="w-full text-xs rounded-xl border-slate-200 p-3 text-slate-800 focus:ring-teal-600">{{ $record->treatment_notes }}</textarea>
                            </div>

                            <div>
                                <label class="block font-bold uppercase tracking-wider text-slate-500 mb-1">
                                    Catatan Internal Tambahan
                                </label>
                                <textarea name="internal_notes" rows="2" class="w-full text-xs rounded-xl border-slate-200 p-3 text-slate-700 bg-slate-50">{{ $record->internal_notes }}</textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" @click="showAmendModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-xs">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition">
                                Simpan Amendemen ke Audit Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
