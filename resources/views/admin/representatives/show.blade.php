<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.representatives.index') }}" class="button-secondary text-xs p-2 rounded-xl" title="Kembali ke Daftar">
                    &larr;
                </a>
                <div>
                    <span class="eyebrow">Verifikasi &amp; Pemrosesan Representatif</span>
                    <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">
                        {{ $representative->name }}
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                    {{ $representative->registration_number }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Leaflet CSS & JS for Admin Detail Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-center justify-between gap-3 text-emerald-900 text-xs sm:text-sm shadow-xs">
                    <div class="flex items-center gap-2 font-medium">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Left Column: Detailed Information (8 Cols) -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Candidate Summary Card -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                                <span>👤</span> Profil Calon Representatif
                            </h2>
                            <span class="text-xs text-slate-400">
                                Mendaftar pada {{ $representative->created_at->format('d F Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nama Lengkap</span>
                                <div class="font-bold text-slate-900 text-sm">{{ $representative->name }}</div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Nomor Baku Muhammadiyah (NBM)</span>
                                <div class="font-mono font-bold text-slate-900 text-sm">{{ $representative->nbm ?? '-' }}</div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Tanggal Lahir &amp; Usia</span>
                                <div class="font-bold text-slate-800">
                                    {{ $representative->birth_date ? $representative->birth_date->format('d F Y') . ' (' . $representative->birth_date->age . ' tahun)' : '-' }}
                                </div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Alamat Email</span>
                                <div>
                                    <a href="mailto:{{ $representative->email }}" class="font-bold text-teal-800 hover:underline">
                                        {{ $representative->email }}
                                    </a>
                                </div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Kontak WhatsApp</span>
                                <div>
                                    <a href="{{ $representative->whatsapp_link }}" target="_blank" class="font-bold text-emerald-700 hover:underline inline-flex items-center gap-1.5 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        <span>💬</span> Hubungi via WhatsApp ↗
                                    </a>
                                </div>
                            </div>

                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Akun Instagram</span>
                                <div>
                                    @if($representative->instagram_username)
                                        <a href="https://instagram.com/{{ $representative->instagram_username }}" target="_blank" class="font-bold text-slate-800 hover:underline inline-flex items-center gap-1">
                                            <span>📷</span> @<span>{{ $representative->instagram_username }}</span> ↗
                                        </a>
                                    @else
                                        <span class="text-slate-400">Tidak dicantumkan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wilayah Domisili & Tagging Lokasi Peta Card -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                                <span>🗺️</span> Wilayah Penugasan &amp; Tagging Koordinat
                            </h2>
                            @if($representative->latitude && $representative->longitude)
                                <a href="https://www.google.com/maps?q={{ $representative->latitude }},{{ $representative->longitude }}" target="_blank" class="text-xs font-bold text-teal-800 hover:underline inline-flex items-center gap-1">
                                    Buka di Google Maps ↗
                                </a>
                            @endif
                        </div>

                        <!-- Address Metadata Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Provinsi</span>
                                <span class="font-bold text-slate-900">{{ $representative->province_name }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Kota/Kabupaten</span>
                                <span class="font-bold text-slate-900">{{ $representative->city_name }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Kecamatan</span>
                                <span class="font-bold text-slate-900">{{ $representative->district_name }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Desa/Kelurahan</span>
                                <span class="font-bold text-slate-900">{{ $representative->village_name }}</span>
                            </div>
                        </div>

                        <!-- Map Preview -->
                        @if($representative->latitude && $representative->longitude)
                            <div class="space-y-2">
                                <div id="admin-map" class="h-64 sm:h-72 w-full rounded-2xl border border-slate-200 shadow-inner z-10"></div>
                                <div class="text-[11px] text-slate-500 flex items-center justify-between">
                                    <span>Koordinat: <strong class="font-mono text-slate-700">{{ $representative->latitude }}, {{ $representative->longitude }}</strong></span>
                                    @if($representative->formatted_address)
                                        <span class="truncate max-w-md ml-2" title="{{ $representative->formatted_address }}">
                                            📍 {{ $representative->formatted_address }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center text-xs text-slate-500">
                                Titik koordinat GPS tidak tersedia untuk pendaftaran ini.
                            </div>
                        @endif
                    </div>

                    <!-- Keaktifan Organisasi & Dokumen Lampiran Card -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                                <span>🏛️</span> Keaktifan Muhammadiyah &amp; Dokumen Lampiran
                            </h2>
                        </div>

                        <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200 space-y-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-teal-800 block">
                                Pimpinan Muhammadiyah / Ortom yang Sedang Aktif Diikuti
                            </span>
                            <div class="font-bold text-teal-950 text-sm sm:text-base">
                                {{ $representative->muhammadiyah_active_leadership }}
                            </div>
                        </div>

                        <!-- Document Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            
                            <!-- SK Pimpinan Document -->
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-3 flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-900 text-xs">📄 Surat Keputusan (SK) Pimpinan</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-teal-100 text-teal-800">PDF</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Berkas SK pengangkatan/kepengurusan aktif</p>
                                </div>

                                @if($representative->sk_pimpinan_document_path && Storage::disk('public')->exists($representative->sk_pimpinan_document_path))
                                    <div class="pt-2">
                                        <a href="{{ asset('storage/' . $representative->sk_pimpinan_document_path) }}" 
                                           target="_blank" 
                                           class="button-primary text-xs w-full py-2 justify-center font-bold bg-teal-700 hover:bg-teal-800 text-white">
                                            Buka Dokumen SK (PDF) ↗
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-rose-500 font-semibold">Berkas tidak ditemukan</span>
                                @endif
                            </div>

                            <!-- KTAM / MASA Document -->
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-3 flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-900 text-xs">🖼️ KTAM Fisik / Aplikasi MASA</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-sky-100 text-sky-800">Foto/PDF</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Tangkapan layar identitas keanggotaan KTAM</p>
                                </div>

                                @if($representative->ktam_document_path && Storage::disk('public')->exists($representative->ktam_document_path))
                                    <div class="pt-2">
                                        <a href="{{ asset('storage/' . $representative->ktam_document_path) }}" 
                                           target="_blank" 
                                           class="button-primary text-xs w-full py-2 justify-center font-bold bg-sky-700 hover:bg-sky-800 text-white">
                                            Lihat Berkas KTAM ↗
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-rose-500 font-semibold">Berkas tidak ditemukan</span>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Wawasan Kesrawan (Animal Welfare) Essay Card -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                                <span>🐾</span> Wawasan Kesejahteraan Hewan (Kesrawan / Animal Welfare)
                            </h2>
                            <span class="text-xs font-mono text-slate-400">
                                {{ strlen($representative->animal_welfare_essay) }} karakter
                            </span>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line font-normal">
                            {{ $representative->animal_welfare_essay }}
                        </div>
                    </div>

                </div>

                <!-- Right Column: Status & Admin Processing Box (4 Cols) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                    <!-- Admin Review & Status Form -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="font-outfit font-bold text-base text-slate-900 flex items-center gap-2">
                                <span>⚙️</span> Tindakan Verifikator
                            </h2>
                            @php $badge = $representative->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>

                        <form action="{{ route('admin.representatives.update-status', $representative) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <!-- Status Selector -->
                            <div>
                                <label for="status" class="form-label text-xs">Ubah Status Pendaftaran <span class="text-rose-500">*</span></label>
                                <select id="status" name="status" required class="form-input text-xs py-2">
                                    <option value="pending" {{ $representative->status === 'pending' ? 'selected' : '' }}>Menunggu Review (Pending)</option>
                                    <option value="reviewed" {{ $representative->status === 'reviewed' ? 'selected' : '' }}>Sedang Direview (Reviewed)</option>
                                    <option value="approved" {{ $representative->status === 'approved' ? 'selected' : '' }}>Diterima / Terverifikasi (Approved)</option>
                                    <option value="rejected" {{ $representative->status === 'rejected' ? 'selected' : '' }}>Ditolak / Perlu Perbaikan (Rejected)</option>
                                </select>
                            </div>

                            <!-- Admin Notes -->
                            <div>
                                <label for="admin_notes" class="form-label text-xs">Catatan Internal Verifikator / Pengurus</label>
                                <textarea id="admin_notes" 
                                          name="admin_notes" 
                                          rows="4" 
                                          placeholder="Tuliskan catatan hasil verifikasi berkas SK, tindak lanjut koordinasi WA, nomor SK penugasan, dll..."
                                          class="form-input text-xs leading-relaxed">{{ old('admin_notes', $representative->admin_notes) }}</textarea>
                            </div>

                            <!-- Review Meta -->
                            @if($representative->reviewer)
                                <div class="text-[11px] text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-0.5">
                                    <div>Terakhir diproses oleh: <strong class="text-slate-800">{{ $representative->reviewer->name }}</strong></div>
                                    @if($representative->reviewed_at)
                                        <div class="text-slate-400">{{ $representative->reviewed_at->format('d/m/Y H:i') }} WIB</div>
                                    @endif
                                </div>
                            @endif

                            <button type="submit" class="button-primary w-full py-2.5 text-xs font-bold bg-teal-700 hover:bg-teal-800 text-white shadow-xs">
                                💾 Simpan Pembaruan Status
                            </button>
                        </form>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Aksi Cepat</span>
                        
                        <a href="{{ $representative->whatsapp_link }}" target="_blank" class="button-secondary text-xs w-full py-2.5 justify-center font-bold text-emerald-800 border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100 flex items-center gap-2">
                            <span>💬</span> Hubungi Calon via WhatsApp
                        </a>

                        <button type="button" onclick="window.print()" class="button-secondary text-xs w-full py-2 justify-center font-semibold text-slate-700">
                            <span>🖨️</span> Cetak Berkas Calon
                        </button>

                        <form action="{{ route('admin.representatives.destroy', $representative) }}" 
                              method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data calon representatif {{ $representative->name }} ({{ $representative->registration_number }})? Tindakan ini tidak dapat dibatalkan.');"
                              class="pt-2 border-t border-slate-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger text-xs w-full py-2 justify-center font-bold">
                                🗑️ Hapus Pendaftaran
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>

    @if($representative->latitude && $representative->longitude)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const lat = {{ $representative->latitude }};
                const lng = {{ $representative->longitude }};
                
                const map = L.map('admin-map').setView([lat, lng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap kontributor'
                }).addTo(map);

                const marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup("<b>{{ $representative->name }}</b><br>{{ $representative->village_name }}, {{ $representative->city_name }}").openPopup();
            });
        </script>
    @endif
</x-app-layout>
