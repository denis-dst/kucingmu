<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('peta-anabul.index') }}"
                    class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                    title="Kembali ke Peta Anabul">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-900 tracking-tight">{{ $petaAnabul->name }}</h1>
                        <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold">
                            {{ $petaAnabul->unique_code }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Dipetakan oleh <span class="font-semibold text-slate-700">{{ $petaAnabul->user->name ?? 'Member KucingMU' }}</span>
                        pada {{ $petaAnabul->created_at->translatedFormat('d F Y, H:i') }}
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                @if($petaAnabul->canEditBy(Auth::user()))
                    <a href="{{ route('peta-anabul.edit', $petaAnabul->id) }}"
                        class="button-secondary min-h-[40px] px-3.5 text-xs font-bold">
                        ✏️ Edit Data
                    </a>
                @endif

                @auth
                    <button type="button" @click="openUpdateModal = true"
                        class="button-primary min-h-[40px] px-4 text-xs font-bold shadow-sm">
                        + Beri Kabar / Update Info
                    </button>
                @else
                    <a href="{{ route('login') }}"
                        class="button-primary min-h-[40px] px-4 text-xs font-bold shadow-sm"
                        title="Masuk akun member untuk menambahkan pembaruan">
                        + Beri Kabar (Masuk Member)
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="{ openUpdateModal: false, activePhoto: '{{ $petaAnabul->thumbnail_url }}' }">
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

            <!-- Top Grid: Photos & Main Info -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left Column: Photo Gallery (5 cols) -->
                <div class="lg:col-span-5 space-y-3">
                    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-xs overflow-hidden">
                        <!-- Main large photo view -->
                        <div class="relative aspect-4/3 rounded-xl overflow-hidden bg-slate-100">
                            <img :src="activePhoto" alt="{{ $petaAnabul->name }}" class="w-full h-full object-cover">
                            <!-- Category Badge -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                @if($petaAnabul->isPet())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-teal-700 text-white shadow-xs">
                                        🏠 Kucing Peliharaan Member
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-600 text-white shadow-xs">
                                        🐾 Kucing Liar / Sekitar
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Gallery Thumbnails -->
                        @if($petaAnabul->photos->count() > 1)
                            <div class="flex items-center gap-2 pt-3 overflow-x-auto">
                                @foreach($petaAnabul->photos as $photo)
                                    <button type="button" @click="activePhoto = '{{ $photo->url }}'"
                                        class="w-16 h-16 rounded-lg overflow-hidden border-2 shrink-0 transition"
                                        :class="activePhoto === '{{ $photo->url }}' ? 'border-teal-700 ring-2 ring-teal-200' : 'border-slate-200 opacity-70 hover:opacity-100'">
                                        <img src="{{ $photo->url }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Condition Status Box -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-2">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status & Kondisi Terkini</div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $petaAnabul->condition_badge_class }}">
                                Kondisi: {{ $petaAnabul->condition_label }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $petaAnabul->status_label }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Steril: {{ $petaAnabul->sterilization_status === 'sudah' ? 'Sudah Disteril' : ($petaAnabul->sterilization_status === 'belum' ? 'Belum Disteril' : 'Belum Diketahui') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details & Information (7 cols) -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Physical Traits Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                        <h2 class="text-base font-outfit font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                            <span>🔍</span> Ciri Fisik & Profil Anabul
                        </h2>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block mb-0.5">Jenis Kelamin</span>
                                <span class="font-bold text-slate-800 capitalize">
                                    {{ $petaAnabul->gender === 'jantan' ? '♂ Jantan' : ($petaAnabul->gender === 'betina' ? '♀ Betina' : 'Belum Diketahui') }}
                                </span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Perkiraan Usia</span>
                                <span class="font-bold text-slate-800">{{ $petaAnabul->age_estimate ?: 'Tidak dicatat' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Warna Bulu</span>
                                <span class="font-bold text-slate-800">{{ $petaAnabul->coat_color ?: '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Pola Bulu</span>
                                <span class="font-bold text-slate-800">{{ $petaAnabul->coat_pattern ?: '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Terakhir Terlihat</span>
                                <span class="font-bold text-slate-800">
                                    {{ $petaAnabul->last_seen_at ? $petaAnabul->last_seen_at->translatedFormat('d M Y, H:i') : '-' }}
                                </span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Kode Registrasi</span>
                                <span class="font-mono font-bold text-teal-800">{{ $petaAnabul->unique_code }}</span>
                            </div>
                        </div>

                        <!-- Special Characteristics -->
                        @if($petaAnabul->characteristics)
                            <div class="pt-3 border-t border-slate-100">
                                <span class="text-xs font-bold text-slate-500 block mb-1">Ciri Khas Fisik Khusus:</span>
                                <p class="text-xs sm:text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200/80 leading-relaxed">
                                    {{ $petaAnabul->characteristics }}
                                </p>
                            </div>
                        @endif

                        <!-- Description Notes -->
                        @if($petaAnabul->description)
                            <div class="pt-2">
                                <span class="text-xs font-bold text-slate-500 block mb-1">Catatan Tambahan:</span>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $petaAnabul->description }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Spatial Location Map Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-outfit font-bold text-slate-800 flex items-center gap-2">
                                <span>📍</span> Lokasi Peta (GIS)
                            </h2>
                            <span class="text-xs text-slate-400 font-mono">
                                {{ number_format($petaAnabul->latitude, 4) }}, {{ number_format($petaAnabul->longitude, 4) }}
                            </span>
                        </div>

                        <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                            <div id="cat-location-map" style="height: 220px; width: 100%;"></div>
                        </div>

                        <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                            <span class="font-semibold text-slate-700">Alamat Terdata:</span>
                            {{ $petaAnabul->formatted_address ?: ($petaAnabul->village . ', ' . $petaAnabul->district . ', ' . $petaAnabul->city . ', ' . $petaAnabul->province) }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Section: Community Updates & Sightings Timeline -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-outfit font-bold text-slate-900 flex items-center gap-2">
                            <span>💬</span> Riwayat Pembaruan & Kabar Komunitas
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Catatan kondisi terbaru, penampakan, dan riwayat penanganan oleh relawan serta member KucingMU.
                        </p>
                    </div>
                    <button type="button" @click="openUpdateModal = true"
                        class="button-secondary min-h-[38px] px-3.5 py-1.5 text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto">
                        <span>✍️</span>
                        <span>Tambah Kabar Anabul</span>
                    </button>
                </div>

                @if($petaAnabul->updates->isEmpty())
                    <div class="p-8 text-center bg-slate-50/70 rounded-xl border border-dashed border-slate-200 space-y-2">
                        <span class="text-2xl">📝</span>
                        <p class="text-xs sm:text-sm font-semibold text-slate-700">Belum Ada Riwayat Pembaruan</p>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Pernah melihat kucing ini atau tahu perkembangannya? Berikan informasi terbaru untuk membantu komunitas.
                        </p>
                        <button type="button" @click="openUpdateModal = true" class="button-primary min-h-[36px] px-4 text-xs font-bold mt-2">
                            + Kirim Kabar Pertama
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($petaAnabul->updates as $update)
                            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $update->type_badge_class }}">
                                            {{ $update->type_label }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-800">
                                            {{ $update->user->name ?? 'Member Komunitas' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">
                                        {{ $update->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                    {{ $update->notes }}
                                </p>

                                @if($update->photo_url)
                                    <div class="pt-1">
                                        <img src="{{ $update->photo_url }}" alt="Foto Pembaruan" class="w-24 h-24 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Nearby Mapped Cats Section -->
            @if(isset($nearbyCats) && $nearbyCats->isNotEmpty())
                <div class="space-y-3">
                    <h3 class="text-sm font-outfit font-bold text-slate-800 uppercase tracking-wider">
                        🐾 Anabul Lain di Sekitar Radius 10 km
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($nearbyCats as $near)
                            <a href="{{ route('peta-anabul.show', $near->id) }}"
                                class="bg-white rounded-xl border border-slate-200 p-2.5 hover:shadow-sm transition flex items-center gap-2.5">
                                <img src="{{ $near->thumbnail_url }}" alt="{{ $near->name }}" class="w-12 h-12 rounded-lg object-cover shrink-0">
                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-slate-800 truncate">{{ $near->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ round($near->distance_km, 1) }} km &bull; {{ $near->ownership_label }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- MODAL: Tambah Kabar / Update Info Anabul -->
        <div x-show="openUpdateModal" x-cloak
             @keydown.escape.window="openUpdateModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            
            <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-5 sm:p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="openUpdateModal = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-outfit font-bold text-base text-slate-900">
                        Tambah Kabar / Update Anabul
                    </h3>
                    <button type="button" @click="openUpdateModal = false" class="text-slate-400 hover:text-slate-700 text-lg leading-none">&times;</button>
                </div>

                <form action="{{ route('peta-anabul.update-info', $petaAnabul->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="form-label">Jenis Pembaruan / Kontribusi <span class="text-rose-600">*</span></label>
                        <select name="update_type" required class="form-input text-xs sm:text-sm">
                            <option value="sighting">👀 Informasi Penampakan Terbaru</option>
                            <option value="condition_update">🩺 Pembaruan Kondisi Kesehatan</option>
                            <option value="handled">✅ Sudah Ditangani / Diselamatkan</option>
                            <option value="not_seen">❓ Sudah Tidak Terlihat Lagi di Lokasi</option>
                            <option value="correction">✏️ Koreksi Ciri Fisik atau Posisi Lokasi</option>
                            <option value="duplicate_report">⚠️ Laporan Data Duplikat</option>
                            <option value="report_inappropriate">🚨 Laporan Konten Tidak Sesuai</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Perbarui Kondisi (Opsional)</label>
                        <select name="condition" class="form-input text-xs sm:text-sm">
                            <option value="">- Tetap seperti data saat ini -</option>
                            <option value="sehat">Sehat</option>
                            <option value="sakit">Sakit</option>
                            <option value="terluka">Terluka</option>
                            <option value="butuh_pertolongan">Butuh Pertolongan Segera</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Keterangan / Catatan Anda <span class="text-rose-600">*</span></label>
                        <textarea name="notes" rows="3" required
                            placeholder="Tuliskan keterangan detail mengenai apa yang Anda lihat atau tindakan yang telah dilakukan..."
                            class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="form-label">Foto Tambahan (Opsional)</label>
                        <input type="file" name="photo" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openUpdateModal = false" class="button-secondary min-h-[40px] px-4 text-xs">
                            Batal
                        </button>
                        <button type="submit" class="button-primary min-h-[40px] px-5 text-xs font-bold">
                            Kirim Kabar Komunitas
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lat = {{ $petaAnabul->latitude }};
            const lng = {{ $petaAnabul->longitude }};
            const isPet = {{ $petaAnabul->isPet() ? 'true' : 'false' }};

            const map = L.map('cat-location-map', {
                zoomControl: false,
                scrollWheelZoom: false
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap | KucingMU'
            }).addTo(map);

            const badgeEmoji = isPet ? '🏠' : '🐾';
            const markerClass = isPet ? 'border-teal-700 shadow-teal-500/30' : 'border-orange-600 shadow-orange-500/30';

            const customPin = L.divIcon({
                className: 'single-cat-pin',
                html: `
                    <div style="position:relative; width:40px; height:40px;">
                        <img src="{{ $petaAnabul->thumbnail_url }}" style="width:40px; height:40px; border-radius:50%; border:3px solid ${isPet ? '#0f766e' : '#ea580c'}; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                        <span style="position:absolute; bottom:-3px; right:-3px; width:18px; height:18px; border-radius:50%; background:${isPet ? '#0f766e' : '#ea580c'}; color:white; font-size:10px; display:flex; align-items:center; justify-content:center; border:1.5px solid white;">${badgeEmoji}</span>
                    </div>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            L.marker([lat, lng], { icon: customPin }).addTo(map);
        });
    </script>
</x-app-layout>

