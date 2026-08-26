<x-app-layout>
    <div class="py-8" x-data="{ openDraftModal: false, draftUrl: '' }" @keydown.escape.window="openDraftModal = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="content-card border-teal-200 bg-teal-900 text-white p-6 sm:p-8">
                <div class="max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Panel Pemilik Kucing</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-white mt-1">
                        Selamat Datang, {{ Auth::user()->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-teal-100 mt-2 leading-relaxed">
                        Kelola data profil kucing, jadwalkan pemeriksaan kesehatan gratis bersama dokter hewan mitra, dan pantau status penerbitan Kartu KTAM Kucing.
                    </p>
                    @if(Auth::user()->muhammadiyah_id)
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-md bg-teal-800 border border-teal-700 text-teal-100 text-xs font-medium">
                            <span>Nomor Baku Muhammadiyah (NBM): {{ Auth::user()->muhammadiyah_id }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
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
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-5">
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Daftar Kucing Peliharaan</h2>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 text-xs font-bold">{{ $cats->count() }} Ekor</span>
                        </div>

                        @if($cats->isEmpty())
                            <div class="text-center py-10 border border-dashed border-slate-200 rounded-lg bg-slate-50">
                                <div class="text-3xl" aria-hidden="true">🐱</div>
                                <h3 class="mt-2 text-sm font-bold text-slate-800">Belum ada kucing yang didaftarkan</h3>
                                <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">Silakan isi formulir di kolom sebelah kanan untuk mendaftarkan kucing kesayangan Anda.</p>
                            </div>
                        @else
                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach($cats as $cat)
                                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center gap-3.5">
                                                <div class="rounded-lg bg-slate-200 border border-slate-300 overflow-hidden flex-shrink-0 w-20 h-20">
                                                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $cat->name }}</h3>
                                                    <p class="text-xs text-slate-600 mt-0.5">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Lahir: {{ $cat->date_of_birth->format('d M Y') }}</p>
                                                    @if($cat->biometric_type && $cat->biometric_type !== 'none')
                                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200">
                                                            Biometrik {{ strtoupper($cat->biometric_type) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-3.5 space-y-1.5 border-t border-slate-200 pt-2.5 text-xs text-slate-600">
                                                @if($cat->photos->count() > 1)
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Galeri Foto:</span>
                                                        <span>{{ $cat->photos->count() }} foto tersimpan (1 foto utama KTAM)</span>
                                                    </div>
                                                @endif
                                                @if($cat->allergies)
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Alergi:</span>
                                                        <span>{{ $cat->allergies }}</span>
                                                    </div>
                                                @endif
                                                @if($cat->vaccine_history)
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Riwayat Vaksin:</span>
                                                        <span>{{ $cat->vaccine_history }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
                                            @if($cat->ktamCard)
                                                <div class="text-left">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 block">KTAM RESMI</span>
                                                    <span class="text-xs font-mono font-bold text-slate-800">{{ $cat->ktamCard->ktam_number }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs min-h-[38px]">
                                                        Ubah
                                                    </a>
                                                    <a href="{{ route('ktam.download', $cat->id) }}" class="button-primary px-3 py-1.5 text-xs min-h-[38px]">
                                                        Unduh PDF
                                                    </a>
                                                </div>
                                            @elseif($cat->medicalRecords->isNotEmpty())
                                                <div class="text-left">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">PROSES VERIFIKASI ADMIN</span>
                                                    <span class="text-[11px] text-slate-600">Pemeriksaan Dokter Selesai</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click.prevent="draftUrl = '{{ route('ktam.preview', $cat->id) }}'; openDraftModal = true" class="button-secondary px-3 py-1.5 text-xs min-h-[38px] border-amber-300 text-amber-900 bg-amber-50 hover:bg-amber-100">
                                                        Lihat Draft
                                                    </button>
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs min-h-[38px]">
                                                        Ubah
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-left">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">STATUS KTAM</span>
                                                    <span class="text-[11px] text-slate-600">Belum Periksa Dokter</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click.prevent="draftUrl = '{{ route('ktam.preview', $cat->id) }}'; openDraftModal = true" class="button-secondary px-3 py-1.5 text-xs min-h-[38px]">
                                                        Lihat Draft
                                                    </button>
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs min-h-[38px]">
                                                        Ubah
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Appointments / Bookings Section -->
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
                </div>

                <!-- Right Column (Forms) -->
                <div class="space-y-6">
                    
                    <!-- Register Cat Form -->
                    <div class="content-card">
                        <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-2.5 mb-4">Daftarkan Kucing Baru</h2>
                        <form method="POST" action="{{ route('cat.store') }}" enctype="multipart/form-data" class="space-y-3.5">
                            @csrf
                            <div>
                                <label for="cat_name" class="form-label text-xs">Nama Kucing</label>
                                <input type="text" id="cat_name" name="name" required class="form-input text-xs" placeholder="Contoh: Mochi">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="cat_breed" class="form-label text-xs">Ras / Jenis</label>
                                    <input type="text" id="cat_breed" name="breed" required class="form-input text-xs" placeholder="Contoh: Domestik / Persia">
                                </div>
                                <div>
                                    <label for="cat_color" class="form-label text-xs">Warna / Pola Bulu</label>
                                    <input type="text" id="cat_color" name="color" class="form-input text-xs" placeholder="Contoh: Calico / Tabby / Oranye">
                                </div>
                            </div>
                            <div>
                                <label for="cat_wilayah" class="form-label text-xs">Wilayah Muhammadiyah (Master Wilayah)</label>
                                <select id="cat_wilayah" name="wilayah_code" class="form-input text-xs">
                                    @if(isset($masterWilayahs))
                                        @foreach($masterWilayahs as $wil)
                                            <option value="{{ $wil->kode }}" {{ $wil->kode === '34' ? 'selected' : '' }}>
                                                {{ $wil->kode }} - {{ $wil->nama }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="34">34 - D.I. Yogyakarta (PWM DIY)</option>
                                    @endif
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="cat_gender" class="form-label text-xs">Jenis Kelamin</label>
                                    <select id="cat_gender" name="gender" required class="form-input text-xs">
                                        <option value="male">Jantan</option>
                                        <option value="female">Betina</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cat_dob" class="form-label text-xs">Tanggal Lahir</label>
                                    <input type="date" id="cat_dob" name="date_of_birth" required class="form-input text-xs">
                                </div>
                            </div>
                            <div>
                                <label for="cat_photo" class="form-label text-xs">Foto Kucing (Galeri / Kamera)</label>
                                <input type="file" id="cat_photo" name="photo" accept="image/*" class="form-input text-xs">
                            </div>
                            <div>
                                <label for="cat_allergies" class="form-label text-xs">Alergi Kucing <span class="text-slate-500 font-normal">(Opsional)</span></label>
                                <input type="text" id="cat_allergies" name="allergies" class="form-input text-xs" placeholder="Contoh: Alergi makanan tertentu">
                            </div>
                            <div>
                                <label for="cat_vaccine" class="form-label text-xs">Riwayat Vaksin <span class="text-slate-500 font-normal">(Opsional)</span></label>
                                <input type="text" id="cat_vaccine" name="vaccine_history" class="form-input text-xs" placeholder="Contoh: Tricat, Rabies">
                            </div>
                            <button type="submit" class="w-full button-primary text-xs font-semibold py-2.5">
                                Daftarkan Data Kucing
                            </button>
                        </form>
                    </div>

                    <!-- Book Appointment Form -->
                    <div class="content-card">
                        <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-2.5 mb-4">Buat Janji Pemeriksaan</h2>
                        @if($cats->isEmpty())
                            <p class="text-xs text-slate-600 text-center py-4 bg-slate-50 rounded-lg border border-slate-200">
                                Daftarkan kucing terlebih dahulu sebelum membuat jadwal janji temu pemeriksaan dokter.
                            </p>
                        @else
                            <form method="POST" action="{{ route('appointment.store') }}" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label for="select_cat" class="form-label text-xs">Pilih Kucing</label>
                                    <select id="select_cat" name="cat_id" required class="form-input text-xs">
                                        @foreach($cats as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="app_date" class="form-label text-xs">Tanggal Pemeriksaan</label>
                                    <input type="date" id="app_date" name="date" required class="form-input text-xs">
                                </div>
                                <div>
                                    <label for="app_slot" class="form-label text-xs">Sesi Waktu</label>
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
                                <button type="submit" class="w-full button-primary text-xs font-semibold py-2.5">
                                    Konfirmasi Janji Temu
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Active Events / Kegiatan Sosialisasi -->
                    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
                        <div class="content-card border-teal-200 bg-teal-50/40">
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
        <div x-show="openDraftModal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="draft-modal-title" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
            <div @click.away="openDraftModal = false" class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col w-full max-w-lg max-h-[92vh] border border-slate-200">
                <div class="px-5 py-3.5 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <div>
                        <h3 id="draft-modal-title" class="font-outfit font-bold text-slate-900 text-sm">Pratinjau Kartu KTAM (Front & Back)</h3>
                        <p class="text-[11px] text-slate-500">Tampilan tampak depan & belakang kartu KTAM resmi.</p>
                    </div>
                    <button type="button" @click="openDraftModal = false" aria-label="Tutup pratinjau draft" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="flex-1 bg-slate-950 flex items-center justify-center p-3 overflow-y-auto">
                    <iframe :src="draftUrl" title="Pratinjau Kartu KTAM" class="w-full h-[460px] border-0 rounded-lg" scrolling="yes"></iframe>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
