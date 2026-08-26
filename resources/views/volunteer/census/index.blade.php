<x-app-layout>
    <div class="py-8" x-data="{ selectedCensus: null, showDetailModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Modul Pengambilan Data Lapangan</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Sensus Stray Cat PTMA
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Pengambilan data sensus kucing liar berbasis klaster kampus PTMA (UMY, UAD, UMP, UMS). Mencakup identifikasi morfometri, indeks kesejahteraan (BCS & lesi klinis), hingga analisis mikro-habitat.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('volunteer.census.scan') }}" class="button-primary px-5 py-2.5 inline-flex items-center gap-2 text-xs font-bold shadow-md bg-teal-800 hover:bg-teal-900">
                            <span>🔍</span> Pindai / Scan Wajah Kucing
                        </a>
                        <a href="{{ route('volunteer.census.create') }}" class="button-secondary px-5 py-2.5 inline-flex items-center gap-2 text-xs font-semibold bg-white">
                            <span>➕</span> Input Sensus Baru
                        </a>
                        <a href="{{ route('volunteer.census.export') }}" class="button-secondary px-5 py-2.5 inline-flex items-center gap-2 text-xs font-semibold bg-white">
                            <span>📥</span> Unduh Semua Data (CSV)
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    📊
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-6">
                <div class="content-card bg-white p-4 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Sensus</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ $stats['total'] }}</span>
                        <span class="text-[11px] font-semibold text-teal-700">Ekor</span>
                    </div>
                </div>

                <div class="content-card bg-white p-4 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klaster UMY</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['umy'] }}</span>
                        <span class="text-[11px] font-semibold text-slate-500">Ekor</span>
                    </div>
                </div>

                <div class="content-card bg-white p-4 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klaster UAD</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['uad'] }}</span>
                        <span class="text-[11px] font-semibold text-slate-500">Ekor</span>
                    </div>
                </div>

                <div class="content-card bg-white p-4 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klaster UMP</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['ump'] }}</span>
                        <span class="text-[11px] font-semibold text-slate-500">Ekor</span>
                    </div>
                </div>

                <div class="content-card bg-white p-4 border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klaster UMS</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-800">{{ $stats['ums'] }}</span>
                        <span class="text-[11px] font-semibold text-slate-500">Ekor</span>
                    </div>
                </div>

                <div class="content-card bg-teal-50/70 p-4 border border-teal-200">
                    <span class="text-[10px] font-bold text-teal-800 uppercase tracking-wider block">Input Anda</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-950">{{ $stats['my_submissions'] }}</span>
                        <span class="text-[11px] font-semibold text-teal-700">Data</span>
                    </div>
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs font-semibold flex items-center gap-2" role="alert">
                    <span aria-hidden="true">✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Main Content Card & Filter -->
            <div class="content-card space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-outfit text-lg font-bold text-slate-900">Daftar Data Sensus Kucing PTMA</h2>
                        <p class="text-xs text-slate-500">Menampilkan seluruh rekaman identifikasi lapangan yang telah dihimpun.</p>
                    </div>

                    <!-- Search and Campus Filter -->
                    <form method="GET" action="{{ route('volunteer.census.index') }}" class="flex flex-wrap items-center gap-2">
                        <select name="kampus" class="form-input text-xs py-1.5 px-3 min-h-[38px] w-auto bg-slate-50" onchange="this.form.submit()">
                            <option value="">Semua Kampus PTMA</option>
                            <option value="UMY" @selected(request('kampus') === 'UMY')>UMY</option>
                            <option value="UAD" @selected(request('kampus') === 'UAD')>UAD</option>
                            <option value="UMP" @selected(request('kampus') === 'UMP')>UMP</option>
                            <option value="UMS" @selected(request('kampus') === 'UMS')>UMS</option>
                            <option value="Lainnya" @selected(request('kampus') === 'Lainnya')>Lainnya</option>
                        </select>

                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID / Zona / Warna..." class="form-input text-xs py-1.5 px-3 pr-8 min-h-[38px] w-48 sm:w-60">
                            @if(request('search'))
                                <a href="{{ route('volunteer.census.index', ['kampus' => request('kampus')]) }}" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">✕</a>
                            @endif
                        </div>

                        <button type="submit" class="button-primary text-xs font-semibold px-3 py-1.5 min-h-[38px]">
                            Cari
                        </button>
                    </form>
                </div>

                <!-- Table Content -->
                @if($censuses->isEmpty())
                    <div class="text-center py-12 text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <span class="text-3xl block mb-2">📋</span>
                        <h3 class="font-bold text-sm text-slate-800">Belum ada data sensus kucing</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Klik tombol "Input Sensus Baru" untuk mencatat data stray cat pada area kampus Anda.</p>
                        <div class="mt-4">
                            <a href="{{ route('volunteer.census.create') }}" class="button-primary text-xs font-semibold px-4 py-2 min-h-[38px]">
                                Input Sensus Pertama
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs" aria-label="Tabel Sensus Kucing PTMA">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 font-bold bg-slate-50/50">
                                    <th class="py-3 px-3">Foto / ID Kucing</th>
                                    <th class="py-3 px-3">Kampus & Zona</th>
                                    <th class="py-3 px-3">Usia & Gender</th>
                                    <th class="py-3 px-3">Pola Warna</th>
                                    <th class="py-3 px-3">BCS & Klinis</th>
                                    <th class="py-3 px-3">Morfometri</th>
                                    <th class="py-3 px-3">Pakan & Habitat</th>
                                    <th class="py-3 px-3">Waktu / Surveyor</th>
                                    <th class="py-3 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($censuses as $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <!-- Foto / ID -->
                                        <td class="py-3 px-3">
                                            <div class="flex items-center gap-2.5">
                                                @if($item->foto_wajah_url)
                                                    <img src="{{ $item->foto_wajah_url }}" alt="{{ $item->id_kucing }}" class="w-11 h-11 object-cover rounded-lg border border-slate-200 shadow-xs flex-shrink-0 cursor-pointer" @click="selectedCensus = {{ $item->toJson() }}; showDetailModal = true">
                                                @elseif($item->foto_atas_url)
                                                    <img src="{{ $item->foto_atas_url }}" alt="{{ $item->id_kucing }}" class="w-11 h-11 object-cover rounded-lg border border-slate-200 shadow-xs flex-shrink-0 cursor-pointer" @click="selectedCensus = {{ $item->toJson() }}; showDetailModal = true">
                                                @else
                                                    <div class="w-11 h-11 rounded-lg bg-teal-50 text-teal-800 font-bold flex items-center justify-center text-xs border border-teal-100 flex-shrink-0">
                                                        🐾
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="font-mono font-bold text-slate-900 block text-xs cursor-pointer hover:text-teal-700" @click="selectedCensus = {{ $item->toJson() }}; showDetailModal = true">
                                                        {{ $item->id_kucing }}
                                                    </span>
                                                    @if($item->latitude && $item->longitude)
                                                        <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="text-[10px] text-teal-700 font-medium hover:underline inline-flex items-center gap-0.5 mt-0.5">
                                                            <span>📍</span> GPS Map
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Kampus & Zona -->
                                        <td class="py-3 px-3">
                                            <span class="font-bold text-slate-900 block">{{ $item->display_kampus }}</span>
                                            <span class="text-[11px] text-slate-500">{{ $item->zona }}</span>
                                        </td>

                                        <!-- Usia & Gender -->
                                        <td class="py-3 px-3">
                                            <span class="font-semibold text-slate-800 block">{{ $item->usia }}</span>
                                            <span class="text-[11px] text-slate-500">{{ $item->gender }}</span>
                                        </td>

                                        <!-- Pola Warna -->
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                                {{ $item->display_warna }}
                                            </span>
                                        </td>

                                        <!-- BCS & Klinis -->
                                        <td class="py-3 px-3">
                                            <span class="font-bold text-slate-900 block text-[11px]">BCS: {{ $item->bcs }}</span>
                                            <div class="flex flex-wrap gap-1 mt-0.5">
                                                @if(is_array($item->kondisi_klinis))
                                                    @foreach($item->kondisi_klinis as $k)
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $k === 'Sehat' ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                                            {{ $k }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Morfometri -->
                                        <td class="py-3 px-3 text-[11px]">
                                            <div>Badan: <strong>{{ $item->panjang_badan_cm ? $item->panjang_badan_cm . ' cm' : '-' }}</strong></div>
                                            <div class="text-slate-500">Ekor: <strong>{{ $item->panjang_ekor_cm ? $item->panjang_ekor_cm . ' cm' : '-' }}</strong></div>
                                        </td>

                                        <!-- Pakan & Habitat -->
                                        <td class="py-3 px-3 text-[11px]">
                                            <div class="font-medium text-slate-800">{{ $item->display_jenis_pakan }} ({{ $item->jarak_pakan ? $item->jarak_pakan . 'm' : '-' }})</div>
                                            <div class="text-[10px] text-slate-500">Ancaman: {{ $item->display_ancaman }}</div>
                                        </td>

                                        <!-- Waktu / Surveyor -->
                                        <td class="py-3 px-3 text-[11px]">
                                            <span class="text-slate-900 font-medium block">{{ $item->created_at->format('d M Y, H:i') }}</span>
                                            <span class="text-slate-500 text-[10px]">{{ $item->volunteer->name ?? 'Relawan' }}</span>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3 px-3 text-right">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" @click="selectedCensus = {{ $item->toJson() }}; showDetailModal = true" class="button-secondary text-[11px] font-semibold px-2 py-1 min-h-[30px]" title="Lihat Rincian">
                                                    Detail
                                                </button>
                                                <a href="{{ route('volunteer.census.edit', $item->id) }}" class="button-secondary text-[11px] font-semibold px-2 py-1 min-h-[30px]" title="Edit Data">
                                                    Ubah
                                                </a>
                                                <form action="{{ route('volunteer.census.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sensus [{{ $item->id_kucing }}]?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="button-secondary text-[11px] font-semibold px-2 py-1 min-h-[30px] text-rose-700 hover:bg-rose-50 border-rose-200" title="Hapus Data">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 border-t border-slate-100 pt-3">
                        {{ $censuses->links() }}
                    </div>
                @endif
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- DETAIL MODAL -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div x-show="showDetailModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white w-full max-w-2xl rounded-2xl border border-slate-200 overflow-hidden shadow-2xl space-y-4 p-6 max-h-[90vh] overflow-y-auto" @click.outside="showDetailModal = false">
                    <template x-if="selectedCensus">
                        <div class="space-y-4">
                            <!-- Modal Header -->
                            <div class="flex items-start justify-between border-b border-slate-200 pb-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700">Detail Sensus Stray Cat PTMA</span>
                                    <h3 class="font-outfit text-xl font-bold text-slate-900" x-text="selectedCensus.id_kucing"></h3>
                                    <p class="text-xs text-slate-500" x-text="`${selectedCensus.kampus_custom || selectedCensus.kampus} - ${selectedCensus.zona}`"></p>
                                </div>
                                <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-700 text-base font-bold p-1">✕</button>
                            </div>

                            <!-- 4 Foto Gallery -->
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Dokumentasi 4 Sudut Pandang:</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center">
                                        <span class="text-[10px] font-bold text-slate-600 block mb-1">1. Wajah</span>
                                        <template x-if="selectedCensus.foto_wajah">
                                            <img :src="`/storage/${selectedCensus.foto_wajah}`" class="w-full aspect-square object-cover rounded">
                                        </template>
                                        <template x-if="!selectedCensus.foto_wajah">
                                            <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded">-</div>
                                        </template>
                                    </div>

                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center">
                                        <span class="text-[10px] font-bold text-slate-600 block mb-1">2. Tampak Atas</span>
                                        <template x-if="selectedCensus.foto_atas">
                                            <img :src="`/storage/${selectedCensus.foto_atas}`" class="w-full aspect-square object-cover rounded">
                                        </template>
                                        <template x-if="!selectedCensus.foto_atas">
                                            <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded">-</div>
                                        </template>
                                    </div>

                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center">
                                        <span class="text-[10px] font-bold text-slate-600 block mb-1">3. Samping Kiri</span>
                                        <template x-if="selectedCensus.foto_samping_kiri">
                                            <img :src="`/storage/${selectedCensus.foto_samping_kiri}`" class="w-full aspect-square object-cover rounded">
                                        </template>
                                        <template x-if="!selectedCensus.foto_samping_kiri">
                                            <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded">-</div>
                                        </template>
                                    </div>

                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center">
                                        <span class="text-[10px] font-bold text-slate-600 block mb-1">4. Opsional</span>
                                        <template x-if="selectedCensus.foto_opsional">
                                            <img :src="`/storage/${selectedCensus.foto_opsional}`" class="w-full aspect-square object-cover rounded">
                                        </template>
                                        <template x-if="!selectedCensus.foto_opsional">
                                            <div class="w-full aspect-square flex items-center justify-center bg-slate-100 text-slate-400 text-xs rounded">-</div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
                                <div>
                                    <span class="text-slate-500 block">Usia & Gender</span>
                                    <strong class="text-slate-900" x-text="`${selectedCensus.usia} (${selectedCensus.gender})`"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Pola Warna Bulu</span>
                                    <strong class="text-slate-900" x-text="selectedCensus.warna_custom || selectedCensus.warna"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">BCS (Body Condition)</span>
                                    <strong class="text-slate-900" x-text="`Skala ${selectedCensus.bcs} / 9`"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Panjang Badan</span>
                                    <strong class="text-slate-900" x-text="selectedCensus.panjang_badan_cm ? `${selectedCensus.panjang_badan_cm} cm` : '-'"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Panjang Ekor</span>
                                    <strong class="text-slate-900" x-text="selectedCensus.panjang_ekor_cm ? `${selectedCensus.panjang_ekor_cm} cm` : '-'"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Jarak Sumber Pakan</span>
                                    <strong class="text-slate-900" x-text="selectedCensus.jarak_pakan ? `${selectedCensus.jarak_pakan} Meter` : '-'"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Jenis Pakan Utama</span>
                                    <strong class="text-slate-900" x-text="selectedCensus.jenis_pakan_custom || selectedCensus.jenis_pakan"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Ancaman Lingkungan</span>
                                    <strong class="text-slate-900" x-text="selectedCensus.ancaman_custom || selectedCensus.ancaman"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-500 block">Koordinat GPS</span>
                                    <template x-if="selectedCensus.latitude && selectedCensus.longitude">
                                        <a :href="`https://www.google.com/maps?q=${selectedCensus.latitude},${selectedCensus.longitude}`" target="_blank" class="text-teal-700 font-bold hover:underline">
                                            <span x-text="`${selectedCensus.latitude}, ${selectedCensus.longitude}`"></span> ↗
                                        </a>
                                    </template>
                                    <template x-if="!selectedCensus.latitude">
                                        <span class="text-slate-400">-</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Catatan Surveyor -->
                            <template x-if="selectedCensus.catatan">
                                <div class="bg-amber-50/70 border border-amber-200 p-3 rounded-lg text-xs text-amber-900">
                                    <strong class="block mb-0.5">Catatan Lapangan:</strong>
                                    <p x-text="selectedCensus.catatan"></p>
                                </div>
                            </template>

                            <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                                <span class="text-[11px] text-slate-500" x-text="`Dicatat pada: ${selectedCensus.created_at || '-'}`"></span>
                                <div class="flex gap-2">
                                    <a :href="`/sensus-kucing/${selectedCensus.id}/edit`" class="button-primary text-xs font-semibold px-4 py-2 min-h-[36px]">
                                        Ubah Data
                                    </a>
                                    <button type="button" @click="showDetailModal = false" class="button-secondary text-xs font-semibold px-4 py-2 min-h-[36px]">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
