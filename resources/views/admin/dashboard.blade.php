<x-app-layout>
    <div class="py-8" x-data="{ searchKtam: '', searchPending: '', showModal: false, activeCat: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Administrator Utama</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Selamat Datang, Admin KucingMu!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Verifikasi pendaftaran dan pemeriksaan kesehatan kucing, tinjau sampel biometrik dan galeri foto, lalu terbitkan Kartu Tanda Anggota KucingMu (KTAKuMu).
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('admin.users.index') }}" class="button-primary text-xs font-bold px-4 py-2.5 shadow-sm bg-teal-800 hover:bg-teal-900">
                            <span>👥</span> Kelola & Rekrut Pengguna
                        </a>
                        <a href="{{ route('export-data') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📊</span> Ekspor Data (CSV)
                        </a>
                        <a href="{{ route('superadmin.wilayah.index') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>🏛️</span> Master Wilayah
                        </a>
                        <a href="{{ route('superadmin.albums.index') }}" class="button-secondary text-xs font-bold px-4 py-2.5 shadow-sm">
                            <span>📸</span> Album Kegiatan
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    🏢
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                
                <div class="content-card bg-white border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kucing</span>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold">
                            <span class="text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200">🟢 {{ $stats['cats_alive_count'] }}</span>
                            <span class="text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">⚪ {{ $stats['cats_deceased_count'] }}</span>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['cats_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Ekor</span>
                    </div>
                </div>
                
                <div class="content-card bg-white border border-slate-200 p-4">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemeriksaan Dokter</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['records_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Selesai</span>
                    </div>
                </div>

                <div class="content-card bg-amber-50/70 border border-amber-200 p-4">
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Pending Verifikasi</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-3xl font-bold text-amber-900">{{ $stats['pending_verification_count'] }}</span>
                        <span class="text-xs font-semibold text-amber-700">Perlu KTAKuMu</span>
                    </div>
                </div>

                <div class="content-card bg-white border border-slate-200 p-4">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">KTAKuMu Terbit</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-3xl font-bold text-teal-800">{{ $stats['ktam_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Kartu</span>
                    </div>
                </div>

                <div class="content-card bg-white border border-slate-200 p-4">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Janji Temu</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['appointments_count'] }}</span>
                        <span class="text-xs font-semibold text-slate-500">Janji</span>
                    </div>
                </div>

            </div>

            <!-- Admin Verification Alert Section (Pending KTAKuMu Verification) -->
            <div class="content-card border-l-4 border-amber-500 bg-white" style="box-sizing: border-box; overflow: hidden; width: 100%;">
                <!-- Header Toolbar -->
                <div style="display: flex; flex-direction: column; gap: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                        <div style="flex: 1 1 300px; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <h2 class="font-outfit text-lg font-bold text-slate-900 leading-tight" style="margin: 0;">
                                    Permintaan Verifikasi & Penerbitan KTAKuMu
                                </h2>
                                @if($pendingVerificationCats->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        {{ $pendingVerificationCats->count() }} Menunggu
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Kucing di bawah ini telah diperiksa dokter dan menunggu peninjauan Admin untuk penerbitan Kartu KTAKuMu resmi.</p>
                        </div>

                        @if($pendingVerificationCats->count() > 0)
                            <!-- Clean Search Bar for Pending Cards -->
                            <div style="position: relative; width: 100%; max-width: 280px; flex-shrink: 0;">
                                <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 12px; pointer-events: none;">🔍</span>
                                <input type="text" 
                                       x-model="searchPending" 
                                       placeholder="Filter nama, NBM, pemilik..." 
                                       style="width: 100%; box-sizing: border-box; font-size: 12px; padding: 8px 30px 8px 30px; border-radius: 10px; border: 1px solid #cbd5e1; background-color: #f8fafc; outline: none; transition: border-color 0.15s ease-in-out;">
                                <button type="button" 
                                        x-show="searchPending" 
                                        @click="searchPending = ''" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 12px; font-weight: bold; background: none; border: none; cursor: pointer; display: none;">✕</button>
                            </div>
                        @endif
                    </div>
                </div>

                @if($pendingVerificationCats->isEmpty())
                    <div class="text-center py-8 text-slate-500 text-xs bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <span class="text-2xl block mb-1">🎉</span>
                        <p class="font-semibold text-slate-700">Tidak ada antrian verifikasi pending</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Semua pemeriksaan dokter telah diverifikasi dan kartu KTAM telah diterbitkan.</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" style="width: 100%; box-sizing: border-box;">
                        @foreach($pendingVerificationCats as $cat)
                            <div x-show="!searchPending || '{{ strtolower(addslashes($cat->name . ' ' . $cat->breed . ' ' . $cat->owner->name . ' ' . ($cat->owner->muhammadiyah_id ?? '') . ' ' . ($cat->owner->phone ?? ''))) }}'.includes(searchPending.toLowerCase().trim())"
                                 x-transition
                                 class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs space-y-3 flex flex-col justify-between hover:shadow-md hover:border-amber-300 transition"
                                 style="overflow: hidden; box-sizing: border-box;">
                                <div class="space-y-2.5">
                                    <div style="display: flex; align-items: flex-start; gap: 12px; overflow: hidden;">
                                        <div style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; max-width: 56px; max-height: 56px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; flex-shrink: 0; background-color: #f1f5f9;">
                                            <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                        </div>
                                        <div style="flex: 1 1 auto; min-width: 0; overflow: hidden;">
                                            <h3 class="font-bold text-slate-900 text-sm" style="margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $cat->name }}</h3>
                                            <p class="text-xs text-slate-500" style="margin: 2px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                            <p class="text-[11px] text-slate-400" style="margin: 2px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pemilik: <strong class="text-slate-700">{{ $cat->owner->name }}</strong></p>
                                            <p class="text-[10px] font-mono text-slate-400" style="margin: 1px 0 0 0;">NBM: <span class="font-semibold text-slate-600">{{ $cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? '-') }}</span></p>
                                        </div>
                                    </div>

                                    <!-- Biometric Badge -->
                                    <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
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
                                    </div>

                                    <!-- Last Doctor Record snippet -->
                                    @if($cat->medicalRecords->isNotEmpty())
                                        @php $lastRecord = $cat->medicalRecords->first(); @endphp
                                        <div class="bg-slate-50 p-2.5 rounded-lg text-xs space-y-1 text-slate-600 border border-slate-100">
                                            <div class="font-semibold text-slate-800 flex justify-between items-center">
                                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">{{ $lastRecord->vet->name ?? 'Dokter Hewan' }}</span>
                                                <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $lastRecord->created_at->format('d M Y') }}</span>
                                            </div>
                                            <p class="text-[11px]">Kondisi: <strong class="text-slate-800">{{ $lastRecord->general_condition }}</strong> ({{ $lastRecord->weight }}kg, {{ $lastRecord->temperature }}°C)</p>
                                        </div>
                                    @else
                                        <div class="bg-slate-50 p-2 rounded-lg text-xs text-slate-500 border border-slate-100 flex items-center gap-1.5">
                                            <span class="text-[11px] text-slate-400">Siap verifikasi langsung Admin</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center gap-2" style="margin-top: 8px;">
                                    <a href="{{ route('cat.edit', $cat->id) }}" class="btn-action-secondary py-2 px-2.5 text-center text-xs font-semibold">
                                        <span>✏️</span> Ubah
                                    </a>
                                    <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memverifikasi dan menerbitkan kartu KTAKuMu untuk {{ $cat->name }}?')" class="btn-action-primary w-full py-2 text-center text-xs font-semibold">
                                            <span>✓</span> Terbitkan KTAKuMu
                                        </button>
                                    </form>
                                    <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-danger py-2 px-2.5 text-xs font-semibold" title="Hapus Kucing">
                                            <span>🗑</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Main Full-Width Cat Registry Datatable Card -->
            <div id="cat-registry-table" class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-mt-6">
                @php
                    $currentSort = $sort ?? 'created_at';
                    $currentDir = $direction ?? 'desc';
                    $currentStatus = $statusFilter ?? 'all';
                    
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
                                <h2 class="font-outfit text-xl font-bold text-slate-900 leading-tight">Database Anggota KucingMu</h2>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                    {{ $cats->total() }} Kucing Terdaftar
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Kelola data identitas, status kehidupan, kartu KTAKuMu, serta aksi verifikasi & penghapusan.</p>
                        </div>

                        @if(request('search') || request('status') || request('sort'))
                            <a href="{{ route('dashboard') }}#cat-registry-table" class="button-secondary text-xs px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 self-start sm:self-auto shrink-0 inline-flex items-center gap-1">
                                <span>✕</span> Reset Semua Filter
                            </a>
                        @endif
                    </div>

                    <!-- Filter, Search, & Sort Bar -->
                    <form method="GET" action="{{ route('dashboard') }}#cat-registry-table" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-2">
                        <!-- Search Input (Span 5 on LG) -->
                        <div class="sm:col-span-2 lg:col-span-5 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari nama kucing, pemilik, NIAKuMu, ras, NBM..." 
                                   class="w-full text-xs pl-10 pr-8 py-2.5 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition placeholder:text-slate-400">
                            @if(request('search'))
                                <a href="{{ route('dashboard', array_merge(request()->except(['search', 'page']))) }}#cat-registry-table" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold" title="Hapus pencarian">✕</a>
                            @endif
                        </div>

                        <!-- Filter Status Dropdown (Span 3 on LG) -->
                        <div class="lg:col-span-3">
                            <select id="admin_filter_status" name="status" onchange="this.form.submit()" class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition font-medium text-slate-700">
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>Semua Status Kehidupan</option>
                                <option value="alive" {{ $currentStatus === 'alive' ? 'selected' : '' }}>🟢 Hidup (Aktif)</option>
                                <option value="deceased" {{ $currentStatus === 'deceased' ? 'selected' : '' }}>⚪ Mati (Meninggal)</option>
                            </select>
                        </div>

                        <!-- Quick Sort Dropdown (Span 3 on LG) -->
                        <div class="lg:col-span-3">
                            <select id="admin_sort_select" name="sort_direction" onchange="
                                const val = this.value.split(':');
                                const sortInput = this.form.querySelector('input[name=sort]');
                                const dirInput = this.form.querySelector('input[name=direction]');
                                if (sortInput && dirInput) {
                                    sortInput.value = val[0];
                                    dirInput.value = val[1];
                                    this.form.submit();
                                }
                            " class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-300 bg-slate-50/70 focus:bg-white focus:border-teal-600 focus:ring-4 focus:ring-teal-100 transition font-medium text-slate-700">
                                <option value="created_at:desc" {{ ($currentSort == 'created_at' && $currentDir == 'desc') ? 'selected' : '' }}>Urutan: Terbaru Terdaftar</option>
                                <option value="created_at:asc" {{ ($currentSort == 'created_at' && $currentDir == 'asc') ? 'selected' : '' }}>Urutan: Terlama Terdaftar</option>
                                <option value="name:asc" {{ ($currentSort == 'name' && $currentDir == 'asc') ? 'selected' : '' }}>Nama Kucing (A - Z)</option>
                                <option value="name:desc" {{ ($currentSort == 'name' && $currentDir == 'desc') ? 'selected' : '' }}>Nama Kucing (Z - A)</option>
                                <option value="owner:asc" {{ ($currentSort == 'owner' && $currentDir == 'asc') ? 'selected' : '' }}>Pemilik (A - Z)</option>
                                <option value="owner:desc" {{ ($currentSort == 'owner' && $currentDir == 'desc') ? 'selected' : '' }}>Pemilik (Z - A)</option>
                                <option value="breed:asc" {{ ($currentSort == 'breed' && $currentDir == 'asc') ? 'selected' : '' }}>Ras Kucing (A - Z)</option>
                                <option value="date_of_birth:asc" {{ ($currentSort == 'date_of_birth' && $currentDir == 'asc') ? 'selected' : '' }}>Umur (Paling Tua)</option>
                                <option value="date_of_birth:desc" {{ ($currentSort == 'date_of_birth' && $currentDir == 'desc') ? 'selected' : '' }}>Umur (Paling Muda)</option>
                                <option value="unique_code:asc" {{ ($currentSort == 'unique_code' && $currentDir == 'asc') ? 'selected' : '' }}>Nomor NIAKuMu (Asc)</option>
                                <option value="status:asc" {{ ($currentSort == 'status' && $currentDir == 'asc') ? 'selected' : '' }}>Status Hidup / Mati</option>
                            </select>
                            <input type="hidden" name="sort" value="{{ $currentSort }}">
                            <input type="hidden" name="direction" value="{{ $currentDir }}">
                        </div>

                        <!-- Apply Button (Span 1 on LG) -->
                        <div class="lg:col-span-1">
                            <button type="submit" class="w-full button-primary text-xs font-bold py-2.5 px-3 rounded-xl shadow-xs">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Datatable Content -->
                @if($cats->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">🐱</div>
                        <h3 class="text-sm font-bold text-slate-800">Tidak ada data kucing yang sesuai</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan coba ubah kata kunci pencarian atau ubah filter status di atas.</p>
                        <a href="{{ route('dashboard') }}#cat-registry-table" class="inline-flex items-center gap-1.5 mt-4 text-xs font-bold text-teal-700 hover:text-teal-900 underline">
                            Tampilkan Semua Data Kucing
                        </a>
                    </div>
                @else
                    <!-- DESKTOP / TABLET VIEW: Modern Datatable -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse" aria-label="Database Anggota Kucing">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-600 font-bold bg-slate-50/80 select-none uppercase tracking-wider text-[11px]">
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan Nama">
                                            Identitas Kucing
                                             <span class="text-teal-700 font-bold">{{ $getSortIndicator('name') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('owner') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan Pemilik">
                                            Pemilik & NBM
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('owner') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('breed') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan Ras">
                                            Ras & Biometrik
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('breed') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('unique_code') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan berdasarkan NIAKuMu">
                                            Nomor NIAKuMu
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('unique_code') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">
                                        <a href="{{ $makeSortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition" title="Urutkan Status Hidup/Mati">
                                            Status Hidup
                                            <span class="text-teal-700 font-bold">{{ $getSortIndicator('status') }}</span>
                                        </a>
                                    </th>
                                    <th class="py-3.5 px-4">Status KTAKuMu</th>
                                    <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi / Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-normal">
                                @foreach($cats as $cat)
                                    <tr class="hover:bg-teal-50/30 transition-colors {{ $cat->isDeceased() ? 'bg-slate-50/60 text-slate-500' : '' }}">
                                        <!-- Foto & Nama Kucing -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-11 h-11 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shrink-0 shadow-2xs">
                                                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover {{ $cat->isDeceased() ? 'grayscale opacity-75' : '' }}">
                                                    @if($cat->isDeceased())
                                                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                                            <span class="text-[8px] font-bold text-white uppercase bg-black/60 px-1 rounded">Mati</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 text-sm leading-snug flex items-center gap-1.5 truncate">
                                                        <span>{{ $cat->name }}</span>
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                                        {{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }} &bull; <span class="font-medium text-slate-600">{{ $cat->age_text }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Pemilik & NBM -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-1.5 justify-between">
                                                <div class="font-semibold text-slate-900 text-xs">{{ $cat->owner ? $cat->owner->name : '-' }}</div>
                                                @if($cat->owner && Auth::id() !== $cat->owner->id)
                                                    <form action="{{ route('admin.users.impersonate', $cat->owner->id) }}" method="POST" class="inline" onsubmit="return confirm('Masuk sebagai pemilik {{ $cat->owner->name }}?')">
                                                        @csrf
                                                        <button type="submit" class="text-[10px] bg-slate-100 hover:bg-teal-100 hover:text-teal-900 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200 font-bold transition flex items-center gap-0.5" title="Login Sebagai Pemilik (Impersonate)">
                                                            <span>🎭</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                NBM: <span class="text-slate-700 font-medium">{{ $cat->owner ? ($cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? '-')) : '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- Ras & Biometrik -->
                                        <td class="py-3.5 px-4">
                                            <div class="text-xs font-semibold text-slate-800">{{ $cat->breed }}</div>
                                            @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-teal-50 text-teal-800 border border-teal-200 uppercase whitespace-nowrap mt-1 inline-flex items-center gap-1">
                                                    <span>🐾</span> {{ $cat->biometric_type }}
                                                </span>
                                            @else
                                                <span class="text-[11px] text-slate-400">Standar Foto</span>
                                            @endif
                                        </td>

                                        <!-- Nomor KTAM -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-mono text-xs font-bold text-teal-900">{{ $cat->formatted_unique_code }}</div>
                                            <div class="text-[10px] text-slate-500 font-sans mt-0.5">
                                                {{ $cat->wilayah ? $cat->wilayah->nama : 'PWM DIY' }} &bull; <span class="font-mono">{{ $cat->ktamCard ? $cat->ktamCard->ktam_number : 'Draf' }}</span>
                                            </div>
                                        </td>

                                        <!-- Status Hidup / Mati -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                @if($cat->isAlive())
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1" title="Kucing Hidup (Aktif)">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Hidup
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300 inline-flex items-center gap-1" title="Kucing Mati / Meninggal">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Mati
                                                    </span>
                                                @endif

                                                <!-- Quick Toggle Status Button -->
                                                <form action="{{ route('cat.toggle-status', $cat->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Ubah status kucing {{ $cat->name }} menjadi {{ $cat->isAlive() ? 'MATI (Meninggal)' : 'HIDUP (Aktif)' }}?')"
                                                            class="text-xs text-slate-400 hover:text-teal-700 hover:bg-slate-100 p-1.5 rounded-lg transition" 
                                                            title="Ubah status hidup/mati">
                                                        🔄
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                        <!-- Status Penerbitan KTAKuMu -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($cat->ktamCard)
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200 inline-flex items-center gap-1">
                                                    <span>✓</span> KTAKuMu Terbit
                                                </span>
                                            @elseif($cat->medicalRecords->isNotEmpty())
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-300 inline-flex items-center gap-1">
                                                    <span>⏳</span> Perlu Verifikasi
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1">
                                                    <span>•</span> Terdaftar
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Aksi / Tindakan -->
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                <a href="{{ route('cat.edit', $cat->id) }}" title="Update Foto, Identitas & Biometrik" class="btn-action-secondary py-1.5 px-2.5 text-xs font-semibold">
                                                    <span>✏️</span> Ubah
                                                </a>
                                                
                                                @if($cat->ktamCard)
                                                    <a href="{{ route('ktam.download', $cat->id) }}" class="btn-action-success py-1.5 px-2.5 text-xs font-bold" title="Unduh Kartu PDF">
                                                        <span>📄</span> PDF
                                                    </a>
                                                @else
                                                    <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Verifikasi & terbitkan kartu KTAKuMu untuk {{ $cat->name }}?')" class="btn-action-primary py-1.5 px-2.5 text-xs font-bold" title="Terbitkan Kartu KTAKuMu">
                                                            <span>✓</span> Verifikasi
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs font-semibold" title="Hapus Kucing">
                                                        <span>🗑</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- MOBILE VIEW: Interactive Responsive Cards -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($cats as $cat)
                            <div class="p-4 space-y-3.5 {{ $cat->isDeceased() ? 'bg-slate-50/50' : 'bg-white' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs shrink-0 {{ $cat->isDeceased() ? 'grayscale opacity-75' : '' }}">
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $cat->name }}</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                            <p class="text-[11px] text-slate-400">Umur: {{ $cat->age_text }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-1 shrink-0">
                                        @if($cat->isAlive())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                🟢 Hidup
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                                ⚪ Mati
                                            </span>
                                        @endif

                                        @if($cat->ktamCard)
                                            <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                                KTAKuMu Terbit
                                            </span>
                                        @elseif($cat->medicalRecords->isNotEmpty())
                                            <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                Perlu Verifikasi
                                            </span>
                                        @else
                                            <span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                Terdaftar
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-3 text-xs space-y-1.5 border border-slate-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500">Pemilik:</span>
                                        <span class="font-semibold text-slate-800">{{ $cat->owner ? $cat->owner->name : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500">NBM:</span>
                                        <span class="font-mono text-slate-700">{{ $cat->owner ? ($cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? '-')) : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500">Nomor NIAKuMu:</span>
                                        <span class="font-mono font-bold text-teal-800">{{ $cat->formatted_unique_code }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons on Mobile -->
                                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                    <div class="flex items-center gap-1.5">
                                        <!-- Toggle Status -->
                                        <form action="{{ route('cat.toggle-status', $cat->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Ubah status kucing {{ $cat->name }} menjadi {{ $cat->isAlive() ? 'MATI' : 'HIDUP' }}?')"
                                                    class="button-secondary text-xs py-1.5 px-2.5 rounded-lg text-slate-600" 
                                                    title="Ubah Status Hidup/Mati">
                                                🔄 Ubah Status
                                            </button>
                                        </form>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('cat.edit', $cat->id) }}" class="btn-action-secondary py-1.5 px-2.5 text-xs font-semibold">
                                            ✏️ Ubah
                                        </a>

                                        @if($cat->ktamCard)
                                            <a href="{{ route('ktam.download', $cat->id) }}" class="btn-action-success py-1.5 px-2.5 text-xs font-bold">
                                                📄 PDF
                                            </a>
                                        @else
                                            <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Verifikasi KTAKuMu untuk {{ $cat->name }}?')" class="btn-action-primary py-1.5 px-2.5 text-xs font-bold">
                                                    ✓ Verifikasi
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs font-semibold" title="Hapus Kucing">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50">
                        {{ $cats->fragment('cat-registry-table')->links() }}
                    </div>
                @endif
            </div>

            <!-- Bottom Utilities Grid: KTAKuMu Verification Simulator & Quick Appointments -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- KTAKuMu Verification Card -->
                <div class="content-card bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div>
                            <h2 class="font-outfit text-base font-bold text-slate-900">Periksa Keaslian KTAKuMu</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Uji verifikasi QR atau lookup kartu KTAKuMu publik.</p>
                        </div>
                        <span class="text-2xl">🔍</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="search-ktam-input" class="form-label text-xs font-semibold text-slate-700">Nomor Kartu KTAKuMu / NIAKuMu</label>
                            <input id="search-ktam-input" type="text" x-model="searchKtam" placeholder="Contoh: 34.kcg.0001" class="form-input text-xs font-mono font-bold">
                        </div>
                        
                        <button type="button" @click="if(searchKtam.trim()) { window.location.href = `/verify/${searchKtam.trim()}` } else { alert('Silakan masukkan nomor NIAKuMu terlebih dahulu.') }" class="w-full button-primary flex justify-center py-2.5 text-xs font-bold shadow-xs">
                            Cek Status Validitas KTAKuMu
                        </button>
                    </div>

                    @if($stats['ktam_count'] > 0)
                        <div class="mt-4 border-t border-slate-100 pt-3">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilihan Cepat Nomor NIAKuMu:</span>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($cats->filter(fn($c) => $c->ktamCard)->take(4) as $c)
                                    <button type="button" @click="searchKtam = '{{ $c->ktamCard->ktam_number }}'" class="text-xs font-mono bg-slate-50 hover:bg-teal-50 hover:text-teal-800 px-2.5 py-1 rounded-lg border border-slate-200 font-bold transition">
                                        {{ $c->ktamCard->ktam_number }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Recent Appointments & Quick Actions -->
                <div class="content-card bg-white border border-slate-200 p-6 rounded-2xl shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="font-outfit text-base font-bold text-slate-900">Janji Temu Terkini</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Antrian jadwal pemeriksaan kucing.</p>
                        </div>
                        <span class="text-2xl">📅</span>
                    </div>

                    @if($appointments->isEmpty())
                        <div class="text-center py-6 text-slate-500 text-xs bg-slate-50 rounded-xl border border-slate-100">
                            Belum ada jadwal janji temu terbaru.
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach($appointments as $appt)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-900 font-bold flex items-center justify-center text-xs shrink-0">
                                            🐱
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $appt->cat ? $appt->cat->name : 'Kucing' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $appt->cat && $appt->cat->owner ? $appt->cat->owner->name : '-' }} &bull; {{ $appt->time_slot }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono text-[11px] font-bold text-slate-700 block">{{ $appt->date ? $appt->date->format('d M Y') : '-' }}</span>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $appt->status === 'completed' ? 'bg-teal-100 text-teal-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $appt->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @if(request()->has('search') || request()->has('page'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tableEl = document.getElementById('cat-registry-table');
                if (tableEl) {
                    tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        </script>
    @endif
</x-app-layout>
