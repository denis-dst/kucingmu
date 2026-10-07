<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="eyebrow">Wadah Resmi Komunitas KucingMu</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1 flex items-center gap-2">
                    <span>💖</span> Adopsi Aku
                </h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @auth
                    @if(Auth::user()->hasRole('member'))
                        <a href="{{ route('dashboard') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                            <span>🐱</span> Buka Kucing Saya untuk Adopsi
                        </a>
                    @endif
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.adoptions.applications') }}" class="button-primary text-xs font-bold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                            <span>📋</span> Kelola Permohonan Adopsi
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-900 via-teal-800 to-emerald-900 text-white p-6 sm:p-8 shadow-md border border-teal-700/60">
                <div class="relative z-10 max-w-3xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-xs border border-white/20 text-xs font-semibold text-teal-200">
                        <span>🐾</span> Etalase Resmi Open Adopsi KucingMu
                    </div>
                    <h2 class="font-outfit text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                        Temukan Sahabat Berbulu & Berikan Rumah Penuh Cinta
                    </h2>
                    <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
                        Wadah perantara adopsi amanah untuk anabul yang didaftarkan oleh member serta kucing hasil sensus & penyelamatan relawan Muhammadiyah. Semua komunikasi dimediasi aman oleh admin demi privasi bersama.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 text-xs bg-teal-950/40 px-3 py-1.5 rounded-xl border border-teal-600/40 text-teal-200">
                            <span>🛡️</span> Kontak Pemilik Dimediasi Admin
                        </div>
                        <div class="flex items-center gap-2 text-xs bg-teal-950/40 px-3 py-1.5 rounded-xl border border-teal-600/40 text-teal-200">
                            <span>🩺</span> Dilengkapi Riwayat Kesehatan
                        </div>
                        <div class="flex items-center gap-2 text-xs bg-teal-950/40 px-3 py-1.5 rounded-xl border border-teal-600/40 text-teal-200">
                            <span>🆓</span> Bebas Biaya Adopsi
                        </div>
                    </div>
                </div>

                <!-- Background Illustration / Watermark -->
                <div class="absolute -right-6 -bottom-8 text-9xl opacity-10 select-none pointer-events-none">
                    🐱
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('adoption.index', ['status' => 'available', 'source' => 'all']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-emerald-50/50 border border-emerald-200 block {{ $statusFilter === 'available' && $sourceFilter === 'all' ? 'ring-2 ring-emerald-500/30' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Siap Diadopsi</span>
                        <span class="text-sm">🟢</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-emerald-900">{{ number_format($stats['total_available']) }}</span>
                        <span class="text-xs font-semibold text-emerald-700">Ekor</span>
                    </div>
                </a>

                <a href="{{ route('adoption.index', ['source' => 'member']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-white border border-slate-200 block {{ $sourceFilter === 'member' ? 'ring-2 ring-teal-500/30' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kucing dari Member</span>
                        <span class="text-sm">🐱</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ number_format($stats['member_cats_count']) }}</span>
                        <span class="text-xs font-semibold text-slate-500">Ekor</span>
                    </div>
                </a>

                <a href="{{ route('adoption.index', ['source' => 'rescue']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-white border border-slate-200 block {{ $sourceFilter === 'rescue' ? 'ring-2 ring-teal-500/30' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Hasil Sensus / Rescue</span>
                        <span class="text-sm">📋</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-indigo-900">{{ number_format($stats['rescue_cats_count']) }}</span>
                        <span class="text-xs font-semibold text-indigo-700">Ekor</span>
                    </div>
                </a>

                <a href="{{ route('adoption.index', ['status' => 'adopted']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-purple-50/50 border border-purple-200 block {{ $statusFilter === 'adopted' ? 'ring-2 ring-purple-500/30' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-purple-800 uppercase tracking-wider">Sudah Diadopsi</span>
                        <span class="text-sm">🏠</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-purple-900">{{ number_format($stats['adopted_count']) }}</span>
                        <span class="text-xs font-semibold text-purple-700">Bertemu Keluarga</span>
                    </div>
                </a>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm space-y-4">
                <form method="GET" action="{{ route('adoption.index') }}" class="space-y-4">
                    
                    <!-- Source Tabs -->
                    <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 pb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Kategori:</span>
                        <a href="{{ route('adoption.index', array_merge(request()->except(['source', 'page']), ['source' => 'all'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $sourceFilter === 'all' ? 'bg-teal-800 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🌟 Semua Sumber ({{ $allAdoptionCats->count() }})
                        </a>
                        <a href="{{ route('adoption.index', array_merge(request()->except(['source', 'page']), ['source' => 'member'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $sourceFilter === 'member' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🐱 Dari Member ({{ $stats['member_cats_count'] }})
                        </a>
                        <a href="{{ route('adoption.index', array_merge(request()->except(['source', 'page']), ['source' => 'rescue'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $sourceFilter === 'rescue' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            📋 Rescue Relawan ({{ $stats['rescue_cats_count'] }})
                        </a>
                    </div>

                    <!-- Filter Dropdowns Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        <input type="hidden" name="source" value="{{ $sourceFilter }}">

                        <!-- Status Filter -->
                        <div>
                            <label class="form-label text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Ketersediaan:</label>
                            <select name="status" class="form-input text-xs py-2">
                                <option value="available" {{ $statusFilter === 'available' ? 'selected' : '' }}>🟢 Siap Diadopsi</option>
                                <option value="in_process" {{ $statusFilter === 'in_process' ? 'selected' : '' }}>🟡 Sedang Proses Seleksi</option>
                                <option value="adopted" {{ $statusFilter === 'adopted' ? 'selected' : '' }}>🟣 Sudah Diadopsi</option>
                                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                            </select>
                        </div>

                        <!-- Gender Filter -->
                        <div>
                            <label class="form-label text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin:</label>
                            <select name="gender" class="form-input text-xs py-2">
                                <option value="all" {{ $genderFilter === 'all' ? 'selected' : '' }}>Semua Jenis Kelamin</option>
                                <option value="male" {{ $genderFilter === 'male' ? 'selected' : '' }}>Jantan ♂</option>
                                <option value="female" {{ $genderFilter === 'female' ? 'selected' : '' }}>Betina ♀</option>
                            </select>
                        </div>

                        <!-- Wilayah Filter -->
                        <div>
                            <label class="form-label text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Wilayah / Lokasi:</label>
                            <select name="wilayah" class="form-input text-xs py-2">
                                <option value="all">Semua Wilayah</option>
                                @foreach($masterWilayah as $w)
                                    <option value="{{ $w->kode }}" {{ $wilayahFilter === $w->kode ? 'selected' : '' }}>
                                        {{ $w->kode }} - {{ $w->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Box -->
                        <div>
                            <label class="form-label text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Anabul:</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Nama, ras, bulu..." class="form-input text-xs pl-8 py-2 w-full">
                            </div>
                        </div>
                    </div>

                    <!-- Submit & Reset -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span class="text-xs text-slate-500">
                            Menampilkan <strong>{{ $allAdoptionCats->count() }}</strong> kucing siap adopsi
                        </span>
                        <div class="flex items-center gap-2">
                            @if($search || $genderFilter !== 'all' || $statusFilter !== 'available' || $wilayahFilter !== 'all' || $sourceFilter !== 'all')
                                <a href="{{ route('adoption.index') }}" class="button-secondary text-xs px-3 py-1.5">
                                    Reset Filter
                                </a>
                            @endif
                            <button type="submit" class="button-primary text-xs font-bold px-4 py-1.5 shadow-xs">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Adoption Cats Grid -->
            @if($allAdoptionCats->isEmpty())
                <div class="content-card bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-3">
                    <div class="text-5xl">🐱</div>
                    <h3 class="font-outfit text-lg font-bold text-slate-800">Belum Ada Kucing Sesuai Kriteria</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                        Tidak ditemukan anabul open adopsi dengan filter saat ini. Anda dapat mencoba mengatur ulang filter atau mendaftarkan kucing untuk diadopsi melalui dashboard member.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('adoption.index') }}" class="button-primary text-xs font-bold px-4 py-2">
                            Lihat Semua Kucing Open Adopsi
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($allAdoptionCats as $cat)
                        <div class="content-card bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md hover:border-teal-300 transition duration-300 flex flex-col justify-between group">
                            
                            <div>
                                <!-- Cat Photo with Badges -->
                                <div class="relative aspect-4/3 w-full bg-slate-100 overflow-hidden">
                                    <img src="{{ $cat->photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    
                                    <!-- Source Badge Top Left -->
                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs border border-slate-200 flex items-center gap-1">
                                            {{ $cat->source_label }}
                                        </span>
                                    </div>

                                    <!-- Status Badge Top Right -->
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border shadow-xs backdrop-blur-xs {{ $cat->badge_class }}">
                                            {{ $cat->status_label }}
                                        </span>
                                    </div>

                                    <!-- Fee Type Bottom Left -->
                                    <div class="absolute bottom-3 left-3">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-900/80 text-white shadow-xs backdrop-blur-xs">
                                            🎁 {{ $cat->fee_type }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-5 space-y-3">
                                    
                                    <!-- Name & Breed -->
                                    <div>
                                        <div class="flex items-center justify-between gap-2">
                                            <h3 class="font-outfit text-lg font-bold text-slate-900 group-hover:text-teal-800 transition">
                                                {{ $cat->name }}
                                            </h3>
                                            @if($cat->gender)
                                                <span class="text-xs font-bold {{ $cat->gender === 'male' ? 'text-blue-600 bg-blue-50 border-blue-200' : 'text-rose-600 bg-rose-50 border-rose-200' }} px-2 py-0.5 rounded-full border">
                                                    {{ $cat->gender_label }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $cat->breed }} &bull; <span class="font-medium text-slate-700">{{ $cat->age_text }}</span>
                                        </p>
                                    </div>

                                    <!-- Quick Specs -->
                                    <div class="grid grid-cols-2 gap-2 text-[11px] pt-2 border-t border-slate-100">
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-slate-400">📍</span>
                                            <span class="truncate" title="{{ $cat->location }}">{{ $cat->location }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-slate-400">🩺</span>
                                            <span>{{ $cat->has_medical ? 'Rekam Medis Ada' : 'Sehat Aktif' }}</span>
                                        </div>
                                    </div>

                                    <!-- Privacy Safe Guardian Info -->
                                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-[11px] text-slate-600 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="text-slate-400">👤</span>
                                            <span class="truncate">Wali: <strong>{{ $cat->guardian_name }}</strong></span>
                                        </div>
                                        <span class="text-[9px] font-bold text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200 shrink-0">
                                            Via Admin
                                        </span>
                                    </div>

                                    @if(!empty($cat->notes))
                                        <p class="text-xs text-slate-600 line-clamp-2 italic leading-relaxed pt-1">
                                            "{{ strip_tags($cat->notes) }}"
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Footer Action -->
                            <div class="p-5 pt-0">
                                <a href="{{ route('adoption.show', [$cat->source_type, $cat->id]) }}" 
                                   class="w-full button-primary text-xs font-bold py-2.5 rounded-xl shadow-xs flex items-center justify-center gap-2 group-hover:bg-teal-900 transition">
                                    <span>💖</span> Lihat Profil & Ajukan Adopsi
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Privacy Guarantee Information Banner -->
            <div class="bg-gradient-to-r from-teal-50 to-emerald-50 rounded-2xl p-5 border border-teal-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-teal-950">
                <div class="flex items-start gap-3">
                    <span class="text-2xl shrink-0">🛡️</span>
                    <div class="space-y-1">
                        <strong class="font-bold text-teal-900 text-sm">Privasi & Standar Adopsi Amanah KucingMu:</strong>
                        <p class="text-teal-800 leading-relaxed">
                            Nomor kontak pribadi member dan relawan dijaga kerahasiaannya. Seluruh proses perkenalan, verifikasi kelayakan calon pengadopsi, hingga penyerahan anabul difasilitasi oleh Tim Pengelola KucingMu tanpa dipungut biaya komersial.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
