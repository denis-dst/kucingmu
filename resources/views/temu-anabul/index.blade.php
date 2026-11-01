<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl" aria-hidden="true">📢</span>
                    <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-900 tracking-tight">Temu Anabul</h1>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Platform pelaporan kucing hilang dan ditemukan. Bantu anabul pulang ke pelukan pemiliknya.
                </p>
            </div>

            <!-- Action CTA Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('temu-anabul.map') }}"
                    class="button-secondary min-h-[44px] px-3.5 text-xs sm:text-sm font-semibold flex items-center gap-1.5">
                    <span>🗺️</span>
                    <span>Peta Temu</span>
                </a>
                <a href="{{ route('temu-anabul.create', ['type' => 'found']) }}"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3.5 py-2.5 text-xs sm:text-sm font-bold text-indigo-700 hover:bg-indigo-100 min-h-[44px] transition">
                    <span>🐱</span>
                    <span>Lapor Ditemukan</span>
                </a>
                <a href="{{ route('temu-anabul.create', ['type' => 'lost']) }}"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-rose-700 min-h-[44px] transition">
                    <span>🚨</span>
                    <span>Lapor Kucing Hilang</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash notification -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 text-xs sm:text-sm font-semibold flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-teal-600 hover:text-teal-900 text-base leading-none">&times;</button>
                </div>
            @endif

            <!-- Guest Callout Banner (Akses Publik vs Member) -->
            @guest
                <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50/80 border border-rose-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg shrink-0">
                            📢
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-rose-950">Layanan Temu Anabul Terbuka untuk Umum</div>
                            <div class="text-[11px] sm:text-xs text-rose-800 mt-0.5">
                                Seluruh daftar dan peta laporan dapat diakses publik tanpa login. Untuk membuat laporan kehilangan atau penemuan kucing, silakan masuk dengan akun member KucingMU.
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('login') }}" class="button-secondary min-h-[38px] px-3.5 text-xs font-semibold">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="button-primary min-h-[38px] px-3.5 text-xs font-bold bg-rose-600 hover:bg-rose-700">
                            Daftar Member
                        </a>
                    </div>
                </div>
            @endguest

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl shrink-0">
                        📢
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Laporan</div>
                        <div class="text-xl font-outfit font-black text-slate-800">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                        🚨
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Kucing Hilang</div>
                        <div class="text-xl font-outfit font-black text-slate-800">{{ number_format($stats['lost']) }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                        🐱
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Kucing Ditemukan</div>
                        <div class="text-xl font-outfit font-black text-slate-800">{{ number_format($stats['found']) }}</div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        ✨
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Berhasil Reuni</div>
                        <div class="text-xl font-outfit font-black text-slate-800">{{ number_format($stats['reunited']) }}</div>
                    </div>
                </div>
            </div>

            <!-- Tabs & Search Filter Bar -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-4">
                <!-- Category Tabs -->
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 overflow-x-auto text-xs font-bold">
                    <a href="{{ route('temu-anabul.index') }}"
                        class="px-4 py-2 rounded-xl transition shrink-0 {{ !request('type') && !request('status') ? 'bg-teal-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        Semua Laporan
                    </a>
                    <a href="{{ route('temu-anabul.index', ['type' => 'lost']) }}"
                        class="px-4 py-2 rounded-xl transition shrink-0 flex items-center gap-1.5 {{ request('type') === 'lost' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>🚨</span>
                        <span>Kucing Hilang</span>
                    </a>
                    <a href="{{ route('temu-anabul.index', ['type' => 'found']) }}"
                        class="px-4 py-2 rounded-xl transition shrink-0 flex items-center gap-1.5 {{ request('type') === 'found' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>🐱</span>
                        <span>Kucing Ditemukan</span>
                    </a>
                    <a href="{{ route('temu-anabul.index', ['status' => 'reunited']) }}"
                        class="px-4 py-2 rounded-xl transition shrink-0 flex items-center gap-1.5 {{ request('status') === 'reunited' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>✨</span>
                        <span>Berhasil Reuni</span>
                    </a>
                </div>

                <!-- Search form -->
                <form method="GET" action="{{ route('temu-anabul.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="sm:col-span-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Cari nama kucing, ciri fisik, atau alamat..."
                                class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-100 outline-none">
                        </div>
                    </div>

                    <div>
                        <select name="province" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs sm:text-sm bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 outline-none">
                            <option value="">Semua Provinsi</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="button-primary min-h-[38px] px-4 text-xs font-bold w-full sm:w-auto">
                            Cari
                        </button>
                        <a href="{{ route('temu-anabul.index') }}" class="button-secondary min-h-[38px] px-3 text-xs w-full sm:w-auto text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Reports Feed Grid -->
            @if($reports->isEmpty())
                <div class="p-8 sm:p-12 text-center bg-white rounded-2xl border border-slate-200 shadow-xs space-y-3">
                    <span class="text-4xl">🔍</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800">Tidak Ada Laporan yang Ditemukan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                        Belum ada laporan yang sesuai dengan kriteria filter saat ini.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                        <a href="{{ route('temu-anabul.create', ['type' => 'lost']) }}" class="button-primary text-xs">
                            + Lapor Kucing Hilang
                        </a>
                        <a href="{{ route('temu-anabul.create', ['type' => 'found']) }}" class="button-secondary text-xs">
                            + Lapor Kucing Ditemukan
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($reports as $report)
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <!-- Card Image -->
                                <div class="relative aspect-4/3 bg-slate-100 overflow-hidden">
                                    <img src="{{ $report->thumbnail_url }}" alt="{{ $report->cat_name }}" class="w-full h-full object-cover">
                                    
                                    <!-- Badges -->
                                    <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1.5">
                                        @if($report->isReunited())
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-xs">
                                                ✨ Berhasil Reuni
                                            </span>
                                        @elseif($report->isLost())
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs">
                                                🚨 Hilang
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-600 text-white shadow-xs">
                                                🐱 Ditemukan
                                            </span>
                                        @endif

                                        @if($report->reward_info)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-amber-950 shadow-xs">
                                                🎁 Ada Imbalan
                                            </span>
                                        @endif
                                    </div>

                                    <div class="absolute bottom-2.5 right-2.5 bg-black/60 backdrop-blur-xs text-white px-2 py-0.5 rounded-lg text-[10px] font-medium">
                                        {{ $report->incident_date ? $report->incident_date->translatedFormat('d M Y') : '-' }}
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="p-4 space-y-2.5">
                                    <div>
                                        <h3 class="font-outfit font-black text-slate-900 text-base leading-tight">
                                            {{ $report->cat_name }}
                                        </h3>
                                        <div class="text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                                            {{ $report->title }}
                                        </div>
                                    </div>

                                    <div class="text-xs text-slate-600 space-y-1 bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                                        <div><span class="text-slate-400">Ciri:</span> {{ Str::limit($report->distinctive_features, 60) }}</div>
                                        <div><span class="text-slate-400">Lokasi:</span> {{ $report->city ?: $report->province ?: 'Lokasi terdata' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="p-4 pt-0 flex items-center gap-2">
                                <a href="{{ route('temu-anabul.show', $report->id) }}"
                                    class="button-primary flex-1 min-h-[40px] text-xs font-bold flex items-center justify-center">
                                    Lihat Detail Laporan
                                </a>

                                @if($report->is_contact_public && $report->whatsapp_url)
                                    <a href="{{ $report->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                                        class="p-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 min-h-[40px] min-w-[40px] flex items-center justify-center transition"
                                        title="Hubungi via WhatsApp">
                                        <span class="text-base">💬</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $reports->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

