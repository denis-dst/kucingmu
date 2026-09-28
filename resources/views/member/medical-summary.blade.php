<x-app-layout>
    @php
        $advices = $record->advices->keyBy('category');
        $issuedRx = $record->prescriptions->first();
        $followup = $record->followups->first();
    @endphp

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('dashboard') }}" class="hover:text-teal-700 font-medium">Dashboard</a>
                <span>&bull;</span>
                <span class="text-slate-800 font-semibold">Ringkasan Medis Kucing: {{ $record->cat->name }}</span>
            </div>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            
            <!-- Header -->
            <div class="p-6 md:p-8 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-teal-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    @if($record->cat->primary_photo_url)
                        <img src="{{ $record->cat->primary_photo_url }}" alt="{{ $record->cat->name }}" class="w-16 h-16 rounded-2xl object-cover border border-teal-200 shadow-2xs shrink-0">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-teal-800 text-white font-bold text-2xl flex items-center justify-center shrink-0">
                            {{ substr($record->cat->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/80 border border-teal-200 text-teal-800 text-[10px] font-bold uppercase tracking-wider mb-1">
                            <span>🩺</span>
                            <span>Hasil Pemeriksaan Dokter Hewan</span>
                        </div>
                        <h1 class="font-outfit text-2xl font-bold text-slate-900 leading-tight">
                            Ringkasan Medis: {{ $record->cat->name }}
                        </h1>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Pemeriksaan dilakukan pada <strong>{{ $record->completed_at ? $record->completed_at->format('d F Y, H:i') : $record->created_at->format('d F Y') }} WIB</strong> &bull; {{ $record->clinic_name ?: 'Klinik Hewan KucingMu' }}
                        </p>
                    </div>
                </div>

                <div class="text-left md:text-right text-xs text-slate-600 space-y-1 shrink-0 font-mono">
                    <div>No. RM: <strong class="text-slate-900 text-sm">{{ $record->record_number }}</strong></div>
                    <div>Dokter: <span class="text-slate-800 font-sans font-semibold">{{ $record->doctor->name ?? '-' }}</span></div>
                    <div>Layanan: <span class="text-slate-700 font-sans">{{ $record->service_type_label }}</span></div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 md:p-8 space-y-8">
                
                <!-- Diagnosis & Kondisi Fisik -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-teal-200 bg-teal-50/40 space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 block">Hasil Diagnosis Dokter</span>
                        <div class="font-outfit font-bold text-slate-900 text-base">
                            {{ $record->primaryDiagnosis->diagnosis_name ?? ($record->general_condition ?: 'Pemeriksaan Rutin') }}
                        </div>
                        <p class="text-xs text-slate-600">
                            Keluhan Awal: <span class="italic text-slate-800">"{{ $record->chief_complaint }}"</span>
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Hasil Pengukuran Fisik</span>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>Berat Badan: <strong class="text-slate-900">{{ $record->weight }} kg</strong></div>
                            <div>Suhu Tubuh: <strong class="text-slate-900">{{ $record->temperature }} °C</strong></div>
                            <div>Kondisi Umum: <strong class="text-slate-900">{{ $record->general_condition }}</strong></div>
                            <div>Status Kucing: <strong class="text-emerald-700">Hidup & Rawat Jalan</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Resep Obat yang Diterbitkan (Issued) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h2 class="font-outfit font-bold text-slate-900 text-base flex items-center gap-2">
                            <span>💊</span>
                            <span>Resep Obat & Aturan Pakai</span>
                        </h2>
                        @if($issuedRx)
                            <span class="text-xs font-mono font-semibold text-slate-500">No. Rx: {{ $issuedRx->prescription_number }}</span>
                        @endif
                    </div>

                    @if($issuedRx && $issuedRx->items->isNotEmpty())
                        <div class="divide-y divide-slate-100 border border-teal-200 rounded-xl overflow-hidden bg-white">
                            @foreach($issuedRx->items as $item)
                                <div class="p-4 text-xs space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                            <span>{{ $item->medicine_name_snapshot }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-slate-100 text-slate-700">
                                                {{ $item->dosage_form }}
                                            </span>
                                        </div>
                                        <span class="text-slate-500 font-medium">Qty: {{ $item->quantity_value }} {{ $item->quantity_unit }}</span>
                                    </div>
                                    <div class="text-slate-700 font-medium">
                                        Dosis: <strong class="text-teal-800">{{ $item->dose_value }} {{ $item->dose_unit }}</strong> &bull;
                                        Frekuensi: <strong>{{ $item->frequency_value }} {{ $item->frequency_unit }}</strong> &bull;
                                        Durasi: <strong>{{ $item->duration_value }} {{ $item->duration_unit }}</strong> &bull;
                                        Rute: <strong>{{ $item->administration_route }}</strong>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-teal-50/50 border border-teal-100 text-teal-900">
                                        <strong>Petunjuk Penggunaan:</strong> {{ $item->usage_instructions }}
                                        @if($item->warnings)
                                            <div class="text-rose-700 mt-1 font-semibold">⚠️ Catatan: {{ $item->warnings }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center text-xs text-slate-500">
                            Tidak ada resep obat khusus yang diterbitkan pada pemeriksaan ini.
                        </div>
                    @endif
                </div>

                <!-- Instruksi Perawatan Rumah & Tanda Bahaya -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Home Care -->
                    <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-200 space-y-2 text-xs">
                        <span class="font-bold text-emerald-900 block text-xs flex items-center gap-1.5">
                            <span>🏡</span>
                            <span>Instruksi Perawatan di Rumah</span>
                        </span>
                        <div class="text-slate-700 leading-relaxed">
                            {{ $advices['home_care']->instruction ?? ($record->recommendation ?: 'Jaga kucing tetap hangat, berikan makanan bernutrisi dan air minum segar.') }}
                        </div>
                    </div>

                    <!-- Warning Signs (Red Flags) -->
                    <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-200 space-y-2 text-xs">
                        <span class="font-bold text-rose-900 block text-xs flex items-center gap-1.5">
                            <span>🚨</span>
                            <span>Tanda Bahaya (Kapan Harus Segera Kembali)</span>
                        </span>
                        <div class="text-slate-700 leading-relaxed">
                            {{ $advices['warning_sign']->instruction ?? 'Jika kucing lemas hebat, kesulitan bernapas, atau muntah terus-menerus, segera hubungi dokter hewan terdekat.' }}
                        </div>
                    </div>
                </div>

                <!-- Jadwal Kontrol Lanjutan (Follow-up) -->
                @if($followup && $followup->scheduled_at)
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <span class="font-bold text-blue-900 block text-[11px] uppercase tracking-wider">Jadwal Kontrol Ulang</span>
                            <div class="text-slate-800">
                                Tanggal: <strong>{{ $followup->scheduled_at->format('d F Y') }}</strong> &bull; Catatan: {{ $followup->reason }}
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-bold text-[10px]">
                            Disarankan Dokter
                        </span>
                    </div>
                @endif

            </div>

            <!-- Footer -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 text-center text-xs text-slate-500">
                Dokumen ini adalah ringkasan rekam medis resmi untuk pemilik kucing. Simpan catatan ini untuk memantau kesehatan kucing Anda.
            </div>

        </div>

    </div>
</x-app-layout>
