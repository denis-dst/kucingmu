<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold">
                    <span>🩺</span>
                    <span>Modul Rekam Medis Veteriner (SOAP)</span>
                </div>
                <h1 class="font-outfit text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">
                    Portal & Antrian Dokter Hewan
                </h1>
                <p class="text-sm text-slate-600 max-w-2xl">
                    Kelola antrian pemeriksaan kucing pasien hari ini, buat catatan klinis terstandar SOAP (Subjective, Objective, Assessment, Plan), terbitkan resep elektronik, dan tinjau riwayat kesehatan kucing.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('dokter.medical-records.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Mulai Periksa (+ Pasien)</span>
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-medium flex items-center gap-2">
                <span class="text-emerald-700 font-bold">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-sm font-medium flex items-center gap-2">
                <span class="text-blue-700 font-bold">ℹ</span>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm font-medium flex items-center gap-2">
                <span class="text-rose-700 font-bold">⚠</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Antrian Hari Ini</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['today_queue'] }}</span>
                    <span class="text-xs text-slate-500">pasien</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Draft Berjalan</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-amber-600">{{ $stats['in_progress'] }}</span>
                    <span class="text-xs text-slate-500">belum difinalisasi</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pemeriksaan Selesai</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-emerald-700">{{ $stats['completed'] }}</span>
                    <span class="text-xs text-slate-500">terfinalisasi</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Resep Diterbitkan</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="font-outfit text-2xl font-bold text-indigo-700">{{ $stats['total_issued_rx'] }}</span>
                    <span class="text-xs text-slate-500">e-Prescription</span>
                </div>
            </div>
        </div>

        <!-- Section 1: Antrian Pemeriksaan Hari Ini -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">📋</span>
                    <div>
                        <h2 class="font-outfit text-lg font-bold text-slate-900 leading-tight">Antrian Pemeriksaan Hari Ini</h2>
                        <p class="text-xs text-slate-500">Janji temu yang sudah dijadwalkan atau di-check-in oleh relawan/klinik.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-bold border border-teal-200">
                    {{ $queue->count() }} Menunggu
                </span>
            </div>

            @if($queue->isEmpty())
                <div class="text-center py-10 text-slate-500">
                    <span class="text-3xl">📭</span>
                    <p class="mt-3 text-sm font-semibold text-slate-700">Tidak ada antrian pasien untuk hari ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Mulai Periksa (+ Pasien)" untuk memeriksa kucing secara langsung di luar antrian janji temu.</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($queue as $app)
                        <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/70 hover:bg-white hover:border-teal-300 transition flex flex-col justify-between gap-4">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        @if($app->cat->primary_photo_url)
                                            <img src="{{ $app->cat->primary_photo_url }}" alt="{{ $app->cat->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-800 font-bold text-lg flex items-center justify-center">
                                                {{ substr($app->cat->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-outfit font-bold text-slate-900 text-base leading-tight">{{ $app->cat->name }}</h3>
                                            <p class="text-xs text-slate-500">{{ $app->cat->breed }} &bull; {{ $app->cat->gender === 'male' ? 'Jantan' : 'Betina' }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $app->status === 'checked_in' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                        {{ $app->status === 'checked_in' ? 'Siap Periksa' : 'Direncanakan' }}
                                    </span>
                                </div>

                                <div class="text-xs text-slate-600 bg-white p-2.5 rounded-lg border border-slate-100 space-y-1">
                                    <div><span class="text-slate-400">Pemilik:</span> <span class="font-semibold text-slate-700">{{ $app->cat->owner->name ?? '-' }}</span></div>
                                    <div><span class="text-slate-400">NBM:</span> <span class="font-mono text-slate-700">{{ $app->cat->owner->formatted_nbm ?? '-' }}</span></div>
                                    @if($app->notes)
                                        <div class="pt-1 text-slate-500 italic border-t border-slate-100">"{{ $app->notes }}"</div>
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('dokter.medical-records.store') }}">
                                @csrf
                                <input type="hidden" name="cat_id" value="{{ $app->cat_id }}">
                                <input type="hidden" name="appointment_id" value="{{ $app->id }}">
                                <input type="hidden" name="service_type" value="clinic">
                                <input type="hidden" name="chief_complaint" value="{{ $app->notes ?: 'Pemeriksaan rutin antrian klinik' }}">
                                <button type="submit" class="w-full py-2 px-3 rounded-lg bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs flex items-center justify-center gap-1.5 transition">
                                    <span>🩺 Mulai Pemeriksaan SOAP</span>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Section 2: Daftar & Riwayat Seluruh Rekam Medis -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-outfit text-lg font-bold text-slate-900 leading-tight">Daftar Rekam Medis Pasien</h2>
                    <p class="text-xs text-slate-500">Arsip seluruh rekam medis, status draft berjalan, dan catatan yang telah difinalisasi.</p>
                </div>

                <!-- Filters -->
                <form method="GET" action="{{ route('dokter.medical-records.index') }}" class="flex flex-wrap items-center gap-2.5">
                    <!-- Status Filter -->
                    <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-1.5 px-3 font-medium text-slate-700 focus:ring-teal-600 focus:border-teal-600">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Diperiksa</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Final)</option>
                    </select>

                    <!-- Service Type Filter -->
                    <select name="service_type" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-1.5 px-3 font-medium text-slate-700 focus:ring-teal-600 focus:border-teal-600">
                        <option value="all" {{ request('service_type') === 'all' || !request('service_type') ? 'selected' : '' }}>Semua Layanan</option>
                        <option value="clinic" {{ request('service_type') === 'clinic' ? 'selected' : '' }}>Pemeriksaan Klinik</option>
                        <option value="online" {{ request('service_type') === 'online' ? 'selected' : '' }}>Telekonsultasi Online</option>
                    </select>

                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kucing / No RM..." class="text-xs rounded-xl border-slate-200 py-1.5 pl-3 pr-8 w-44 md:w-56 text-slate-700 focus:ring-teal-600 focus:border-teal-600">
                        <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            🔍
                        </button>
                    </div>

                    @if(request()->anyFilled(['status', 'service_type', 'search']))
                        <a href="{{ route('dokter.medical-records.index') }}" class="text-xs text-rose-600 hover:underline font-semibold">Reset</a>
                    @endif
                </form>
            </div>

            @if($records->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <span class="text-3xl">📁</span>
                    <p class="mt-3 text-sm font-semibold text-slate-700">Belum ada data rekam medis yang sesuai kriteria.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-3">No. Rekam Medis</th>
                                <th class="py-3 px-3">Pasien Kucing</th>
                                <th class="py-3 px-3">Pemilik</th>
                                <th class="py-3 px-3">Layanan</th>
                                <th class="py-3 px-3">Diagnosis Utama</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Tanggal</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($records as $rec)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-3 font-mono font-semibold text-slate-900">
                                        {{ $rec->record_number }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-slate-900">{{ $rec->cat->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $rec->cat->breed ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-medium text-slate-800">{{ $rec->member->name ?? ($rec->cat->owner->name ?? '-') }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $rec->member->formatted_nbm ?? ($rec->cat->owner->formatted_nbm ?? '-') }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $rec->service_type === 'online' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-teal-50 text-teal-800 border border-teal-200' }}">
                                            {{ $rec->service_type_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($rec->primaryDiagnosis)
                                            <div class="font-semibold text-slate-900">{{ $rec->primaryDiagnosis->diagnosis_name }}</div>
                                            <div class="text-[10px] text-slate-400 capitalize">{{ $rec->primaryDiagnosis->certainty }}</div>
                                        @elseif($rec->general_condition)
                                            <div class="text-slate-600">{{ $rec->general_condition }}</div>
                                        @else
                                            <span class="text-slate-400 italic">Belum ditetapkan</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rec->status_badge_class }}">
                                            {{ $rec->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                                        {{ $rec->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap space-x-1.5">
                                        @if($rec->isCompleted())
                                            <a href="{{ route('dokter.medical-records.show', $rec) }}" class="inline-flex items-center px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition">
                                                Lihat Rekam Medis
                                            </a>
                                            <a href="{{ route('dokter.medical-records.print', $rec) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition">
                                                🖨️ Cetak
                                            </a>
                                        @else
                                            <a href="{{ route('dokter.medical-records.examine', $rec) }}" class="inline-flex items-center px-2.5 py-1 rounded bg-teal-700 hover:bg-teal-800 text-white font-semibold text-[11px] transition shadow-xs">
                                                Lanjutkan SOAP &rarr;
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    {{ $records->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
