<x-app-layout>
    <div class="py-8" x-data="offlineManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Relawan Lapangan</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Bertugas, {{ Auth::user()->name }}!
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Fasilitasi pendaftaran langsung peserta di lokasi kegiatan, jalankan sensus kucing PTMA, catat kehadiran check-in antrian dokter, dan sinkronkan data lapangan.
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-2.5">
                        <!-- Connection Status Badge -->
                        <span :class="isOnline ? 'bg-teal-50 text-teal-800 border-teal-200' : 'bg-rose-50 text-rose-800 border-rose-200'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md border text-xs font-semibold">
                            <span :class="isOnline ? 'bg-teal-600' : 'bg-rose-600'" class="h-2 w-2 rounded-full" aria-hidden="true"></span>
                            <span x-text="isOnline ? 'Status: Terhubung Online' : 'Status: Mode Offline (Lokal)'"></span>
                        </span>
                        
                        <!-- Offline Queue Status -->
                        <button x-show="offlineQueue.length > 0" @click="syncOfflineData()" :disabled="syncing" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-100 hover:bg-amber-200 border border-amber-300 text-amber-900 text-xs font-semibold cursor-pointer min-h-[38px]">
                            <span class="font-bold font-mono" x-text="offlineQueue.length"></span> Data Tersimpan Offline (Klik untuk Sinkronisasi)
                        </button>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    🐾
                </div>
            </div>

            <!-- Modules Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Sensus Kucing PTMA Card -->
                <a href="{{ route('volunteer.census.index') }}" class="content-card border-teal-200 bg-teal-50/40 hover:bg-teal-50/80 transition p-5 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Modul Lapangan Klaster 1</span>
                            <span class="text-lg">📊</span>
                        </div>
                        <h2 class="font-outfit text-base font-bold text-slate-900 mt-1">Sensus Stray Cat PTMA</h2>
                        <p class="text-xs text-slate-600 mt-1">Formulir sensus berbasis kampus (UMY, UAD, UMP, UMS) dengan auto-tagging GPS, 4 foto sudut pandang, morfometri, dan mikro-habitat.</p>
                    </div>
                    <div class="pt-2">
                        <span class="button-primary text-xs font-semibold px-4 py-2 min-h-[36px] inline-flex items-center gap-1">
                            Buka Sensus PTMA →
                        </span>
                    </div>
                </a>

                <!-- eSurveillance Card -->
                <a href="{{ route('volunteer.surveillance.index') }}" class="content-card border-slate-200 bg-slate-50/60 hover:bg-slate-50 transition p-5 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Surveilans One Health</span>
                            <span class="text-lg">📋</span>
                        </div>
                        <h2 class="font-outfit text-base font-bold text-slate-900 mt-1">eSurveillance Populasi Kucing</h2>
                        <p class="text-xs text-slate-600 mt-1">Survei komprehensif 7 langkah: sensus visual, parasitologi feses, sampling tanah, survei KAP, dan evaluasi K3L kampus.</p>
                    </div>
                    <div class="pt-2">
                        <span class="button-secondary text-xs font-semibold px-4 py-2 min-h-[36px] inline-flex items-center gap-1 bg-white">
                            Buka Form Surveilans →
                        </span>
                    </div>
                </a>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center gap-2" role="alert">
                    <span aria-hidden="true" class="text-teal-600 font-bold">✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Registration Summary Card (Credentials & Member Guidance) -->
            @if(session('registration_summary'))
                @php $summary = session('registration_summary'); @endphp
                <div class="content-card border-teal-300 bg-gradient-to-br from-teal-50/90 via-white to-teal-50/50 shadow-sm p-5 space-y-4" role="region" aria-label="Ringkasan Pendaftaran">
                    <div class="flex items-start justify-between gap-3 border-b border-teal-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="h-9 w-9 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                🐾
                            </div>
                            <div>
                                <h2 class="font-outfit text-base font-bold text-teal-950">
                                    {{ $summary['is_new_member'] ? 'Akun Member Baru & Kucing Berhasil Didaftarkan' : 'Kucing Berhasil Ditambahkan ke Akun Member' }}
                                </h2>
                                <p class="text-xs text-teal-700">Kucing telah masuk ke sistem antrian periksa dokter hari ini (Status: Checked-In).</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                            Antrian Dokter Aktif
                        </span>
                    </div>

                    <!-- Credential & Cat Detail Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        <div class="p-2.5 rounded-lg bg-white border border-teal-100 shadow-2xs">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Nama Pemilik</span>
                            <p class="font-bold text-slate-800 truncate mt-0.5">{{ $summary['owner_name'] }}</p>
                            <p class="text-[11px] text-slate-500">{{ $summary['owner_phone'] ?: '-' }}</p>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-teal-100 shadow-2xs">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Email Login</span>
                            <p class="font-bold text-teal-900 truncate mt-0.5 select-all">{{ $summary['owner_email'] }}</p>
                            <span class="text-[10px] text-slate-500">Username untuk masuk</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-teal-100 shadow-2xs">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Password Bawaan</span>
                            <div class="flex items-center justify-between mt-0.5">
                                <span class="font-mono font-bold text-sm text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 select-all">{{ $summary['default_password'] }}</span>
                                <button type="button" @click="copyText('{{ $summary['default_password'] }}', 'Password berhasil disalin!')" class="text-[11px] font-semibold text-teal-700 hover:text-teal-900 underline ml-1 cursor-pointer">Salin</button>
                            </div>
                            <span class="text-[10px] text-slate-500">Bisa diubah pemilik nanti</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-teal-100 shadow-2xs">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Kucing Terdaftar</span>
                            <p class="font-bold text-slate-800 truncate mt-0.5">{{ $summary['cat_name'] }}</p>
                            <span class="font-mono text-[10px] text-teal-700 font-semibold">{{ $summary['cat_code'] }}</span>
                        </div>
                    </div>

                    <!-- Next steps guidance for volunteer -->
                    <div class="bg-teal-100/60 border border-teal-200/80 rounded-lg p-3 text-xs text-teal-950 space-y-1.5">
                        <p class="font-bold flex items-center gap-1.5 text-teal-900">
                            <span>💡</span> Langkah Selanjutnya untuk Disampaikan ke Pemilik Kucing:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-slate-700 text-[11px] leading-relaxed pl-1">
                            <li><strong>Informasikan Password:</strong> Beritahu pemilik bahwa mereka dapat login ke KucingMu dengan email <strong>{{ $summary['owner_email'] }}</strong> dan password bawaan: <code class="bg-white px-1.5 py-0.5 rounded font-mono font-bold text-amber-800 border border-amber-200">{{ $summary['default_password'] }}</code>.</li>
                            <li><strong>Akses Member:</strong> Setelah login, pemilik dapat melihat rekam medis pemeriksaan, sertifikat vaksin, dan Kartu Tanda Anggota (KTAM) kucing secara digital.</li>
                            <li><strong>Menunggu Panggilan Dokter:</strong> Kucing sudah otomatis masuk ke daftar antrian periksa dokter hari ini. Pemilik dipersilakan menunggu di ruang tunggu pemeriksaan.</li>
                        </ul>
                    </div>

                    <!-- Action buttons (copy message for WhatsApp / dismiss) -->
                    @php
                        $waTemplate = "Halo Kak " . $summary['owner_name'] . ",\n\nPendaftaran kucing *" . $summary['cat_name'] . "* di KucingMu telah berhasil!\n\nBerikut informasi akun KucingMu Anda:\n- Email Login: " . $summary['owner_email'] . "\n- Password Bawaan: " . $summary['default_password'] . "\n- Kode Kucing: " . $summary['cat_code'] . "\n\nKucing Anda telah otomatis masuk ke antrian periksa dokter hari ini. Anda dapat login ke web KucingMu untuk melihat rekam medis digital dan kartu KTAM.\n\nTerima kasih!\nTim Relawan KucingMu";
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-teal-100" x-data="{ copiedMsg: false }">
                        <button type="button" 
                                @click="copyText(`{{ addslashes($waTemplate) }}`, 'Format pesan WhatsApp berhasil disalin!'); copiedMsg = true; setTimeout(() => copiedMsg = false, 3000);"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white font-semibold text-xs transition shadow-2xs cursor-pointer">
                            <span>📋</span>
                            <span x-text="copiedMsg ? 'Tersalin ke Clipboard! ✓' : 'Salin Format Pesan WhatsApp untuk Pemilik'">Salin Format Pesan WhatsApp untuk Pemilik</span>
                        </button>
                        @if(!empty($summary['owner_phone']))
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $summary['owner_phone']) }}?text={{ rawurlencode($waTemplate) }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-2xs">
                                <span>💬</span> Kirim Langsung via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-2" role="alert">
                    <div class="flex items-center gap-2 font-bold text-rose-800">
                        <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Gagal Menyimpan Data. Silakan periksa formulir pendaftaran:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Sync Success Alert (Client Side) -->
            <div x-show="syncSuccessMsg" x-transition class="p-4 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center justify-between" role="status">
                <span x-text="syncSuccessMsg"></span>
                <button type="button" @click="syncSuccessMsg = null" class="font-semibold text-teal-900 text-xs underline p-1">Tutup</button>
            </div>

            <!-- Grid Layout -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Left Section: Register Forms (Online & Offline Tab) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Tabs -->
                    <div class="content-card">
                        <div class="flex border-b border-slate-200 mb-5" role="tablist">
                            <button type="button" role="tab" @click="activeTab = 'online'" :aria-selected="activeTab === 'online'" :class="activeTab === 'online' ? 'border-teal-700 text-teal-900 font-bold border-b-2 bg-slate-50' : 'text-slate-600 hover:text-slate-900'" class="min-h-[44px] py-2.5 px-4 text-xs font-semibold focus-visible:ring-2 focus-visible:ring-teal-700 transition">
                                Registrasi Langsung (Online)
                            </button>
                            <button type="button" role="tab" @click="activeTab = 'offline'" :aria-selected="activeTab === 'offline'" :class="activeTab === 'offline' ? 'border-teal-700 text-teal-900 font-bold border-b-2 bg-slate-50' : 'text-slate-600 hover:text-slate-900'" class="min-h-[44px] py-2.5 px-4 text-xs font-semibold focus-visible:ring-2 focus-visible:ring-teal-700 transition flex items-center gap-2">
                                Mode Lapangan (Offline)
                                <span class="h-2 w-2 rounded-full bg-amber-500" x-show="offlineQueue.length > 0" aria-label="Ada data pending"></span>
                            </button>
                        </div>

                        <!-- ONLINE REGISTER FORM -->
                        <div x-show="activeTab === 'online'" role="tabpanel">
                            <p class="text-xs text-slate-600 mb-3">Pendaftaran cepat untuk peserta yang datang langsung di lokasi pemeriksaan medis hari ini.</p>
                            
                            <!-- Information banner explaining password and procedure -->
                            <div class="bg-blue-50/80 border border-blue-200 rounded-lg p-3 text-xs text-blue-900 flex items-start gap-2.5 mb-4">
                                <span class="text-base leading-none">ℹ️</span>
                                <div class="space-y-1">
                                    <strong class="font-semibold text-blue-950">Informasi Akun Member & Prosedur Pendaftaran:</strong>
                                    <p class="text-[11px] text-blue-800 leading-relaxed">
                                        Member baru akan dibuatkan akun otomatis dengan <strong>Password default: <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-blue-200 text-teal-800 font-bold">kucingmu123</code></strong>. Jika email pemilik sudah pernah terdaftar, kucing akan langsung ditambahkan ke akun tersebut. Setelah pendaftaran disimpan, kucing otomatis masuk ke antrian periksa dokter hari ini.
                                    </p>
                                </div>
                            </div>
                            
                            <form method="POST" action="{{ route('quick-register') }}" class="space-y-5"
                                  x-data="{ isSubmittingQuick: false }"
                                  @submit="if(isSubmittingQuick) { $event.preventDefault(); return false; } isSubmittingQuick = true;">
                                @csrf
                                <div class="grid gap-4 md:grid-cols-2">
                                    <!-- Owner Info -->
                                    <div class="space-y-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-teal-800 border-b border-slate-200 pb-1">1. Data Pemilik Kucing</h3>
                                        <div>
                                            <label class="form-label text-xs">Nama Lengkap Pemilik</label>
                                            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="form-input text-xs" placeholder="Contoh: Siti Rahma">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Alamat Email</label>
                                            <input type="email" name="owner_email" value="{{ old('owner_email') }}" required class="form-input text-xs" placeholder="Contoh: siti@email.com">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Nomor WhatsApp</label>
                                            <input type="text" name="owner_phone" value="{{ old('owner_phone') }}" required class="form-input text-xs" placeholder="Contoh: 0812345678">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">NBM Muhammadiyah <span class="text-slate-500 font-normal">(Opsional - 7 Digit)</span></label>
                                            <input type="text" name="owner_nbm" value="{{ old('owner_nbm') }}" class="form-input text-xs font-mono" placeholder="Contoh: 1.234.567" maxlength="11">
                                        </div>
                                    </div>

                                    <!-- Cat Info -->
                                    <div class="space-y-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-teal-800 border-b border-slate-200 pb-1">2. Data Kucing</h3>
                                        <div>
                                            <label class="form-label text-xs">Nama Kucing</label>
                                            <input type="text" name="cat_name" value="{{ old('cat_name') }}" required class="form-input text-xs" placeholder="Contoh: Milo">
                                        </div>
                                        <div x-data="{ selectedBreed: '{{ old('cat_breed', 'Domestik') }}' }" class="space-y-2">
                                            <label class="form-label text-xs">Ras / Jenis Kucing</label>
                                            <select name="cat_breed" x-model="selectedBreed" class="form-input text-xs">
                                                @php
                                                    $breedList = $masterBreeds ?? \App\Models\MasterBreed::getAllBreedNames();
                                                @endphp
                                                @foreach($breedList as $b)
                                                    <option value="{{ $b }}" {{ old('cat_breed', 'Domestik') === $b ? 'selected' : '' }}>{{ $b }}</option>
                                                @endforeach
                                                <option value="Lainnya" {{ old('cat_breed') === 'Lainnya' ? 'selected' : '' }}>➕ Lainnya (Input Sendiri)</option>
                                            </select>
                                            <div x-show="selectedBreed === 'Lainnya'" x-transition class="bg-amber-50/80 p-2 rounded-lg border border-amber-200">
                                                <input type="text" name="cat_breed_custom" value="{{ old('cat_breed_custom') }}" placeholder="Tuliskan nama ras baru..." class="form-input text-xs bg-white w-full">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Jenis Kelamin</label>
                                            <select name="cat_gender" required class="form-input text-xs">
                                                <option value="male" {{ old('cat_gender', 'male') === 'male' ? 'selected' : '' }}>Jantan</option>
                                                <option value="female" {{ old('cat_gender') === 'female' ? 'selected' : '' }}>Betina</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Tanggal Lahir / Estimasi</label>
                                            <input type="date" name="cat_dob" max="{{ date('Y-m-d') }}" value="{{ old('cat_dob') }}" class="form-input text-xs">
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-slate-200 pt-4">
                                    <button type="submit" 
                                            :disabled="isSubmittingQuick"
                                            class="w-full button-primary text-xs font-semibold py-2.5 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <template x-if="isSubmittingQuick">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="isSubmittingQuick ? 'Menyimpan & Mendaftarkan...' : 'Simpan & Daftarkan ke Antrian Hari Ini'">Simpan & Daftarkan ke Antrian Hari Ini</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- OFFLINE REGISTER FORM -->
                        <div x-show="activeTab === 'offline'" role="tabpanel" style="display: none;">
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-900 mb-4">
                                <strong>Mode Offline:</strong> Data disimpan di penyimpanan browser lokal dan dapat disinkronkan saat koneksi internet tersedia. Member yang didaftarkan akan memiliki password default: <code>kucingmu123</code>.
                            </div>
                            
                            <form @submit.prevent="saveOfflineRegistration()" class="space-y-5">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <!-- Owner Info -->
                                    <div class="space-y-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 border-b border-slate-200 pb-1">1. Data Pemilik</h3>
                                        <div>
                                            <label class="form-label text-xs">Nama Lengkap</label>
                                            <input type="text" x-model="offlineForm.owner_name" required class="form-input text-xs" placeholder="Nama pemilik">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Alamat Email</label>
                                            <input type="email" x-model="offlineForm.owner_email" required class="form-input text-xs" placeholder="email@domain.com">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Nomor WhatsApp</label>
                                            <input type="text" x-model="offlineForm.owner_phone" required class="form-input text-xs" placeholder="08xxxxxxxx">
                                        </div>
                                    </div>

                                    <!-- Cat Info -->
                                    <div class="space-y-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 border-b border-slate-200 pb-1">2. Data Kucing</h3>
                                        <div>
                                            <label class="form-label text-xs">Nama Kucing</label>
                                            <input type="text" x-model="offlineForm.cat_name" required class="form-input text-xs" placeholder="Nama kucing">
                                        </div>
                                        <div x-data="{ offlineBreedMode: 'Domestik' }" class="space-y-2">
                                            <label class="form-label text-xs">Ras Kucing</label>
                                            <select x-model="offlineBreedMode" @change="offlineForm.cat_breed = (offlineBreedMode === 'Lainnya' ? '' : offlineBreedMode)" class="form-input text-xs">
                                                @php
                                                    $breedList = $masterBreeds ?? \App\Models\MasterBreed::getAllBreedNames();
                                                @endphp
                                                @foreach($breedList as $b)
                                                    <option value="{{ $b }}">{{ $b }}</option>
                                                @endforeach
                                                <option value="Lainnya">➕ Lainnya (Input Sendiri)</option>
                                            </select>
                                            <div x-show="offlineBreedMode === 'Lainnya'" x-transition class="bg-amber-50/80 p-2 rounded-lg border border-amber-200">
                                                <input type="text" x-model="offlineForm.cat_breed" placeholder="Tuliskan nama ras baru..." class="form-input text-xs bg-white w-full">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Jenis Kelamin</label>
                                            <select x-model="offlineForm.cat_gender" required class="form-input text-xs">
                                                <option value="male">Jantan</option>
                                                <option value="female">Betina</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-slate-200 pt-4">
                                    <button type="submit" class="w-full button-secondary text-xs font-semibold py-2.5 border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900">
                                        Simpan ke Antrian Offline Lokal
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- Right Column: Check-in Queue for Today -->
                <div class="space-y-6">
                    <div class="content-card">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                            <div>
                                <h2 class="font-outfit text-base font-bold text-slate-900">Antrian Periksa Hari Ini</h2>
                                <p class="text-[11px] text-slate-600">{{ \Carbon\Carbon::today()->format('d F Y') }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-teal-100 text-teal-900">{{ $todayAppointments->count() }} Antrian</span>
                        </div>

                        @if($todayAppointments->isEmpty())
                            <div class="text-center py-8 text-xs text-slate-600 bg-slate-50 rounded-lg border border-slate-200">
                                Tidak ada jadwal kunjungan atau antrian periksa untuk hari ini.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($todayAppointments as $app)
                                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50 space-y-2">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="font-bold text-slate-900 text-xs">{{ $app->cat->name }}</h3>
                                                <p class="text-[11px] text-slate-600">Pemilik: {{ $app->cat->owner->name }}</p>
                                                <p class="text-[11px] font-mono text-slate-500">{{ $app->time_slot }}</p>
                                            </div>
                                            <div>
                                                @if($app->status === 'scheduled')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900 border border-blue-200">Terjadwal</span>
                                                @elseif($app->status === 'checked_in')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">Checked-in</span>
                                                @elseif($app->status === 'completed')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200">Selesai</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($app->status === 'scheduled')
                                            <form method="POST" action="{{ route('appointment.checkin', $app->id) }}" class="pt-1">
                                                @csrf
                                                <button type="submit" class="w-full button-primary text-xs font-semibold py-1.5 min-h-[38px]">
                                                    Konfirmasi Kehadiran (Check-In)
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Clipboard helper & Alpine offline queue helper script -->
    <script>
        function copyText(text, msg) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    alert(msg || 'Berhasil disalin!');
                }).catch(() => {
                    fallbackCopyText(text, msg);
                });
            } else {
                fallbackCopyText(text, msg);
            }
        }

        function fallbackCopyText(text, msg) {
            const el = document.createElement('textarea');
            el.value = text;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert(msg || 'Berhasil disalin!');
        }

        function offlineManager() {
            return {
                isOnline: navigator.onLine,
                activeTab: 'online',
                syncing: false,
                syncSuccessMsg: null,
                offlineQueue: JSON.parse(localStorage.getItem('kucingmu_offline_queue') || '[]'),
                offlineForm: {
                    owner_name: '',
                    owner_email: '',
                    owner_phone: '',
                    cat_name: '',
                    cat_breed: 'Domestik',
                    cat_gender: 'male',
                    cat_dob: '{{ date("Y-m-d") }}'
                },
                init() {
                    window.addEventListener('online', () => { this.isOnline = true; });
                    window.addEventListener('offline', () => { this.isOnline = false; });
                },
                saveOfflineRegistration() {
                    this.offlineQueue.push({ ...this.offlineForm, savedAt: new Date().toISOString() });
                    localStorage.setItem('kucingmu_offline_queue', JSON.stringify(this.offlineQueue));
                    this.offlineForm = {
                        owner_name: '',
                        owner_email: '',
                        owner_phone: '',
                        cat_name: '',
                        cat_breed: 'Domestik',
                        cat_gender: 'male',
                        cat_dob: '{{ date("Y-m-d") }}'
                    };
                    alert('Data registrasi berhasil disimpan di memori offline lokal.');
                },
                async syncOfflineData() {
                    if (this.offlineQueue.length === 0 || this.syncing) return;
                    this.syncing = true;
                    try {
                        const response = await fetch('{{ route("volunteer.sync-offline") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ items: this.offlineQueue })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.offlineQueue = [];
                            localStorage.removeItem('kucingmu_offline_queue');
                            this.syncSuccessMsg = data.message || 'Sinkronisasi data offline selesai.';
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else {
                            alert('Sinkronisasi gagal: ' + (data.message || 'Terjadi kesalahan.'));
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server. Pastikan koneksi internet stabil.');
                    } finally {
                        this.syncing = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
