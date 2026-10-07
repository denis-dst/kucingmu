<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="eyebrow">Pengurus &amp; Relawan Persyarikatan</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                    Penjaringan Representatif Wilayah
                </h1>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.representatives.export', request()->query()) }}" 
                   class="button-secondary text-xs px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 font-semibold text-slate-700">
                    <span>📊</span> Unduh Data (CSV)
                </a>
                <a href="{{ route('representative.register') }}" 
                   target="_blank"
                   class="button-primary text-xs px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 font-bold bg-teal-700 hover:bg-teal-800 text-white">
                    <span>➕</span> Buka Formulir Publik ↗
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Success Flash Message -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-center justify-between gap-3 text-emerald-900 text-xs sm:text-sm shadow-xs">
                    <div class="flex items-center gap-2 font-medium">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Summary Statistics Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Pendaftar</span>
                    <div class="font-outfit text-2xl font-extrabold text-slate-900">{{ number_format($stats['total']) }}</div>
                    <span class="text-[10px] text-slate-500">Semua pendaftaran</span>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 shadow-xs space-y-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 block">Menunggu Review</span>
                    <div class="font-outfit text-2xl font-extrabold text-amber-900">{{ number_format($stats['pending']) }}</div>
                    <span class="text-[10px] text-amber-700 font-semibold">Perlu diproses</span>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 shadow-xs space-y-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block">Sedang Direview</span>
                    <div class="font-outfit text-2xl font-extrabold text-blue-900">{{ number_format($stats['reviewed']) }}</div>
                    <span class="text-[10px] text-blue-700">Tahap validasi</span>
                </div>

                <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200 shadow-xs space-y-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-800 block">Diterima / Sah</span>
                    <div class="font-outfit text-2xl font-extrabold text-teal-900">{{ number_format($stats['approved']) }}</div>
                    <span class="text-[10px] text-teal-700">Representatif resmi</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs space-y-1 col-span-2 sm:col-span-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Provinsi Terwakili</span>
                    <div class="font-outfit text-2xl font-extrabold text-slate-800">{{ $stats['total_provinces'] }} <span class="text-xs font-normal text-slate-500">/ 38</span></div>
                    <span class="text-[10px] text-slate-500">Sebaran wilayah</span>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-4">
                <form method="GET" action="{{ route('admin.representatives.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    
                    <!-- Search Input -->
                    <div class="sm:col-span-5">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                🔍
                            </span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari nama, NBM, nomor registrasi, WA, kota..."
                                   class="form-input pl-9 text-xs py-2">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:col-span-3">
                        <select name="status" class="form-input text-xs py-2" onchange="this.form.submit()">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Sedang Direview</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <!-- Province Filter -->
                    <div class="sm:col-span-3">
                        <select name="province" class="form-input text-xs py-2" onchange="this.form.submit()">
                            <option value="">Semua Provinsi</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="sm:col-span-1 flex items-center gap-1.5">
                        <button type="submit" class="button-primary text-xs p-2 rounded-xl w-full justify-center" title="Terapkan Filter">
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'status', 'province']))
                            <a href="{{ route('admin.representatives.index') }}" class="button-secondary text-xs p-2 rounded-xl" title="Reset Filter">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Representatives Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                @if($representatives->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <span class="text-4xl">📭</span>
                        <h3 class="font-outfit font-bold text-base text-slate-800">Tidak Ada Data Pendaftaran</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Belum ada pendaftaran calon representatif yang sesuai dengan kriteria filter saat ini.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3.5 px-4">No. Registrasi &amp; Tanggal</th>
                                    <th class="py-3.5 px-4">Calon Representatif</th>
                                    <th class="py-3.5 px-4">Wilayah Penugasan</th>
                                    <th class="py-3.5 px-4">Pimpinan / Ortom</th>
                                    <th class="py-3.5 px-4">Dokumen</th>
                                    <th class="py-3.5 px-4 text-center">Status</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($representatives as $rep)
                                    @php $badge = $rep->status_badge; @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <!-- No. Registrasi & Tanggal -->
                                        <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                            <a href="{{ route('admin.representatives.show', $rep) }}" class="font-mono font-bold text-teal-800 hover:underline">
                                                {{ $rep->registration_number }}
                                            </a>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $rep->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>

                                        <!-- Calon Representatif -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900 text-sm">{{ $rep->name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                                                <span>NBM: <strong class="text-slate-700 font-mono">{{ $rep->nbm ?? '-' }}</strong></span>
                                                <span>&bull;</span>
                                                <span>Usia: {{ $rep->birth_date ? $rep->birth_date->age . ' th' : '-' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <a href="{{ $rep->whatsapp_link }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-700 hover:underline font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                    <span>💬</span> {{ $rep->whatsapp_number }}
                                                </a>
                                                @if($rep->instagram_username)
                                                    <a href="https://instagram.com/{{ $rep->instagram_username }}" target="_blank" class="text-[10px] text-slate-500 hover:text-slate-900 font-medium">
                                                        @<span>{{ $rep->instagram_username }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Wilayah Penugasan -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-800">{{ $rep->province_name }}</div>
                                            <div class="text-[11px] text-slate-600">{{ $rep->city_name }}</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                Kec. {{ $rep->district_name }}, {{ $rep->village_name }}
                                            </div>
                                            @if($rep->latitude && $rep->longitude)
                                                <a href="https://www.google.com/maps?q={{ $rep->latitude }},{{ $rep->longitude }}" target="_blank" class="text-[10px] text-teal-700 hover:underline font-semibold inline-flex items-center gap-0.5 mt-1">
                                                    <span>📍</span> Peta GPS ↗
                                                </a>
                                            @endif
                                        </td>

                                        <!-- Pimpinan / Ortom -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-medium text-slate-800 text-[11px] max-w-xs leading-relaxed">
                                                {{ $rep->muhammadiyah_active_leadership }}
                                            </div>
                                        </td>

                                        <!-- Dokumen -->
                                        <td class="py-3.5 px-4 align-top space-y-1">
                                            @if($rep->sk_pimpinan_document_path)
                                                <a href="{{ asset('storage/' . $rep->sk_pimpinan_document_path) }}" target="_blank" class="block text-[10px] font-bold text-teal-800 bg-teal-50 hover:bg-teal-100 px-2 py-1 rounded border border-teal-200 text-center">
                                                    📄 SK Pimpinan (PDF)
                                                </a>
                                            @endif
                                            @if($rep->ktam_document_path)
                                                <a href="{{ asset('storage/' . $rep->ktam_document_path) }}" target="_blank" class="block text-[10px] font-bold text-sky-800 bg-sky-50 hover:bg-sky-100 px-2 py-1 rounded border border-sky-200 text-center">
                                                    🖼️ KTAM / MASA
                                                </a>
                                            @endif
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-3.5 px-4 align-top text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                                {{ $badge['label'] }}
                                            </span>
                                            @if($rep->reviewer)
                                                <div class="text-[9px] text-slate-400 mt-1">
                                                    oleh {{ $rep->reviewer->name }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 align-top text-right space-y-1 whitespace-nowrap">
                                            <a href="{{ route('admin.representatives.show', $rep) }}" class="btn-action-primary block text-center">
                                                Detail &amp; Proses
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($representatives->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                            {{ $representatives->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
