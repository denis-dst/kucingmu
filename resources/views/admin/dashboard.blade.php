<x-app-layout>
    <div class="py-12" x-data="{ searchKtam: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Administrator Utama</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Datang, Admin KucingMu!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Monitor statistik partisipasi gerakan kesehatan kucing Muhammadiyah, verifikasi keaslian kartu KTAM melalui pemindai nomor kartu, dan ekspor laporan berkala.
                    </p>
                    
                    <div class="mt-5">
                        <a href="{{ route('export-data') }}" class="button-primary px-5 py-2.5 inline-flex items-center gap-2">
                            <span>📊</span> Ekspor Semua Data (CSV)
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    🏢
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                
                <!-- Stat 1 -->
                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kucing Terdaftar</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['cats_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-600">Ekor</span>
                    </div>
                </div>
                
                <!-- Stat 2 -->
                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Jadwal Pemeriksaan</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['appointments_count'] }}</span>
                        <span class="text-xs font-semibold text-slate-500">Janji</span>
                    </div>
                </div>
                
                <!-- Stat 3 -->
                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemeriksaan Selesai</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['records_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-600">Selesai</span>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">KTAM Diterbitkan</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['ktam_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-600">Kartu</span>
                    </div>
                </div>

            </div>

            <!-- Grid Content -->
            <div class="grid gap-8 lg:grid-cols-3">
                
                <!-- Left Section: Cat Registry & Status -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="content-card">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Database Anggota KucingMu</h2>
                        </div>

                        @if($cats->isEmpty())
                            <p class="text-sm text-slate-500 text-center py-8">Belum ada data kucing terdaftar.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-slate-400 font-bold">
                                            <th class="py-3 px-1">Kucing / Ras</th>
                                            <th class="py-3 px-1">Pemilik / NBM</th>
                                            <th class="py-3 px-1">Nomor KTAM</th>
                                            <th class="py-3 px-1">Status</th>
                                            <th class="py-3 px-1">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @foreach($cats as $cat)
                                            <tr>
                                                <td class="py-4 px-1">
                                                    <div class="font-bold text-slate-900">{{ $cat->name }}</div>
                                                    <div class="text-xs text-slate-400">{{ $cat->breed }}</div>
                                                </td>
                                                <td class="py-4 px-1">
                                                    <div class="text-slate-950 font-semibold">{{ $cat->owner->name }}</div>
                                                    <div class="text-xs text-slate-500">NBM: {{ $cat->owner->muhammadiyah_id ?? '-' }}</div>
                                                </td>
                                                <td class="py-4 px-1 font-mono text-xs">
                                                    {{ $cat->ktamCard ? $cat->ktamCard->ktam_number : '-' }}
                                                </td>
                                                <td class="py-4 px-1">
                                                    @if($cat->ktamCard)
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">KTAM Terbit</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Belum Periksa</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-1">
                                                    @if($cat->ktamCard)
                                                        <a href="{{ route('ktam.download', $cat->id) }}" class="text-xs font-semibold text-teal-700 hover:text-teal-900">Unduh PDF</a>
                                                    @else
                                                        <span class="text-xs text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $cats->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Section: Verification Scanner Simulation -->
                <div class="space-y-8">
                    
                    <!-- KTAM Verification Card -->
                    <div class="content-card bg-slate-50/50">
                        <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Verifikasi KTAM Kucing</h2>
                        <p class="text-xs text-slate-500 mb-4">Masukkan nomor KTAM kucing di bawah ini untuk melihat detail pemilik dan riwayat medis secara instan (Menyimulasikan scanning QR Code di lokasi event).</p>

                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Nomor Kartu KTAM</label>
                                <input type="text" x-model="searchKtam" placeholder="e.g. KM-20260707-0001" class="form-input">
                            </div>
                            
                            <button @click="if(searchKtam.trim()) { window.location.href = `/verify/${searchKtam.trim()}` } else { alert('Silakan masukkan nomor KTAM terlebih dahulu.') }" class="w-full button-primary flex justify-center py-2.5 text-xs">
                                Periksa Keaslian Kartu
                            </button>
                        </div>

                        <!-- Sample copy-paste card numbers helper -->
                        @if($stats['ktam_count'] > 0)
                            <div class="mt-6 border-t border-slate-200/60 pt-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nomor KTAM Terbit (Salin untuk tes):</span>
                                <div class="mt-2 space-y-1.5">
                                    @foreach($cats->filter(fn($c) => $c->ktamCard)->take(3) as $c)
                                        <div class="flex items-center justify-between bg-white px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                                            <span class="font-mono text-slate-700 font-bold" x-text="'{{ $c->ktamCard->ktam_number }}'"></span>
                                            <button @click="searchKtam = '{{ $c->ktamCard->ktam_number }}'" class="text-teal-700 font-bold hover:underline text-[10px]">Pilih</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Upcoming Schedule Checklist -->
                    <div class="content-card">
                        <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Pemeriksaan Terbaru</h2>
                        <div class="space-y-3.5">
                            @forelse($appointments as $app)
                                <div class="text-xs p-3 rounded-lg border border-slate-200 bg-white leading-relaxed">
                                    <div class="flex justify-between font-semibold">
                                        <span class="text-slate-800 font-bold">{{ $app->cat->name }}</span>
                                        <span class="text-slate-500 font-mono">{{ $app->date->format('d M') }}</span>
                                    </div>
                                    <p class="text-slate-500 mt-1">Status: <span class="font-semibold text-slate-700">{{ $app->status }}</span></p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-4">Belum ada kunjungan pemeriksaan.</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
