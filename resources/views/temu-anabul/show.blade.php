<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('temu-anabul.index') }}"
                    class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                    title="Kembali ke Temu Anabul">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-outfit font-black text-slate-900 tracking-tight">{{ $temuAnabul->cat_name }}</h1>
                        @if($temuAnabul->isReunited())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                ✨ Sudah Reuni
                            </span>
                        @elseif($temuAnabul->isLost())
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                🚨 Kucing Hilang
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                🐱 Kucing Ditemukan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Dilaporkan oleh <span class="font-semibold text-slate-700">{{ $temuAnabul->contact_name }}</span>
                        pada {{ $temuAnabul->created_at->translatedFormat('d F Y, H:i') }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($temuAnabul->isLost())
                    <a href="{{ route('temu-anabul.poster', $temuAnabul->id) }}" target="_blank"
                        class="button-secondary min-h-[40px] px-3.5 text-xs font-bold flex items-center gap-1.5">
                        <span>📄</span>
                        <span>Cetak Poster Pencarian</span>
                    </a>
                @endif

                @if($temuAnabul->canEditBy(Auth::user()))
                    <a href="{{ route('temu-anabul.edit', $temuAnabul->id) }}"
                        class="button-secondary min-h-[40px] px-3.5 text-xs font-bold">
                        ✏️ Edit
                    </a>

                    @if(!$temuAnabul->isReunited())
                        <button type="button" @click="openReunionModal = true"
                            class="button-primary min-h-[40px] px-3.5 text-xs font-bold bg-emerald-700 hover:bg-emerald-800">
                            ✨ Tandai Sudah Reuni
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-6 sm:py-8" x-data="{ openSightingModal: false, openReunionModal: false, activePhoto: '{{ $temuAnabul->thumbnail_url }}' }">
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

            <!-- Status Banner -->
            @if($temuAnabul->isReunited())
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl sm:text-3xl">🎉</span>
                        <div>
                            <h3 class="font-outfit font-black text-base sm:text-lg">Kabar Gembira: Anabul Berhasil Reuni</h3>
                            <p class="text-xs sm:text-sm text-emerald-100 mt-0.5">
                                {{ $temuAnabul->reunion_notes ?: 'Anabul telah kembali berkumpul bersama pemiliknya dengan selamat.' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-bold text-white shrink-0 self-start sm:self-auto">
                        Selesai pada {{ $temuAnabul->reunited_at ? $temuAnabul->reunited_at->translatedFormat('d M Y') : 'Baru saja' }}
                    </span>
                </div>
            @elseif($temuAnabul->isLost())
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-rose-600 to-red-700 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl sm:text-3xl">🚨</span>
                        <div>
                            <h3 class="font-outfit font-black text-base sm:text-lg">Kucing Hilang dalam Pencarian Komunitas</h3>
                            <p class="text-xs sm:text-sm text-rose-100 mt-0.5">
                                Terakhir terlihat pada {{ $temuAnabul->incident_date ? $temuAnabul->incident_date->translatedFormat('d F Y, H:i') : '-' }} di {{ $temuAnabul->city ?: 'lokasi' }}.
                                @if($temuAnabul->reward_info)
                                    <span class="font-bold underline ml-1">Hadiah/Imbalan: {{ $temuAnabul->reward_info }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @auth
                        <button type="button" @click="openSightingModal = true"
                            class="px-4 py-2 bg-white text-rose-700 hover:bg-rose-50 rounded-xl font-bold text-xs shadow-xs transition shrink-0 self-start sm:self-auto">
                            👀 Saya Pernah Melihat Kucing Ini
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 bg-white text-rose-700 hover:bg-rose-50 rounded-xl font-bold text-xs shadow-xs transition shrink-0 self-start sm:self-auto"
                            title="Masuk sebagai member untuk mengirim info penampakan">
                            👀 Saya Pernah Melihat Kucing Ini (Masuk Member)
                        </a>
                    @endauth
                </div>
            @else
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-700 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl sm:text-3xl">🐱</span>
                        <div>
                            <h3 class="font-outfit font-black text-base sm:text-lg">Kucing Ditemukan Mencari Pemilik Asli</h3>
                            <p class="text-xs sm:text-sm text-indigo-100 mt-0.5">
                                Ditemukan pada {{ $temuAnabul->incident_date ? $temuAnabul->incident_date->translatedFormat('d F Y, H:i') : '-' }}.
                            </p>
                        </div>
                    </div>
                    @auth
                        <button type="button" @click="openSightingModal = true"
                            class="px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 rounded-xl font-bold text-xs shadow-xs transition shrink-0 self-start sm:self-auto">
                            💬 Hubungi / Beri Kabar
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 rounded-xl font-bold text-xs shadow-xs transition shrink-0 self-start sm:self-auto"
                            title="Masuk sebagai member untuk memberi kabar">
                            💬 Hubungi / Beri Kabar (Masuk Member)
                        </a>
                    @endauth
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left Column: Photos & Contact (5 cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-xs space-y-3">
                        <div class="relative aspect-4/3 rounded-xl overflow-hidden bg-slate-100">
                            <img :src="activePhoto" alt="{{ $temuAnabul->cat_name }}" class="w-full h-full object-cover">
                        </div>

                        <!-- Thumbnails -->
                        @if($temuAnabul->photos->count() > 1)
                            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                                @foreach($temuAnabul->photos as $photo)
                                    <button type="button" @click="activePhoto = '{{ $photo->url }}'"
                                        class="w-16 h-16 rounded-lg overflow-hidden border-2 shrink-0 transition"
                                        :class="activePhoto === '{{ $photo->url }}' ? 'border-teal-700 ring-2 ring-teal-200' : 'border-slate-200 opacity-70 hover:opacity-100'">
                                        <img src="{{ $photo->url }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Contact Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
                        <h3 class="font-outfit font-bold text-sm text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📞</span> Kontak Pelapor
                        </h3>

                        <div class="text-xs space-y-1">
                            <div class="font-bold text-slate-800 text-sm">{{ $temuAnabul->contact_name }}</div>
                            <div class="text-slate-500">Nomor: {{ $temuAnabul->is_contact_public ? $temuAnabul->contact_phone : 'Tersimpan (Privasi)' }}</div>
                        </div>

                        @if($temuAnabul->is_contact_public && $temuAnabul->whatsapp_url)
                            <div class="pt-2">
                                <a href="{{ $temuAnabul->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-full button-primary min-h-[44px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs">
                                    <span class="text-base">💬</span>
                                    <span>Hubungi Langsung via WhatsApp</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Details & Map (7 cols) -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Physical Features Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                        <div>
                            <span class="text-xs font-bold text-teal-700 uppercase tracking-wider">Judul Laporan</span>
                            <h2 class="text-base sm:text-lg font-outfit font-bold text-slate-900 mt-0.5">
                                {{ $temuAnabul->title }}
                            </h2>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 text-xs pt-2 border-t border-slate-100">
                            <div>
                                <span class="text-slate-400 block mb-0.5">Jenis Kelamin</span>
                                <span class="font-bold text-slate-800 capitalize">
                                    {{ $temuAnabul->gender === 'jantan' ? '♂ Jantan' : ($temuAnabul->gender === 'betina' ? '♀ Betina' : 'Belum Diketahui') }}
                                </span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Perkiraan Usia</span>
                                <span class="font-bold text-slate-800">{{ $temuAnabul->age_estimate ?: '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Warna Bulu</span>
                                <span class="font-bold text-slate-800">{{ $temuAnabul->coat_color ?: '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Pola Bulu</span>
                                <span class="font-bold text-slate-800">{{ $temuAnabul->coat_pattern ?: '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Kondisi Fisik</span>
                                <span class="font-bold text-slate-800 capitalize">{{ str_replace('_', ' ', $temuAnabul->condition) }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 block mb-0.5">Status Laporan</span>
                                <span class="font-bold text-slate-800">{{ $temuAnabul->status_label }}</span>
                            </div>
                        </div>

                        <!-- Ciri Khas -->
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-xs font-bold text-slate-500 block mb-1">Ciri Khas Fisik Spesifik:</span>
                            <p class="text-xs sm:text-sm text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-200/80 leading-relaxed font-medium">
                                {{ $temuAnabul->distinctive_features }}
                            </p>
                        </div>

                        @if($temuAnabul->notes)
                            <div class="pt-2">
                                <span class="text-xs font-bold text-slate-500 block mb-1">Catatan Kronologi:</span>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $temuAnabul->notes }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Spatial GIS Location Map Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-outfit font-bold text-sm text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📍</span> Lokasi Kejadian di Peta (GIS)
                            </h3>
                            @if($temuAnabul->isLost())
                                <span class="text-xs text-rose-600 font-semibold">
                                    Radius Pencarian: {{ $temuAnabul->search_radius_km }} km
                                </span>
                            @endif
                        </div>

                        <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                            <div id="temu-detail-map" style="height: 220px; width: 100%;"></div>
                        </div>

                        <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                            <span class="font-semibold text-slate-700">Alamat Kejadian:</span>
                            {{ $temuAnabul->formatted_address ?: ($temuAnabul->village . ', ' . $temuAnabul->district . ', ' . $temuAnabul->city . ', ' . $temuAnabul->province) }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- SMART MATCHING SECTION (Rekomendasi Pencocokan Cerdas) -->
            @if(isset($potentialMatches) && $potentialMatches->isNotEmpty())
                <div class="bg-gradient-to-br from-teal-50/70 via-sky-50/40 to-slate-50 rounded-2xl border border-teal-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">✨</span>
                        <div>
                            <h3 class="font-outfit font-black text-slate-900 text-base leading-tight">
                                Rekomendasi Pencocokan Cerdas (Smart Matching)
                            </h3>
                            <p class="text-xs text-slate-600">
                                Sistem menemukan anabul dengan ciri atau wilayah yang berdekatan. Silakan periksa apakah anabul ini cocok:
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($potentialMatches as $match)
                            <a href="{{ route('temu-anabul.show', $match->id) }}"
                                class="bg-white rounded-xl border border-slate-200 p-3 hover:shadow-md transition flex flex-col justify-between">
                                <div>
                                    <div class="relative aspect-video rounded-lg overflow-hidden bg-slate-100 mb-2">
                                        <img src="{{ $match->thumbnail_url }}" alt="{{ $match->cat_name }}" class="w-full h-full object-cover">
                                        <span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded text-[9px] font-bold text-white {{ $match->isLost() ? 'bg-rose-600' : 'bg-indigo-600' }}">
                                            {{ $match->type_label }}
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-xs text-slate-900 leading-tight">{{ $match->cat_name }}</h4>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $match->title }}</p>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        📍 {{ $match->city ?: $match->province }}
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-teal-700 mt-2 block">
                                    Lihat Kecocokan &rarr;
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- COMMUNITY SIGHTINGS TIMELINE (Kesaksian & Jejak Penampakan) -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                            <span>👀</span> Jejak Penampakan & Kesaksian Komunitas ({{ $temuAnabul->sightings->count() }})
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Informasi dari anggota komunitas yang pernah melihat kucing mirip ini di jalan atau sekitarnya.
                        </p>
                    </div>

                    @auth
                        <button type="button" @click="openSightingModal = true"
                            class="button-primary min-h-[38px] px-3.5 py-1.5 text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto">
                            <span>+</span>
                            <span>Saya Melihat Kucing Ini</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="button-primary min-h-[38px] px-3.5 py-1.5 text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto">
                            <span>+</span>
                            <span>Saya Melihat Kucing Ini (Masuk Member)</span>
                        </a>
                    @endauth
                </div>

                @if($temuAnabul->sightings->isEmpty())
                    <div class="p-8 text-center bg-slate-50/70 rounded-xl border border-dashed border-slate-200 space-y-2">
                        <span class="text-2xl">🐾</span>
                        <p class="text-xs sm:text-sm font-semibold text-slate-700">Belum Ada Informasi Penampakan</p>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Pernah berpapasan atau melihat kucing mirip ini? Laporkan titik penampakan untuk membantu pemilik.
                        </p>
                        @auth
                            <button type="button" @click="openSightingModal = true" class="button-secondary min-h-[36px] px-4 text-xs font-bold mt-2">
                                + Kirim Info Penampakan
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="button-secondary min-h-[36px] px-4 text-xs font-bold mt-2 inline-flex items-center">
                                + Masuk Member untuk Beri Info
                            </a>
                        @endauth
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($temuAnabul->sightings as $sighting)
                            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-800">
                                            {{ $sighting->user->name ?? 'Member Komunitas' }}
                                        </span>
                                        @if($sighting->location_name)
                                            <span class="text-[11px] text-slate-500">
                                                di <span class="font-medium text-slate-700">{{ $sighting->location_name }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-400">
                                        {{ $sighting->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                    {{ $sighting->notes }}
                                </p>

                                @if($sighting->photo_url)
                                    <div class="pt-1">
                                        <img src="{{ $sighting->photo_url }}" alt="Foto Penampakan" class="w-24 h-24 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- MODAL: Kirim Info Penampakan (Sighting Tip) -->
        <div x-show="openSightingModal" x-cloak
             @keydown.escape.window="openSightingModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            
            <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-5 sm:p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="openSightingModal = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-outfit font-bold text-base text-slate-900">
                        Kirim Informasi Penampakan Anabul
                    </h3>
                    <button type="button" @click="openSightingModal = false" class="text-slate-400 hover:text-slate-700 text-lg leading-none">&times;</button>
                </div>

                @auth
                    <form action="{{ route('temu-anabul.sighting', $temuAnabul->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="form-label">Keterangan Penampakan <span class="text-rose-600">*</span></label>
                            <textarea name="notes" rows="3" required
                                placeholder="Ceritakan kapan dan di mana Anda melihatnya, serta kondisinya saat itu..."
                                class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                        </div>

                        <div>
                            <label class="form-label">Nama Tempat / Patokan Lokasi</label>
                            <input type="text" name="location_name" placeholder="Contoh: Depan Minimarket Jalan Kaliurang KM 5" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Waktu Melihat</label>
                            <input type="datetime-local" name="sighting_time" value="{{ now()->format('Y-m-d\TH:i') }}" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Nomor Kontak Anda (Bila Pemilik Ingin Bertanya Langsung)</label>
                            <input type="text" name="contact_phone" value="{{ Auth::user()?->phone }}" placeholder="Contoh: 08123456789" class="form-input text-xs sm:text-sm">
                        </div>

                        <div>
                            <label class="form-label">Foto Penampakan (Opsional)</label>
                            <input type="file" name="photo" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="openSightingModal = false" class="button-secondary min-h-[40px] px-4 text-xs">
                                Batal
                            </button>
                            <button type="submit" class="button-primary min-h-[40px] px-5 text-xs font-bold">
                                Kirim Informasi
                            </button>
                        </div>
                    </form>
                @else
                    <div class="py-6 text-center space-y-3">
                        <span class="text-3xl">🔒</span>
                        <div class="font-bold text-sm text-slate-800">Masuk sebagai Member untuk Mengirim Informasi</div>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Untuk menjaga keaslian data penampakan, pelaporan informasi penampakan hanya dapat dilakukan oleh member terdaftar.
                        </p>
                        <div class="pt-2 flex items-center justify-center gap-2">
                            <button type="button" @click="openSightingModal = false" class="button-secondary min-h-[40px] px-4 text-xs">
                                Tutup
                            </button>
                            <a href="{{ route('login') }}" class="button-primary min-h-[40px] px-5 text-xs font-bold inline-flex items-center">
                                Masuk Sekarang
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- MODAL: Tandai Sudah Reuni -->
        <div x-show="openReunionModal" x-cloak
             @keydown.escape.window="openReunionModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            
            <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-5 sm:p-6 shadow-xl space-y-4"
                 @click.outside="openReunionModal = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                        <span>✨</span> Tandai Anabul Sudah Reuni
                    </h3>
                    <button type="button" @click="openReunionModal = false" class="text-slate-400 hover:text-slate-700 text-lg leading-none">&times;</button>
                </div>

                <form action="{{ route('temu-anabul.reunited', $temuAnabul->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="form-label">Cerita / Catatan Reuni</label>
                        <textarea name="reunion_notes" rows="3"
                            placeholder="Alhamdulillah anabul telah berhasil kembali dan berkumpul bersama pemilik..."
                            class="form-input text-xs sm:text-sm leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="form-label">Foto Bersama Anabul (Opsional)</label>
                        <input type="file" name="reunion_photo" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openReunionModal = false" class="button-secondary min-h-[40px] px-4 text-xs">
                            Batal
                        </button>
                        <button type="submit" class="button-primary min-h-[40px] px-5 text-xs font-bold bg-emerald-700 hover:bg-emerald-800">
                            Simpan & Selesaikan Laporan
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
            const lat = {{ $temuAnabul->latitude }};
            const lng = {{ $temuAnabul->longitude }};
            const isLost = {{ $temuAnabul->isLost() ? 'true' : 'false' }};
            const searchRadiusKm = {{ $temuAnabul->search_radius_km ?? 3.0 }};

            const map = L.map('temu-detail-map', {
                zoomControl: false,
                scrollWheelZoom: false
            }).setView([lat, lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap | KucingMU'
            }).addTo(map);

            const pinColor = isLost ? '#e11d48' : '#7c3aed';
            const badgeEmoji = isLost ? '🚨' : '🐱';

            const customPin = L.divIcon({
                className: 'single-temu-pin',
                html: `
                    <div style="position:relative; width:40px; height:40px;">
                        <img src="{{ $temuAnabul->thumbnail_url }}" style="width:40px; height:40px; border-radius:50%; border:3px solid ${pinColor}; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                        <span style="position:absolute; bottom:-3px; right:-3px; width:18px; height:18px; border-radius:50%; background:${pinColor}; color:white; font-size:10px; display:flex; align-items:center; justify-content:center; border:1.5px solid white;">${badgeEmoji}</span>
                    </div>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            L.marker([lat, lng], { icon: customPin }).addTo(map);

            if (isLost && searchRadiusKm) {
                L.circle([lat, lng], {
                    radius: searchRadiusKm * 1000,
                    color: '#e11d48',
                    fillColor: '#e11d48',
                    fillOpacity: 0.1,
                    dashArray: '4, 4',
                    weight: 1.5
                }).addTo(map);
            }
        });
    </script>
</x-app-layout>

