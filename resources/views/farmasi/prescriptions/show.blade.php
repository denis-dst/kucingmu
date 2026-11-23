<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('farmasi.prescriptions.index') }}"
                   class="button-secondary text-xs py-1.5 px-2.5 flex items-center gap-1">
                    <span>← Kembali</span>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-outfit">
                        Detail Resep #{{ $prescription->prescription_number }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Verifikasi item sediaan dan penyerahan obat ke member.
                    </p>
                </div>
            </div>
            <div>
                @if($prescription->isDispensed())
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        ✓ Status: Obat Sudah Diserahkan
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        ⏳ Status: Menunggu Penyerahan
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $record = $prescription->medicalRecord;
                $cat = $record?->cat;
                $owner = $cat?->owner;
            @endphp

            <!-- Patient & Doctor Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-start justify-between gap-4 flex-wrap border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3.5">
                        @if($cat && $cat->primary_photo_url)
                            <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}"
                                 class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-xs">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-2xl">🐱</div>
                        @endif
                        <div>
                            <h2 class="text-base font-bold text-slate-900 font-outfit">{{ $cat->name ?? 'Pasien Kucing' }}</h2>
                            <p class="text-xs text-slate-600 mt-0.5">
                                {{ $cat->breed ?? 'Domestik' }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }} &bull; {{ $cat->age_text }}
                            </p>
                            @if($cat && $cat->unique_code)
                                <span class="font-mono text-[10px] text-teal-800 font-bold bg-teal-50 px-2 py-0.5 rounded border border-teal-200 mt-1 inline-block">
                                    NIAKuMu: {{ $cat->unique_code }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right text-xs space-y-1">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Pemilik Kucing</span>
                        <strong class="text-slate-900 text-sm block">{{ $owner->name ?? '-' }}</strong>
                        <span class="text-slate-500 font-mono block">{{ $owner->phone ?: '-' }}</span>
                        @if($owner && $owner->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $owner->phone) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:underline">
                                <span>💬 Hubungi via WhatsApp</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Info Medis Singkat -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-slate-50 p-3.5 rounded-xl border border-slate-200/80">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Dokter Pemeriksa</span>
                        <p class="font-bold text-slate-800 mt-0.5">drh. {{ $record->doctor->name ?? '-' }}</p>
                        <span class="text-[11px] text-slate-500">Rekam Medis #{{ $record->record_number }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Diagnosis Utama</span>
                        <p class="font-bold text-teal-900 mt-0.5">
                            {{ $record->primaryDiagnosis->diagnosis_name ?? ($record->general_condition ?: 'Pemeriksaan Rutin') }}
                        </p>
                        <span class="text-[11px] text-slate-500">Bobot: {{ $record->weight }} kg | Suhu: {{ $record->temperature }} &deg;C</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Waktu Terbit Resep</span>
                        <p class="font-bold text-slate-800 mt-0.5">
                            {{ $prescription->issued_at ? $prescription->issued_at->format('d M Y, H:i') : ($prescription->created_at ? $prescription->created_at->format('d M Y') : '-') }} WIB
                        </p>
                        <span class="text-[11px] text-slate-500">Tipe: {{ $prescription->type_label }}</span>
                    </div>
                </div>
            </div>

            <!-- Resep Obat Section -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">💊</span>
                        <h3 class="font-outfit font-bold text-slate-900 text-base">Daftar Obat yang Diresepkan</h3>
                    </div>
                    <span class="text-xs text-slate-500 font-mono">No. Rx: {{ $prescription->prescription_number }}</span>
                </div>

                @if($prescription->isManual())
                    <div class="p-4 rounded-xl border border-amber-300 bg-amber-50/80 text-xs space-y-2">
                        <strong class="text-amber-900 font-bold block text-sm">Resep Obat Ditulis di Lembar Fisik Manual</strong>
                        <p class="text-amber-800 leading-relaxed">
                            Dokter memberikan resep tertulis fisik kepada pemilik. Mohon periksa lembar fisik resep saat penyerahan obat kepada member.
                        </p>
                        @if($prescription->notes)
                            <div class="p-2.5 rounded-lg bg-white border border-amber-200 text-slate-800 mt-2">
                                <strong>Catatan Dokter:</strong> {{ $prescription->notes }}
                            </div>
                        @endif
                    </div>
                @elseif($prescription->items->isEmpty())
                    <div class="p-6 text-center text-xs text-slate-500 bg-slate-50 rounded-xl">
                        Tidak ada item obat terdaftar pada resep ini.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($prescription->items as $idx => $item)
                            <div class="p-4 rounded-xl border {{ $item->is_dispensed ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50/50' }} space-y-2">
                                <div class="flex items-start justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="w-6 h-6 rounded-full bg-teal-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $idx + 1 }}
                                        </span>
                                        <h4 class="font-bold text-slate-900 text-sm">
                                            {{ $item->is_racikan ? ($item->racikan_name ?: $item->medicine_name_snapshot) : $item->medicine_name_snapshot }}
                                        </h4>
                                        @if($item->is_racikan)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                                🧪 Racikan
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-200/80 text-slate-700 uppercase">
                                            {{ $item->dosage_form }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-teal-900 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                            Jumlah: {{ $item->quantity_value }} {{ $item->quantity_unit }}
                                        </span>
                                        @if($item->is_dispensed)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-300">
                                                ✓ Diserahkan
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($item->is_racikan && $item->composition)
                                    <div class="p-2.5 rounded-lg bg-purple-50/70 border border-purple-100 text-purple-950 text-xs">
                                        <strong class="font-bold text-purple-900">Komposisi Bahan:</strong> {{ $item->composition }}
                                    </div>
                                @endif

                                <div class="text-xs text-slate-700 font-medium">
                                    @if(!$item->is_racikan)
                                        Dosis: <strong class="text-teal-800">{{ $item->dose_value }} {{ $item->dose_unit }}</strong> &bull;
                                    @endif
                                    Frekuensi: <strong>{{ $item->frequency_value }} {{ $item->frequency_unit }}</strong> &bull;
                                    Durasi: <strong>{{ $item->duration_value }} {{ $item->duration_unit }}</strong> &bull;
                                    Rute Pemberian: <strong>{{ $item->administration_route }}</strong>
                                </div>

                                <div class="p-2.5 rounded-lg bg-teal-50/60 border border-teal-100 text-teal-950 text-xs">
                                    <strong class="font-bold">Aturan & Petunjuk Pakai:</strong> {{ $item->usage_instructions }}
                                    @if($item->warnings)
                                        <div class="text-rose-700 font-semibold mt-1">⚠️ Perhatian Khusus: {{ $item->warnings }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Form Penyerahan Obat (Dispense Action) -->
            <div class="bg-white p-5 rounded-2xl border {{ $prescription->isDispensed() ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200' }} shadow-xs space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="text-xl">📋</span>
                    <h3 class="font-outfit font-bold text-slate-900 text-base">
                        {{ $prescription->isDispensed() ? 'Catatan Penyerahan Farmasi' : 'Konfirmasi Penyerahan Obat ke Member' }}
                    </h3>
                </div>

                @if($prescription->isDispensed())
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-emerald-900 text-sm flex items-center gap-1.5">
                                <span class="text-emerald-600 text-base">✓</span> Obat Resmi Telah Diberikan kepada Member
                            </span>
                            <span class="text-[11px] text-emerald-700 font-mono">
                                {{ $prescription->dispensed_at ? $prescription->dispensed_at->format('d M Y, H:i') : '-' }} WIB
                            </span>
                        </div>
                        <p class="text-emerald-800">
                            Petugas Farmasi Penyerah: <strong>{{ $prescription->dispenser->name ?? '-' }}</strong>
                        </p>
                        @if($prescription->dispense_notes)
                            <div class="p-3 rounded-lg bg-white border border-emerald-200 text-slate-800 mt-2">
                                <strong>Catatan Edukasi & Penyerahan:</strong>
                                <p class="mt-0.5 leading-relaxed">{{ $prescription->dispense_notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('farmasi.prescriptions.dispense', $prescription->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="dispense_notes" class="form-label text-xs font-semibold text-slate-700">
                                Catatan Edukasi & Penyerahan Obat <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea id="dispense_notes"
                                      name="dispense_notes"
                                      rows="3"
                                      placeholder="Contoh: Obat telah diserahkan langsung kepada pemilik anabul. Cara pemberian setelah makan dan jadwal tetes mata telah diedukasikan."
                                      class="form-input text-xs rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 w-full mt-1"></textarea>
                            <span class="text-[11px] text-slate-500 mt-1 block">
                                Member pemilik kucing akan menerima notifikasi otomatis bahwa obat resep telah siap/diserahkan.
                            </span>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-3">
                            <a href="{{ route('farmasi.prescriptions.index') }}" class="button-secondary text-xs py-2.5 px-4 font-semibold">
                                Batal
                            </a>
                            <button type="submit"
                                    onclick="return confirm('Konfirmasi bahwa seluruh obat resep ini telah disiapkan dan diserahkan kepada member?')"
                                    class="button-primary text-xs py-2.5 px-5 font-bold flex items-center gap-2">
                                <span>✓</span>
                                <span>Tandai Obat Sudah Diberikan kepada Member</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
