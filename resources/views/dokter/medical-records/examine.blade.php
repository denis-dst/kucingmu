<x-app-layout>
    @php
        $subj = $record->subjective;
        $primaryDiag = $record->primaryDiagnosis;
        $activeRx = $record->prescriptions()->where('status', 'draft')->first() ?: $record->prescriptions()->first();
        $advices = $record->advices->keyBy('category');
        $followup = $record->followups->first();
        $objectivesMap = $record->objectives->keyBy('parameter_code');

        // Extract system check values
        $getSys = function($code) use ($objectivesMap) {
            $key = "sys_{$code}";
            return [
                'status' => $objectivesMap[$key]->value_text ?? 'normal',
                'notes' => $objectivesMap[$key]->notes ?? '',
            ];
        };
    @endphp

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6" 
         x-data="{ 
             currentStep: {{ $activeStep ?: 1 }},
             rxMode: '{{ $activeRx ? $activeRx->prescription_type : 'regular' }}',
             setStep(step) {
                 this.currentStep = step;
                 const url = new URL(window.location.href);
                 url.searchParams.set('step', step);
                 window.history.replaceState({}, '', url.toString());
                 fetch('{{ route('dokter.medical-records.touch-step', $record) }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({ step: step })
                 }).catch(() => {});
             },
             showHistoryModal: false,
             showCopyModal: false,
             copyTypeFilter: 'all',
             copySearch: '',
             copyablePrescriptions: {{ json_encode($copyablePrescriptionsData ?? []) }},
             openCopyModal(filterType = 'all') {
                 this.copyTypeFilter = filterType;
                 this.copySearch = '';
                 this.showCopyModal = true;
             },
             get filteredCopyablePrescriptions() {
                 const search = (this.copySearch || '').trim().toLowerCase();
                 return this.copyablePrescriptions.filter(rx => {
                     const matchesType = this.copyTypeFilter === 'all' 
                         || (this.copyTypeFilter === 'regular' && (rx.type === 'regular' || rx.items.some(i => !i.is_racikan)))
                         || (this.copyTypeFilter === 'compounded' && (rx.type === 'compounded' || rx.items.some(i => i.is_racikan)));
                     const matchesSearch = !search || (rx.search_text && rx.search_text.includes(search));
                     return matchesType && matchesSearch;
                 });
             },
             rxMedicineSearch: '',
             rxSelectedMedicine: null,
             medicinesList: {{ $medicines->toJson() }},
             secondaryDiagnoses: {{ json_encode($record->diagnoses()->where('is_primary', false)->get()->map(fn($d) => ['name' => $d->diagnosis_name, 'certainty' => $d->certainty, 'severity' => $d->severity, 'notes' => $d->clinical_reasoning])->values()) }},
             addSecondaryDiagnosis() {
                 this.secondaryDiagnoses.push({ name: '', certainty: 'provisional', severity: 'moderate', notes: '' });
             },
             removeSecondaryDiagnosis(idx) {
                 this.secondaryDiagnoses.splice(idx, 1);
             },
             selectMedicine(med) {
                 this.rxSelectedMedicine = med;
                 document.getElementById('rx_medicine_name').value = med.name;
                 document.getElementById('rx_medicine_id').value = med.id;
                 document.getElementById('rx_dosage_form').value = med.dosage_form || 'tablet';
                 document.getElementById('rx_active_ingredient').value = med.active_ingredient || '';
                 document.getElementById('rx_concentration_value').value = med.concentration_value || '';
                 document.getElementById('rx_concentration_unit').value = med.concentration_unit || '';
                 this.rxMedicineSearch = med.name;
             }
         }">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('dokter.medical-records.index') }}" class="hover:text-teal-700 font-medium">Rekam Medis</a>
                <span>&bull;</span>
                <span class="font-mono text-slate-800 font-semibold">{{ $record->record_number }}</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $record->status_badge_class }}">
                    {{ $record->status_label }}
                </span>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" @click="showHistoryModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                    <span>📜</span>
                    <span>Riwayat Pasien ({{ $previousRecords->count() }})</span>
                </button>
                <a href="{{ route('dokter.medical-records.show', $record) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Tinjau Utuh &rarr;
                </a>
            </div>
        </div>

        <!-- Patient Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    @if($record->cat->primary_photo_url)
                        <img src="{{ $record->cat->primary_photo_url }}" alt="{{ $record->cat->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-2xs shrink-0">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-teal-800 text-white font-bold text-2xl flex items-center justify-center shrink-0">
                            {{ substr($record->cat->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="font-outfit text-xl font-bold text-slate-900">{{ $record->cat->name }}</h1>
                            @if($record->cat->unique_code)
                                <span class="px-2 py-0.5 rounded-md bg-teal-50 border border-teal-200 text-teal-800 text-xs font-mono font-bold">{{ $record->cat->unique_code }}</span>
                            @endif
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-medium">
                                {{ $record->service_type_label }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Ras: <strong class="text-slate-700">{{ $record->cat->breed }}</strong> &bull;
                            Kelamin: <strong class="text-slate-700">{{ $record->cat->gender === 'male' ? 'Jantan' : 'Betina' }}</strong> &bull;
                            Lahir: <strong class="text-slate-700">{{ $record->cat->date_of_birth ? \Carbon\Carbon::parse($record->cat->date_of_birth)->format('d M Y') : '-' }}</strong> &bull;
                            Berat Terakhir: <strong class="text-slate-700">{{ $record->weight > 0 ? $record->weight . ' kg' : 'Belum diukur' }}</strong>
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pemilik: <span class="font-semibold text-slate-800">{{ $record->cat->owner->name ?? '-' }}</span>
                            (NBM: <span class="font-mono text-slate-700">{{ $record->cat->owner->formatted_nbm ?? '-' }}</span> | Telp: <span class="text-slate-700">{{ $record->cat->owner->phone ?? '-' }}</span>)
                        </p>
                    </div>
                </div>

                <div class="text-right border-t md:border-t-0 md:border-l border-slate-100 pt-3 md:pt-0 md:pl-5 shrink-0 text-xs text-slate-500 space-y-1">
                    <div>No. RM: <span class="font-mono font-bold text-slate-900">{{ $record->record_number }}</span></div>
                    <div>Dokter/Inisiator: <span class="font-semibold text-slate-800">{{ $record->doctor->name ?? Auth::user()->name }}</span></div>
                    @if(Auth::id() !== $record->vet_id)
                        <div class="text-[11px] text-teal-800 font-semibold bg-teal-50 px-2 py-0.5 rounded border border-teal-200 text-left md:text-right">
                            Dilanjutkan oleh: {{ Auth::user()->name }}
                        </div>
                    @endif
                    <div>Mulai: <span class="text-slate-700">{{ $record->created_at->format('d M Y, H:i') }}</span></div>
                </div>
            </div>
        </div>

        <!-- Stepper Navigation Bar (Horizontal Compact Bar) -->
        <div class="bg-white rounded-xl border border-slate-200 p-1.5 shadow-2xs overflow-hidden">
            <style>
                .soap-stepper-nav {
                    display: grid;
                    grid-template-columns: repeat(5, minmax(0, 1fr));
                    gap: 0.375rem;
                    width: 100%;
                }
                @media (max-width: 640px) {
                    .soap-stepper-scroll {
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                        padding-bottom: 2px;
                    }
                    .soap-stepper-nav {
                        display: flex;
                        flex-direction: row;
                        min-width: max-content;
                        gap: 0.375rem;
                    }
                    .soap-stepper-nav button {
                        flex: 0 0 auto;
                        padding-left: 0.875rem;
                        padding-right: 0.875rem;
                    }
                }
            </style>
            <div class="soap-stepper-scroll">
                <nav class="soap-stepper-nav" aria-label="Langkah Pemeriksaan SOAP">
                    <button type="button" 
                            @click="setStep(1)" 
                            :class="currentStep === 1 ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'" 
                            class="min-h-[38px] py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" 
                              :class="currentStep === 1 ? 'bg-white text-teal-800' : 'bg-slate-200 text-slate-700'">S</span>
                        <span class="whitespace-nowrap">1. Subjective</span>
                    </button>

                    <button type="button" 
                            @click="setStep(2)" 
                            :class="currentStep === 2 ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'" 
                            class="min-h-[38px] py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" 
                              :class="currentStep === 2 ? 'bg-white text-teal-800' : 'bg-slate-200 text-slate-700'">O</span>
                        <span class="whitespace-nowrap">2. Objective</span>
                    </button>

                    <button type="button" 
                            @click="setStep(3)" 
                            :class="currentStep === 3 ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'" 
                            class="min-h-[38px] py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" 
                              :class="currentStep === 3 ? 'bg-white text-teal-800' : 'bg-slate-200 text-slate-700'">A</span>
                        <span class="whitespace-nowrap">3. Assessment</span>
                    </button>

                    <button type="button" 
                            @click="setStep(4)" 
                            :class="currentStep === 4 ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'" 
                            class="min-h-[38px] py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" 
                              :class="currentStep === 4 ? 'bg-white text-teal-800' : 'bg-slate-200 text-slate-700'">P</span>
                        <span class="whitespace-nowrap">4. Plan & Resep</span>
                    </button>

                    <button type="button" 
                            @click="setStep(5)" 
                            :class="currentStep === 5 ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium'" 
                            class="min-h-[38px] py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" 
                              :class="currentStep === 5 ? 'bg-white text-teal-800' : 'bg-slate-200 text-slate-700'">✓</span>
                        <span class="whitespace-nowrap">5. Review</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- ===================================================================
             STEP 1: SUBJECTIVE (ANAMNESA)
             =================================================================== -->
        <div x-show="currentStep === 1" x-cloak class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Langkah 1 dari 5</span>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">Subjective (Anamnesa Pasien)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Catat keluhan utama pemilik kucing, onset, riwayat fisiologis harian, serta klarifikasi dokter.</p>
                    </div>
                    <span class="text-2xl">📝</span>
                </div>

                <form method="POST" action="{{ route('dokter.medical-records.save-step', ['record' => $record->id, 'step' => 'subjective']) }}" class="space-y-6">
                    @csrf

                    <!-- Keluhan Utama Member -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Keluhan Utama Pemilik (Chief Complaint) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="member_complaint" rows="3" required class="w-full text-sm rounded-xl border-slate-200 p-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600" placeholder="Tuliskan keluhan asli dari pemilik kucing...">{{ old('member_complaint', $subj->member_complaint ?? $record->chief_complaint) }}</textarea>
                    </div>

                    <!-- Onset & Perkembangan Gejala -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Onset Gejala (Kapan Mulai Tampak)
                            </label>
                            <input type="text" name="symptom_onset" value="{{ old('symptom_onset', $subj->symptom_onset ?? '2 hari yang lalu') }}" class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600" placeholder="e.g. 2 hari lalu, tadi pagi, 1 minggu">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Perkembangan Gejala (Progression)
                            </label>
                            <input type="text" name="symptom_history" value="{{ old('symptom_history', $subj->symptom_history ?? '') }}" class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-800 focus:ring-teal-600 focus:border-teal-600" placeholder="e.g. Semakin sering muntah, demam meningkat di malam hari">
                        </div>
                    </div>

                    <!-- Fisiologis Harian (Nafsu makan, minum, urin, defekasi) -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700">Fungsi Fisiologis Harian</span>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nafsu Makan</label>
                                <select name="appetite_history" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5 text-slate-800 focus:ring-teal-600">
                                    <option value="Normal" {{ ($subj->appetite_history ?? '') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Menurun" {{ ($subj->appetite_history ?? '') === 'Menurun' ? 'selected' : '' }}>Menurun</option>
                                    <option value="Anoreksia (Tidak Mau Makan)" {{ ($subj->appetite_history ?? '') === 'Anoreksia (Tidak Mau Makan)' ? 'selected' : '' }}>Anoreksia (Tidak Mau Makan)</option>
                                    <option value="Meningkat (Polifagia)" {{ ($subj->appetite_history ?? '') === 'Meningkat (Polifagia)' ? 'selected' : '' }}>Meningkat (Polifagia)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pola Minum</label>
                                <select name="drinking_history" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5 text-slate-800 focus:ring-teal-600">
                                    <option value="Normal" {{ ($subj->drinking_history ?? '') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Menurun" {{ ($subj->drinking_history ?? '') === 'Menurun' ? 'selected' : '' }}>Menurun / Dehidrasi</option>
                                    <option value="Meningkat (Polidipsia)" {{ ($subj->drinking_history ?? '') === 'Meningkat (Polidipsia)' ? 'selected' : '' }}>Meningkat (Polidipsia)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Buang Air Kecil (BAK)</label>
                                <select name="urination_history" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5 text-slate-800 focus:ring-teal-600">
                                    <option value="Normal" {{ ($subj->urination_history ?? '') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Sakit / Mengedan (Disuria)" {{ ($subj->urination_history ?? '') === 'Sakit / Mengedan (Disuria)' ? 'selected' : '' }}>Sakit / Mengedan (Disuria)</option>
                                    <option value="Berdarah (Hematuria)" {{ ($subj->urination_history ?? '') === 'Berdarah (Hematuria)' ? 'selected' : '' }}>Berdarah (Hematuria)</option>
                                    <option value="Tidak Kencing (Anuria / Blok)" {{ ($subj->urination_history ?? '') === 'Tidak Kencing (Anuria / Blok)' ? 'selected' : '' }}>Tidak Kencing (Blok Urin)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Buang Air Besar (BAB)</label>
                                <select name="defecation_history" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5 text-slate-800 focus:ring-teal-600">
                                    <option value="Normal" {{ ($subj->defecation_history ?? '') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Lembek / Diare Ringan" {{ ($subj->defecation_history ?? '') === 'Lembek / Diare Ringan' ? 'selected' : '' }}>Lembek / Diare Ringan</option>
                                    <option value="Diare Cair / Masif" {{ ($subj->defecation_history ?? '') === 'Diare Cair / Masif' ? 'selected' : '' }}>Diare Cair / Masif</option>
                                    <option value="Diare Berdarah / Lendir" {{ ($subj->defecation_history ?? '') === 'Diare Berdarah / Lendir' ? 'selected' : '' }}>Diare Berdarah / Lendir</option>
                                    <option value="Konstipasi / Sembelit" {{ ($subj->defecation_history ?? '') === 'Konstipasi / Sembelit' ? 'selected' : '' }}>Konstipasi / Sembelit</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Medis Sebelumnya (Obat, Alergi, Vaksin) -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Riwayat Pengobatan Terakhir</label>
                            <input type="text" name="medication_history" value="{{ old('medication_history', $subj->medication_history ?? '') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800" placeholder="e.g. Paracetamol (pernah diberi pemilik), Amoxicillin">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Riwayat Alergi Diketahui</label>
                            <input type="text" name="allergy_history" value="{{ old('allergy_history', $subj->allergy_history ?? '') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800" placeholder="e.g. Tidak ada, Alergi ikan tongkol">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Vaksinasi</label>
                            <input type="text" name="vaccination_history" value="{{ old('vaccination_history', $subj->vaccination_history ?? '') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800" placeholder="e.g. Tricat lengkap, Belum pernah">
                        </div>
                    </div>

                    <!-- Klarifikasi Dokter -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Klarifikasi Dokter Saat Konsultasi
                        </label>
                        <textarea name="doctor_clarification" rows="2" class="w-full text-sm rounded-xl border-slate-200 p-3 text-slate-800 focus:ring-teal-600" placeholder="Catatan tambahan dokter saat menggali keluhan pemilik (terpisah dari kata-kata asli member)...">{{ old('doctor_clarification', $subj->doctor_clarification ?? '') }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Data Subjective akan tersimpan aman.</span>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-xs transition">
                            Simpan & Lanjut ke Step 2 (Objective) &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================================
             STEP 2: OBJECTIVE (PEMERIKSAAN FISIK)
             =================================================================== -->
        <div x-show="currentStep === 2" x-cloak class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Langkah 2 dari 5</span>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">Objective (Pemeriksaan Fisik & Parameter)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Catat tanda vital utama (berat, suhu, denyut, napas) dan evaluasi sistem organ kucing secara sistematis.</p>
                    </div>
                    <span class="text-2xl">⚖️</span>
                </div>

                <form method="POST" action="{{ route('dokter.medical-records.save-step', ['record' => $record->id, 'step' => 'objective']) }}" class="space-y-6">
                    @csrf

                    <!-- Tanda Vital Utama Grid -->
                    <div class="p-5 rounded-2xl bg-teal-50/50 border border-teal-200/80 space-y-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-teal-900">1. Tanda-Tanda Vital (Vital Signs)</span>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Berat Badan (Kg) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0.1" max="25" name="weight" value="{{ old('weight', $record->weight > 0 ? $record->weight : '') }}" required class="w-full text-sm font-semibold rounded-xl border-slate-200 py-2 px-3 text-slate-900 focus:ring-teal-600" placeholder="e.g. 3.45">
                                <span class="text-[10px] text-slate-500 mt-0.5 block">Rentang kucing: 0.3 - 10 kg</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Suhu Rektal (°C) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.1" min="32" max="43" name="temperature" value="{{ old('temperature', $record->temperature > 0 ? $record->temperature : '') }}" required class="w-full text-sm font-semibold rounded-xl border-slate-200 py-2 px-3 text-slate-900 focus:ring-teal-600" placeholder="e.g. 38.6">
                                <span class="text-[10px] text-slate-500 mt-0.5 block">Normal: 38.0 - 39.2 °C</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Denyut Jantung (HR)
                                </label>
                                <input type="number" name="heart_rate" value="{{ old('heart_rate', $objectivesMap['heart_rate']->value_numeric ?? '') }}" class="w-full text-sm rounded-xl border-slate-200 py-2 px-3 text-slate-900 focus:ring-teal-600" placeholder="e.g. 180">
                                <span class="text-[10px] text-slate-500 mt-0.5 block">Normal: 140 - 220 bpm</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Frekuensi Napas (RR)
                                </label>
                                <input type="number" name="respiratory_rate" value="{{ old('respiratory_rate', $objectivesMap['respiratory_rate']->value_numeric ?? '') }}" class="w-full text-sm rounded-xl border-slate-200 py-2 px-3 text-slate-900 focus:ring-teal-600" placeholder="e.g. 30">
                                <span class="text-[10px] text-slate-500 mt-0.5 block">Normal: 20 - 40 rpm</span>
                            </div>
                        </div>

                        <!-- Parameter Fisik Tambahan -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2 border-t border-teal-100">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kondisi Umum <span class="text-rose-500">*</span></label>
                                <select name="general_condition" required class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5">
                                    <option value="Sehat & Aktif" {{ old('general_condition', $record->general_condition) === 'Sehat & Aktif' ? 'selected' : '' }}>Sehat & Aktif</option>
                                    <option value="Lemas / Dehidrasi" {{ old('general_condition', $record->general_condition) === 'Lemas / Dehidrasi' ? 'selected' : '' }}>Lemas / Dehidrasi</option>
                                    <option value="Demam / Febris" {{ old('general_condition', $record->general_condition) === 'Demam / Febris' ? 'selected' : '' }}>Demam / Febris</option>
                                    <option value="Sakit Sedang" {{ old('general_condition', $record->general_condition) === 'Sakit Sedang' ? 'selected' : '' }}>Sakit Sedang</option>
                                    <option value="Kritis / Darurat" {{ old('general_condition', $record->general_condition) === 'Kritis / Darurat' ? 'selected' : '' }}>Kritis / Darurat</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">BCS (Body Condition)</label>
                                <select name="body_condition_score" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5">
                                    <option value="5/9 (Ideal)" {{ ($objectivesMap['bcs']->value_text ?? '') === '5/9 (Ideal)' ? 'selected' : '' }}>5/9 (Ideal / Normal)</option>
                                    <option value="3/9 (Underweight)" {{ ($objectivesMap['bcs']->value_text ?? '') === '3/9 (Underweight)' ? 'selected' : '' }}>3/9 (Kurus)</option>
                                    <option value="1/9 (Emaciated)" {{ ($objectivesMap['bcs']->value_text ?? '') === '1/9 (Emaciated)' ? 'selected' : '' }}>1/9 (Sangat Kurus)</option>
                                    <option value="7/9 (Overweight)" {{ ($objectivesMap['bcs']->value_text ?? '') === '7/9 (Overweight)' ? 'selected' : '' }}>7/9 (Gemuk)</option>
                                    <option value="9/9 (Obese)" {{ ($objectivesMap['bcs']->value_text ?? '') === '9/9 (Obese)' ? 'selected' : '' }}>9/9 (Obesitas)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Selaput Lendir (Mukosa)</label>
                                <select name="mucous_membrane" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5">
                                    <option value="Pink & Lembab" {{ ($objectivesMap['mucous_membrane']->value_text ?? '') === 'Pink & Lembab' ? 'selected' : '' }}>Pink & Lembab (Normal)</option>
                                    <option value="Pucat (Anemis)" {{ ($objectivesMap['mucous_membrane']->value_text ?? '') === 'Pucat (Anemis)' ? 'selected' : '' }}>Pucat (Anemis)</option>
                                    <option value="Kuning (Ikterik)" {{ ($objectivesMap['mucous_membrane']->value_text ?? '') === 'Kuning (Ikterik)' ? 'selected' : '' }}>Kuning (Ikterik)</option>
                                    <option value="Kongesti (Merah Gelap)" {{ ($objectivesMap['mucous_membrane']->value_text ?? '') === 'Kongesti (Merah Gelap)' ? 'selected' : '' }}>Kongesti (Merah Gelap)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">CRT (Capillary Refill)</label>
                                <select name="crt" class="w-full text-xs rounded-lg border-slate-200 py-2 px-2.5">
                                    <option value="< 2 detik (Normal)" {{ ($objectivesMap['crt']->value_text ?? '') === '< 2 detik (Normal)' ? 'selected' : '' }}>&lt; 2 detik (Normal)</option>
                                    <option value="> 2 detik (Lambat)" {{ ($objectivesMap['crt']->value_text ?? '') === '> 2 detik (Lambat)' ? 'selected' : '' }}>&gt; 2 detik (Dehidrasi / Sirkulasi Buruk)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pemeriksaan Fisik Sistemik -->
                    <div class="space-y-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700">2. Pemeriksaan Fisik Sistem Organ (Systematic Exam)</span>

                        <div class="grid md:grid-cols-2 gap-4">
                            @php
                                $sysList = [
                                    'eyes' => ['title' => 'Mata (Ophthalmology)', 'icon' => '👁️', 'hint' => 'Konjungtiva, kornea, sekret/discharge'],
                                    'ears' => ['title' => 'Telinga (Otic)', 'icon' => '👂', 'hint' => 'Kebersihan kanal, ear-mite, eritema, bau'],
                                    'oral_teeth' => ['title' => 'Mulut & Gigi (Oral Cavity)', 'icon' => '🦷', 'hint' => 'Gingivitis, tartar/karang, ulkus lidah'],
                                    'skin_coat' => ['title' => 'Kulit & Rambut (Dermatology)', 'icon' => '🐈', 'hint' => 'Alopecia, jamur/ringworm, kutu, kerak'],
                                    'musculoskeletal' => ['title' => 'Muskuloskeletal & Gerak', 'icon' => '🐾', 'hint' => 'Kepincangan, simetri palpasi tulang, nyeri sendi'],
                                    'thoracic_lung' => ['title' => 'Toraks & Paru (Auskultasi)', 'icon' => '🫁', 'hint' => 'Suara wheezing, crackles, stridor, murmur'],
                                    'abdominal_digestive' => ['title' => 'Abdomen & Palpasi Perut', 'icon' => '🥣', 'hint' => 'Nyeri tekan, pembesaran organ, feses padat'],
                                    'lymph_nodes' => ['title' => 'Kelenjar Limfonodus', 'icon' => '🟣', 'hint' => 'Limfonodus submandibular, prescapular, popliteal'],
                                ];
                            @endphp

                            @foreach($sysList as $sysCode => $info)
                                @php $cur = $getSys($sysCode); @endphp
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                            <span>{{ $info['icon'] }}</span>
                                            <span>{{ $info['title'] }}</span>
                                        </span>
                                        <select name="systems[{{ $sysCode }}][status]" class="text-[11px] rounded-lg border-slate-200 py-1 px-2 font-medium">
                                            <option value="normal" {{ $cur['status'] === 'normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="abnormal" {{ $cur['status'] === 'abnormal' ? 'selected' : '' }}>Abnormal</option>
                                            <option value="not_examined" {{ $cur['status'] === 'not_examined' ? 'selected' : '' }}>Tidak Diperiksa</option>
                                        </select>
                                    </div>
                                    <input type="text" name="systems[{{ $sysCode }}][notes]" value="{{ $cur['notes'] }}" placeholder="{{ $info['hint'] }}..." class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2.5 text-slate-700 bg-white">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tindakan Cepat (Obat Cacing, Kutu, Vitamin) -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700">3. Tindakan Rutin di Tempat (Opsional)</span>
                        <div class="flex flex-wrap items-center gap-6 pt-1">
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="deworming_given" value="1" {{ $record->deworming_given ? 'checked' : '' }} class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Pemberian Obat Cacing (Deworming)</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="anti_flea_given" value="1" {{ $record->anti_flea_given ? 'checked' : '' }} class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Pengobatan Kutu (Anti-Flea)</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="supplement_given" value="1" {{ $record->supplement_given ? 'checked' : '' }} class="rounded text-teal-700 focus:ring-teal-600">
                                <span>Pemberian Vitamin / Suplemen</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" @click="setStep(1)" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs">
                            &larr; Kembali ke Step 1
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-xs transition">
                            Simpan & Lanjut ke Step 3 (Assessment) &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================================
             STEP 3: ASSESSMENT (DIAGNOSIS)
             =================================================================== -->
        <div x-show="currentStep === 3" x-cloak class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Langkah 3 dari 5</span>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">Assessment (Diagnosis & Evaluasi Klinis)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tetapkan diagnosis utama, tingkat kepastian (Dugaan, Sementara, Definitif), dan diagnosis banding jika ada.</p>
                    </div>
                    <span class="text-2xl">🔬</span>
                </div>

                <form method="POST" action="{{ route('dokter.medical-records.save-step', ['record' => $record->id, 'step' => 'assessment']) }}" class="space-y-6">
                    @csrf

                    <!-- Primary Diagnosis Card -->
                    <div class="p-5 rounded-2xl bg-teal-50/50 border border-teal-200 space-y-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-teal-900">Diagnosis Utama (Primary Diagnosis) <span class="text-rose-500">*</span></span>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Diagnosis / Penyakit Utama <span class="text-rose-500">*</span></label>
                                <input type="text" name="primary_diagnosis" required value="{{ old('primary_diagnosis', $primaryDiag->diagnosis_name ?? '') }}" list="diagnosis_suggestions" class="w-full text-sm rounded-xl border-slate-200 py-2.5 px-3.5 text-slate-900 font-semibold focus:ring-teal-600" placeholder="e.g. Feline Upper Respiratory Tract Infection (URTI) / Flu Kucing">
                                <datalist id="diagnosis_suggestions">
                                    <option value="Feline Upper Respiratory Tract Infection (URTI)">
                                    <option value="Feline Panleukopenia Virus (FPV)">
                                    <option value="Feline Infectious Peritonitis (FIP)">
                                    <option value="Scabies / Notoedres cati">
                                    <option value="Dermatophytosis / Ringworm (Microsporum canis)">
                                    <option value="Otitis Externa (Ear Mites)">
                                    <option value="Gastroenteritis Akut / Enteritis">
                                    <option value="Feline Lower Urinary Tract Disease (FLUTD)">
                                    <option value="Gingivostomatitis Kronis">
                                    <option value="Helminthiasis (Cacingan)">
                                    <option value="Trauma Fisik / Abses Gigitan">
                                    <option value="Malnutrisi / Kakeksia">
                                </datalist>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Kepastian (Certainty) <span class="text-rose-500">*</span></label>
                                <select name="primary_certainty" required class="w-full text-xs rounded-xl border-slate-200 py-2.5 px-3">
                                    <option value="provisional" {{ ($primaryDiag->certainty ?? '') === 'provisional' ? 'selected' : '' }}>Provisional (Diagnosis Kerja)</option>
                                    <option value="suspected" {{ ($primaryDiag->certainty ?? '') === 'suspected' ? 'selected' : '' }}>Suspected (Dugaan Awal)</option>
                                    <option value="confirmed" {{ ($primaryDiag->certainty ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed (Definitif / Terkonfirmasi)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Keparahan (Severity) <span class="text-rose-500">*</span></label>
                                <select name="primary_severity" required class="w-full text-xs rounded-xl border-slate-200 py-2 px-3">
                                    <option value="mild" {{ ($primaryDiag->severity ?? '') === 'mild' ? 'selected' : '' }}>Ringan (Mild)</option>
                                    <option value="moderate" {{ ($primaryDiag->severity ?? '') === 'moderate' || !isset($primaryDiag->severity) ? 'selected' : '' }}>Sedang (Moderate)</option>
                                    <option value="severe" {{ ($primaryDiag->severity ?? '') === 'severe' ? 'selected' : '' }}>Berat / Parah (Severe)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Penalaran Klinis (Clinical Reasoning)</label>
                                <input type="text" name="clinical_reasoning" value="{{ old('clinical_reasoning', $primaryDiag->clinical_reasoning ?? '') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800" placeholder="e.g. Gejala bersin serosa, demam 39.4C, selaput lendir kongesti, riwayat kontak...">
                            </div>
                        </div>
                    </div>

                    <!-- Secondary & Differential Diagnoses -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-700">Diagnosis Tambahan / Banding (Differential)</span>
                            <button type="button" @click="addSecondaryDiagnosis()" class="inline-flex items-center gap-1 text-xs font-bold text-teal-700 hover:text-teal-900">
                                <span>+ Tambah Diagnosis Banding</span>
                            </button>
                        </div>

                        <template x-for="(sec, idx) in secondaryDiagnoses" :key="idx">
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col md:flex-row items-center gap-3">
                                <div class="flex-1 w-full">
                                    <input type="text" :name="`secondary_diagnoses[${idx}][name]`" x-model="sec.name" placeholder="Nama diagnosis sekunder/banding..." class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                                </div>
                                <div class="w-full md:w-36">
                                    <select :name="`secondary_diagnoses[${idx}][type]`" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                        <option value="secondary">Sekunder</option>
                                        <option value="differential">Banding</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-36">
                                    <select :name="`secondary_diagnoses[${idx}][certainty]`" x-model="sec.certainty" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                        <option value="provisional">Provisional</option>
                                        <option value="suspected">Suspected</option>
                                        <option value="confirmed">Confirmed</option>
                                    </select>
                                </div>
                                <button type="button" @click="removeSecondaryDiagnosis(idx)" class="text-rose-600 hover:text-rose-800 text-xs font-bold shrink-0 p-1">
                                    ✕ Hapus
                                </button>
                            </div>
                        </template>

                        <div x-show="secondaryDiagnoses.length === 0" class="text-xs text-slate-400 italic py-2">
                            Tidak ada diagnosis tambahan/banding. Klik "+ Tambah Diagnosis Banding" jika diperlukan.
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" @click="setStep(2)" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs">
                            &larr; Kembali ke Step 2
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-xs transition">
                            Simpan & Lanjut ke Step 4 (Plan & Resep) &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================================
             STEP 4: PLAN & E-PRESCRIPTION
             =================================================================== -->
        <div x-show="currentStep === 4" x-cloak class="space-y-6">
            
            <!-- Resep Elektronik & Peresepan Builder Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Peresepan Dokter Hewan</span>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">Peresepan Obat Pasien</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tersedia 3 mode peresepan: Resep Reguler, Resep Racikan Veteriner, atau Manual (Kertas Fisik).</p>
                    </div>
                    <span class="text-2xl">💊</span>
                </div>

                <!-- 3-Mode Prescription Selector -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                            Pilih Mode Peresepan
                        </label>
                        @if($activeRx)
                            <span class="text-[11px] px-2.5 py-0.5 rounded-full font-bold {{ $activeRx->isManual() ? 'bg-amber-100 text-amber-800' : ($activeRx->isCompounded() ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800') }}">
                                Mode Aktif: {{ $activeRx->type_label }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Mode 1: Resep Reguler -->
                        <label class="relative flex flex-col p-3.5 rounded-xl border cursor-pointer transition select-none"
                               :class="rxMode === 'regular' ? 'bg-teal-50/70 border-teal-600 ring-2 ring-teal-600/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-xs" :class="rxMode === 'regular' ? 'text-teal-950' : 'text-slate-800'">1. Resep Reguler</span>
                                <input type="radio" name="ui_prescription_type" value="regular" x-model="rxMode" class="text-teal-600 focus:ring-teal-500 h-4 w-4">
                            </div>
                            <p class="text-[11px] leading-snug" :class="rxMode === 'regular' ? 'text-teal-800' : 'text-slate-500'">
                                Obat jadi terdaftar (tablet, sirup, salep, injeksi, tetes) dengan basis dosis tetap / per kg.
                            </p>
                        </label>

                        <!-- Mode 2: Resep Racikan -->
                        <label class="relative flex flex-col p-3.5 rounded-xl border cursor-pointer transition select-none"
                               :class="rxMode === 'compounded' ? 'bg-teal-50/70 border-teal-600 ring-2 ring-teal-600/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-xs" :class="rxMode === 'compounded' ? 'text-teal-950' : 'text-slate-800'">2. Resep Racikan</span>
                                <input type="radio" name="ui_prescription_type" value="compounded" x-model="rxMode" class="text-teal-600 focus:ring-teal-500 h-4 w-4">
                            </div>
                            <p class="text-[11px] leading-snug" :class="rxMode === 'compounded' ? 'text-teal-800' : 'text-slate-500'">
                                Sediaan racikan veteriner (puyer/pulveres, kapsul, suspensi oral) dengan komposisi campuran obat.
                            </p>
                        </label>

                        <!-- Mode 3: Manual -->
                        <label class="relative flex flex-col p-3.5 rounded-xl border cursor-pointer transition select-none"
                               :class="rxMode === 'manual' ? 'bg-teal-50/70 border-teal-600 ring-2 ring-teal-600/20' : 'bg-white border-slate-200 hover:border-slate-300'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-xs" :class="rxMode === 'manual' ? 'text-teal-950' : 'text-slate-800'">3. Manual (Kertas Fisik)</span>
                                <input type="radio" name="ui_prescription_type" value="manual" x-model="rxMode" class="text-teal-600 focus:ring-teal-500 h-4 w-4">
                            </div>
                            <p class="text-[11px] leading-snug" :class="rxMode === 'manual' ? 'text-teal-800' : 'text-slate-500'">
                                Tulis resep di lembar kertas tanpa input sistem. Pada cetak Rekam Medis berlabel "Resep Manual".
                            </p>
                        </label>
                    </div>
                </div>

                <!-- Existing Prescribed Items List (Shown if items exist) -->
                @if($activeRx && $activeRx->items->isNotEmpty())
                    <div class="rounded-xl border border-teal-200 overflow-hidden">
                        <div class="bg-teal-50 px-4 py-2.5 flex items-center justify-between border-b border-teal-100 flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-teal-900">Daftar Obat dalam Resep (#{{ $activeRx->prescription_number }})</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $activeRx->isCompounded() ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800' }}">
                                    {{ $activeRx->type_label }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="openCopyModal('all')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-teal-100 text-teal-800 border border-teal-300 text-xs font-bold shadow-2xs transition">
                                    <span>📋</span>
                                    <span>Salin dari Resep Lain (Copy Resep)</span>
                                </button>
                                <span class="text-[11px] text-teal-700 font-semibold">{{ $activeRx->items->count() }} Item Obat</span>
                            </div>
                        </div>
                        <div class="divide-y divide-slate-100 bg-white">
                            @foreach($activeRx->items as $item)
                                <div class="p-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                                    <div class="space-y-1">
                                        <div class="font-bold text-slate-900 text-sm flex items-center gap-2 flex-wrap">
                                            @if($item->is_racikan)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                                    🧪 Racikan
                                                </span>
                                                <span>{{ $item->racikan_name ?: $item->medicine_name_snapshot }}</span>
                                            @else
                                                <span>{{ $item->medicine_name_snapshot }}</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 uppercase">
                                                {{ $item->dosage_form }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 text-teal-800">
                                                Rute: {{ $item->administration_route }}
                                            </span>
                                        </div>

                                        @if($item->is_racikan && $item->composition)
                                            <div class="p-2 rounded-lg bg-purple-50/70 border border-purple-100 text-purple-950 font-medium">
                                                <span class="font-bold text-purple-900">Formula Komposisi:</span> {{ $item->composition }}
                                            </div>
                                        @endif

                                        <div class="text-slate-600">
                                            @if(!$item->is_racikan)
                                                Dosis: <strong class="text-slate-800">{{ $item->dose_value }} {{ $item->dose_unit }}</strong>
                                                ({{ $item->dose_basis === 'per_kg' ? 'per kg BB' : 'tetap' }}) &bull;
                                            @endif
                                            Frekuensi: <strong class="text-slate-800">{{ $item->frequency_value }} {{ $item->frequency_unit }}</strong> &bull;
                                            Durasi: <strong class="text-slate-800">{{ $item->duration_value }} {{ $item->duration_unit }}</strong> &bull;
                                            Jumlah: <strong class="text-slate-800">{{ $item->quantity_value }} {{ $item->quantity_unit }}</strong>
                                        </div>
                                        <div class="text-slate-500 italic">
                                            Instruksi: "{{ $item->usage_instructions }}"
                                            @if($item->warnings)
                                                <span class="text-rose-600 font-medium"> &bull; Peringatan: {{ $item->warnings }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('dokter.medical-records.prescription.delete', $item) }}" onsubmit="return confirm('Hapus obat ini dari resep?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] transition">
                                            ✕ Hapus
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($activeRx && $activeRx->isManual())
                    <!-- Manual Prescription Notice -->
                    <div class="p-4 rounded-xl border border-amber-300 bg-amber-50/80 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-600 text-white font-bold text-xs uppercase tracking-wide">
                                Resep Manual
                            </span>
                            <span class="text-xs font-mono font-bold text-amber-900">No. Rx: {{ $activeRx->prescription_number }}</span>
                        </div>
                        <p class="text-xs text-amber-900">
                            Mode saat ini telah disetel ke <strong>Resep Manual (Tulis di Kertas Fisik)</strong>. Dokter menuliskan resep secara manual pada blanko kertas fisik. Label <strong>"Resep Manual"</strong> akan otomatis tercantum pada cetakan hasil Rekam Medis.
                        </p>
                        @if($activeRx->notes)
                            <div class="p-2.5 rounded-lg bg-white border border-amber-200 text-xs text-slate-800">
                                <strong>Catatan Resep Fisik:</strong> {{ $activeRx->notes }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6 px-4 border border-dashed border-slate-200 rounded-xl text-slate-500 text-xs space-y-3">
                        <p class="text-slate-600">Belum ada item obat dalam resep ini. Pilih mode di atas lalu isi formulir yang tersedia.</p>
                        <div>
                            <button type="button"
                                    @click="openCopyModal('all')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-300 font-bold text-xs shadow-2xs transition min-h-[44px]">
                                <span>📋</span>
                                <span>Salin Resep Pasien Lain (Copy Resep)</span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- =========================================================
                     PANEL MODE 1: RESEP REGULER (Single / Finished Medicine)
                     ========================================================= -->
                <div x-show="rxMode === 'regular'" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-800">+ Tambah Obat Reguler ke Resep</span>
                        <button type="button"
                                @click="openCopyModal('regular')"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-300 text-xs font-bold shadow-2xs transition min-h-[44px]">
                            <span>📋</span>
                            <span>Salin dari Resep Lain (Copy Resep)</span>
                        </button>
                    </div>

                    <!-- Quick search from master medicines -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Cari Master Obat Hewan (Autocomplete)</label>
                        <div class="relative">
                            <input type="text" x-model="rxMedicineSearch" placeholder="Ketik nama obat, e.g. Clavamox, Doxycat, Meloxicam..." class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800 bg-white">
                        </div>

                        <!-- Dropdown Results -->
                        <div x-show="rxMedicineSearch && !rxSelectedMedicine" class="mt-1 bg-white border border-slate-200 rounded-xl shadow-md max-h-48 overflow-y-auto z-10 divide-y divide-slate-100">
                            <template x-for="med in medicinesList.filter(m => m.name.toLowerCase().includes(rxMedicineSearch.toLowerCase()) || (m.active_ingredient && m.active_ingredient.toLowerCase().includes(rxMedicineSearch.toLowerCase())))" :key="med.id">
                                <div @click="selectMedicine(med)" class="p-2.5 hover:bg-teal-50 cursor-pointer text-xs flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-slate-800" x-text="med.name"></div>
                                        <div class="text-[10px] text-slate-500" x-text="med.active_ingredient ? med.active_ingredient + ' (' + med.dosage_form + ')' : med.dosage_form"></div>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-teal-100 text-teal-800 font-bold">Pilih</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('dokter.medical-records.prescription.add', $record) }}" class="space-y-4 pt-2 border-t border-slate-200">
                        @csrf
                        <input type="hidden" name="is_racikan" value="0">
                        <input type="hidden" id="rx_medicine_id" name="medicine_id" value="">
                        <input type="hidden" id="rx_active_ingredient" name="active_ingredient" value="">
                        <input type="hidden" id="rx_concentration_value" name="concentration_value" value="">
                        <input type="hidden" id="rx_concentration_unit" name="concentration_unit" value="">

                        <div class="grid md:grid-cols-3 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Obat <span class="text-rose-500">*</span></label>
                                <input type="text" id="rx_medicine_name" name="medicine_name" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white font-semibold" placeholder="Nama obat atau pilih dari master">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Bentuk Sediaan <span class="text-rose-500">*</span></label>
                                <select id="rx_dosage_form" name="dosage_form" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                    <option value="tablet">Tablet / Kapsul</option>
                                    <option value="sirup">Sirup / Suspensi Oral</option>
                                    <option value="salep">Salep / Krim Kulit</option>
                                    <option value="tetes">Tetes (Mata / Telinga)</option>
                                    <option value="spot-on">Spot-on Tetes Tengkuk</option>
                                    <option value="injeksi">Injeksi / Suntik</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nilai Dosis <span class="text-rose-500">*</span></label>
                                <input type="number" step="0.01" name="dose_value" required value="1" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Satuan Dosis <span class="text-rose-500">*</span></label>
                                <input type="text" name="dose_unit" required value="tablet" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. tablet, ml, mg">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Basis Dosis <span class="text-rose-500">*</span></label>
                                <select name="dose_basis" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                    <option value="fixed">Dosis Tetap (Fixed)</option>
                                    <option value="per_kg">Per Kg BB</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Rute Pemberian <span class="text-rose-500">*</span></label>
                                <select name="administration_route" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                    <option value="oral">Oral (Minum)</option>
                                    <option value="topikal">Topikal (Kulit Luar)</option>
                                    <option value="tetes_mata">Tetes Mata</option>
                                    <option value="tetes_telinga">Tetes Telinga</option>
                                    <option value="subkutan">Subkutan (SC)</option>
                                    <option value="intramuskular">Intramuskular (IM)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Frekuensi <span class="text-rose-500">*</span></label>
                                <input type="text" name="frequency_value" required value="2x sehari" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. 2x sehari, tiap 12 jam">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Durasi Pemberian <span class="text-rose-500">*</span></label>
                                <input type="text" name="duration_value" required value="5 hari" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. 5 hari, 7 hari">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jumlah Obat (Qty)</label>
                                <input type="number" step="0.1" name="quantity_value" value="1" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Satuan Jumlah</label>
                                <input type="text" name="quantity_unit" value="tablet" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. tablet, botol, tube">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Instruksi Cara Penggunaan <span class="text-rose-500">*</span></label>
                                <input type="text" name="usage_instructions" required value="Diberikan sesudah makan, habiskan sesuai durasi" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Peringatan Khusus</label>
                                <input type="text" name="warnings" value="Beri minum setelah obat ditelan" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. Jangan diberikan bersama susu">
                            </div>
                        </div>

                        <button type="submit" class="px-4 py-2 rounded-xl bg-teal-800 hover:bg-teal-900 text-white font-semibold text-xs transition">
                            + Masukkan Obat ke Resep
                        </button>
                    </form>
                </div>

                <!-- =========================================================
                     PANEL MODE 2: RESEP RACIKAN VETERINER
                     ========================================================= -->
                <div x-show="rxMode === 'compounded'" class="p-5 rounded-2xl bg-purple-50/50 border border-purple-200 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-purple-950">🧪 Tambah Sediaan Racikan ke Resep (Veteriner)</span>
                            <span class="text-[10px] text-purple-700 font-semibold bg-purple-100 px-2 py-0.5 rounded mt-1 inline-block">Standar Praktik Hewan Kecil</span>
                        </div>
                        <button type="button"
                                @click="openCopyModal('compounded')"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 border border-purple-300 text-xs font-bold shadow-2xs transition min-h-[44px]">
                            <span>📋</span>
                            <span>Salin Formula Racikan (Copy Resep)</span>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('dokter.medical-records.prescription.add', $record) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="is_racikan" value="1">

                        <div class="grid md:grid-cols-3 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Racikan <span class="text-rose-500">*</span></label>
                                <input type="text" name="racikan_name" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white font-semibold" placeholder="e.g. Puyer Batuk & Pilek Pasien, Kapsul Imun & Antibiotik">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Bentuk Sediaan Racikan <span class="text-rose-500">*</span></label>
                                <select name="dosage_form" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                    <option value="puyer">Puyer / Pulveres (Serbuk Bungkus)</option>
                                    <option value="kapsul">Kapsul Racikan</option>
                                    <option value="sirup">Sirup / Suspensi Oral Racik</option>
                                    <option value="salep">Salep / Krim Kulit Racik</option>
                                    <option value="tetes">Larutan Tetes Racik</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                Komposisi / Formula Racikan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="composition" rows="2" required class="w-full text-xs rounded-lg border-slate-200 p-2.5 bg-white font-mono" placeholder="Tuliskan komponen obat & dosis per sediaan, e.g. Amoxicillin 50 mg + Dexamethasone 0.25 mg + Bromhexine 1 mg (per bungkus)"></textarea>
                            <span class="text-[10px] text-slate-500">Cantumkan nama bahan aktif atau obat jadi yang digerus dan takaran dosis per sediaan/bungkus.</span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jumlah Racikan (Qty) <span class="text-rose-500">*</span></label>
                                <input type="number" step="1" name="quantity_value" required value="10" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Satuan Jumlah <span class="text-rose-500">*</span></label>
                                <input type="text" name="quantity_unit" required value="bungkus" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. bungkus, kapsul, botol">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Rute Pemberian <span class="text-rose-500">*</span></label>
                                <select name="administration_route" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-2 bg-white">
                                    <option value="oral">Oral (Minum)</option>
                                    <option value="topikal">Topikal (Kulit Luar)</option>
                                    <option value="tetes_mata">Tetes Mata</option>
                                    <option value="tetes_telinga">Tetes Telinga</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Frekuensi Pemberian <span class="text-rose-500">*</span></label>
                                <input type="text" name="frequency_value" required value="2x sehari" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. 2x sehari, tiap 12 jam">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Durasi Pemberian <span class="text-rose-500">*</span></label>
                                <input type="text" name="duration_value" required value="5 hari" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white" placeholder="e.g. 5 hari">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Instruksi Cara Penggunaan <span class="text-rose-500">*</span></label>
                                <input type="text" name="usage_instructions" required value="Campur 1 bungkus ke sedikit wet food / madu, berikan sesudah makan" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Peringatan Khusus</label>
                            <input type="text" name="warnings" value="Habiskan seluruh bungkus racikan, simpan di tempat kering dan sejuk" class="w-full text-xs rounded-lg border-slate-200 py-1.5 px-3 bg-white">
                        </div>

                        <button type="submit" class="px-4 py-2 rounded-xl bg-purple-800 hover:bg-purple-900 text-white font-semibold text-xs transition">
                            + Masukkan Racikan ke Resep
                        </button>
                    </form>
                </div>

                <!-- =========================================================
                     PANEL MODE 3: MANUAL (Tulis di Kertas tanpa Sistem)
                     ========================================================= -->
                <div x-show="rxMode === 'manual'" class="p-5 rounded-2xl bg-amber-50/70 border border-amber-300 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="block text-xs font-bold uppercase tracking-wider text-amber-950">📝 Mode Resep Manual (Tulis di Kertas Fisik)</span>
                        <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-2 py-0.5 rounded">Cetak RM: "Resep Manual"</span>
                    </div>

                    <p class="text-xs text-amber-900 leading-relaxed">
                        Gunakan mode ini apabila resep obat ditulis secara langsung pada lembar kertas resep fisik dokter hewan (blanko klinik atau resep apotek luar). Anda tidak perlu menginput item obat satu per satu ke dalam sistem. Pada cetakan hasil Rekam Medis, sistem akan otomatis mencantumkan label tegas <strong>"Resep Manual"</strong>.
                    </p>

                    <form method="POST" action="{{ route('dokter.medical-records.prescription.mode', $record) }}" class="space-y-3 pt-2 border-t border-amber-200">
                        @csrf
                        <input type="hidden" name="prescription_type" value="manual">

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-800 mb-1">
                                Catatan Resep Fisik / Keterangan Tambahan (Opsional)
                            </label>
                            <textarea name="notes" rows="2" class="w-full text-xs rounded-lg border-amber-300 p-2.5 bg-white text-slate-800 focus:ring-amber-500" placeholder="e.g. Resep fisik no. 024 diberikan ke pemilik untuk ditebus di Apotek Kimia Farma / K24">{{ old('notes', $activeRx && $activeRx->isManual() ? $activeRx->notes : '') }}</textarea>
                            <span class="text-[10px] text-slate-500">Catatan ini akan tercetak pada lembar Rekam Medis di bawah label Resep Manual.</span>
                        </div>

                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-semibold text-xs shadow-xs transition">
                            ✓ Konfirmasi & Simpan Mode Resep Manual
                        </button>
                    </form>
                </div>
            </div>

            <!-- Treatment Plan & Home Care Form -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Langkah 4 dari 5</span>
                    <h2 class="font-outfit text-xl font-bold text-slate-900">Plan & Instruksi Perawatan Pasien</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Catat tindakan medis klinik, instruksi perawatan di rumah (terbit untuk member), tanda bahaya, dan rencana kontrol.</p>
                </div>

                <form method="POST" action="{{ route('dokter.medical-records.save-step', ['record' => $record->id, 'step' => 'plan']) }}" class="space-y-6">
                    @csrf

                    <!-- Tindakan Medis di Klinik -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tindakan Medis / Terapi di Klinik (Clinical Procedures)
                        </label>
                        <textarea name="treatment_notes" rows="3" class="w-full text-sm rounded-xl border-slate-200 p-3 text-slate-800 focus:ring-teal-600" placeholder="Tindakan medis yang dilakukan di klinik, misalnya: Pembersihan telinga dengan larutan NaCl, nebulisasi saline 15 menit, injeksi analgesik...">{{ old('treatment_notes', $record->treatment_notes) }}</textarea>
                    </div>

                    <!-- Instruksi Perawatan Rumah -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Instruksi Perawatan di Rumah (Home Care - Ditampilkan kepada Member) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="home_care_instruction" rows="3" required class="w-full text-sm rounded-xl border-slate-200 p-3 text-slate-800 focus:ring-teal-600" placeholder="Petunjuk praktis untuk pemilik kucing, misalnya: Tempatkan kucing di ruang hangat, jauhkan dari debu, bersihkan mata setiap pagi dengan kassa steril...">{{ old('home_care_instruction', $advices['home_care']->instruction ?? $record->recommendation) }}</textarea>
                    </div>

                    <!-- Tanda Bahaya (Red Flags) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-rose-700 mb-1.5">
                            Tanda Bahaya (Kapan Pemilik Harus Segera Kembali / Darurat) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="warning_signs" rows="2" required class="w-full text-sm rounded-xl border-rose-200 bg-rose-50/30 p-3 text-slate-800 focus:ring-rose-600" placeholder="e.g. Jika kucing napas megap-megap (mulut terbuka), lemas tidak bergerak, gusi membiru, atau kejang, segera bawa kembali ke klinik...">{{ old('warning_signs', $advices['warning_sign']->instruction ?? 'Jika kucing mengalami kesulitan napas, lemas total, atau muntah hebat terus menerus, segera hubungi atau bawa ke klinik.') }}</textarea>
                    </div>

                    <!-- Jadwal Kontrol Lanjutan (Follow-up) -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700">Rencana Kontrol Ulang (Follow-up)</span>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kontrol Yang Disarankan</label>
                                <input type="date" name="followup_date" min="{{ date('Y-m-d') }}" value="{{ old('followup_date', $followup && $followup->scheduled_at ? $followup->scheduled_at->format('Y-m-d') : '') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Catatan Kontrol</label>
                                <input type="text" name="followup_reason" value="{{ old('followup_reason', $followup->reason ?? 'Evaluasi respons pengobatan dan penimbangan berat badan') }}" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 text-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Internal Dokter (Rahasia) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Catatan Internal Dokter (Kerahasiaan Medis / Tidak Ditampilkan ke Member)
                        </label>
                        <textarea name="internal_notes" rows="2" class="w-full text-xs rounded-xl border-slate-200 p-2.5 text-slate-700 bg-slate-50" placeholder="Catatan internal dokter/klinik, pertimbangan klinis khusus...">{{ old('internal_notes', $record->internal_notes) }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" @click="setStep(3)" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs">
                            &larr; Kembali ke Step 3
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs shadow-xs transition">
                            Simpan & Lanjut ke Step 5 (Review & Finalisasi) &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================================
             STEP 5: REVIEW & FINALISASI
             =================================================================== -->
        <div x-show="currentStep === 5" x-cloak class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs space-y-8">
                <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700">Langkah 5 dari 5</span>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">Review Akhir & Finalisasi Rekam Medis</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tinjau seluruh catatan SOAP sebelum difinalisasi. Setelah difinalisasi, rekam medis dikunci dan ringkasan serta resep otomatis diterbitkan untuk member.</p>
                    </div>
                    <span class="text-2xl">📋</span>
                </div>

                <!-- Unified Summary Cards -->
                <div class="grid md:grid-cols-2 gap-6">
                    
                    <!-- Subjective Summary -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">1. Subjective (Anamnesa)</h3>
                            <button type="button" @click="setStep(1)" class="text-xs text-teal-700 font-bold hover:underline">Edit</button>
                        </div>
                        <p class="text-xs text-slate-700"><strong>Keluhan:</strong> {{ $subj->member_complaint ?? $record->chief_complaint }}</p>
                        <p class="text-xs text-slate-600"><strong>Onset:</strong> {{ $subj->symptom_onset ?? '-' }}</p>
                        <p class="text-xs text-slate-600"><strong>Pola Makan/Minum:</strong> {{ $subj->appetite_history ?? 'Normal' }} / {{ $subj->drinking_history ?? 'Normal' }}</p>
                        <p class="text-xs text-slate-600"><strong>Urin/BAB:</strong> {{ $subj->urination_history ?? 'Normal' }} / {{ $subj->defecation_history ?? 'Normal' }}</p>
                    </div>

                    <!-- Objective Summary -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">2. Objective (Pemeriksaan Fisik)</h3>
                            <button type="button" @click="setStep(2)" class="text-xs text-teal-700 font-bold hover:underline">Edit</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div><strong>Berat:</strong> {{ $record->weight }} kg</div>
                            <div><strong>Suhu:</strong> {{ $record->temperature }} °C</div>
                            <div><strong>Kondisi:</strong> {{ $record->general_condition }}</div>
                            <div><strong>BCS:</strong> {{ $objectivesMap['bcs']->value_text ?? '5/9' }}</div>
                        </div>
                    </div>

                    <!-- Assessment Summary -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">3. Assessment (Diagnosis)</h3>
                            <button type="button" @click="setStep(3)" class="text-xs text-teal-700 font-bold hover:underline">Edit</button>
                        </div>
                        @if($primaryDiag)
                            <div class="text-xs font-bold text-slate-900">{{ $primaryDiag->diagnosis_name }}</div>
                            <div class="text-[11px] text-slate-500">
                                Kepastian: <span class="capitalize font-semibold text-slate-700">{{ $primaryDiag->certainty }}</span> &bull;
                                Keparahan: <span class="capitalize font-semibold text-slate-700">{{ $primaryDiag->severity }}</span>
                            </div>
                        @else
                            <div class="text-xs text-rose-600 font-semibold">⚠ Belum ada diagnosis utama yang disimpan!</div>
                        @endif
                    </div>

                    <!-- Plan & Prescriptions Summary -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">4. Plan & Resep Terbit</h3>
                            <button type="button" @click="setStep(4)" class="text-xs text-teal-700 font-bold hover:underline">Edit</button>
                        </div>
                        <div class="text-xs text-slate-700">
                            <strong>Resep Obat:</strong> 
                            @if(!$activeRx)
                                Tidak ada resep
                            @elseif($activeRx->isManual())
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Resep Manual (Kertas Fisik)</span>
                            @elseif($activeRx->isCompounded())
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">Resep Racikan</span>
                                ({{ $activeRx->items->count() }} item)
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800">Resep Reguler</span>
                                ({{ $activeRx->items->count() }} item obat)
                            @endif
                        </div>
                        @if($activeRx && $activeRx->isManual() && $activeRx->notes)
                            <div class="text-xs text-slate-600 italic">
                                Catatan Resep Fisik: "{{ $activeRx->notes }}"
                            </div>
                        @endif
                        <div class="text-xs text-slate-700">
                            <strong>Kontrol Ulang:</strong> {{ $followup && $followup->scheduled_at ? $followup->scheduled_at->format('d M Y') : 'Tidak dijadwalkan' }}
                        </div>
                    </div>

                </div>

                <!-- Finalize Action Box -->
                <div class="p-5 rounded-2xl bg-teal-50 border border-teal-200 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="text-2xl">🔒</div>
                        <div>
                            <h3 class="font-outfit font-bold text-slate-900 text-sm">Konfirmasi Finalisasi Rekam Medis</h3>
                            <p class="text-xs text-slate-600 mt-0.5">
                                Dengan memfinalisasi, status rekam medis akan berubah menjadi <strong>Selesai (Completed)</strong>. Resep elektronik akan resmi diterbitkan dengan nomor registrasi, dan ringkasan medis akan dapat dilihat oleh pemilik kucing (Member). Koreksi selanjutnya hanya dapat dilakukan melalui jalur amendemen resmi.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('dokter.medical-records.finalize', $record) }}" class="space-y-4 pt-2">
                        @csrf
                        <label class="flex items-center gap-2.5 text-xs font-semibold text-slate-800 cursor-pointer">
                            <input type="checkbox" required class="rounded text-teal-700 focus:ring-teal-600">
                            <span>Saya mengonfirmasi bahwa seluruh hasil pemeriksaan medis, diagnosis, dan resep di atas telah diverifikasi secara klinis.</span>
                        </label>

                        <div class="flex items-center justify-between pt-2 border-t border-teal-200">
                            <button type="button" @click="setStep(4)" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-white font-semibold text-xs">
                                &larr; Kembali ke Step 4
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                                <span>✓ Finalisasi & Terbitkan Rekam Medis</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- ===================================================================
             MODAL: RIWAYAT PASIEN SEBELUMNYA
             =================================================================== -->
        <div x-show="showHistoryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="showHistoryModal = false" class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"></div>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl">📜</span>
                            <h3 class="font-outfit text-lg font-bold text-slate-900">Riwayat Medis Pasien: {{ $record->cat->name }}</h3>
                        </div>
                        <button type="button" @click="showHistoryModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-sm">✕</button>
                    </div>

                    <div class="p-6 max-h-[70vh] overflow-y-auto space-y-4">
                        @if($previousRecords->isEmpty())
                            <div class="text-center py-8 text-slate-400 text-xs">
                                <span>Tidak ada riwayat rekam medis terdahulu untuk kucing ini. Ini adalah kunjungan pemeriksaan pertamanya.</span>
                            </div>
                        @else
                            @foreach($previousRecords as $prev)
                                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="font-mono text-xs font-bold text-slate-800">{{ $prev->record_number }}</div>
                                        <span class="text-[10px] text-slate-500">{{ $prev->completed_at ? $prev->completed_at->format('d M Y, H:i') : $prev->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="text-xs text-slate-700">
                                        <strong>Dokter:</strong> {{ $prev->doctor->name ?? '-' }} &bull;
                                        <strong>BB:</strong> {{ $prev->weight }} kg &bull;
                                        <strong>Suhu:</strong> {{ $prev->temperature }} °C
                                    </div>
                                    <div class="text-xs text-slate-700">
                                        <strong>Diagnosis:</strong> {{ $prev->primaryDiagnosis->diagnosis_name ?? ($prev->general_condition ?? '-') }}
                                    </div>
                                    @if($prev->treatment_notes)
                                        <div class="text-xs text-slate-500 italic bg-white p-2 rounded border border-slate-100">
                                            "{{ $prev->treatment_notes }}"
                                        </div>
                                    @endif
                                    <div class="text-right pt-1">
                                        <a href="{{ route('dokter.medical-records.show', $prev) }}" target="_blank" class="text-[11px] font-bold text-teal-700 hover:underline">Buka Detail Rekam Medis &rarr;</a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 text-right">
                        <button type="button" @click="showHistoryModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================================================================
             MODAL: SALIN RESEP PASIEN LAIN (COPY RESEP)
             =================================================================== -->
        <div x-show="showCopyModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="copy-modal-title"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="showCopyModal = false">
            <div class="min-h-full flex items-end sm:items-center justify-center p-0 sm:p-4 text-center">
                <div @click="showCopyModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" aria-hidden="true"></div>

                <div class="relative w-full max-w-full sm:max-w-3xl bg-white rounded-t-2xl sm:rounded-2xl text-left shadow-2xl transform transition-all border border-slate-200 overflow-hidden flex flex-col max-h-[92vh] sm:max-h-[85vh] z-10">
                    <!-- Modal Header (Fixed at top) -->
                    <div class="bg-gradient-to-r from-teal-900 to-slate-900 px-4 sm:px-6 py-3.5 sm:py-4 text-white flex items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xl sm:text-2xl shrink-0">📋</span>
                            <div class="min-w-0">
                                <h3 id="copy-modal-title" class="font-outfit text-sm sm:text-lg font-bold text-white truncate">Salin Resep Pasien Lain</h3>
                                <p class="text-[11px] sm:text-xs text-teal-100 truncate">Gunakan resep reguler atau formula racikan dari kucing lain</p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="showCopyModal = false" 
                                aria-label="Tutup modal"
                                class="w-10 h-10 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-teal-100 hover:text-white font-bold text-sm transition">
                            ✕
                        </button>
                    </div>

                    <!-- Search & Filter Bar (Fixed under header) -->
                    <div class="p-3 sm:p-4 bg-slate-50 border-b border-slate-200 space-y-2.5 sm:space-y-3 shrink-0">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
                            <input type="text"
                                   x-model="copySearch"
                                   placeholder="Cari nama kucing, pemilik, no. resep, obat, racikan..."
                                   class="w-full text-xs sm:text-sm pl-9 pr-8 py-2 rounded-xl border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:border-teal-600">
                            <button type="button"
                                    x-show="copySearch"
                                    @click="copySearch = ''"
                                    aria-label="Hapus kata kunci pencarian"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                ✕
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 sm:pb-0 text-xs scrollbar-none">
                            <span class="text-slate-500 font-semibold text-[11px] whitespace-nowrap shrink-0">Filter:</span>
                            <button type="button"
                                    @click="copyTypeFilter = 'all'"
                                    :class="copyTypeFilter === 'all' ? 'bg-teal-700 text-white font-bold shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 font-medium'"
                                    :style="copyTypeFilter === 'all' ? 'background-color: #0f766e !important; color: #ffffff !important;' : ''"
                                    class="px-2.5 sm:px-3 py-1.5 rounded-lg whitespace-nowrap transition min-h-[34px] text-xs shrink-0 cursor-pointer">
                                Semua (<span x-text="copyablePrescriptions.length"></span>)
                            </button>
                            <button type="button"
                                    @click="copyTypeFilter = 'regular'"
                                    :class="copyTypeFilter === 'regular' ? 'bg-teal-700 text-white font-bold shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 font-medium'"
                                    :style="copyTypeFilter === 'regular' ? 'background-color: #0f766e !important; color: #ffffff !important;' : ''"
                                    class="px-2.5 sm:px-3 py-1.5 rounded-lg whitespace-nowrap transition min-h-[34px] text-xs shrink-0 cursor-pointer">
                                💊 Resep Reguler
                            </button>
                            <button type="button"
                                    @click="copyTypeFilter = 'compounded'"
                                    :class="copyTypeFilter === 'compounded' ? 'bg-teal-700 text-white font-bold shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 font-medium'"
                                    :style="copyTypeFilter === 'compounded' ? 'background-color: #0f766e !important; color: #ffffff !important;' : ''"
                                    class="px-2.5 sm:px-3 py-1.5 rounded-lg whitespace-nowrap transition min-h-[34px] text-xs shrink-0 cursor-pointer">
                                🧪 Resep Racikan
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable List of Prescriptions -->
                    <div class="p-3 sm:p-5 flex-1 overflow-y-auto overscroll-contain space-y-3 sm:space-y-4">
                        <template x-if="filteredCopyablePrescriptions.length === 0">
                            <div class="text-center py-10 sm:py-14 text-slate-500 text-xs space-y-2">
                                <span class="text-3xl block">🔍</span>
                                <div class="font-semibold text-slate-700 text-sm">Tidak ada resep yang sesuai</div>
                                <p class="text-slate-500 text-[11px] max-w-xs mx-auto">Coba ubah kata kunci pencarian atau ganti filter kategori resep di atas.</p>
                            </div>
                        </template>

                        <template x-for="rx in filteredCopyablePrescriptions" :key="rx.id">
                            <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-2xs hover:border-teal-300 transition space-y-3">
                                <!-- Prescription Card Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                    <div class="flex items-start sm:items-center gap-3 min-w-0">
                                        <template x-if="rx.cat_photo">
                                            <img :src="rx.cat_photo" :alt="rx.cat_name" class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl object-cover border border-slate-200 shrink-0">
                                        </template>
                                        <template x-if="!rx.cat_photo">
                                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-800 text-white font-bold text-sm sm:text-base flex items-center justify-center shrink-0" x-text="rx.cat_name.substring(0, 1)"></div>
                                        </template>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                <h4 class="font-bold text-xs sm:text-sm text-slate-900" x-text="rx.cat_name"></h4>
                                                <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold bg-slate-100 text-slate-700" x-text="rx.prescription_number"></span>
                                                <span class="text-[10px] px-2 py-0.5 rounded font-bold"
                                                      :class="rx.type === 'compounded' ? 'bg-purple-100 text-purple-800' : 'bg-teal-100 text-teal-800'"
                                                      x-text="rx.type_label"></span>
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                <span>Pemilik: <strong class="text-slate-700" x-text="rx.owner_name"></strong></span>
                                                <span>&bull;</span>
                                                <span x-text="rx.date_formatted"></span>
                                                <span class="hidden sm:inline">&bull;</span>
                                                <span class="hidden sm:inline">Dokter: <span x-text="rx.doctor_name"></span></span>
                                            </div>
                                            <div class="text-[11px] text-teal-800 font-medium mt-0.5">
                                                Diagnosis: <span class="font-semibold" x-text="rx.diagnosis_name"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action: Copy Entire Prescription -->
                                    <form method="POST" action="{{ route('dokter.medical-records.prescription.copy', $record) }}" class="w-full sm:w-auto shrink-0">
                                        @csrf
                                        <input type="hidden" name="prescription_id" :value="rx.id">
                                        <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs shadow-xs transition min-h-[44px]">
                                            <span>📥</span>
                                            <span>Salin Semua (<span x-text="rx.items_count"></span> Item)</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- Prescription Items Preview -->
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 block">Daftar Item Obat / Formula:</span>
                                    <div class="grid gap-2">
                                        <template x-for="item in rx.items" :key="item.id">
                                            <div class="p-2.5 sm:p-3 rounded-xl border border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                                                <div class="space-y-1 min-w-0 flex-1">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <template x-if="item.is_racikan">
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-800">🧪 Racikan</span>
                                                        </template>
                                                        <template x-if="!item.is_racikan">
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-teal-100 text-teal-800">💊 Obat Jadi</span>
                                                        </template>
                                                        <strong class="text-slate-900" x-text="item.name"></strong>
                                                        <span class="text-[10px] text-slate-500 uppercase font-mono" x-text="'(' + item.dosage_form + ')'"></span>
                                                    </div>

                                                    <template x-if="item.is_racikan && item.composition">
                                                        <div class="text-[11px] text-purple-950 font-mono bg-purple-50 p-2 rounded-lg border border-purple-100 break-words">
                                                            <strong>Formula:</strong> <span x-text="item.composition"></span>
                                                        </div>
                                                    </template>

                                                    <div class="text-[11px] text-slate-600 leading-relaxed" x-text="item.summary"></div>
                                                    <template x-if="item.instructions">
                                                        <div class="text-[11px] text-slate-500 italic leading-snug">
                                                            Aturan pakai: "<span x-text="item.instructions"></span>"
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Copy Single Item Button -->
                                                <form method="POST" action="{{ route('dokter.medical-records.prescription.copy', $record) }}" class="w-full sm:w-auto shrink-0 pt-1 sm:pt-0 border-t sm:border-t-0 border-slate-200/60">
                                                    @csrf
                                                    <input type="hidden" name="prescription_item_id" :value="item.id">
                                                    <button type="submit"
                                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-white hover:bg-teal-50 text-slate-700 hover:text-teal-800 font-semibold text-xs border border-slate-200 hover:border-teal-300 shadow-2xs transition min-h-[40px] sm:min-h-[36px]">
                                                        <span>+</span>
                                                        <span>Salin Item Ini</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer (Fixed at bottom) -->
                    <div class="bg-slate-50 px-4 sm:px-6 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 text-xs shrink-0">
                        <div class="text-slate-500 text-[11px] flex items-center gap-1.5">
                            <span>💡</span>
                            <span>Item yang disalin akan masuk ke draf resep aktif pasien saat ini.</span>
                        </div>
                        <button type="button" 
                                @click="showCopyModal = false" 
                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 font-semibold text-xs transition min-h-[44px] flex items-center justify-center">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
