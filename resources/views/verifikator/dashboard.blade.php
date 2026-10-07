<x-app-layout>
    <div class="py-8" x-data="{ 
        showDetailModal: false, 
        selectedCat: null,
        openCatDetail(cat) {
            this.selectedCat = cat;
            this.showDetailModal = true;
        },
        closeCatDetail() {
            this.showDetailModal = false;
            this.selectedCat = null;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
                </div>
            @endif

            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Verifikator KTAKuMu</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Selamat Datang, Verifikator KucingMu!
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Tinjau pendaftaran kucing, berkas identitas, rekam medis dokter hewan, serta verifikasi penerbitan Kartu Tanda Anggota KucingMu (KTAKuMu) resmi.
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('dashboard', ['ktam_status' => 'pending']) }}#cat-registry-table" class="button-primary text-xs font-bold px-4 py-2.5 shadow-sm bg-sky-700 hover:bg-sky-800">
                            <span>⏳</span> Antrian Verifikasi ({{ $stats['pending_verification_count'] }})
                        </a>
                        <a href="{{ route('dashboard') }}#cat-registry-table" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📋</span> Database Seluruh Kucing
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    ✅
                </div>
            </div>

            <!-- Stats Widgets: Status Verifikasi & KTAKuMu -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Statistik Verifikasi & Penerbitan KTAKuMu</h2>
                    <span class="text-[11px] text-slate-400">Pembaruan data otomatis</span>
                </div>
                <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                    
                    <!-- 1. Total Kucing -->
                    <a href="{{ route('dashboard', ['status' => 'all']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-sky-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span>Total Kucing</span>
                            <div class="flex items-center gap-1 text-[10px] font-bold">
                                <span class="text-emerald-700">🟢 {{ $stats['cats_alive_count'] }}</span>
                                <span class="text-slate-500">⚪ {{ $stats['cats_deceased_count'] }}</span>
                            </div>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['cats_count'] }}</span>
                            <span class="text-xs font-semibold text-sky-700">Kucing</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500">
                            Semua data terdaftar
                        </div>
                    </a>

                    <!-- 2. Menunggu Verifikasi Total -->
                    <a href="{{ route('dashboard', ['ktam_status' => 'pending']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-amber-400 hover:shadow-md bg-white {{ ($currentKtamStatus ?? '') === 'pending' ? 'ring-2 ring-amber-500 bg-amber-50/50' : '' }}">
                        <div class="flex items-center justify-between text-xs font-bold text-amber-700 uppercase tracking-wider">
                            <span>Antrian Verifikasi</span>
                            <span>⏳</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-amber-900">{{ $stats['pending_verification_count'] }}</span>
                            <span class="text-xs font-semibold text-amber-700">Kucing</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500">
                            Belum diterbitkan KTAM
                        </div>
                    </a>

                    <!-- 3. KTAKuMu Diterbitkan Resmi -->
                    <a href="{{ route('dashboard', ['ktam_status' => 'issued']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-sky-400 hover:shadow-md bg-white {{ ($currentKtamStatus ?? '') === 'issued' ? 'ring-2 ring-sky-500 bg-sky-50/50' : '' }}">
                        <div class="flex items-center justify-between text-xs font-bold text-sky-700 uppercase tracking-wider">
                            <span>KTAKuMu Terbit</span>
                            <span>🪪</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-sky-900">{{ $stats['ktam_count'] }}</span>
                            <span class="text-xs font-semibold text-sky-700">Kartu</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500">
                            Resmi terverifikasi
                        </div>
                    </a>

                    <!-- 4. Diverifikasi Oleh Saya -->
                    <div class="content-card p-4 bg-white border-slate-200">
                        <div class="flex items-center justify-between text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            <span>Oleh Akun Saya</span>
                            <span>🎖️</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-indigo-900">{{ $stats['my_verified_count'] }}</span>
                            <span class="text-xs font-semibold text-indigo-700">Kucing</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500">
                            Total verifikasi Anda
                        </div>
                    </div>

                </div>
            </div>

            <!-- Database Anggota KucingMu Table -->
            <div id="cat-registry-table" class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-mt-6">
                @php
                    $currentSort = $sort ?? 'created_at';
                    $currentDir = $direction ?? 'desc';
                    $currentStatus = $statusFilter ?? 'all';
                    $currentKtamStatus = $ktamStatusFilter ?? 'all';
                    
                    $makeSortUrl = function($col) use ($currentSort, $currentDir) {
                        $newDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                        return route('dashboard', array_merge(request()->except(['page']), [
                            'sort' => $col,
                            'direction' => $newDir,
                        ])) . '#cat-registry-table';
                    };

                    $getSortIndicator = function($col) use ($currentSort, $currentDir) {
                        if ($currentSort === $col) {
                            return $currentDir === 'asc' ? ' ↑' : ' ↓';
                        }
                        return '';
                    };
                @endphp

                <!-- Toolbar Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h2 class="font-outfit text-xl font-bold text-slate-900 leading-tight">Database Seluruh Kucing Terdaftar</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                    {{ $cats->total() }} Kucing Terdaftar
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Cari dan filter seluruh data pendaftaran kucing, status KTAKuMu, pratinjau, dan unduh kartu.</p>
                        </div>

                        @if(request('search') || (request('status') && request('status') !== 'all') || (request('ktam_status') && request('ktam_status') !== 'all') || request('sort'))
                            <a href="{{ route('dashboard') }}#cat-registry-table" class="button-secondary text-xs px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 self-start sm:self-auto shrink-0 inline-flex items-center gap-1 font-semibold">
                                <span>✕</span> Reset Semua Filter
                            </a>
                        @endif
                    </div>

                    <!-- Filter, Search, & Sort Bar -->
                    <form method="GET" action="{{ route('dashboard') }}#cat-registry-table" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-2">
                        <!-- Search Input (Span 4 on LG) -->
                        <div class="sm:col-span-2 lg:col-span-4 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   @input.debounce.500ms="$el.form.submit()"
                                   placeholder="Cari nama kucing, pemilik, NIAKuMu, ras, NBM..." 
                                   class="w-full text-xs pl-10 pr-8 py-2.5 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition placeholder:text-slate-400">
                            @if(request('search'))
                                <a href="{{ route('dashboard', array_merge(request()->except(['search', 'page']))) }}#cat-registry-table" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold" title="Hapus pencarian">✕</a>
                            @endif
                        </div>

                        <!-- Filter Status Kehidupan (Span 2 on LG) -->
                        <div class="lg:col-span-2">
                            <select id="verifikator_filter_status" name="status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition font-medium text-slate-700">
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>Semua Status Hidup</option>
                                <option value="alive" {{ $currentStatus === 'alive' ? 'selected' : '' }}>🟢 Hidup (Aktif)</option>
                                <option value="deceased" {{ $currentStatus === 'deceased' ? 'selected' : '' }}>⚪ Mati (Meninggal)</option>
                            </select>
                        </div>

                        <!-- Filter Status KTAKuMu (Span 3 on LG) -->
                        <div class="lg:col-span-3">
                            <select id="verifikator_filter_ktam_status" name="ktam_status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition font-medium text-slate-700">
                                <option value="all" {{ ($currentKtamStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status KTAKuMu</option>
                                <option value="pending" {{ ($currentKtamStatus ?? '') === 'pending' ? 'selected' : '' }}>⏳ Antrian Verifikasi ({{ $stats['pending_verification_count'] }})</option>
                                <option value="issued" {{ ($currentKtamStatus ?? '') === 'issued' ? 'selected' : '' }}>✓ KTAKuMu Terbit ({{ $stats['ktam_count'] }})</option>
                            </select>
                        </div>

                        <!-- Quick Sort Dropdown (Span 2 on LG) -->
                        <div class="lg:col-span-2">
                            <select id="verifikator_sort_select" name="sort_direction" onchange="
                                const val = this.value.split(':');
                                const sortInput = this.form.querySelector('input[name=sort]');
                                const dirInput = this.form.querySelector('input[name=direction]');
                                if (sortInput && dirInput) {
                                    sortInput.value = val[0];
                                    dirInput.value = val[1];
                                    this.form.submit();
                                }
                            " class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition font-medium text-slate-700">
                                <option value="created_at:desc" {{ ($currentSort == 'created_at' && $currentDir == 'desc') ? 'selected' : '' }}>Urutan: Terbaru</option>
                                <option value="created_at:asc" {{ ($currentSort == 'created_at' && $currentDir == 'asc') ? 'selected' : '' }}>Urutan: Terlama</option>
                                <option value="name:asc" {{ ($currentSort == 'name' && $currentDir == 'asc') ? 'selected' : '' }}>Nama Kucing (A - Z)</option>
                                <option value="name:desc" {{ ($currentSort == 'name' && $currentDir == 'desc') ? 'selected' : '' }}>Nama Kucing (Z - A)</option>
                                <option value="owner:asc" {{ ($currentSort == 'owner' && $currentDir == 'asc') ? 'selected' : '' }}>Pemilik (A - Z)</option>
                                <option value="owner:desc" {{ ($currentSort == 'owner' && $currentDir == 'desc') ? 'selected' : '' }}>Pemilik (Z - A)</option>
                                <option value="breed:asc" {{ ($currentSort == 'breed' && $currentDir == 'asc') ? 'selected' : '' }}>Ras Kucing (A - Z)</option>
                                <option value="date_of_birth:asc" {{ ($currentSort == 'date_of_birth' && $currentDir == 'asc') ? 'selected' : '' }}>Umur (Paling Tua)</option>
                                <option value="date_of_birth:desc" {{ ($currentSort == 'date_of_birth' && $currentDir == 'desc') ? 'selected' : '' }}>Umur (Paling Muda)</option>
                                <option value="unique_code:asc" {{ ($currentSort == 'unique_code' && $currentDir == 'asc') ? 'selected' : '' }}>Nomor NIAKuMu</option>
                                <option value="status:asc" {{ ($currentSort == 'status' && $currentDir == 'asc') ? 'selected' : '' }}>Status Hidup/Mati</option>
                            </select>
                            <input type="hidden" name="sort" value="{{ $currentSort }}">
                            <input type="hidden" name="direction" value="{{ $currentDir }}">
                        </div>

                        <!-- Cari Button (Span 1 on LG) -->
                        <div class="lg:col-span-1">
                            <button type="submit" class="w-full text-xs font-bold py-2.5 px-3 rounded-xl shadow-xs inline-flex items-center justify-center gap-1.5 bg-sky-700 hover:bg-sky-800 text-white transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>Cari</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-4">
                                    <a href="{{ $makeSortUrl('name') }}" class="hover:text-sky-700 flex items-center gap-1">
                                        Kucing {{ $getSortIndicator('name') }}
                                    </a>
                                </th>
                                <th class="py-3 px-4">
                                    <a href="{{ $makeSortUrl('owner') }}" class="hover:text-sky-700 flex items-center gap-1">
                                        Pemilik & NBM {{ $getSortIndicator('owner') }}
                                    </a>
                                </th>
                                <th class="py-3 px-4">
                                    <a href="{{ $makeSortUrl('breed') }}" class="hover:text-sky-700 flex items-center gap-1">
                                        Ras & Warna {{ $getSortIndicator('breed') }}
                                    </a>
                                </th>
                                <th class="py-3 px-4">
                                    <a href="{{ $makeSortUrl('gender') }}" class="hover:text-sky-700 flex items-center gap-1">
                                        Kelamin / Usia {{ $getSortIndicator('gender') }}
                                    </a>
                                </th>
                                <th class="py-3 px-4">
                                    <a href="{{ $makeSortUrl('status') }}" class="hover:text-sky-700 flex items-center gap-1">
                                        Status Kehidupan {{ $getSortIndicator('status') }}
                                    </a>
                                </th>
                                <th class="py-3 px-4">
                                    <a href="{{ $makeSortUrl('unique_code') }}" class="hover:text-sky-700 flex items-center gap-1">
                                        Status KTAKuMu {{ $getSortIndicator('unique_code') }}
                                    </a>
                                </th>
                                <th class="py-3 px-4 text-center">Rekam Medis</th>
                                <th class="py-3 px-4 text-right">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($cats as $cat)
                                <tr class="hover:bg-slate-50/70 transition">
                                    
                                    <!-- Kucing Info -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shrink-0 bg-slate-100">
                                                <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 text-xs truncate">{{ $cat->name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $cat->unique_code ?: 'Draf Belum Terbit' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Pemilik & NBM -->
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800">{{ $cat->owner->name }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? 'Bukan Anggota') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $cat->owner->phone ?: '-' }}</div>
                                    </td>

                                    <!-- Ras & Warna -->
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-800">{{ $cat->breed }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $cat->color ?: 'Warna tidak dicatat' }}</div>
                                    </td>

                                    <!-- Kelamin / Usia -->
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-800">{{ $cat->gender == 'male' ? '♂ Jantan' : '♀ Betina' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $cat->age }}</div>
                                    </td>

                                    <!-- Status Kehidupan -->
                                    <td class="py-3 px-4">
                                        @if($cat->status === 'deceased' || $cat->status === 'mati')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800">
                                                ⚪ Mati
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                🟢 Hidup
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status KTAKuMu -->
                                    <td class="py-3 px-4">
                                        @if($cat->unique_code && $cat->ktamCard)
                                            @php
                                                $verifiedAt = $cat->verified_at ?? $cat->ktamCard->verified_at;
                                                $verifier = $cat->verifier ?? $cat->ktamCard->verifier;
                                            @endphp
                                            <div class="space-y-0.5">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                                    <span>✓</span> Terbit
                                                </span>
                                                <div class="text-[10px] font-mono font-bold text-slate-800">{{ $cat->unique_code }}</div>
                                                @if($verifiedAt)
                                                    <div class="text-[10px] text-slate-500 flex items-center gap-1" title="Waktu Verifikasi">
                                                        <span>🕒</span>
                                                        <span>{{ $verifiedAt->format('d M Y, H:i') }}</span>
                                                    </div>
                                                @endif
                                                @if($verifier)
                                                    <div class="text-[9px] text-slate-400 truncate max-w-[140px]" title="Diverifikasi oleh {{ $verifier->name }}">
                                                        Oleh: {{ $verifier->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span>⏳</span> Antrian Verifikasi
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Rekam Medis Count -->
                                    <td class="py-3 px-4 text-center">
                                        @if($cat->medicalRecords->isNotEmpty())
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                                                🩺 {{ $cat->medicalRecords->count() }} Kali
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                            @if(!$cat->unique_code || !$cat->ktamCard)
                                                <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Apakah Anda yakin ingin memverifikasi dan menerbitkan kartu KTAKuMu untuk {{ $cat->name }}?')" 
                                                            class="button-primary py-1 px-2.5 text-[11px] font-bold bg-sky-700 hover:bg-sky-800 shadow-2xs">
                                                        <span>✓</span> Terbitkan
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('ktam.download', $cat->id) }}" 
                                                   class="btn-action-primary py-1 px-2 text-[11px] font-semibold" 
                                                   title="Unduh PDF KTAKuMu Resmi">
                                                    <span>⬇️</span> PDF
                                                </a>
                                            @endif

                                            <a href="{{ route('ktam.preview', $cat->id) }}" 
                                               target="_blank"
                                               class="btn-action-secondary py-1 px-2 text-[11px] font-semibold" 
                                               title="Lihat Pratinjau Kartu">
                                                <span>👁️</span> Pratinjau
                                            </a>

                                            <a href="{{ route('cat.edit', $cat->id) }}" 
                                               class="btn-action-secondary py-1 px-2 text-[11px] font-semibold" 
                                               title="Ubah Data Profil Kucing">
                                                <span>✏️</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12 text-slate-500">
                                        <span class="text-3xl block mb-2">🔍</span>
                                        <p class="font-bold text-slate-700">Tidak ada data kucing yang sesuai filter pencarian</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci pencarian atau reset filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($cats->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $cats->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    @if(request()->has('search') || request()->has('page') || request()->has('status') || request()->has('ktam_status') || request()->has('sort'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tableEl = document.getElementById('cat-registry-table');
                if (tableEl && window.location.hash === '#cat-registry-table') {
                    tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput && "{{ request('search') }}") {
                    searchInput.focus();
                    const val = searchInput.value;
                    searchInput.setSelectionRange(val.length, val.length);
                }
            });
        </script>
    @endif
</x-app-layout>
