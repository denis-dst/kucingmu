<x-app-layout>
    <div class="py-12" x-data="{ openDraftModal: false, draftUrl: '' }" @keydown.escape.window="openDraftModal = false">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Panel Pemilik Kucing</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Datang, {{ Auth::user()->name }}!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Kelola data kucing peliharaan Anda, jadwalkan pemeriksaan kesehatan, dan unduh Kartu Tanda Anggota Muhammadiyah (KTAM) khusus untuk kucing kesayangan Anda.
                    </p>
                    @if(Auth::user()->muhammadiyah_id)
                        <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-100/60 border border-teal-200/50 text-teal-800 text-xs font-semibold">
                            <span>NBM: {{ Auth::user()->muhammadiyah_id }}</span>
                        </div>
                    @endif
                </div>
                <div class="hidden md:block text-5xl">
                    🐈
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center gap-2">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Main grid -->
            <div class="grid gap-8 lg:grid-cols-3">
                
                <!-- Left Column (Cats List & Add Form) -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Cat Profiles Section -->
                    <div class="content-card">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Kucing Saya</h2>
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">{{ $cats->count() }} Ekor</span>
                        </div>

                        @if($cats->isEmpty())
                            <div class="text-center py-12">
                                <span class="text-4xl">🐱</span>
                                <h3 class="mt-4 text-base font-bold text-slate-700">Belum ada kucing terdaftar</h3>
                                <p class="text-sm text-slate-500 mt-1">Silakan daftarkan kucing Anda menggunakan formulir di sebelah kanan.</p>
                            </div>
                        @else
                            <div class="grid gap-6 sm:grid-cols-2">
                                @foreach($cats as $cat)
                                    <div class="rounded-2xl border border-slate-200 p-5 bg-slate-50/50 hover:bg-white hover:shadow-md transition flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center gap-4">
                                                <div class="h-16 w-16 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0">
                                                    @if($cat->photo_path)
                                                        <img src="{{ asset('storage/' . $cat->photo_path) }}" alt="{{ $cat->name }}" class="h-100 w-100 object-cover">
                                                    @else
                                                        <div class="h-100 w-100 flex items-center justify-center text-2xl bg-teal-50 text-teal-700 font-bold">
                                                            {{ substr($cat->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $cat->name }}</h3>
                                                    <p class="text-xs text-slate-500 mt-0.5">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                                    <p class="text-xs text-slate-400 mt-0.5">Lahir: {{ $cat->date_of_birth->format('d M Y') }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-4 space-y-2 border-t border-slate-100 pt-3 text-xs">
                                                @if($cat->allergies)
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Alergi:</span>
                                                        <span class="text-slate-600 block">{{ $cat->allergies }}</span>
                                                    </div>
                                                @endif
                                                @if($cat->vaccine_history)
                                                    <div>
                                                        <span class="font-semibold text-slate-700">Riwayat Vaksin:</span>
                                                        <span class="text-slate-600 block">{{ $cat->vaccine_history }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                            @if($cat->ktamCard)
                                                <div class="text-left">
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-teal-700 block">KTAM AKTIF</span>
                                                    <span class="text-[10px] font-mono font-bold text-slate-700">{{ $cat->ktamCard->ktam_number }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs">
                                                        Ubah
                                                    </a>
                                                    <a href="{{ route('ktam.download', $cat->id) }}" class="button-primary px-3 py-1.5 text-xs">
                                                        Unduh
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-left">
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-600 block">STATUS KTAM</span>
                                                    <span class="text-[10px] text-slate-500">Menunggu Hasil Pemeriksaan</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click.prevent="draftUrl = '{{ route('ktam.preview', $cat->id) }}'; openDraftModal = true" class="button-primary px-3 py-1.5 text-xs border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100">
                                                        Lihat Draft KTAM
                                                    </button>
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="button-secondary px-3 py-1.5 text-xs">
                                                        Ubah Profil
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
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Riwayat Janji Temu & Pemeriksaan</h2>
                        </div>

                        @if($appointments->isEmpty())
                            <div class="text-center py-8 text-slate-500 text-sm">
                                Belum ada riwayat janji temu pemeriksaan kesehatan.
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-slate-400 font-bold">
                                            <th class="py-3 px-1">Kucing</th>
                                            <th class="py-3 px-1">Tanggal</th>
                                            <th class="py-3 px-1">Sesi Waktu</th>
                                            <th class="py-3 px-1">Status</th>
                                            <th class="py-3 px-1">Catatan Medis</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @foreach($appointments as $app)
                                            <tr>
                                                <td class="py-4 px-1 font-bold">{{ $app->cat->name }}</td>
                                                <td class="py-4 px-1">{{ $app->date->format('d F Y') }}</td>
                                                <td class="py-4 px-1 font-mono text-xs">{{ $app->time_slot }}</td>
                                                <td class="py-4 px-1">
                                                    @if($app->status == 'scheduled')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Direncanakan</span>
                                                    @elseif($app->status == 'checked_in')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Checked-in</span>
                                                    @elseif($app->status == 'completed')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">Selesai</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Dibatalkan</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-1">
                                                    @if($app->medicalRecord)
                                                        <div class="text-xs">
                                                            <p><span class="font-semibold">Kondisi:</span> {{ $app->medicalRecord->general_condition }}</p>
                                                            <p><span class="font-semibold">BB/Suhu:</span> {{ $app->medicalRecord->weight }}kg / {{ $app->medicalRecord->temperature }}°C</p>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-slate-400">-</span>
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
                <div class="space-y-8">
                    
                    <!-- Register Cat Form -->
                    <div class="content-card">
                        <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Daftarkan Kucing Baru</h2>
                        <form method="POST" action="{{ route('cat.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label for="cat_name" class="form-label">Nama Kucing</label>
                                <input type="text" id="cat_name" name="name" required class="form-input" placeholder="e.g. Mochi">
                            </div>
                            <div>
                                <label for="cat_breed" class="form-label">Ras / Breed</label>
                                <input type="text" id="cat_breed" name="breed" required class="form-input" placeholder="e.g. Persia / Domestik">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="cat_gender" class="form-label">Jenis Kelamin</label>
                                    <select id="cat_gender" name="gender" required class="form-input py-2">
                                        <option value="male">Jantan</option>
                                        <option value="female">Betina</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="cat_dob" class="form-label">Tanggal Lahir</label>
                                    <input type="date" id="cat_dob" name="date_of_birth" required class="form-input">
                                </div>
                            </div>
                            <div>
                                <label for="cat_photo" class="form-label">Foto Kucing</label>
                                <input type="file" id="cat_photo" name="photo" class="form-input py-1 text-xs">
                            </div>
                            <div>
                                <label for="cat_allergies" class="form-label">Alergi Kucing <span class="text-xs text-slate-400">(Opsional)</span></label>
                                <input type="text" id="cat_allergies" name="allergies" class="form-input" placeholder="e.g. Alergi ayam">
                            </div>
                            <div>
                                <label for="cat_vaccine" class="form-label">Riwayat Vaksin <span class="text-xs text-slate-400">(Opsional)</span></label>
                                <input type="text" id="cat_vaccine" name="vaccine_history" class="form-input" placeholder="e.g. Tricat, Rabies">
                            </div>
                            <button type="submit" class="w-full button-primary flex justify-center py-2.5">
                                Daftarkan Kucing
                            </button>
                        </form>
                    </div>

                    <!-- Book Appointment Form -->
                    <div class="content-card">
                        <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">Buat Janji Pemeriksaan</h2>
                        @if($cats->isEmpty())
                            <p class="text-sm text-slate-500 text-center py-4">Silakan daftarkan kucing Anda terlebih dahulu sebelum membuat janji temu.</p>
                        @else
                            <form method="POST" action="{{ route('appointment.store') }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="select_cat" class="form-label">Pilih Kucing</label>
                                    <select id="select_cat" name="cat_id" required class="form-input py-2">
                                        @foreach($cats as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="app_date" class="form-label">Tanggal Pemeriksaan</label>
                                    <input type="date" id="app_date" name="date" required class="form-input">
                                </div>
                                <div>
                                    <label for="app_slot" class="form-label">Sesi Waktu</label>
                                    <select id="app_slot" name="time_slot" required class="form-input py-2">
                                        <option value="Sesi Pagi (09:00 - 11:30)">Sesi Pagi (09:00 - 11:30)</option>
                                        <option value="Sesi Siang (13:00 - 15:30)">Sesi Siang (13:00 - 15:30)</option>
                                        <option value="Sesi Sore (16:00 - 17:30)">Sesi Sore (16:00 - 17:30)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="app_notes" class="form-label">Keluhan / Catatan Kunjungan</label>
                                    <textarea id="app_notes" name="notes" rows="2" class="form-input" placeholder="Tuliskan keluhan atau tujuan pemeriksaan..."></textarea>
                                </div>
                                <button type="submit" class="w-full button-primary flex justify-center py-2.5">
                                    Buat Janji Temu
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Active Events / Kegiatan Sosialisasi -->
                    @if(isset($activeEvents) && $activeEvents->isNotEmpty())
                        <div class="content-card border-teal-200 bg-teal-50/20">
                            <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">📢 Kegiatan & Sosialisasi Terdekat</h2>
                            <div class="space-y-4">
                                @foreach($activeEvents as $event)
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 space-y-2">
                                        @if($event->banner_path)
                                            <div class="h-28 w-full bg-slate-100 rounded-lg overflow-hidden mb-2">
                                                <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                            </div>
                                        @endif
                                        <h3 class="font-bold text-slate-950 text-sm leading-snug">{{ $event->title }}</h3>
                                        <p class="text-[10px] font-mono text-slate-500">{{ $event->date->format('d F Y') }} &bull; {{ $event->location }}</p>
                                        <p class="text-xs text-slate-600 line-clamp-2">{{ $event->description }}</p>
                                        @if($event->registration_link)
                                            <div class="pt-2">
                                                <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" class="w-full button-primary flex justify-center py-2 text-xs font-bold text-center">
                                                    Daftar (gentix-apps.com)
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
        <div x-show="openDraftModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="openDraftModal = false" class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col transform transition-all" style="width: 375px; height: 280px;">
                <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-900 text-sm">Preview Draft KTAM</h3>
                    <button @click="openDraftModal = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="flex-1 bg-[#061d12] flex items-center justify-center p-2 relative overflow-hidden">
                    <iframe :src="draftUrl" class="w-full h-full border-0" scrolling="no"></iframe>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
