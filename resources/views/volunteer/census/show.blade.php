<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumbs & Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-teal-700 uppercase tracking-wider">
                        <a href="{{ route('volunteer.census.index') }}" class="hover:underline">Sensus Kucing PTMA</a>
                        <span>/</span>
                        <span class="text-slate-500">ID: {{ $census->id_kucing }}</span>
                    </div>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Data Sensus Stray Cat [{{ $census->id_kucing }}]
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        {{ $census->display_kampus }} &bull; {{ $census->zona }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('volunteer.census.edit', $census->id) }}" class="button-primary text-xs font-semibold px-4 py-2.5 min-h-[40px] inline-flex items-center gap-1.5">
                        <span>✏️</span> Ubah Data
                    </a>
                    <a href="{{ route('volunteer.census.index') }}" class="button-secondary text-xs font-semibold px-4 py-2.5 min-h-[40px] inline-flex items-center gap-1.5">
                        <span>←</span> Kembali
                    </a>
                </div>
            </div>

            <!-- Detail Card -->
            <div class="content-card space-y-6">
                <!-- 4 Photo Gallery -->
                <div class="space-y-2.5">
                    <h2 class="font-outfit text-sm font-bold text-slate-900 uppercase tracking-wider">Dokumentasi 4 Sudut Pandang</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
                            <span class="text-xs font-bold text-slate-700 block mb-1.5">1. Foto Wajah</span>
                            @if($census->foto_wajah_url)
                                <img src="{{ $census->foto_wajah_url }}" alt="Wajah {{ $census->id_kucing }}" class="w-full aspect-square object-cover rounded-lg border border-slate-200 shadow-xs">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded-lg">Tidak ada foto</div>
                            @endif
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
                            <span class="text-xs font-bold text-slate-700 block mb-1.5">2. Tampak Atas</span>
                            @if($census->foto_atas_url)
                                <img src="{{ $census->foto_atas_url }}" alt="Atas {{ $census->id_kucing }}" class="w-full aspect-square object-cover rounded-lg border border-slate-200 shadow-xs">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded-lg">Tidak ada foto</div>
                            @endif
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
                            <span class="text-xs font-bold text-slate-700 block mb-1.5">3. Samping Kiri</span>
                            @if($census->foto_samping_kiri_url)
                                <img src="{{ $census->foto_samping_kiri_url }}" alt="Samping {{ $census->id_kucing }}" class="w-full aspect-square object-cover rounded-lg border border-slate-200 shadow-xs">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded-lg">Tidak ada foto</div>
                            @endif
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center">
                            <span class="text-xs font-bold text-slate-700 block mb-1.5">4. Foto Opsional</span>
                            @if($census->foto_opsional_url)
                                <img src="{{ $census->foto_opsional_url }}" alt="Opsional {{ $census->id_kucing }}" class="w-full aspect-square object-cover rounded-lg border border-slate-200 shadow-xs">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded-lg">Tidak ada foto</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Structured Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-200 pt-5">
                    <!-- Left Column: Identifikasi & Lokasi -->
                    <div class="space-y-4">
                        <h3 class="font-outfit text-sm font-bold text-slate-900 border-b border-slate-100 pb-1.5">A. Lokasi & Identifikasi</h3>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">ID Kucing:</span>
                                <span class="font-mono font-bold text-teal-900">{{ $census->id_kucing }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Kampus PTMA:</span>
                                <span class="font-semibold text-slate-900">{{ $census->display_kampus }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Zona / Sektor:</span>
                                <span class="font-semibold text-slate-900">{{ $census->zona }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Koordinat GPS:</span>
                                @if($census->latitude && $census->longitude)
                                    <a href="https://www.google.com/maps?q={{ $census->latitude }},{{ $census->longitude }}" target="_blank" class="text-teal-700 font-bold hover:underline">
                                        {{ $census->latitude }}, {{ $census->longitude }} ↗
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Estimasi Usia:</span>
                                <span class="font-semibold text-slate-900">{{ $census->usia }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Jenis Kelamin:</span>
                                <span class="font-semibold text-slate-900">{{ $census->gender }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Pola Warna Bulu:</span>
                                <span class="font-semibold text-slate-900">{{ $census->display_warna }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Welfare & Habitat -->
                    <div class="space-y-4">
                        <h3 class="font-outfit text-sm font-bold text-slate-900 border-b border-slate-100 pb-1.5">B. Kesejahteraan & Mikro-Habitat</h3>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Body Condition Score (BCS):</span>
                                <span class="font-bold text-slate-900">Skala {{ $census->bcs }} / 9</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100 items-center">
                                <span class="text-slate-500">Kondisi Klinis:</span>
                                <div class="flex flex-wrap gap-1 justify-end">
                                    @if(is_array($census->kondisi_klinis))
                                        @foreach($census->kondisi_klinis as $k)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $k === 'Sehat' ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                                {{ $k }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-slate-700">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Panjang Badan Total:</span>
                                <span class="font-semibold text-slate-900">{{ $census->panjang_badan_cm ? $census->panjang_badan_cm . ' cm' : '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Panjang Ekor:</span>
                                <span class="font-semibold text-slate-900">{{ $census->panjang_ekor_cm ? $census->panjang_ekor_cm . ' cm' : '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Jarak ke Sumber Pakan:</span>
                                <span class="font-semibold text-slate-900">{{ $census->jarak_pakan ? $census->jarak_pakan . ' Meter' : '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Jenis Sumber Pakan:</span>
                                <span class="font-semibold text-slate-900">{{ $census->display_jenis_pakan }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">Ancaman Lingkungan:</span>
                                <span class="font-semibold text-slate-900">{{ $census->display_ancaman }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Field Notes -->
                @if($census->catatan)
                    <div class="bg-amber-50/70 border border-amber-200 p-4 rounded-xl text-xs text-amber-900 space-y-1">
                        <strong class="font-bold block">Catatan Lapangan Surveyor:</strong>
                        <p class="leading-relaxed">{{ $census->catatan }}</p>
                    </div>
                @endif

                <!-- Metadata Footer -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-200 pt-4 text-xs text-slate-500">
                    <div>
                        Surveyor: <strong class="text-slate-800">{{ $census->volunteer->name ?? 'Relawan' }}</strong>
                    </div>
                    <div>
                        Waktu Sensus: <strong class="text-slate-800">{{ $census->created_at->format('d F Y, H:i') }} WIB</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
