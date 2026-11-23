<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xl">💊</span>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-outfit">
                        Ruang Farmasi & Apotek KucingMu
                    </h1>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Pelayanan, penyiapan, dan penyerahan resep obat dokter hewan kepada member.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-900 border border-teal-200">
                    Tenaga Farmasi Resmi
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-xs" role="alert">
                    <span class="text-teal-600 font-bold text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-xs" role="alert">
                    <span class="text-rose-600 font-bold text-base">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistik Kartu Ringkasan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('farmasi.prescriptions.index', ['status' => 'waiting']) }}"
                   class="p-4 rounded-2xl border transition shadow-xs {{ $status === 'waiting' ? 'bg-amber-50/90 border-amber-300 ring-2 ring-amber-400/30' : 'bg-white border-slate-200 hover:border-amber-300' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Menunggu Penyerahan</span>
                        <span class="text-lg">⏳</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-bold font-outfit text-amber-900">{{ $stats['waiting_count'] }}</span>
                        <span class="text-xs text-amber-700">antrian obat</span>
                    </div>
                    <span class="text-[11px] text-amber-700 mt-1 block">Resep dokter siap disiapkan & diserahkan</span>
                </a>

                <a href="{{ route('farmasi.prescriptions.index', ['status' => 'dispensed']) }}"
                   class="p-4 rounded-2xl border transition shadow-xs {{ $status === 'dispensed' ? 'bg-emerald-50/90 border-emerald-300 ring-2 ring-emerald-400/30' : 'bg-white border-slate-200 hover:border-emerald-300' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Sudah Diserahkan</span>
                        <span class="text-lg">✓</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-bold font-outfit text-emerald-900">{{ $stats['dispensed_count'] }}</span>
                        <span class="text-xs text-emerald-700">resep tuntas</span>
                    </div>
                    <span class="text-[11px] text-emerald-700 mt-1 block">Telah diterima oleh pemilik kucing</span>
                </a>

                <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Resep Masuk Hari Ini</span>
                        <span class="text-lg">📅</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-bold font-outfit text-slate-800">{{ $stats['today_count'] }}</span>
                        <span class="text-xs text-slate-500">resep baru</span>
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Tanggal {{ date('d M Y') }}</span>
                </div>
            </div>

            <!-- Filter & Pencarian -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <!-- Status Filter Tabs -->
                    <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl overflow-x-auto">
                        <a href="{{ route('farmasi.prescriptions.index', ['status' => 'waiting', 'search' => $search]) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'waiting' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            ⏳ Menunggu ({{ $stats['waiting_count'] }})
                        </a>
                        <a href="{{ route('farmasi.prescriptions.index', ['status' => 'dispensed', 'search' => $search]) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'dispensed' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            ✓ Sudah Diserahkan ({{ $stats['dispensed_count'] }})
                        </a>
                        <a href="{{ route('farmasi.prescriptions.index', ['status' => 'all', 'search' => $search]) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ $status === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Semua Resep
                        </a>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('farmasi.prescriptions.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari no. resep, anabul, pemilik..."
                               class="form-input text-xs rounded-xl py-2 px-3 border-slate-300 focus:border-teal-500 focus:ring-teal-500 w-full sm:w-64">
                        <button type="submit" class="button-primary text-xs font-semibold py-2 px-3.5 whitespace-nowrap">
                            Cari
                        </button>
                        @if($search)
                            <a href="{{ route('farmasi.prescriptions.index', ['status' => $status]) }}" class="button-secondary text-xs py-2 px-2.5">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Daftar Resep Table -->
                @if($prescriptions->isEmpty())
                    <div class="py-12 text-center text-slate-500 space-y-2">
                        <span class="text-3xl block">💊</span>
                        <p class="font-bold text-sm text-slate-700">Tidak ada resep obat yang ditemukan.</p>
                        <p class="text-xs text-slate-500">
                            @if($status === 'waiting')
                                Semua resep dokter telah selesai diserahkan kepada member.
                            @else
                                Belum ada data resep sesuai filter atau kata kunci pencarian.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-800 font-bold uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="py-3 px-3.5">No. Resep & Tanggal</th>
                                    <th class="py-3 px-3.5">Pasien Anabul</th>
                                    <th class="py-3 px-3.5">Pemilik Kucing</th>
                                    <th class="py-3 px-3.5">Dokter & Diagnosis</th>
                                    <th class="py-3 px-3.5">Item Terapi</th>
                                    <th class="py-3 px-3.5">Status Penyerahan</th>
                                    <th class="py-3 px-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($prescriptions as $rx)
                                    @php
                                        $record = $rx->medicalRecord;
                                        $cat = $record?->cat;
                                        $owner = $cat?->owner;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            <span class="font-mono font-bold text-teal-900 block">{{ $rx->prescription_number }}</span>
                                            <span class="text-[10px] text-slate-400">
                                                {{ $rx->issued_at ? $rx->issued_at->format('d M Y, H:i') : ($rx->created_at ? $rx->created_at->format('d M Y') : '-') }}
                                            </span>
                                            <span class="inline-block mt-0.5 text-[9px] px-1.5 py-0.2 rounded font-semibold {{ $rx->isManual() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $rx->type_label }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5">
                                            <div class="flex items-center gap-2">
                                                @if($cat && $cat->primary_photo_url)
                                                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0">
                                                @else
                                                    <div class="w-8 h-8 rounded-lg bg-teal-50 border border-teal-200 flex items-center justify-center text-xs shrink-0">🐱</div>
                                                @endif
                                                <div class="min-w-0">
                                                    <strong class="font-bold text-slate-900 block truncate">{{ $cat->name ?? 'Pasien' }}</strong>
                                                    <span class="text-[11px] text-slate-500 block truncate">{{ $cat->breed ?? 'Domestik' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            <span class="font-semibold text-slate-800 block">{{ $owner->name ?? '-' }}</span>
                                            <span class="text-[11px] text-slate-500 font-mono">{{ $owner->phone ?: '-' }}</span>
                                        </td>
                                        <td class="py-3 px-3.5">
                                            <span class="font-medium text-slate-800 block">drh. {{ $record->doctor->name ?? '-' }}</span>
                                            <span class="text-[11px] text-teal-800 font-semibold block truncate max-w-[180px]">
                                                {{ $record->primaryDiagnosis->diagnosis_name ?? ($record->general_condition ?: 'Pemeriksaan Rutin') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5">
                                            @if($rx->isManual())
                                                <span class="text-amber-800 font-medium text-[11px]">Resep Fisik Manual</span>
                                            @else
                                                <span class="font-bold text-slate-800">{{ $rx->items->count() }} item obat</span>
                                                <div class="text-[10px] text-slate-500 truncate max-w-[180px]">
                                                    {{ $rx->items->pluck('medicine_name_snapshot')->filter()->join(', ') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            @if($rx->isDispensed())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    ✓ Sudah Diserahkan
                                                </span>
                                                @if($rx->dispenser)
                                                    <span class="text-[10px] text-slate-400 block mt-0.5">Oleh: {{ $rx->dispenser->name }}</span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    ⏳ Menunggu Penyerahan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                            <a href="{{ route('farmasi.prescriptions.show', $rx->id) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-xs {{ $rx->isDispensed() ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'button-primary' }}">
                                                <span>{{ $rx->isDispensed() ? 'Lihat Detail' : 'Proses Resep →' }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-2">
                        {{ $prescriptions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
