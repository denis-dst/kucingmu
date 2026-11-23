<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Relawan Lapangan & Surveilans</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Bertugas, {{ Auth::user()->name }}!
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Fokus pada kegiatan lapangan Sensus Kucing PTMA di klaster kampus serta surveilans One Health populasi kucing liar di lingkungan sekitar.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md border text-xs font-semibold bg-teal-50 text-teal-800 border-teal-200">
                            <span class="h-2 w-2 rounded-full bg-teal-600" aria-hidden="true"></span>
                            <span>Relawan Lapangan Resmi</span>
                        </span>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    📊
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-xs" role="alert">
                    <span class="text-teal-600 font-bold text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Dua Modul Utama Relawan (Sensus PTMA & eSurveillance) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Sensus Kucing PTMA Card -->
                <div class="content-card border-teal-200 bg-gradient-to-br from-teal-50/60 to-white p-6 flex flex-col justify-between space-y-4 shadow-xs">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-100/80 px-2.5 py-0.5 rounded-full">
                                Modul Lapangan 1
                            </span>
                            <span class="text-2xl">📋</span>
                        </div>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">Sensus Kucing PTMA</h2>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pendataan sistematis populasi kucing liar berbasis klaster kampus Perguruan Tinggi Muhammadiyah 'Aisyiyah. Mencakup identifikasi visual, morfometri, indeks kesejahteraan (BCS & lesi klinis), hingga analisis mikro-habitat.
                        </p>
                        <div class="pt-2 flex items-center gap-2 text-xs font-mono text-teal-900">
                            <strong class="text-lg font-bold">{{ $totalCensusCount ?? 0 }}</strong>
                            <span class="text-slate-500">kucing tersensus di sistem</span>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-teal-100/80 flex flex-wrap gap-2">
                        <a href="{{ route('volunteer.census.index') }}" class="button-primary text-xs font-bold py-2.5 px-4 inline-flex items-center gap-1.5">
                            <span>Buka Sensus PTMA</span>
                            <span>→</span>
                        </a>
                        <a href="{{ route('volunteer.census.create') }}" class="button-secondary text-xs font-semibold py-2.5 px-3.5 inline-flex items-center gap-1 bg-white">
                            <span>+ Input Data Sensus Baru</span>
                        </a>
                        <a href="{{ route('volunteer.census.scan') }}" class="button-secondary text-xs font-semibold py-2.5 px-3.5 inline-flex items-center gap-1 bg-white">
                            <span>📷 Scan Foto Kucing</span>
                        </a>
                    </div>
                </div>

                <!-- 2. eSurveillance Card -->
                <div class="content-card border-blue-200 bg-gradient-to-br from-blue-50/60 to-white p-6 flex flex-col justify-between space-y-4 shadow-xs">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-100/80 px-2.5 py-0.5 rounded-full">
                                Modul Lapangan 2
                            </span>
                            <span class="text-2xl">🩺</span>
                        </div>
                        <h2 class="font-outfit text-xl font-bold text-slate-900">eSurveillance Populasi Kucing</h2>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Surveilans kesehatan populasi kucing jalanan berbasis pendekatan One Health. Mencakup 7 tahapan komprehensif: sensus visual, pemeriksaan feses, sampling tanah, survei pengetahuan masyarakat (KAP), dan evaluasi keselamatan lingkungan.
                        </p>
                        <div class="pt-2 flex items-center gap-2 text-xs font-mono text-blue-900">
                            <strong class="text-lg font-bold">{{ $totalSurveyCount ?? 0 }}</strong>
                            <span class="text-slate-500">survei tercatat di database</span>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-blue-100/80 flex flex-wrap gap-2">
                        <a href="{{ route('volunteer.surveillance.index') }}" class="button-primary text-xs font-bold py-2.5 px-4 inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800">
                            <span>Buka eSurveillance</span>
                            <span>→</span>
                        </a>
                        <a href="{{ route('volunteer.surveillance.index') }}#form-survei" class="button-secondary text-xs font-semibold py-2.5 px-3.5 inline-flex items-center gap-1 bg-white">
                            <span>+ Isi Formulir Surveilans</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Terbaru Sensus PTMA & eSurveillance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Rekap Sensus PTMA Terbaru -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">📋</span>
                            <h3 class="font-outfit font-bold text-slate-900 text-sm sm:text-base">Data Sensus PTMA Terbaru</h3>
                        </div>
                        <a href="{{ route('volunteer.census.index') }}" class="text-xs font-bold text-teal-700 hover:underline">
                            Lihat Semua →
                        </a>
                    </div>

                    @if(!isset($recentCensus) || $recentCensus->isEmpty())
                        <div class="py-8 text-center text-xs text-slate-500 bg-slate-50 rounded-xl">
                            Belum ada entri data sensus kucing PTMA terbaru.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100 overflow-hidden">
                            @foreach($recentCensus as $census)
                                <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        @if($census->photo_path)
                                            <img src="{{ asset('storage/' . $census->photo_path) }}" alt="Kucing" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0">
                                        @else
                                            <div class="w-9 h-9 rounded-lg bg-teal-50 border border-teal-200 flex items-center justify-center text-xs shrink-0">🐾</div>
                                        @endif
                                        <div class="min-w-0">
                                            <strong class="font-bold text-slate-900 block truncate">{{ $census->cat_id ?? 'Kucing #' . $census->id }}</strong>
                                            <span class="text-[11px] text-slate-500 block truncate">{{ $census->location_cluster ?: 'Klaster Kampus' }} &bull; {{ $census->breed ?: 'Domestik' }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[10px] text-slate-400 font-mono block">{{ $census->created_at ? $census->created_at->format('d M Y') : '-' }}</span>
                                        <a href="{{ route('volunteer.census.show', $census->id) }}" class="text-[11px] font-bold text-teal-700 hover:underline">Detail</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Rekap eSurveillance Terbaru -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🩺</span>
                            <h3 class="font-outfit font-bold text-slate-900 text-sm sm:text-base">Laporan eSurveillance Terbaru</h3>
                        </div>
                        <a href="{{ route('volunteer.surveillance.index') }}" class="text-xs font-bold text-blue-700 hover:underline">
                            Lihat Semua →
                        </a>
                    </div>

                    @if(!isset($recentSurveys) || $recentSurveys->isEmpty())
                        <div class="py-8 text-center text-xs text-slate-500 bg-slate-50 rounded-xl">
                            Belum ada entri laporan eSurveillance terbaru.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100 overflow-hidden">
                            @foreach($recentSurveys as $survey)
                                <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                                    <div class="min-w-0">
                                        <strong class="font-bold text-slate-900 block truncate">Laporan Surveilans #{{ $survey->id }}</strong>
                                        <span class="text-[11px] text-slate-500 block truncate">{{ $survey->location_name ?: ($survey->campus ?: 'Lokasi Survei') }}</span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[10px] text-slate-400 font-mono block">{{ $survey->created_at ? $survey->created_at->format('d M Y') : '-' }}</span>
                                        <a href="{{ route('volunteer.surveillance.pdf', $survey->id) }}" target="_blank" class="text-[11px] font-bold text-blue-700 hover:underline">PDF</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
