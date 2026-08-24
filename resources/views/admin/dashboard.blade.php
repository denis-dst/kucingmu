<x-app-layout>
    <div class="py-8" x-data="{ searchKtam: '', showModal: false, activeCat: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="content-card bg-teal-900 text-white p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Portal Administrator</span>
                        <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-white mt-1">
                            Panel Pengelolaan KucingMu
                        </h1>
                        <p class="text-xs sm:text-sm text-teal-100 mt-2 leading-relaxed">
                            Verifikasi data pemeriksaan dokter hewan, tinjau kelengkapan berkas dan sampel biometrik, serta terbitkan Kartu Tanda Anggota Muhammadiyah Kucing (KTAM).
                        </p>
                    </div>
                    
                    <div class="flex-shrink-0">
                        <a href="{{ route('export-data') }}" class="button-secondary text-xs font-semibold px-4 py-2.5 min-h-[44px]">
                            Ekspor Data (CSV)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                
                <div class="content-card">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kucing</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ $stats['cats_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Ekor</span>
                    </div>
                </div>
                
                <div class="content-card">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pemeriksaan Dokter</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ $stats['records_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Selesai</span>
                    </div>
                </div>

                <div class="content-card bg-amber-50 border-amber-300">
                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Pending Verifikasi</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-amber-900">{{ $stats['pending_verification_count'] }}</span>
                        <span class="text-xs font-semibold text-amber-800">Perlu KTAM</span>
                    </div>
                </div>

                <div class="content-card">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">KTAM Diterbitkan</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['ktam_count'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Kartu</span>
                    </div>
                </div>

                <div class="content-card">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Janji Temu</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ $stats['appointments_count'] }}</span>
                        <span class="text-xs font-semibold text-slate-600">Janji</span>
                    </div>
                </div>

            </div>

            <!-- Admin Verification Alert Section (Pending KTAM Verification) -->
            <div class="content-card border-l-4 border-amber-600">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                    <div>
                        <h2 class="font-outfit text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>Permintaan Verifikasi & Penerbitan KTAM</span>
                            @if($pendingVerificationCats->count() > 0)
                                <span class="bg-amber-100 text-amber-900 text-xs px-2.5 py-0.5 rounded-full font-bold border border-amber-300">{{ $pendingVerificationCats->count() }} Menunggu</span>
                            @endif
                        </h2>
                        <p class="text-xs text-slate-600 mt-0.5">Daftar kucing yang telah diperiksa oleh dokter hewan dan siap diverifikasi untuk penerbitan nomor KTAM resmi.</p>
                    </div>
                </div>

                @if($pendingVerificationCats->isEmpty())
                    <div class="text-center py-6 text-slate-600 text-xs bg-slate-50 rounded-lg border border-dashed border-slate-200">
                        Tidak ada kucing yang menunggu verifikasi. Seluruh pemeriksaan telah diterbitkan kartu KTAM.
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($pendingVerificationCats as $cat)
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-start gap-3">
                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-14 h-14 object-cover rounded-lg border border-slate-200 flex-shrink-0">
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $cat->name }}</h3>
                                            <p class="text-xs text-slate-600">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Pemilik: <strong class="text-slate-800">{{ $cat->owner->name }}</strong> (NBM: {{ $cat->owner->muhammadiyah_id ?? '-' }})</p>
                                        </div>
                                    </div>

                                    <!-- Biometric Badge -->
                                    <div class="flex items-center gap-1.5 pt-1">
                                        @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200">
                                                Biometrik {{ strtoupper($cat->biometric_type) }} Terdaftar
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-200 text-slate-700">
                                                Biometrik Standar
                                            </span>
                                        @endif

                                        @if($cat->photos->count() > 1)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-200 text-slate-800">
                                                {{ $cat->photos->count() }} Foto
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Last Doctor Record snippet -->
                                    @if($cat->medicalRecords->isNotEmpty())
                                        @php $lastRecord = $cat->medicalRecords->first(); @endphp
                                        <div class="bg-white p-2.5 rounded-lg text-xs space-y-1 text-slate-600 border border-slate-200">
                                            <div class="font-semibold text-slate-800 flex justify-between">
                                                <span>{{ $lastRecord->vet->name ?? 'Dokter Hewan' }}</span>
                                                <span class="text-[10px] text-slate-500">{{ $lastRecord->created_at->format('d M Y') }}</span>
                                            </div>
                                            <p class="text-[11px]">Kondisi: <strong>{{ $lastRecord->general_condition }}</strong> ({{ $lastRecord->weight }}kg, {{ $lastRecord->temperature }}°C)</p>
                                        </div>
                                    @else
                                        <div class="bg-white p-2 rounded-lg text-xs text-slate-500 border border-slate-200">
                                            Belum ada periksa dokter (Verifikasi langsung admin)
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-2 border-t border-slate-200 flex items-center gap-2">
                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary py-1.5 px-3 text-xs font-semibold whitespace-nowrap min-h-[38px]">
                                        Ubah Data
                                    </a>
                                    <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memverifikasi dan menerbitkan kartu KTAM untuk {{ $cat->name }}?')" class="w-full button-primary py-1.5 text-xs text-center font-semibold min-h-[38px]">
                                            Terbitkan KTAM
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Grid Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Left Section: Cat Registry & Status -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="content-card">
                        <div class="border-b border-slate-200 pb-3 mb-4 flex justify-between items-center">
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Database Anggota KucingMu</h2>
                            <span class="text-xs text-slate-500">Total data tercatat</span>
                        </div>

                        @if($cats->isEmpty())
                            <p class="text-xs text-slate-500 text-center py-6">Belum ada data kucing terdaftar di database.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs" aria-label="Database Anggota Kucing">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-slate-700 font-bold bg-slate-50">
                                            <th class="py-2.5 px-3">Kucing</th>
                                            <th class="py-2.5 px-3">Pemilik</th>
                                            <th class="py-2.5 px-3">Biometrik</th>
                                            <th class="py-2.5 px-3">Nomor KTAM</th>
                                            <th class="py-2.5 px-3">Status</th>
                                            <th class="py-2.5 px-3">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 text-slate-700">
                                        @foreach($cats as $cat)
                                            <tr class="hover:bg-slate-50">
                                                <td class="py-3 px-3">
                                                    <div class="flex items-center gap-2.5">
                                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-9 h-9 object-cover rounded-lg border border-slate-200">
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $cat->name }}</div>
                                                            <div class="text-[11px] text-slate-500">{{ $cat->breed }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-3">
                                                    <div class="font-semibold text-slate-900">{{ $cat->owner->name }}</div>
                                                    <div class="text-[11px] text-slate-500">NBM: {{ $cat->owner->muhammadiyah_id ?? '-' }}</div>
                                                </td>
                                                <td class="py-3 px-3">
                                                    @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200 uppercase">
                                                            {{ $cat->biometric_type }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 font-mono text-[11px]">
                                                    {{ $cat->ktamCard ? $cat->ktamCard->ktam_number : '-' }}
                                                </td>
                                                <td class="py-3 px-3">
                                                    @if($cat->ktamCard)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200">KTAM Terbit</span>
                                                    @elseif($cat->medicalRecords->isNotEmpty())
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">Menunggu KTAM</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-800">Belum Periksa</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary text-[11px] font-semibold px-2.5 py-1 min-h-[32px]">
                                                            Ubah
                                                        </a>
                                                        @if($cat->ktamCard)
                                                            <a href="{{ route('ktam.download', $cat->id) }}" class="button-primary text-[11px] font-semibold px-2.5 py-1 min-h-[32px]">Unduh PDF</a>
                                                        @else
                                                            <form action="{{ route('admin.verify-ktam', $cat->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" onclick="return confirm('Verifikasi & terbitkan kartu KTAM untuk {{ $cat->name }}?')" class="button-primary text-[11px] font-semibold px-2.5 py-1 min-h-[32px]">
                                                                    Verifikasi
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
                <div class="space-y-6">
                    
                    <!-- KTAM Verification Card -->
                    <div class="content-card bg-slate-50">
                        <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-2.5 mb-3">Pemeriksaan Keaslian KTAM</h2>
                        <p class="text-xs text-slate-600 mb-3.5">Masukkan nomor kartu KTAM untuk melihat data verifikasi, biometrik, dan rekam medis kucing.</p>

                        <div class="space-y-3">
                            <div>
                                <label for="search-ktam-input" class="form-label text-xs">Nomor Kartu KTAM</label>
                                <input id="search-ktam-input" type="text" x-model="searchKtam" placeholder="Contoh: KM-20260707-0001" class="form-input text-xs font-mono">
                            </div>
                            
                            <button type="button" @click="if(searchKtam.trim()) { window.location.href = `/verify/${searchKtam.trim()}` } else { alert('Silakan masukkan nomor KTAM terlebih dahulu.') }" class="w-full button-primary flex justify-center py-2.5 text-xs font-semibold">
                                Periksa Validitas KTAM
                            </button>
                        </div>

                        <!-- Sample copy-paste card numbers helper -->
                        @if($stats['ktam_count'] > 0)
                            <div class="mt-4 border-t border-slate-200 pt-3">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Nomor KTAM Terbit (Pilihan Cepat):</span>
                                <div class="mt-2 space-y-1.5">
                                    @foreach($cats->filter(fn($c) => $c->ktamCard)->take(3) as $c)
                                        <div class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded border border-slate-200 text-xs">
                                            <span class="font-mono text-slate-800 font-semibold" x-text="'{{ $c->ktamCard->ktam_number }}'"></span>
                                            <button type="button" @click="searchKtam = '{{ $c->ktamCard->ktam_number }}'" class="text-teal-800 font-semibold hover:underline text-[11px] p-1">Pilih</button>
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
