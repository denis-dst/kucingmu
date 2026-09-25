<x-app-layout>
    <div class="py-8" x-data="{ 
        openDraftModal: false, 
        draftUrl: '',
        showRegistrationSuccessModal: {{ session('cat_registered') ? 'true' : 'false' }}
    }" @keydown.escape.window="openDraftModal = false; showRegistrationSuccessModal = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="content-card border-teal-200 bg-teal-900 text-white p-6 sm:p-8">
                <div class="max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Panel Pemilik Kucing</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-white mt-1">
                        Selamat Datang, {{ Auth::user()->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-teal-100 mt-2 leading-relaxed">
                        Kelola data profil kucing, jadwalkan pemeriksaan kesehatan gratis bersama dokter hewan mitra, dan pantau status penerbitan Kartu KTAKuMu Kucing.
                    </p>
                    @if(Auth::user()->muhammadiyah_id)
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-md bg-teal-800 border border-teal-700 text-teal-100 text-xs font-medium">
                            <span>Nomor Baku Muhammadiyah (NBM): <strong class="font-mono">{{ Auth::user()->formatted_nbm ?? Auth::user()->muhammadiyah_id }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Banner Jumlah Kucing Yang Perlu Diverifikasi -->
            @php
                $unverifiedCatsCount = $cats->filter(fn($c) => empty($c->unique_code))->count();
            @endphp

            @if($unverifiedCatsCount > 0)
                <div class="rounded-2xl bg-gradient-to-r from-amber-500/15 via-amber-50 to-orange-50/80 border border-amber-300 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                            ⏳
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-outfit font-bold text-slate-900 text-sm sm:text-base">
                                    {{ $unverifiedCatsCount }} Kucing Perlu Diverifikasi
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                    Menunggu Verifikasi Booth / Event
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Terdapat <strong>{{ $unverifiedCatsCount }} ekor kucing</strong> yang telah didaftarkan namun belum diverifikasi. Silakan lakukan verifikasi keanggotaan kucing Anda di <strong>Booth/Event KucingMu di Kota Anda</strong> untuk penerbitan Kartu KTAKuMu resmi.
                            </p>
                        </div>
                    </div>
                    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
                        <a href="#events-section" class="button-primary bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shrink-0 text-center shadow-xs flex items-center justify-center gap-1.5 self-start sm:self-auto">
                            <span>📍</span> Cek Jadwal Event
                        </a>
                    @endif
                </div>
            @endif

            <!-- Success Alert (if any standard message) -->
            @if(session('success') && !session('cat_registered'))
                <div class="p-4 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center gap-2" role="alert">
                    <span aria-hidden="true">✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Main grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Left Column (Cats List & Appointments) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Cat Profiles Section -->
                    <div class="content-card">
                        @php
                            $mSort = $sort ?? 'created_at';
                            $mDir = $direction ?? 'desc';
                            $mStatus = $statusFilter ?? 'all';
                        @endphp

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 pb-3 mb-4 gap-3">
                            <div>
                                <h2 class="font-outfit text-lg font-bold text-slate-900 leading-tight">Daftar Kucing Peliharaan</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola identitas, foto KTAKuMu, dan status kesehatan/kehidupan kucing Anda.</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 text-xs font-bold shrink-0 self-start sm:self-auto">
                                {{ $cats->count() }} Ekor
                            </span>
                        </div>

                        <!-- Member Filter & Sort Toolbar -->
                        <form method="GET" action="{{ route('dashboard') }}" class="mb-5 grid grid-cols-1 sm:grid-cols-12 gap-2.5 bg-slate-50/90 p-3 rounded-2xl border border-slate-200">
                            <!-- Search & Action -->
                            <div class="sm:col-span-6 flex items-center gap-1.5">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="Cari nama atau ras kucing..." 
                                           class="w-full text-xs pl-3.5 pr-12 py-2 rounded-xl border border-slate-300 bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 transition"
                                           style="padding-left: 0.875rem; padding-right: 2.75rem;">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5 pointer-events-none" style="position: absolute; right: 0.75rem; top: 0; bottom: 0; display: flex; align-items: center;">
                                        @if(request('search'))
                                            <a href="{{ route('dashboard', array_merge(request()->except(['search']))) }}" class="text-slate-400 hover:text-slate-600 text-xs font-bold pointer-events-auto px-1" title="Hapus pencarian">✕</a>
                                        @endif
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" class="button-primary text-xs px-3.5 py-2 rounded-xl shrink-0 min-h-[36px] flex items-center gap-1 shadow-2xs font-semibold">
                                    <span>Cari</span>
                                </button>
                            </div>

                            <!-- Filter Status -->
                            <div class="sm:col-span-3">
                                <select id="member_filter_status" name="status" onchange="this.form.submit()" class="w-full text-xs py-2 px-2.5 rounded-xl border border-slate-300 bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 font-medium text-slate-700">
                                    <option value="all" {{ $mStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="alive" {{ $mStatus === 'alive' ? 'selected' : '' }}>🟢 Hidup (Aktif)</option>
                                    <option value="deceased" {{ $mStatus === 'deceased' ? 'selected' : '' }}>⚪ Mati (Meninggal)</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="sm:col-span-3 flex items-center gap-1.5">
                                <select id="member_sort_select" name="sort_direction" onchange="
                                    const val = this.value.split(':');
                                    const sInput = this.form.querySelector('input[name=sort]');
                                    const dInput = this.form.querySelector('input[name=direction]');
                                    if (sInput && dInput) {
                                        sInput.value = val[0];
                                        dInput.value = val[1];
                                        this.form.submit();
                                    }
                                " class="w-full text-xs py-2 px-2.5 rounded-xl border border-slate-300 bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 font-medium text-slate-700">
                                    <option value="created_at:desc" {{ ($mSort == 'created_at' && $mDir == 'desc') ? 'selected' : '' }}>Terbaru</option>
                                    <option value="name:asc" {{ ($mSort == 'name' && $mDir == 'asc') ? 'selected' : '' }}>Nama (A - Z)</option>
                                    <option value="name:desc" {{ ($mSort == 'name' && $mDir == 'desc') ? 'selected' : '' }}>Nama (Z - A)</option>
                                    <option value="date_of_birth:asc" {{ ($mSort == 'date_of_birth' && $mDir == 'asc') ? 'selected' : '' }}>Umur (Tertua)</option>
                                    <option value="date_of_birth:desc" {{ ($mSort == 'date_of_birth' && $mDir == 'desc') ? 'selected' : '' }}>Umur (Termuda)</option>
                                    <option value="breed:asc" {{ ($mSort == 'breed' && $mDir == 'asc') ? 'selected' : '' }}>Ras (A - Z)</option>
                                    <option value="status:asc" {{ ($mSort == 'status' && $mDir == 'asc') ? 'selected' : '' }}>Status Hidup</option>
                                </select>
                                <input type="hidden" name="sort" value="{{ $mSort }}">
                                <input type="hidden" name="direction" value="{{ $mDir }}">

                                @if(request('search') || request('status') || request('sort'))
                                    <a href="{{ route('dashboard') }}" class="button-secondary text-xs px-2.5 py-2 rounded-xl text-slate-500 hover:text-slate-800 shrink-0" title="Reset filter">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>

                        @if($cats->isEmpty())
                            <div class="text-center py-12 px-4 border border-dashed border-slate-200 rounded-2xl bg-slate-50">
                                <div class="text-3xl mb-2" aria-hidden="true">🐱</div>
                                <h3 class="text-sm font-bold text-slate-800">Belum ada data kucing yang sesuai</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan daftarkan profil kucing Anda atau ubah kata kunci filter di atas.</p>
                            </div>
                        @else
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach($cats as $cat)
                                    <div class="rounded-2xl border border-slate-200 p-4 sm:p-5 bg-white shadow-2xs hover:shadow-md hover:border-teal-300 transition flex flex-col justify-between space-y-3 {{ $cat->isDeceased() ? 'bg-slate-50/70 border-slate-300' : '' }}">
                                        <div class="space-y-3">
                                            <!-- Top Header: Photo & Core Metadata -->
                                            <div class="flex items-start gap-3.5">
                                                <div class="rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 shadow-inner">
                                                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover {{ $cat->isDeceased() ? 'grayscale opacity-75' : '' }}">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1.5 flex-wrap">
                                                        <h3 class="font-outfit font-bold text-slate-900 text-base leading-tight truncate">{{ $cat->name }}</h3>
                                                        @if($cat->isAlive())
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                                🟢 Hidup
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                                                ⚪ Mati
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-slate-600 mt-0.5">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Lahir: {{ $cat->date_of_birth ? $cat->date_of_birth->format('d M Y') : '-' }} <span class="text-slate-400">({{ $cat->age_text }})</span></p>
                                                    
                                                    @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-900 border border-teal-200">
                                                            🐾 Biometrik {{ strtoupper($cat->biometric_type) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Additional Attributes -->
                                            <div class="space-y-1.5 border-t border-slate-100 pt-3 text-xs text-slate-600 bg-slate-50/70 p-3 rounded-xl">
                                                <div class="flex justify-between items-center text-[11px]">
                                                    <span class="text-slate-400">Nomor NIAKuMu:</span>
                                                    @if($cat->unique_code)
                                                        <span class="font-mono font-bold text-teal-900">{{ $cat->unique_code }}</span>
                                                    @else
                                                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded text-[10px] font-bold border border-amber-200">Menunggu Verifikasi Admin</span>
                                                    @endif
                                                </div>
                                                @if($cat->photos->count() > 1)
                                                    <div class="flex justify-between items-center text-[11px]">
                                                        <span class="text-slate-400">Galeri Foto:</span>
                                                        <span class="font-semibold text-slate-700">{{ $cat->photos->count() }} foto tersimpan</span>
                                                    </div>
                                                @endif
                                                @if($cat->allergies)
                                                    <div class="text-[11px]">
                                                        <span class="text-slate-400">Alergi:</span>
                                                        <span class="font-medium text-slate-700">{{ $cat->allergies }}</span>
                                                    </div>
                                                @endif
                                                @if($cat->vaccine_history)
                                                    <div class="text-[11px]">
                                                        <span class="text-slate-400">Riwayat Vaksin:</span>
                                                        <span class="font-medium text-slate-700">{{ $cat->vaccine_history }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Card Footer: Status & Action Buttons -->
                                        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2.5">
                                            @if($cat->unique_code && $cat->ktamCard)
                                                <div class="text-left">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 block">KTAKuMu RESMI</span>
                                                    <span class="text-xs font-mono font-bold text-slate-800">{{ $cat->unique_code }}</span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs font-semibold">
                                                        ✏️ Ubah
                                                    </a>
                                                    <a href="{{ route('ktam.download', $cat->id) }}" class="button-primary px-3 py-1.5 text-xs font-bold">
                                                        📄 Unduh PDF
                                                    </a>
                                                    <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="button-danger px-2.5 py-1.5 text-xs" title="Hapus Kucing">
                                                            <span>🗑</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($cat->medicalRecords->isNotEmpty())
                                                <div class="text-left">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">VERIFIKASI ADMIN</span>
                                                    <span class="text-[11px] text-slate-600 font-medium">Periksa Dokter Selesai &bull; Menunggu Admin</span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <button type="button" @click.prevent="draftUrl = '{{ route('ktam.preview', $cat->id) }}'; openDraftModal = true" class="button-secondary px-3 py-1.5 text-xs border-amber-300 text-amber-900 bg-amber-50 hover:bg-amber-100 font-semibold">
                                                        Lihat Draft
                                                    </button>
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs font-semibold">
                                                        ✏️ Ubah
                                                    </a>
                                                    <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="button-danger px-2.5 py-1.5 text-xs" title="Hapus Kucing">
                                                            <span>🗑</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="text-left">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">STATUS KTAKuMu</span>
                                                    <span class="text-[11px] text-amber-700 font-medium">Menunggu Verifikasi Admin</span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <button type="button" @click.prevent="draftUrl = '{{ route('ktam.preview', $cat->id) }}'; openDraftModal = true" class="button-secondary px-3 py-1.5 text-xs font-semibold">
                                                        Lihat Draft
                                                    </button>
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs font-semibold">
                                                        ✏️ Ubah
                                                    </a>
                                                    <form action="{{ route('cat.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kucing {{ $cat->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="button-danger px-2.5 py-1.5 text-xs" title="Hapus Kucing">
                                                            <span>🗑</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Appointments / Bookings Section -->
                    @if(($app_settings['enable_appointments'] ?? '1') == '1')
                        <div class="content-card">
                            <div class="border-b border-slate-200 pb-3 mb-4">
                                <h2 class="font-outfit text-lg font-bold text-slate-900">Riwayat Janji Temu Pemeriksaan</h2>
                            </div>

                            @if($appointments->isEmpty())
                                <div class="text-center py-6 text-slate-600 text-xs bg-slate-50 rounded-lg border border-slate-200">
                                    Belum ada riwayat janji temu pemeriksaan medis. Silakan buat janji temu pada formulir di sebelah kanan.
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs" aria-label="Daftar Janji Temu Medis">
                                        <thead>
                                            <tr class="border-b border-slate-200 text-slate-700 font-bold bg-slate-50">
                                                <th class="py-2.5 px-3">Kucing</th>
                                                <th class="py-2.5 px-3">Tanggal</th>
                                                <th class="py-2.5 px-3">Sesi Waktu</th>
                                                <th class="py-2.5 px-3">Status</th>
                                                <th class="py-2.5 px-3">Catatan Medis</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 text-slate-700">
                                            @foreach($appointments as $app)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="py-3 px-3 font-semibold text-slate-900">{{ $app->cat->name }}</td>
                                                    <td class="py-3 px-3">{{ $app->date->format('d M Y') }}</td>
                                                    <td class="py-3 px-3 font-mono text-[11px]">{{ $app->time_slot }}</td>
                                                    <td class="py-3 px-3">
                                                        @if($app->status == 'scheduled')
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">Terjadwal</span>
                                                        @elseif($app->status == 'checked_in')
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">Hadir di Lokasi</span>
                                                        @elseif($app->status == 'completed')
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200">Selesai</span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-200">Dibatalkan</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-3">
                                                        @if($app->medicalRecord)
                                                            <div class="text-[11px]">
                                                                <p><strong class="text-slate-800">Kondisi:</strong> {{ $app->medicalRecord->general_condition }}</p>
                                                                <p><strong class="text-slate-800">BB/Suhu:</strong> {{ $app->medicalRecord->weight }}kg / {{ $app->medicalRecord->temperature }}°C</p>
                                                            </div>
                                                        @else
                                                            <span class="text-slate-400">Menunggu pemeriksaan</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column (Forms) -->
                <div class="space-y-6">
                    
                    <!-- Register Cat Form -->
                    <div class="content-card" x-data="{
                        isSubmitting: false,
                        photoPreview: null,
                        photoCompressed: '',
                        photoSizeText: '',
                        isCompressing: false,
                        handlePhotoSelect(event) {
                            const file = event.target.files && event.target.files[0];
                            if (!file) return;

                            // Format validation
                            const validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                            if (!validMimes.includes(file.type) && !file.name.match(/\.(jpe?g|png|webp)$/i)) {
                                alert('Format file tidak didukung! Gunakan format JPG, PNG, atau WEBP.');
                                event.target.value = '';
                                return;
                            }

                            this.isCompressing = true;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const img = new Image();
                                img.onload = () => {
                                    const maxDim = 1200;
                                    let w = img.width;
                                    let h = img.height;
                                    if (w > maxDim || h > maxDim) {
                                        const ratio = Math.min(maxDim / w, maxDim / h);
                                        w = Math.round(w * ratio);
                                        h = Math.round(h * ratio);
                                    }

                                    const canvas = document.createElement('canvas');
                                    canvas.width = w;
                                    canvas.height = h;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, w, h);

                                    const base64 = canvas.toDataURL('image/jpeg', 0.82);
                                    this.photoCompressed = base64;
                                    this.photoPreview = base64;
                                    const approxKb = Math.round((base64.length * 3 / 4) / 1024);
                                    this.photoSizeText = approxKb + ' KB (Siap diunggah)';
                                    this.isCompressing = false;
                                };
                                img.onerror = () => {
                                    this.photoPreview = e.target.result;
                                    this.photoCompressed = e.target.result;
                                    this.isCompressing = false;
                                };
                                img.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        },
                        clearPhoto() {
                            this.photoPreview = null;
                            this.photoCompressed = '';
                            this.photoSizeText = '';
                            const fileInput = document.getElementById('cat_photo');
                            if (fileInput) fileInput.value = '';
                        }
                    }">
                        <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-2.5 mb-4">Daftarkan Kucing Baru</h2>
                        <form method="POST" action="{{ route('cat.store') }}" enctype="multipart/form-data" class="space-y-3.5"
                              @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;">
                            @csrf
                            <div>
                                <label for="cat_name" class="form-label text-xs">Nama Kucing <span class="text-rose-500">*</span></label>
                                <input type="text" id="cat_name" name="name" required class="form-input text-xs" placeholder="Contoh: Mochi" value="{{ old('name') }}">
                            </div>

                            <!-- Master Ras & Warna Kucing -->
                            <div x-data="{ selectedBreed: '{{ old('breed', 'Domestik') }}' }" class="space-y-2.5">
                                <div>
                                    <label for="cat_breed" class="form-label text-xs">Ras / Jenis Kucing <span class="text-rose-500">*</span></label>
                                    <select id="cat_breed" name="breed" x-model="selectedBreed" required class="form-input text-xs">
                                        @php
                                            $breedList = $masterBreeds ?? \App\Models\MasterBreed::getAllBreedNames();
                                        @endphp
                                        @foreach($breedList as $b)
                                            <option value="{{ $b }}" {{ old('breed', 'Domestik') === $b ? 'selected' : '' }}>{{ $b }}</option>
                                        @endforeach
                                        <option value="Lainnya" {{ old('breed') === 'Lainnya' ? 'selected' : '' }}>➕ Lainnya (Input Sendiri)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cat_color" class="form-label text-xs">Warna / Pola Bulu</label>
                                    <input type="text" id="cat_color" name="color" class="form-input text-xs" placeholder="Contoh: Calico / Tabby / Oranye" value="{{ old('color') }}">
                                </div>

                                <!-- Input Kustom Ras Baru jika pilih Lainnya -->
                                <div x-show="selectedBreed === 'Lainnya'" x-transition class="bg-amber-50/80 p-2.5 rounded-xl border border-amber-200">
                                    <label for="breed_custom" class="form-label text-[11px] text-amber-900 mb-1">
                                        Tuliskan Nama Ras Kucing Baru <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" id="breed_custom" name="breed_custom" placeholder="Contoh: Munchkin / Balinese" class="form-input text-xs bg-white w-full" value="{{ old('breed_custom') }}">
                                    <p class="text-[10px] text-amber-700 mt-1">Ras baru ini otomatis tersimpan ke master dan menjadi pilihan ke depannya.</p>
                                </div>
                            </div>
                            <div>
                                <label for="cat_wilayah" class="form-label text-xs">Wilayah Muhammadiyah (Master Wilayah)</label>
                                <select id="cat_wilayah" name="wilayah_code" class="form-input text-xs">
                                    @php
                                        $validWilayahs = collect($masterWilayahs ?? [])->filter(function ($item) {
                                            if (is_object($item)) {
                                                return !($item instanceof \__PHP_Incomplete_Class) && !empty($item->kode) && !str_starts_with((string)$item->kode, '__PHP_');
                                            }
                                            if (is_array($item)) {
                                                return !empty($item['kode']) && !str_starts_with((string)$item['kode'], '__PHP_');
                                            }
                                            return false;
                                        });
                                    @endphp
                                    @if($validWilayahs->isNotEmpty())
                                        @foreach($validWilayahs as $wil)
                                            @php
                                                $wCode = is_object($wil) ? $wil->kode : $wil['kode'];
                                                $wName = is_object($wil) ? $wil->nama : $wil['nama'];
                                            @endphp
                                            <option value="{{ $wCode }}" {{ old('wilayah_code', '34') == $wCode ? 'selected' : '' }}>
                                                {{ $wCode }} - {{ $wName }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="34" selected>34 - D.I. Yogyakarta (PWM DIY)</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="cat_gender" class="form-label text-xs">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select id="cat_gender" name="gender" required class="form-input text-xs">
                                    <option value="male">Jantan</option>
                                    <option value="female">Betina</option>
                                </select>
                            </div>
                            <div>
                                <label for="cat_dob" class="form-label text-xs">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input type="date" id="cat_dob" name="date_of_birth" max="{{ date('Y-m-d') }}" required class="form-input text-xs">
                            </div>
                            
                            <!-- Foto Kucing (Kamera & Galeri dengan Auto Kompresi) -->
                            <div class="space-y-2">
                                <label class="form-label text-xs">Foto Kucing (Kamera / Galeri)</label>
                                
                                <div class="flex items-center gap-2">
                                    <!-- Input File Utama -->
                                    <input type="file" 
                                           id="cat_photo" 
                                           name="photo" 
                                           accept="image/*" 
                                           class="hidden"
                                           @change="handlePhotoSelect($event)">
                                    
                                    <button type="button" 
                                            @click="document.getElementById('cat_photo').click()" 
                                            class="button-secondary text-xs py-2 px-3 flex-1 flex items-center justify-center gap-1.5 bg-white hover:bg-slate-100 shadow-2xs">
                                        <span>📷</span>
                                        <span x-text="photoPreview ? 'Ganti Foto' : 'Ambil Kamera / Galeri'">Ambil Kamera / Galeri</span>
                                    </button>

                                    <template x-if="photoPreview">
                                        <button type="button" 
                                                @click="clearPhoto()" 
                                                class="button-danger text-xs py-2 px-2.5 shrink-0" 
                                                title="Hapus foto terpilih">
                                            ✕ Batal
                                        </button>
                                    </template>
                                </div>

                                <input type="hidden" name="photo_cam" :value="photoCompressed">

                                <!-- Preview Thumbnail -->
                                <template x-if="photoPreview">
                                    <div class="flex items-center gap-3 p-2.5 bg-teal-50/70 border border-teal-200 rounded-xl mt-2">
                                        <img :src="photoPreview" alt="Pratinjau Kucing" class="w-14 h-14 object-cover rounded-lg border border-teal-300 shrink-0 shadow-2xs">
                                        <div class="text-[11px] text-teal-900 min-w-0">
                                            <span class="font-bold block text-teal-800">✓ Foto Siap Digunakan</span>
                                            <span class="text-teal-700 block truncate" x-text="photoSizeText || 'Terkonversi optimal'"></span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="isCompressing">
                                    <p class="text-[11px] text-teal-700 font-medium flex items-center gap-1.5 animate-pulse">
                                        <span class="animate-spin">⟳</span> Mengoptimalkan ukuran foto...
                                    </p>
                                </template>

                                <p class="text-[10px] text-slate-500">Mendukung kamera HP langsung & galeri (Otomatis dikompres maks 200 KB).</p>
                            </div>

                            <div>
                                <label for="cat_allergies" class="form-label text-xs">Alergi Kucing <span class="text-slate-500 font-normal">(Opsional)</span></label>
                                <input type="text" id="cat_allergies" name="allergies" class="form-input text-xs" placeholder="Contoh: Alergi makanan tertentu">
                            </div>
                            <div>
                                <label for="cat_vaccine" class="form-label text-xs">Riwayat Vaksin <span class="text-slate-500 font-normal">(Opsional)</span></label>
                                <input type="text" id="cat_vaccine" name="vaccine_history" class="form-input text-xs" placeholder="Contoh: Tricat, Rabies">
                            </div>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="w-full button-primary text-xs font-semibold h-[42px] min-h-[42px] px-4 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Mendaftarkan Kucing...</span>
                                </span>
                                <span x-show="!isSubmitting">Daftarkan Data Kucing</span>
                            </button>
                        </form>
                    </div>

                    <!-- Book Appointment Form -->
                    @if(($app_settings['enable_appointments'] ?? '1') == '1')
                        <div class="content-card">
                            <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-2.5 mb-4">Buat Janji Pemeriksaan</h2>
                            @if($cats->isEmpty())
                                <p class="text-xs text-slate-600 text-center py-4 bg-slate-50 rounded-lg border border-slate-200">
                                    Daftarkan kucing terlebih dahulu sebelum membuat jadwal janji temu pemeriksaan dokter.
                                </p>
                            @else
                                <form method="POST" action="{{ route('appointment.store') }}" class="space-y-3.5"
                                      x-data="{ isSubmittingApp: false }"
                                      @submit="if(isSubmittingApp) { $event.preventDefault(); return false; } isSubmittingApp = true;">
                                    @csrf
                                    <div>
                                        <label for="select_cat" class="form-label text-xs">Pilih Kucing <span class="text-rose-500">*</span></label>
                                        <select id="select_cat" name="cat_id" required class="form-input text-xs">
                                            @foreach($cats as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="app_date" class="form-label text-xs">Tanggal Pemeriksaan <span class="text-rose-500">*</span></label>
                                        <input type="date" id="app_date" name="date" min="{{ date('Y-m-d') }}" required class="form-input text-xs">
                                    </div>
                                    <div>
                                        <label for="app_slot" class="form-label text-xs">Sesi Waktu <span class="text-rose-500">*</span></label>
                                        <select id="app_slot" name="time_slot" required class="form-input text-xs">
                                            <option value="Sesi Pagi (09:00 - 11:30)">Sesi Pagi (09:00 - 11:30)</option>
                                            <option value="Sesi Siang (13:00 - 15:30)">Sesi Siang (13:00 - 15:30)</option>
                                            <option value="Sesi Sore (16:00 - 17:30)">Sesi Sore (16:00 - 17:30)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="app_notes" class="form-label text-xs">Keluhan / Catatan Kunjungan</label>
                                        <textarea id="app_notes" name="notes" rows="2" class="form-input text-xs" placeholder="Tuliskan keluhan atau tujuan pemeriksaan..."></textarea>
                                    </div>
                                    <button type="submit" 
                                            :disabled="isSubmittingApp"
                                            class="w-full button-primary text-xs font-semibold h-[42px] min-h-[42px] px-4 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span x-show="isSubmittingApp" class="inline-flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Memproses Jadwal...</span>
                                        </span>
                                        <span x-show="!isSubmittingApp">Konfirmasi Janji Temu</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <!-- Active Events / Kegiatan Sosialisasi -->
                    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
                        <div id="events-section" class="content-card border-teal-200 bg-teal-50/40 scroll-mt-6">
                            <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-teal-200 pb-2.5 mb-3">Agenda & Sosialisasi Terdekat</h2>
                            <div class="space-y-3">
                                @foreach($activeEvents as $event)
                                    <div class="bg-white p-3.5 rounded-lg border border-slate-200 space-y-2">
                                        @if($event->banner_path)
                                            <div class="h-24 w-full bg-slate-100 rounded-md overflow-hidden mb-1.5">
                                                <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                            </div>
                                        @endif
                                        <h3 class="font-bold text-slate-900 text-xs leading-snug">{{ $event->title }}</h3>
                                        <p class="text-[11px] text-slate-600">{{ $event->date->format('d M Y') }} &bull; {{ $event->location }}</p>
                                        <p class="text-xs text-slate-600 line-clamp-2">{{ $event->description }}</p>
                                        @if($event->registration_link)
                                            <div class="pt-1">
                                                <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" rel="noopener noreferrer" class="w-full button-primary flex justify-center py-2 text-xs font-semibold text-center">
                                                    Daftar Kegiatan
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>

        <!-- Draft Modal -->
        <div x-show="openDraftModal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="draft-modal-title" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/75 backdrop-blur-xs p-3">
            <div @click.away="openDraftModal = false" class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col w-full max-w-md max-h-[92vh] border border-slate-200">
                <div class="px-4 py-3 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h3 id="draft-modal-title" class="font-outfit font-bold text-slate-900 text-sm">Pratinjau Kartu KTAKuMu</h3>
                    <button type="button" @click="openDraftModal = false" aria-label="Tutup pratinjau" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="flex-1 bg-slate-950 flex items-center justify-center p-0 overflow-y-auto">
                    <iframe :src="draftUrl" title="Pratinjau Kartu KTAKuMu" class="w-full h-[520px] border-0" scrolling="auto"></iframe>
                </div>
            </div>
        </div>

        <!-- Registration Success Modal Popup -->
        <div x-show="showRegistrationSuccessModal" 
             style="display: none;" 
             role="dialog" 
             aria-modal="true" 
             aria-labelledby="reg-success-title" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4">
            <div @click.away="showRegistrationSuccessModal = false" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col w-full max-w-lg border border-slate-200 relative text-center p-6 sm:p-8 space-y-5">
                
                <!-- Close 'X' Button Top Right -->
                <button type="button" @click="showRegistrationSuccessModal = false" aria-label="Tutup modal" class="absolute top-4 right-4 p-1.5 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Icon Badge -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center text-3xl sm:text-4xl mx-auto shadow-inner">
                    🎉
                </div>

                <!-- Content -->
                <div class="space-y-2.5">
                    <h3 id="reg-success-title" class="font-outfit text-xl sm:text-2xl font-extrabold text-slate-900">
                        Selamat, Kucing Anda berhasil didaftarkan
                    </h3>
                    @if(session('registered_cat_name'))
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-bold border border-teal-200">
                            <span>🐱</span> <span>{{ session('registered_cat_name') }}</span>
                        </div>
                    @endif
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-md mx-auto pt-1">
                        Selanjutnya silakan lakukan verifikasi keanggotaan Kucing Anda di <strong>Booth/Event KucingMu di Kota Anda</strong>.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
                        <a href="#events-section" @click="showRegistrationSuccessModal = false" class="w-full sm:w-auto button-primary text-xs font-bold py-3 px-5 shadow-sm flex items-center justify-center gap-1.5">
                            <span>📍</span> Lihat Jadwal Booth / Event
                        </a>
                    @endif
                    <button type="button" @click="showRegistrationSuccessModal = false" class="w-full sm:w-auto button-secondary text-xs font-semibold py-3 px-5">
                        Mengerti & Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
