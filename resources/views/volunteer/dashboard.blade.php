<x-app-layout>
    <div class="py-12" x-data="offlineManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Relawan Lapangan</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Tugas Relawan, {{ Auth::user()->name }}!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Bantu pendaftaran peserta, kelola kedatangan check-in kucing di lokasi event, dan gunakan mode offline apabila koneksi internet mengalami hambatan.
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-3">
                        <!-- Connection Status Badge -->
                        <span :class="isOnline ? 'bg-teal-100 text-teal-800 border-teal-200' : 'bg-rose-100 text-rose-800 border-rose-200'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-semibold">
                            <span :class="isOnline ? 'bg-teal-500' : 'bg-rose-500'" class="h-2 w-2 rounded-full"></span>
                            <span x-text="isOnline ? 'Koneksi Online' : 'Koneksi Offline (Lokal)'"></span>
                        </span>
                        
                        <!-- Offline Queue Status -->
                        <button x-show="offlineQueue.length > 0" @click="syncOfflineData()" :disabled="syncing" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 hover:bg-amber-200 border border-amber-200 text-amber-800 text-xs font-semibold cursor-pointer">
                            <span class="animate-bounce font-bold" x-text="offlineQueue.length"></span> Antrian Offline Pending (Singkronkan)
                        </button>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    📋
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center gap-2">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Sync Success Alert (Client Side) -->
            <div x-show="syncSuccessMsg" x-transition class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center justify-between">
                <span x-text="syncSuccessMsg"></span>
                <button @click="syncSuccessMsg = null" class="font-bold text-teal-900 text-xs">Tutup</button>
            </div>

            <!-- Grid Layout -->
            <div class="grid gap-8 lg:grid-cols-3">
                
                <!-- Left Section: Register Forms (Online & Offline Tab) -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Tabs -->
                    <div class="content-card">
                        <div class="flex border-b border-slate-200 mb-6">
                            <button @click="activeTab = 'online'" :class="activeTab === 'online' ? 'border-teal-700 text-teal-800 font-bold border-b-2' : 'text-slate-500'" class="py-2.5 px-4 text-sm focus:outline-none transition">
                                Registrasi Langsung (Online)
                            </button>
                            <button @click="activeTab = 'offline'" :class="activeTab === 'offline' ? 'border-teal-700 text-teal-800 font-bold border-b-2' : 'text-slate-500'" class="py-2.5 px-4 text-sm focus:outline-none transition flex items-center gap-2">
                                Mode Lapangan (Offline First)
                                <span class="h-2 w-2 rounded-full bg-amber-500" x-show="offlineQueue.length > 0"></span>
                            </button>
                        </div>

                        <!-- ONLINE REGISTER FORM -->
                        <div x-show="activeTab === 'online'">
                            <h3 class="text-sm font-semibold text-slate-500 mb-4">Registrasi pemilik dan kucing secara online untuk masuk ke antrian pemeriksaan langsung hari ini.</h3>
                            
                            <form method="POST" action="{{ route('quick-register') }}" class="space-y-6">
                                @csrf
                                <div class="grid gap-4 md:grid-cols-2">
                                    <!-- Owner Info -->
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700">1. Data Pemilik Kucing</h4>
                                        <div>
                                            <label class="form-label">Nama Lengkap Pemilik</label>
                                            <input type="text" name="owner_name" required class="form-input" placeholder="e.g. Siti Rahma">
                                        </div>
                                        <div>
                                            <label class="form-label">Alamat Email</label>
                                            <input type="email" name="owner_email" required class="form-input" placeholder="e.g. siti@email.com">
                                        </div>
                                        <div>
                                            <label class="form-label">Nomor WhatsApp</label>
                                            <input type="text" name="owner_phone" required class="form-input" placeholder="e.g. 0812345678">
                                        </div>
                                        <div>
                                            <label class="form-label">NBM Muhammadiyah <span class="text-xs text-slate-400">(Opsional)</span></label>
                                            <input type="text" name="owner_nbm" class="form-input" placeholder="e.g. 2026-NBM-123">
                                        </div>
                                    </div>

                                    <!-- Cat Info -->
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700">2. Data Kucing</h4>
                                        <div>
                                            <label class="form-label">Nama Kucing</label>
                                            <input type="text" name="cat_name" required class="form-input" placeholder="e.g. Milo">
                                        </div>
                                        <div>
                                            <label class="form-label">Ras Kucing</label>
                                            <input type="text" name="cat_breed" required class="form-input" placeholder="e.g. Ragdoll / Domestik">
                                        </div>
                                        <div>
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select name="cat_gender" required class="form-input py-2">
                                                <option value="male">Jantan</option>
                                                <option value="female">Betina</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input type="date" name="cat_dob" required class="form-input">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full button-primary flex justify-center py-3">
                                    Simpan & Check-in Antrian
                                </button>
                            </form>
                        </div>

                        <!-- OFFLINE REGISTER FORM (Save to LocalStorage) -->
                        <div x-show="activeTab === 'offline'">
                            <div class="p-3.5 mb-5 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-xs leading-relaxed">
                                <strong>⚠️ Mode Lapangan:</strong> Gunakan form ini ketika koneksi internet bermasalah. Data yang dimasukkan akan disimpan di browser Anda secara lokal. Tekan tombol singkronkan di atas apabila koneksi internet kembali online.
                            </div>

                            <form @submit.prevent="saveOfflineEntry()" class="space-y-6">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <!-- Owner Info -->
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-700">1. Data Pemilik (Offline)</h4>
                                        <div>
                                            <label class="form-label">Nama Lengkap Pemilik</label>
                                            <input type="text" x-model="offlineForm.owner_name" required class="form-input" placeholder="e.g. Siti Rahma">
                                        </div>
                                        <div>
                                            <label class="form-label">Alamat Email</label>
                                            <input type="email" x-model="offlineForm.owner_email" required class="form-input" placeholder="e.g. siti@email.com">
                                        </div>
                                        <div>
                                            <label class="form-label">Nomor WhatsApp</label>
                                            <input type="text" x-model="offlineForm.owner_phone" required class="form-input" placeholder="e.g. 0812345678">
                                        </div>
                                        <div>
                                            <label class="form-label">NBM Muhammadiyah</label>
                                            <input type="text" x-model="offlineForm.owner_nbm" class="form-input" placeholder="e.g. 2026-NBM-123">
                                        </div>
                                    </div>

                                    <!-- Cat Info -->
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-700">2. Data Kucing (Offline)</h4>
                                        <div>
                                            <label class="form-label">Nama Kucing</label>
                                            <input type="text" x-model="offlineForm.cat_name" required class="form-input" placeholder="e.g. Milo">
                                        </div>
                                        <div>
                                            <label class="form-label">Ras Kucing</label>
                                            <input type="text" x-model="offlineForm.cat_breed" required class="form-input" placeholder="e.g. Ragdoll / Domestik">
                                        </div>
                                        <div>
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select x-model="offlineForm.cat_gender" required class="form-input py-2">
                                                <option value="male">Jantan</option>
                                                <option value="female">Betina</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input type="date" x-model="offlineForm.cat_dob" required class="form-input">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 text-sm transition">
                                    Simpan dalam Antrian Lokal
                                </button>
                            </form>

                            <!-- Offline Queue List Preview -->
                            <div class="mt-8 border-t border-slate-100 pt-6" x-show="offlineQueue.length > 0">
                                <h4 class="font-bold text-slate-800 text-sm mb-4">Daftar Antrian Offline Saat Ini</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="border-b border-slate-100 text-slate-400 font-bold">
                                                <th class="pb-2">Kucing</th>
                                                <th class="pb-2">Pemilik</th>
                                                <th class="pb-2">WhatsApp</th>
                                                <th class="pb-2">Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                            <template x-for="(entry, index) in offlineQueue" :key="index">
                                                <tr>
                                                    <td class="py-3 font-bold" x-text="entry.cat_name"></td>
                                                    <td class="py-3" x-text="entry.owner_name"></td>
                                                    <td class="py-3" x-text="entry.owner_phone"></td>
                                                    <td class="py-3">
                                                        <button @click="deleteOfflineEntry(index)" class="text-rose-600 hover:text-rose-800 font-bold">Hapus</button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Section: Today's Arrival / Check-in List -->
                <div class="space-y-8">
                    <div class="content-card">
                        <div class="border-b border-slate-100 pb-3 mb-4 flex items-center justify-between">
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Kedatangan Hari Ini</h2>
                            <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-full">{{ $todayAppointments->count() }} Jadwal</span>
                        </div>

                        @if($todayAppointments->isEmpty())
                            <p class="text-xs text-slate-500 text-center py-4">Belum ada pemeriksaan terjadwal untuk hari ini.</p>
                        @else
                            <div class="space-y-3.5 max-h-[500px] overflow-y-auto">
                                @foreach($todayAppointments as $app)
                                    <div class="p-3.5 rounded-xl border border-slate-100 bg-white flex flex-col justify-between gap-3 shadow-sm">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-sm">{{ $app->cat->name }}</h4>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $app->cat->breed }} &bull; {{ $app->time_slot }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">Pemilik: {{ $app->cat->owner->name }}</p>
                                            </div>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $app->status == 'checked_in' ? 'bg-amber-50 text-amber-700' : ($app->status == 'completed' ? 'bg-teal-50 text-teal-700' : 'bg-slate-100 text-slate-700') }}">
                                                @if($app->status == 'scheduled') Menunggu @elseif($app->status == 'checked_in') Antrian Dokter @else Selesai @endif
                                            </span>
                                        </div>
                                        
                                        @if($app->status == 'scheduled')
                                            <form method="POST" action="{{ route('appointment.checkin', $app->id) }}">
                                                @csrf
                                                <button type="submit" class="w-full button-primary text-xs py-1.5 flex justify-center items-center">
                                                    Check-in Kucing
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

    <!-- Script for offline management -->
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
                    owner_nbm: '',
                    cat_name: '',
                    cat_breed: '',
                    cat_gender: 'male',
                    cat_dob: ''
                },
                init() {
                    window.addEventListener('online', () => this.isOnline = true);
                    window.addEventListener('offline', () => this.isOnline = false);
                },
                saveOfflineEntry() {
                    this.offlineQueue.push({...this.offlineForm});
                    localStorage.setItem('kucingmu_offline_queue', JSON.stringify(this.offlineQueue));
                    
                    // Reset cat form only
                    this.offlineForm.cat_name = '';
                    this.offlineForm.cat_breed = '';
                    this.offlineForm.cat_gender = 'male';
                    this.offlineForm.cat_dob = '';

                    alert('Registrasi disimpan secara offline. Kucing ditambahkan ke antrian lokal.');
                },
                deleteOfflineEntry(index) {
                    this.offlineQueue.splice(index, 1);
                    localStorage.setItem('kucingmu_offline_queue', JSON.stringify(this.offlineQueue));
                },
                async syncOfflineData() {
                    if (this.offlineQueue.length === 0) return;
                    if (!navigator.onLine) {
                        alert('Koneksi Anda masih offline. Silakan coba lagi ketika internet kembali tersambung.');
                        return;
                    }

                    this.syncing = true;
                    this.syncSuccessMsg = null;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        const response = await fetch('/sync-offline', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                entries: this.offlineQueue
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.syncSuccessMsg = result.message;
                            this.offlineQueue = [];
                            localStorage.removeItem('kucingmu_offline_queue');
                        } else {
                            alert('Terjadi kesalahan saat sinkronisasi data: ' + result.message);
                        }
                    } catch (e) {
                        alert('Gagal menyinkronkan data. Silakan cek koneksi server.');
                        console.error(e);
                    } finally {
                        this.syncing = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
