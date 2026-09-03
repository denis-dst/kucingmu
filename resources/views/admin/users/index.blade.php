<x-app-layout>
    <div class="py-8" x-data="{ 
        roleModalOpen: false, 
        selectedUser: null, 
        selectedRole: '', 
        openRoleModal(user) { 
            this.selectedUser = user; 
            this.selectedRole = user.role; 
            this.roleModalOpen = true; 
        }, 
        closeRoleModal() { 
            this.roleModalOpen = false; 
            this.selectedUser = null; 
        } 
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                </div>
            @endif

            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Manajemen Pengguna & Rekrutmen Peran</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Kelola Pengguna, Rekrutmen Peran & Impersonasi
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Kelola seluruh data pengguna terdaftar, angkat/promosikan member aktif menjadi Relawan Sensus PTMA atau Dokter Hewan, serta lakukan login sebagai pengguna (Impersonate) untuk membantu penanganan kendala teknis.
                    </p>
                </div>
                <div class="hidden md:block text-5xl">
                    👥
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="{{ route('admin.users.index', ['role' => 'all']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 {{ $roleFilter === 'all' ? 'ring-2 ring-teal-600 bg-teal-50/40' : 'bg-white' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <span>Semua Pengguna</span>
                        <span>👥</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['total'] }}</span>
                        <span class="text-xs font-semibold text-slate-500">Akun</span>
                    </div>
                </a>

                <a href="{{ route('admin.users.index', ['role' => 'member']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 {{ $roleFilter === 'member' ? 'ring-2 ring-teal-600 bg-teal-50/40' : 'bg-white' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-teal-700 uppercase tracking-wider">
                        <span>Member Kucing</span>
                        <span>🐱</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl sm:text-3xl font-bold text-teal-900">{{ $stats['member'] }}</span>
                        <span class="text-xs font-semibold text-teal-700">Orang</span>
                    </div>
                </a>

                <a href="{{ route('admin.users.index', ['role' => 'volunteer']) }}" 
                   class="content-card p-4 transition hover:border-indigo-400 {{ $roleFilter === 'volunteer' ? 'ring-2 ring-indigo-600 bg-indigo-50/40' : 'bg-white' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-indigo-700 uppercase tracking-wider">
                        <span>Relawan (Volunteer)</span>
                        <span>📋</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl sm:text-3xl font-bold text-indigo-900">{{ $stats['volunteer'] }}</span>
                        <span class="text-xs font-semibold text-indigo-700">Orang</span>
                    </div>
                </a>

                <a href="{{ route('admin.users.index', ['role' => 'dokter']) }}" 
                   class="content-card p-4 transition hover:border-emerald-400 {{ $roleFilter === 'dokter' ? 'ring-2 ring-emerald-600 bg-emerald-50/40' : 'bg-white' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-700 uppercase tracking-wider">
                        <span>Dokter Hewan (Vet)</span>
                        <span>🩺</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl sm:text-3xl font-bold text-emerald-900">{{ $stats['dokter'] }}</span>
                        <span class="text-xs font-semibold text-emerald-700">Dokter</span>
                    </div>
                </a>

                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" 
                   class="content-card p-4 transition hover:border-amber-400 {{ $roleFilter === 'admin' ? 'ring-2 ring-amber-600 bg-amber-50/40' : 'bg-white' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-amber-700 uppercase tracking-wider">
                        <span>Administrator</span>
                        <span>🛡️</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl sm:text-3xl font-bold text-amber-900">{{ $stats['admin'] }}</span>
                        <span class="text-xs font-semibold text-amber-700">Staf</span>
                    </div>
                </a>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="content-card bg-white p-4 space-y-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    
                    <!-- Role Filter Tabs -->
                    <div class="md:col-span-6 flex flex-wrap gap-1.5">
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'all'])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'all' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Peran ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'member'])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'member' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🐱 Member ({{ $stats['member'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'volunteer'])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'volunteer' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            📋 Relawan ({{ $stats['volunteer'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'dokter'])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'dokter' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🩺 Dokter ({{ $stats['dokter'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'admin'])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'admin' ? 'bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🛡️ Admin ({{ $stats['admin'] }})
                        </a>
                    </div>

                    <!-- Search Input -->
                    <div class="md:col-span-6 flex gap-2">
                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-sm">🔍</span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari nama, email, nomor HP, atau NBM Muhammadiyah..." 
                                   class="form-input pl-9 text-xs">
                        </div>
                        <button type="submit" class="button-primary text-xs px-4 py-2 font-bold whitespace-nowrap">
                            Cari
                        </button>
                        @if(!empty($search) || $roleFilter !== 'all')
                            <a href="{{ route('admin.users.index') }}" class="button-secondary text-xs px-3 py-2 font-semibold whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="content-card bg-white p-0 overflow-hidden shadow-xs border border-slate-200 rounded-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 divide-y divide-slate-100">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th scope="col" class="py-3.5 px-4">Pengguna</th>
                                <th scope="col" class="py-3.5 px-4">Kontak & NBM</th>
                                <th scope="col" class="py-3.5 px-4">Peran (Role)</th>
                                <th scope="col" class="py-3.5 px-4 text-center">Aktivitas</th>
                                <th scope="col" class="py-3.5 px-4 text-center">Terdaftar Sejak</th>
                                <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/80 transition {{ Auth::id() === $user->id ? 'bg-teal-50/30' : '' }}">
                                    <!-- User info -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs uppercase text-white shadow-xs
                                                @if($user->role === 'admin' || $user->role === 'superadmin') bg-amber-600
                                                @elseif($user->role === 'dokter') bg-emerald-600
                                                @elseif($user->role === 'volunteer') bg-indigo-600
                                                @else bg-teal-600 @endif">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                                    <span>{{ $user->name }}</span>
                                                    @if(Auth::id() === $user->id)
                                                        <span class="text-[9px] bg-teal-100 text-teal-800 font-extrabold px-1.5 py-0.2 rounded border border-teal-300">Akun Anda</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kontak & NBM -->
                                    <td class="py-3.5 px-4">
                                        <div class="space-y-0.5">
                                            <div class="font-semibold text-slate-800 flex items-center gap-1">
                                                <span class="text-slate-400 text-[11px]">📞</span>
                                                <span>{{ $user->phone ?: '-' }}</span>
                                            </div>
                                            <div class="text-[11px] text-slate-500 flex items-center gap-1">
                                                <span class="text-slate-400 text-[11px]">🆔</span>
                                                <span class="font-mono">{{ $user->muhammadiyah_id ? 'NBM: ' . \App\Models\User::formatNbm($user->muhammadiyah_id) : 'Bukan Anggota NBM' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role Badge -->
                                    <td class="py-3.5 px-4">
                                        @if($user->role === 'superadmin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-purple-100 text-purple-800 border border-purple-300">
                                                <span>👑</span> Super Administrator
                                            </span>
                                        @elseif($user->role === 'admin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                <span>🛡️</span> Administrator
                                            </span>
                                        @elseif($user->role === 'dokter')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <span>🩺</span> Dokter Hewan
                                            </span>
                                        @elseif($user->role === 'volunteer')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                <span>📋</span> Relawan Sensus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                                <span>🐱</span> Member
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aktivitas -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-2 text-xs">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[11px]" title="Jumlah Kucing Dimiliki">
                                                🐱 {{ $user->cats_count }} Ekor
                                            </span>
                                            @if($user->vet_records_count > 0)
                                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[11px]" title="Riwayat Pemeriksaan Dokter">
                                                    🩺 {{ $user->vet_records_count }} Periksa
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Terdaftar -->
                                    <td class="py-3.5 px-4 text-center text-slate-500 font-mono text-[11px]">
                                        {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Tombol Ubah Peran / Hire -->
                                            @if(Auth::id() !== $user->id && (!$user->isSuperAdmin() || Auth::user()->isSuperAdmin()))
                                                <button type="button" 
                                                        @click="openRoleModal({{ json_encode($user) }})"
                                                        class="btn-action-secondary py-1.5 px-2.5 text-[11px] font-bold flex items-center gap-1 hover:border-slate-400">
                                                    <span>🤝</span>
                                                    <span>Ubah Peran</span>
                                                </button>
                                            @endif

                                            <!-- Tombol Impersonate / Login Sebagai -->
                                            @if(Auth::id() !== $user->id)
                                                <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin masuk dan login sebagai {{ $user->name }} ({{ $user->role }})?')">
                                                    @csrf
                                                    <button type="submit" class="btn-action-primary py-1.5 px-2.5 text-[11px] font-bold flex items-center gap-1 shadow-xs hover:shadow">
                                                        <span>🎭</span>
                                                        <span>Login Sebagai</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">🔍</div>
                                        <p class="font-bold text-sm text-slate-600">Tidak ada data pengguna ditemukan.</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter peran di atas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                @if($users->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Role Assignment / Hire Modal Dialog -->
        <div x-show="roleModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs p-4"
             @keydown.escape.window="closeRoleModal()">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-100" @click.away="closeRoleModal()">
                
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-sm">
                            🤝
                        </div>
                        <div>
                            <h3 class="font-outfit font-bold text-slate-900 text-base leading-tight">
                                Angkat / Ubah Peran Pengguna
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="selectedUser ? 'Pengguna: ' + selectedUser.name + ' (' + selectedUser.email + ')' : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeRoleModal()" class="text-slate-400 hover:text-slate-700 font-bold text-xl leading-none">&times;</button>
                </div>

                <!-- Form Ubah Role -->
                <form :action="selectedUser ? '{{ url('/admin/users') }}/' + selectedUser.id + '/role' : '#'" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label font-bold text-slate-700">Pilih Peran Baru untuk Pengguna Ini:</label>
                        
                        <div class="space-y-2.5 mt-2">
                            <!-- 1. Member -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition hover:bg-teal-50/50"
                                   :class="selectedRole === 'member' ? 'border-teal-600 bg-teal-50/60 ring-2 ring-teal-100' : 'border-slate-200 bg-white'">
                                <input type="radio" name="role" value="member" x-model="selectedRole" class="mt-1 text-teal-700 focus:ring-teal-500">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                        <span>🐱</span> Member / Pemilik Kucing
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Dapat mendaftarkan kucing, mengajukan janji temu, dan mencetak Kartu Tanda Anggota KucingMu (KTAKuMu).</p>
                                </div>
                            </label>

                            <!-- 2. Relawan (Volunteer) -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition hover:bg-indigo-50/50"
                                   :class="selectedRole === 'volunteer' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-100' : 'border-slate-200 bg-white'">
                                <input type="radio" name="role" value="volunteer" x-model="selectedRole" class="mt-1 text-indigo-700 focus:ring-indigo-500">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                        <span>📋</span> Relawan Sensus PTMA & Surveilans
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Memiliki akses ke modul Sensus Kucing Kampus PTMA, pemindai AI MobileNet, serta input surveilans lapangan.</p>
                                </div>
                            </label>

                            <!-- 3. Dokter Hewan (Dokter) -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition hover:bg-emerald-50/50"
                                   :class="selectedRole === 'dokter' ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-100' : 'border-slate-200 bg-white'">
                                <input type="radio" name="role" value="dokter" x-model="selectedRole" class="mt-1 text-emerald-700 focus:ring-emerald-500">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                        <span>🩺</span> Dokter Hewan (Veterinarian)
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Memiliki wewenang mengisi rekam medis, riwayat vaksin, penanganan klinis, dan menyetujui kelaikan kesehatan kucing.</p>
                                </div>
                            </label>

                            <!-- 4. Administrator -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition hover:bg-amber-50/50"
                                   :class="selectedRole === 'admin' ? 'border-amber-600 bg-amber-50/60 ring-2 ring-amber-100' : 'border-slate-200 bg-white'">
                                <input type="radio" name="role" value="admin" x-model="selectedRole" class="mt-1 text-amber-700 focus:ring-amber-500">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                        <span>🛡️</span> Administrator Sistem
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Memiliki akses verifikasi KTAKuMu resmi, ekspor basis data, kelola event, dan manajemen pengguna.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeRoleModal()" class="button-secondary text-xs font-semibold py-2.5 px-4">
                            Batal
                        </button>
                        <button type="submit" class="button-primary text-xs font-bold py-2.5 px-5 flex items-center gap-1.5">
                            <span>💾</span>
                            <span>Simpan Peran Baru</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-app-layout>
