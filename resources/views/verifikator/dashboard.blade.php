<x-app-layout>
    <div class="py-8" x-data="{ 
        searchPending: '', 
        filterPendingType: 'all', 
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
                        <a href="#pending-queue" class="button-primary text-xs font-bold px-4 py-2.5 shadow-sm bg-sky-700 hover:bg-sky-800">
                            <span>⏳</span> Antrian Verifikasi ({{ $stats['pending_verification_count'] }})
                        </a>
                        <a href="#cat-registry-table" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
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
                <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5">
                    
                    <!-- 1. Menunggu Verifikasi Total -->
                    <a href="{{ route('dashboard', ['ktam_status' => 'pending']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-amber-400 hover:shadow-md bg-white">
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

                    <!-- 2. Ada Rekam Medis (Prioritas) -->
                    <a href="{{ route('dashboard', ['ktam_status' => 'need_verification']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-emerald-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-emerald-700 uppercase tracking-wider">
                            <span>Ada Rekam Medis</span>
                            <span>🩺</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-emerald-900">{{ $stats['need_verification_count'] }}</span>
                            <span class="text-xs font-semibold text-emerald-700">Kucing</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500">
                            Sudah diperiksa dokter
                        </div>
                    </a>

                    <!-- 3. Belum Ada Rekam Medis -->
                    <a href="{{ route('dashboard', ['ktam_status' => 'unverified']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-slate-400 hover:shadow-md bg-white">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600 uppercase tracking-wider">
                            <span>Belum Ada Medis</span>
                            <span>📋</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-outfit text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['unverified_count'] }}</span>
                            <span class="text-xs font-semibold text-slate-600">Kucing</span>
                        </div>
                        <div class="mt-1 text-[11px] text-slate-500">
                            Pendaftaran mandiri
                        </div>
                    </a>

                    <!-- 4. KTAKuMu Diterbitkan Resmi -->
                    <a href="{{ route('dashboard', ['ktam_status' => 'issued']) }}#cat-registry-table" 
                       class="content-card p-4 transition hover:border-sky-400 hover:shadow-md bg-white">
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

                    <!-- 5. Diverifikasi Oleh Saya -->
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

            <!-- Antrian Permintaan Verifikasi & Penerbitan KTAKuMu -->
            <div id="pending-queue" class="content-card bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 scroll-mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="font-outfit text-lg sm:text-xl font-bold text-slate-900 leading-tight">
                                Antrian Verifikasi KTAKuMu
                            </h2>
                            @if($pendingVerificationCats->count() > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                    {{ $pendingVerificationCats->count() }} Menunggu
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Tinjau hasil pemeriksaan dokter hewan dan data kucing untuk menerbitkan kartu KTAKuMu resmi.
                        </p>
                        
                        <!-- Quick Filter Pills for Pending Cards -->
                        @if($pendingVerificationCats->count() > 0)
                            <div class="flex items-center gap-1.5 flex-wrap mt-3">
                                <button type="button" 
                                        @click="filterPendingType = 'all'" 
                                        :class="filterPendingType === 'all' ? 'bg-amber-800 text-white font-bold shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" 
                                        class="text-xs px-3 py-1.5 rounded-xl transition cursor-pointer">
                                    Semua ({{ $pendingVerificationCats->count() }})
                                </button>
                                <button type="button" 
                                        @click="filterPendingType = 'medical'" 
                                        :class="filterPendingType === 'medical' ? 'bg-emerald-700 text-white font-bold shadow-2xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100'" 
                                        class="text-xs px-3 py-1.5 rounded-xl transition cursor-pointer">
                                    🩺 Ada Rekam Medis ({{ $pendingVerificationCats->filter(fn($c) => $c->medicalRecords->isNotEmpty())->count() }})
                                </button>
                                <button type="button" 
                                        @click="filterPendingType = 'non_medical'" 
                                        :class="filterPendingType === 'non_medical' ? 'bg-slate-700 text-white font-bold shadow-2xs' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200'" 
                                        class="text-xs px-3 py-1.5 rounded-xl transition cursor-pointer">
                                    📋 Belum Ada Medis ({{ $pendingVerificationCats->filter(fn($c) => $c->medicalRecords->isEmpty())->count() }})
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($pendingVerificationCats->count() > 0)
                        <!-- Search Bar for Pending Cards -->
                        <div class="relative w-full sm:w-72 shrink-0">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none">🔍</span>
                            <input type="text" 
                                   x-model="searchPending" 
                                   placeholder="Cari nama, NBM, pemilik..." 
                                   class="w-full text-xs pl-9 pr-8 py-2 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition outline-none">
                            <button type="button" 
                                    x-show="searchPending" 
                                    @click="searchPending = ''" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold cursor-pointer">✕</button>
                        </div>
                    @endif
                </div>

                @if($pendingVerificationCats->isEmpty())
                    <div class="text-center py-10 text-slate-500 text-xs bg-slate-50/80 rounded-2xl border border-dashed border-slate-200">
                        <span class="text-3xl block mb-2">🎉</span>
                        <p class="font-bold text-slate-800 text-sm">Tidak ada antrian verifikasi yang pending</p>
                        <p class="text-xs text-slate-400 mt-1">Semua pemeriksaan dokter telah diverifikasi dan kartu KTAKuMu telah resmi diterbitkan.</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($pendingVerificationCats as $cat)
                            <div x-show="(!searchPending || '{{ strtolower(addslashes($cat->name . ' ' . $cat->breed . ' ' . $cat->owner->name . ' ' . ($cat->owner->muhammadiyah_id ?? '') . ' ' . ($cat->owner->phone ?? ''))) }}'.includes(searchPending.toLowerCase().trim())) && (filterPendingType === 'all' || (filterPendingType === 'medical' && {{ $cat->medicalRecords->isNotEmpty() ? 'true' : 'false' }}) || (filterPendingType === 'non_medical' && {{ $cat->medicalRecords->isEmpty() ? 'true' : 'false' }}))"
                                 x-transition
                                 class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs space-y-3.5 flex flex-col justify-between hover:shadow-md hover:border-sky-300 transition">
                                
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-14 h-14 min-w-[56px] min-h-[56px] rounded-xl overflow-hidden border border-slate-200 shrink-0 bg-slate-100">
                                            <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="font-bold text-slate-900 text-sm truncate leading-tight">{{ $cat->name }}</h3>
                                            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">Pemilik: <strong class="text-slate-700">{{ $cat->owner->name }}</strong></p>
                                            <p class="text-[10px] font-mono text-slate-400 mt-0.5">NBM: <span class="font-semibold text-slate-600">{{ $cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? 'Bukan Anggota NBM') }}</span></p>
                                        </div>
                                    </div>

                                    <!-- Badges: Biometrik & Foto -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200 uppercase">
                                                Biometrik {{ strtoupper($cat->biometric_type) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600">
                                                Biometrik Standar
                                            </span>
                                        @endif

                                        @if($cat->photos->count() > 1)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-purple-800 border border-purple-200">
                                                {{ $cat->photos->count() }} Foto
                                            </span>
                                        @endif

                                        @if($cat->wilayah)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600">
                                                🗺️ {{ $cat->wilayah->wilayah_name }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Doctor Record Snippet (if available) -->
                                    @if($cat->medicalRecords->isNotEmpty())
                                        @php $lastRecord = $cat->medicalRecords->first(); @endphp
                                        <div class="bg-emerald-50/60 p-2.5 rounded-xl text-xs space-y-1 text-slate-700 border border-emerald-100">
                                            <div class="font-semibold text-emerald-950 flex justify-between items-center">
                                                <span class="truncate max-w-[150px]">🩺 {{ $lastRecord->vet->name ?? 'Dokter Hewan' }}</span>
                                                <span class="text-[10px] text-emerald-700 font-mono">{{ $lastRecord->created_at->format('d M Y') }}</span>
                                            </div>
                                            <p class="text-[11px] text-slate-600">
                                                Kondisi: <strong class="text-slate-800">{{ $lastRecord->general_condition }}</strong>
                                                @if($lastRecord->weight) &bull; {{ $lastRecord->weight }}kg @endif
                                                @if($lastRecord->temperature) &bull; {{ $lastRecord->temperature }}°C @endif
                                            </p>
                                        </div>
                                    @else
                                        <div class="bg-slate-50 p-2.5 rounded-xl text-xs text-slate-500 border border-slate-100 flex items-center gap-1.5">
                                            <span class="text-slate-400">ℹ️</span>
                                            <span class="text-[11px] text-slate-500">Pendaftaran mandiri (siap verifikasi langsung)</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                                    <a href="{{ route('ktam.preview', $cat->id) }}" 
                                       target="_blank"
                                       class="btn-action-secondary py-2 px-2.5 text-center text-xs font-semibold"
                                       title="Lihat Pratinjau Draf Kartu KTAM">
                                        <span>👁️</span> Pratinjau
                                    </a>

                                    <a href="{{ route('cat.edit', $cat->id) }}" 
                                       class="btn-action-secondary py-2 px-2.5 text-center text-xs font-semibold"
                                       title="Ubah Data Kucing">
                                        <span>✏️</span> Ubah
                                    </a>

                                    <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin memverifikasi dan menerbitkan kartu KTAKuMu resmi untuk {{ $cat->name }}?')" 
                                                class="button-primary w-full py-2 text-center text-xs font-bold bg-sky-700 hover:bg-sky-800 flex items-center justify-center gap-1 shadow-2xs">
                                            <span>✓</span> Terbitkan KTAKuMu
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
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
                        <!-- Search Input -->
                        <div class="sm:col-span-2 lg:col-span-5 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari nama kucing, pemilik, NIAKuMu, ras, NBM..." 
                                   class="w-full text-xs pl-10 pr-8 py-2.5 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition placeholder:text-slate-400">
                            @if(request('search'))
                                <a href="{{ route('dashboard', array_merge(request()->except(['search', 'page']))) }}#cat-registry-table" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold" title="Hapus pencarian">✕</a>
                            @endif
                        </div>

                        <!-- Filter Status Kehidupan -->
                        <div class="lg:col-span-3">
                            <select name="status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition font-medium text-slate-700">
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>Semua Status Hidup</option>
                                <option value="alive" {{ $currentStatus === 'alive' ? 'selected' : '' }}>🟢 Hidup (Aktif)</option>
                                <option value="deceased" {{ $currentStatus === 'deceased' ? 'selected' : '' }}>⚪ Mati (Meninggal)</option>
                            </select>
                        </div>

                        <!-- Filter Status KTAKuMu -->
                        <div class="lg:col-span-4">
                            <select name="ktam_status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-sky-600 focus:ring-2 focus:ring-sky-100 transition font-medium text-slate-700">
                                <option value="all" {{ ($currentKtamStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status KTAKuMu</option>
                                <option value="issued" {{ ($currentKtamStatus ?? '') === 'issued' ? 'selected' : '' }}>✓ KTAKuMu Terbit ({{ $stats['ktam_count'] }})</option>
                                <option value="need_verification" {{ ($currentKtamStatus ?? '') === 'need_verification' ? 'selected' : '' }}>⏳ Perlu Verifikasi ({{ $stats['need_verification_count'] }})</option>
                                <option value="unverified" {{ ($currentKtamStatus ?? '') === 'unverified' ? 'selected' : '' }}>• Belum Verifikasi ({{ $stats['unverified_count'] }})</option>
                            </select>
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
                                                <span>⏳</span> Perlu Verifikasi
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
</x-app-layout>
