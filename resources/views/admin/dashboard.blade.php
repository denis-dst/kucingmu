<x-app-layout>
    <div class="py-12" x-data="{ searchKtam: '', showModal: false, activeCat: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Administrator Utama</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Datang, Admin KucingMu!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Verifikasi pendaftaran & pemeriksaan kesehatan kucing, tinjau sampel biometrik dan galeri foto, lalu terbitkan Kartu Tanda Anggota Muhammadiyah Kucing (KTAM).
                    </p>
                    
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('export-data') }}" class="button-primary px-5 py-2.5 inline-flex items-center gap-2 text-xs">
                            <span>📊</span> Ekspor Semua Data (CSV)
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    🏢
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                
                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kucing</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['cats_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-600">Ekor</span>
                    </div>
                </div>
                
                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pemeriksaan Dokter</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['records_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-600">Selesai</span>
                    </div>
                </div>

                <div class="content-card bg-amber-50/70 border border-amber-200">
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Pending Verifikasi</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-amber-900">{{ $stats['pending_verification_count'] }}</span>
                        <span class="text-xs font-semibold text-amber-700">Perlu KTAM</span>
                    </div>
                </div>

                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">KTAM Terbit</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-teal-700">{{ $stats['ktam_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-600">Kartu</span>
                    </div>
                </div>

                <div class="content-card bg-white border border-slate-200">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Janji Temu</span>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-outfit text-3xl font-bold text-slate-900">{{ $stats['appointments_count'] }}</span>
                        <span class="text-xs font-semibold text-slate-500">Janji</span>
                    </div>
                </div>

            </div>

            <!-- Admin Verification Alert Section (Pending KTAM Verification) -->
            <div class="content-card border-l-4 border-amber-500">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <h2 class="font-outfit text-xl font-bold text-slate-900 flex items-center gap-2">
                            <span>🛡️</span> Permintaan Verifikasi & Penerbitan KTAM
                            @if($pendingVerificationCats->count() > 0)
                                <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full font-bold">{{ $pendingVerificationCats->count() }} Menunggu</span>
                            @endif
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Kucing di bawah ini telah diperiksa oleh Dokter Hewan dan menunggu peninjauan Admin untuk penerbitan Kartu KTAM resmi.</p>
                    </div>
                </div>

                @if($pendingVerificationCats->isEmpty())
                    <div class="text-center py-8 text-slate-500 text-sm bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        ✨ Tidak ada kucing yang menunggu verifikasi. Semua pemeriksaan dokter telah diverifikasi & KTAM diterbitkan!
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($pendingVerificationCats as $cat)
                            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-start gap-3">
                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shadow-sm flex-shrink-0">
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $cat->name }}</h3>
                                            <p class="text-xs text-slate-500">{{ $cat->breed }} • {{ $cat->gender == 'male' ? 'Jantan ♂' : 'Betina ♀' }}</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Pemilik: <strong class="text-slate-700">{{ $cat->owner->name }}</strong> (NBM: {{ $cat->owner->muhammadiyah_id ?? '-' }})</p>
                                        </div>
                                    </div>

                                    <!-- Biometric Badge -->
                                    <div class="flex items-center gap-1.5 pt-1">
                                        @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                🐾 Biometrik {{ strtoupper($cat->biometric_type) }} Terdaftar
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500">
                                                ⚪ Biometrik Standard
                                            </span>
                                        @endif

                                        @if($cat->photos->count() > 1)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                                📷 {{ $cat->photos->count() }} Foto
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Last Doctor Record snippet -->
                                    @if($cat->medicalRecords->isNotEmpty())
                                        @php $lastRecord = $cat->medicalRecords->first(); @endphp
                                        <div class="bg-slate-50 p-2.5 rounded-lg text-xs space-y-1 text-slate-600 border border-slate-100">
                                            <div class="font-semibold text-slate-800 flex justify-between">
                                                <span>👨‍⚕️ {{ $lastRecord->vet->name ?? 'Dokter Hewan' }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $lastRecord->created_at->format('d M Y') }}</span>
                                            </div>
                                            <p class="text-[11px]">Kondisi: <strong>{{ $lastRecord->general_condition }}</strong> ({{ $lastRecord->weight }}kg, {{ $lastRecord->temperature }}°C)</p>
                                        </div>
                                    @else
                                        <div class="bg-slate-50 p-2.5 rounded-lg text-xs text-slate-500 border border-slate-100 flex items-center gap-1.5">
                                            <span>ℹ️</span>
                                            <span class="text-[11px]">Belum ada periksa dokter (Siap verifikasi langsung Admin)</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center gap-2">
                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary py-2 px-3 text-xs flex items-center justify-center gap-1 font-semibold whitespace-nowrap">
                                        <span>✏️</span> Biometrik & Foto
                                    </a>
                                    <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memverifikasi dan menerbitkan kartu KTAM untuk {{ $cat->name }}?')" class="w-full button-primary py-2 text-xs text-center flex items-center justify-center gap-1">
                                            <span>✅</span> Verifikasi KTAM
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Grid Content -->
            <div class="grid gap-8 lg:grid-cols-3">
                
                <!-- Left Section: Cat Registry & Status -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="content-card">
                        <div class="border-b border-slate-100 pb-4 mb-6 flex justify-between items-center">
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Database Anggota KucingMu</h2>
                            <span class="text-xs text-slate-400 font-medium">Foto utama & biometrik tersimpan secara sistem</span>
                        </div>

                        @if($cats->isEmpty())
                            <p class="text-sm text-slate-500 text-center py-8">Belum ada data kucing terdaftar.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-slate-400 font-bold">
                                            <th class="py-3 px-1">Kucing / Foto Utama</th>
                                            <th class="py-3 px-1">Pemilik / NBM</th>
                                            <th class="py-3 px-1">Biometrik</th>
                                            <th class="py-3 px-1">Nomor KTAM</th>
                                            <th class="py-3 px-1">Status KTAM</th>
                                            <th class="py-3 px-1">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @foreach($cats as $cat)
                                            <tr>
                                                <td class="py-4 px-1">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200 shadow-xs">
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $cat->name }}</div>
                                                            <div class="text-xs text-slate-400">{{ $cat->breed }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-1">
                                                    <div class="text-slate-950 font-semibold">{{ $cat->owner->name }}</div>
                                                    <div class="text-xs text-slate-500">NBM: {{ $cat->owner->muhammadiyah_id ?? '-' }}</div>
                                                </td>
                                                <td class="py-4 px-1">
                                                    @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100 uppercase">
                                                            🐾 {{ $cat->biometric_type }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-1 font-mono text-xs">
                                                    {{ $cat->ktamCard ? $cat->ktamCard->ktam_number : '-' }}
                                                </td>
                                                <td class="py-4 px-1">
                                                    @if($cat->ktamCard)
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">KTAM Terbit</span>
                                                    @elseif($cat->medicalRecords->isNotEmpty())
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Periksa Selesai • Belum KTAM</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Terdaftar • Belum KTAM</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-1">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <a href="{{ route('cat.edit', $cat->id) }}" title="Update Foto & Biometrik" class="text-xs font-bold text-slate-700 hover:text-teal-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-2.5 py-1 rounded-lg transition">
                                                            ✏️ Biometrik & Foto
                                                        </a>
                                                        @if($cat->ktamCard)
                                                            <a href="{{ route('ktam.download', $cat->id) }}" class="text-xs font-bold text-teal-700 hover:text-teal-900 bg-teal-50 border border-teal-200 px-2.5 py-1 rounded-lg">Unduh PDF</a>
                                                        @else
                                                            <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" onclick="return confirm('Verifikasi & terbitkan kartu KTAM untuk {{ $cat->name }}?')" class="button-primary px-3 py-1 text-[11px] font-bold shadow-xs">
                                                                    Verifikasi KTAM
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
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
                        <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Cek Keaslian KTAM</h2>
                        <p class="text-xs text-slate-500 mb-4">Masukkan nomor KTAM kucing di bawah ini untuk melihat verifikasi admin, foto utama, biometrik, dan riwayat medis secara instan.</p>

                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Nomor Kartu KTAM</label>
                                <input type="text" x-model="searchKtam" placeholder="e.g. KM-20260707-0001" class="form-input">
                            </div>
                            
                            <button @click="if(searchKtam.trim()) { window.location.href = `/verify/${searchKtam.trim()}` } else { alert('Silakan masukkan nomor KTAM terlebih dahulu.') }" class="w-full button-primary flex justify-center py-2.5 text-xs">
                                Periksa Validitas KTAM
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

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
