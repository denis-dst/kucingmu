<x-app-layout>
    <div class="py-8" x-data="offlineManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="content-card bg-teal-900 text-white p-6 sm:p-8">
                <div class="max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Portal Relawan Lapangan</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-white mt-1">
                        Selamat Bertugas, {{ Auth::user()->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-teal-100 mt-2 leading-relaxed">
                        Fasilitasi pendaftaran langsung peserta di lokasi kegiatan, catat kehadiran check-in antrian dokter, dan sinkronkan data ketika kembali online.
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-2.5">
                        <!-- Connection Status Badge -->
                        <span :class="isOnline ? 'bg-teal-800 text-teal-100 border-teal-700' : 'bg-rose-900 text-rose-100 border-rose-800'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md border text-xs font-medium">
                            <span :class="isOnline ? 'bg-teal-400' : 'bg-rose-400'" class="h-2 w-2 rounded-full" aria-hidden="true"></span>
                            <span x-text="isOnline ? 'Status: Terhubung Online' : 'Status: Mode Offline (Lokal)'"></span>
                        </span>
                        
                        <!-- Offline Queue Status -->
                        <button x-show="offlineQueue.length > 0" @click="syncOfflineData()" :disabled="syncing" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-100 hover:bg-amber-200 border border-amber-300 text-amber-900 text-xs font-semibold cursor-pointer min-h-[38px]">
                            <span class="font-bold font-mono" x-text="offlineQueue.length"></span> Data Tersimpan Offline (Klik untuk Sinkronisasi)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Link to eSurveillance -->
            <a href="{{ route('volunteer.surveillance.index') }}" class="block content-card border-teal-200 bg-teal-50/50 hover:bg-teal-50 transition">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-800">Modul Surveilans Lapangan</span>
                        <h2 class="font-outfit text-lg font-bold text-slate-900 mt-0.5">eSurveillance Populasi Kucing Liar</h2>
                        <p class="text-xs text-slate-600 mt-1">Pendataan observasi titik kumpul kucing, kondisi fisik, ear-tip (sterilisasi), dan triase kesejahteraan hewan.</p>
                    </div>
                    <span class="button-secondary text-xs font-semibold px-3 py-1.5 shrink-0">Buka Formulir Surveilans</span>
                </div>
            </a>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center gap-2" role="alert">
                    <span aria-hidden="true">✓</span> {{ session('success') }}
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
                            <p class="text-xs text-slate-600 mb-4">Pendaftaran cepat untuk peserta yang datang langsung di lokasi pemeriksaan medis hari ini.</p>
                            
                            <form method="POST" action="{{ route('quick-register') }}" class="space-y-5">
                                @csrf
                                <div class="grid gap-4 md:grid-cols-2">
                                    <!-- Owner Info -->
                                    <div class="space-y-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-teal-800 border-b border-slate-200 pb-1">1. Data Pemilik Kucing</h3>
                                        <div>
                                            <label class="form-label text-xs">Nama Lengkap Pemilik</label>
                                            <input type="text" name="owner_name" required class="form-input text-xs" placeholder="Contoh: Siti Rahma">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Alamat Email</label>
                                            <input type="email" name="owner_email" required class="form-input text-xs" placeholder="Contoh: siti@email.com">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Nomor WhatsApp</label>
                                            <input type="text" name="owner_phone" required class="form-input text-xs" placeholder="Contoh: 0812345678">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">NBM Muhammadiyah <span class="text-slate-500 font-normal">(Opsional)</span></label>
                                            <input type="text" name="owner_nbm" class="form-input text-xs" placeholder="Contoh: 2026-NBM-123">
                                        </div>
                                    </div>

                                    <!-- Cat Info -->
                                    <div class="space-y-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-teal-800 border-b border-slate-200 pb-1">2. Data Kucing</h3>
                                        <div>
                                            <label class="form-label text-xs">Nama Kucing</label>
                                            <input type="text" name="cat_name" required class="form-input text-xs" placeholder="Contoh: Milo">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Ras / Jenis Kucing</label>
                                            <input type="text" name="cat_breed" required class="form-input text-xs" placeholder="Contoh: Domestik / Campuran">
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Jenis Kelamin</label>
                                            <select name="cat_gender" required class="form-input text-xs">
                                                <option value="male">Jantan</option>
                                                <option value="female">Betina</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label text-xs">Tanggal Lahir / Estimasi</label>
                                            <input type="date" name="cat_dob" required class="form-input text-xs">
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-slate-200 pt-4">
                                    <button type="submit" class="w-full button-primary text-xs font-semibold py-2.5">
                                        Simpan & Daftarkan ke Antrian Hari Ini
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- OFFLINE REGISTER FORM -->
                        <div x-show="activeTab === 'offline'" role="tabpanel" style="display: none;">
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-900 mb-4">
                                <strong>Mode Offline:</strong> Data disimpan di penyimpanan browser lokal dan dapat disinkronkan saat koneksi internet tersedia.
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
                                        <div>
                                            <label class="form-label text-xs">Ras Kucing</label>
                                            <input type="text" x-model="offlineForm.cat_breed" required class="form-input text-xs" placeholder="Ras / jenis">
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

    <!-- Alpine offline queue helper script -->
    <script>
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
                    cat_breed: '',
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
                        cat_breed: '',
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
