@php
    $currentSort = $sort ?? 'created_at';
    $currentDir = $direction ?? 'desc';

    $makeSortUrl = function($col) use ($currentSort, $currentDir) {
        $newDir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
        return route('admin.users.index', array_merge(request()->query(), [
            'sort' => $col,
            'direction' => $newDir,
            'page' => 1,
        ]));
    };

    $getSortIndicator = function($col) use ($currentSort, $currentDir) {
        if ($currentSort === $col) {
            return $currentDir === 'asc' ? ' ▲' : ' ▼';
        }
        return ' ⇅';
    };
@endphp

<x-app-layout>
    <div class="py-8" x-data="{ 
        roleModalOpen: false, 
        selectedUser: null, 
        selectedRoles: [], 
        openRoleModal(user, roles) { 
            this.selectedUser = user; 
            this.selectedRoles = Array.isArray(roles) ? [...roles] : (user.roles ? (typeof user.roles === 'string' ? JSON.parse(user.roles) : [...user.roles]) : [user.role || 'member']);
            if (!this.selectedRoles.includes('member')) {
                this.selectedRoles.push('member');
            }
            this.roleModalOpen = true; 
        }, 
        closeRoleModal() { 
            this.roleModalOpen = false; 
            this.selectedUser = null; 
            this.selectedRoles = [];
        },
        toggleRole(role) {
            if (role === 'member') return;
            const idx = this.selectedRoles.indexOf(role);
            if (idx > -1) {
                this.selectedRoles.splice(idx, 1);
            } else {
                this.selectedRoles.push(role);
            }
        },
        hasRole(role) {
            return this.selectedRoles.includes(role);
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
                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'all', 'page' => 1])) }}" 
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

                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'member', 'page' => 1])) }}" 
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

                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'volunteer', 'page' => 1])) }}" 
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

                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'dokter', 'page' => 1])) }}" 
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

                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'admin', 'page' => 1])) }}" 
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
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'all', 'page' => 1])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'all' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Peran ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'member', 'page' => 1])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'member' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🐱 Member ({{ $stats['member'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'volunteer', 'page' => 1])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'volunteer' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            📋 Relawan ({{ $stats['volunteer'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'dokter', 'page' => 1])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'dokter' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🩺 Dokter ({{ $stats['dokter'] }})
                        </a>
                        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role' => 'admin', 'page' => 1])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'admin' ? 'bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            🛡️ Admin ({{ $stats['admin'] }})
                        </a>
                    </div>

                    <!-- Search Input & Hidden Sort Fields -->
                    <div class="md:col-span-6 flex gap-2">
                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                        <input type="hidden" name="sort" value="{{ $currentSort }}">
                        <input type="hidden" name="direction" value="{{ $currentDir }}">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari nama, email, nomor HP, atau NBM Muhammadiyah..." 
                                   class="form-input pl-3.5 pr-9 text-xs">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400 text-sm">🔍</span>
                        </div>
                        <button type="submit" class="button-primary text-xs px-4 py-2 font-bold whitespace-nowrap">
                            Cari
                        </button>
                        @if(!empty($search) || $roleFilter !== 'all' || $currentSort !== 'created_at' || $currentDir !== 'desc')
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
                                <th scope="col" class="py-3.5 px-4">
                                    <a href="{{ $makeSortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition select-none group" title="Urutkan berdasarkan Nama Pengguna">
                                        <span>Pengguna</span>
                                        <span class="{{ $currentSort === 'name' ? 'text-teal-700 font-bold' : 'text-slate-400 group-hover:text-slate-600' }} text-[11px]">{{ $getSortIndicator('name') }}</span>
                                    </a>
                                </th>
                                <th scope="col" class="py-3.5 px-4">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ $makeSortUrl('phone') }}" class="inline-flex items-center gap-0.5 hover:text-teal-800 transition select-none group" title="Urutkan Kontak Telepon/HP">
                                            <span>Kontak</span>
                                            <span class="{{ $currentSort === 'phone' ? 'text-teal-700 font-bold' : 'text-slate-400 group-hover:text-slate-600' }} text-[11px]">{{ $getSortIndicator('phone') }}</span>
                                        </a>
                                        <span class="text-slate-300">/</span>
                                        <a href="{{ $makeSortUrl('muhammadiyah_id') }}" class="inline-flex items-center gap-0.5 hover:text-teal-800 transition select-none group" title="Urutkan NBM Muhammadiyah">
                                            <span>NBM</span>
                                            <span class="{{ $currentSort === 'muhammadiyah_id' ? 'text-teal-700 font-bold' : 'text-slate-400 group-hover:text-slate-600' }} text-[11px]">{{ $getSortIndicator('muhammadiyah_id') }}</span>
                                        </a>
                                    </div>
                                </th>
                                <th scope="col" class="py-3.5 px-4">
                                    <a href="{{ $makeSortUrl('role') }}" class="inline-flex items-center gap-1 hover:text-teal-800 transition select-none group" title="Urutkan Peran Utama">
                                        <span>Peran (Role)</span>
                                        <span class="{{ $currentSort === 'role' ? 'text-teal-700 font-bold' : 'text-slate-400 group-hover:text-slate-600' }} text-[11px]">{{ $getSortIndicator('role') }}</span>
                                    </a>
                                </th>
                                <th scope="col" class="py-3.5 px-4 text-center">
                                    <a href="{{ $makeSortUrl('cats_count') }}" class="inline-flex items-center justify-center gap-1 hover:text-teal-800 transition select-none group" title="Urutkan Jumlah Kucing Dimiliki">
                                        <span>Aktivitas</span>
                                        <span class="{{ $currentSort === 'cats_count' ? 'text-teal-700 font-bold' : 'text-slate-400 group-hover:text-slate-600' }} text-[11px]">{{ $getSortIndicator('cats_count') }}</span>
                                    </a>
                                </th>
                                <th scope="col" class="py-3.5 px-4 text-center">
                                    <a href="{{ $makeSortUrl('created_at') }}" class="inline-flex items-center justify-center gap-1 hover:text-teal-800 transition select-none group" title="Urutkan Tanggal Terdaftar">
                                        <span>Terdaftar Sejak</span>
                                        <span class="{{ $currentSort === 'created_at' ? 'text-teal-700 font-bold' : 'text-slate-400 group-hover:text-slate-600' }} text-[11px]">{{ $getSortIndicator('created_at') }}</span>
                                    </a>
                                </th>
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

                                    <!-- Role Badges (Multi-role support) -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-wrap gap-1 max-w-[220px]">
                                            @foreach($user->getAllRoles() as $userRole)
                                                @php
                                                    $meta = \App\Models\User::getWorkspaceMeta($userRole);
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $meta['badge_class'] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                    <span>{{ $meta['icon'] ?? '🏷️' }}</span>
                                                    <span>{{ $meta['short_name'] ?? ucfirst($userRole) }}</span>
                                                </span>
                                            @endforeach
                                        </div>
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
                                            <!-- Tombol Kelola Peran / Multi-Role -->
                                            @if(Auth::id() !== $user->id && (!$user->isSuperAdmin() || Auth::user()->isSuperAdmin()))
                                                <button type="button" 
                                                        @click="openRoleModal({{ json_encode($user) }}, {{ json_encode($user->getAllRoles()) }})"
                                                        class="btn-action-secondary py-1.5 px-2.5 text-[11px] font-bold flex items-center gap-1 hover:border-slate-400">
                                                    <span>🤝</span>
                                                    <span>Kelola Peran</span>
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
                                Kelola Hak Akses & Peran Pengguna
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="selectedUser ? 'Pengguna: ' + selectedUser.name + ' (' + selectedUser.email + ')' : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeRoleModal()" class="text-slate-400 hover:text-slate-700 font-bold text-xl leading-none">&times;</button>
                </div>

                <!-- Form Ubah Role Multi-Role -->
                <form :action="selectedUser ? '{{ url('/admin/users') }}/' + selectedUser.id + '/role' : '#'" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Always include member role -->
                    <input type="hidden" name="roles[]" value="member">

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="form-label font-bold text-slate-800">Pilih Peran Pengguna (Multi-Peran):</label>
                            <span class="text-[11px] text-teal-700 font-semibold">Dapat memilih lebih dari 1</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Member yang diangkat menjadi staf/relawan tetap memiliki ruang pemilik kucing untuk mengelola kucing peliharaannya sendiri.
                        </p>
                        
                        <div class="space-y-2 mt-3">
                            <!-- 1. Member (Permanen / Otomatis) -->
                            <div class="flex items-start gap-3 p-3 rounded-2xl border border-teal-200 bg-teal-50/50">
                                <input type="checkbox" checked disabled class="mt-1 rounded text-teal-700 focus:ring-teal-500 opacity-75">
                                <div>
                                    <div class="font-bold text-xs text-teal-900 flex items-center gap-1.5">
                                        <span>🐱</span> Member / Pemilik Kucing
                                        <span class="text-[9px] bg-teal-200/80 text-teal-900 font-extrabold px-1.5 py-0.2 rounded">Selalu Aktif</span>
                                    </div>
                                    <p class="text-[11px] text-teal-800/80 mt-0.5">Ruang pribadi untuk mendaftarkan kucing sendiri, memantau riwayat medis, dan mencetak KTA Kucing.</p>
                                </div>
                            </div>

                            <!-- 2. Relawan (Volunteer) -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition select-none"
                                   :class="hasRole('volunteer') ? 'border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-100' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="checkbox" name="roles[]" value="volunteer" :checked="hasRole('volunteer')" @change="toggleRole('volunteer')" class="mt-1 rounded text-indigo-700 focus:ring-indigo-500">
                                <div class="flex-1">
                                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5"><span>📋</span> Relawan Sensus PTMA & Lapangan</span>
                                        <span x-show="hasRole('volunteer')" class="text-[9px] bg-indigo-100 text-indigo-800 font-bold px-1.5 py-0.2 rounded">Aktif</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Akses ke Ruang Relawan untuk mendata kucing kampus/liar, sensus populasi, dan surveilans.</p>
                                </div>
                            </label>

                            <!-- 3. Dokter Hewan (Dokter) -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition select-none"
                                   :class="hasRole('dokter') ? 'border-emerald-600 bg-emerald-50/70 ring-2 ring-emerald-100' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="checkbox" name="roles[]" value="dokter" :checked="hasRole('dokter')" @change="toggleRole('dokter')" class="mt-1 rounded text-emerald-700 focus:ring-emerald-500">
                                <div class="flex-1">
                                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5"><span>🩺</span> Dokter Hewan (Veterinarian)</span>
                                        <span x-show="hasRole('dokter')" class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.2 rounded">Aktif</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Akses ke Ruang Dokter untuk rekam medis klinik, diagnosis, tindakan, dan vaksinasi.</p>
                                </div>
                            </label>

                            <!-- 4. Administrator -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition select-none"
                                   :class="hasRole('admin') ? 'border-amber-600 bg-amber-50/70 ring-2 ring-amber-100' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="checkbox" name="roles[]" value="admin" :checked="hasRole('admin')" @change="toggleRole('admin')" class="mt-1 rounded text-amber-700 focus:ring-amber-500">
                                <div class="flex-1">
                                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5"><span>🛡️</span> Administrator Sistem</span>
                                        <span x-show="hasRole('admin')" class="text-[9px] bg-amber-100 text-amber-800 font-bold px-1.5 py-0.2 rounded">Aktif</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Akses ke Ruang Admin untuk kelola pengguna, verifikasi KTAKuMu resmi, dan event.</p>
                                </div>
                            </label>

                            @if(Auth::user()->isSuperAdmin())
                                <!-- 5. Super Administrator (Khusus Superadmin) -->
                                <label class="flex items-start gap-3 p-3 rounded-2xl border cursor-pointer transition select-none"
                                       :class="hasRole('superadmin') ? 'border-purple-600 bg-purple-50/70 ring-2 ring-purple-100' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                    <input type="checkbox" name="roles[]" value="superadmin" :checked="hasRole('superadmin')" @change="toggleRole('superadmin')" class="mt-1 rounded text-purple-700 focus:ring-purple-500">
                                    <div class="flex-1">
                                        <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                                            <span class="flex items-center gap-1.5"><span>👑</span> Super Administrator</span>
                                            <span x-show="hasRole('superadmin')" class="text-[9px] bg-purple-100 text-purple-800 font-bold px-1.5 py-0.2 rounded">Khusus Superadmin</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Wewenang tertinggi sistem, pengaturan server, dan delegasi hak superadmin.</p>
                                    </div>
                                </label>
                            @endif
                        </div>

                        <!-- Keterangan Workspace Switcher -->
                        <div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-[11px] flex items-start gap-2">
                            <span class="text-sm">💡</span>
                            <span>Pengguna dengan multi-peran akan mendapatkan menu <strong>Ganti Ruang Kerja</strong> di bilah navigasi atas untuk berpindah ruang kerja kapan saja.</span>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeRoleModal()" class="button-secondary text-xs font-semibold py-2.5 px-4">
                            Batal
                        </button>
                        <button type="submit" class="button-primary text-xs font-bold py-2.5 px-5 flex items-center gap-1.5">
                            <span>💾</span>
                            <span>Simpan Perubahan Peran</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-app-layout>
