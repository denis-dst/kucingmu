@php
    $authUser = Auth::user();
    $allRoles = $authUser ? $authUser->getAllRoles() : ['member'];
    $activeRole = $authUser ? $authUser->getActiveRole() : 'member';
    $activeMeta = \App\Models\User::getWorkspaceMeta($activeRole);
    $unreadContactCount = in_array($activeRole, ['admin', 'superadmin']) 
        ? \App\Models\ContactMessage::where('status', 'unread')->count() 
        : 0;
@endphp

<!-- Mobile Backdrop Overlay -->
<div x-show="mobileSidebarOpen" 
     x-cloak
     @click="mobileSidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"
     aria-hidden="true">
</div>

<!-- Sidebar Container -->
<aside :class="{
        'translate-x-0': mobileSidebarOpen,
        '-translate-x-full': !mobileSidebarOpen,
        'lg:translate-x-0 lg:w-64': sidebarOpen,
        'lg:-translate-x-full lg:w-0 lg:overflow-hidden lg:border-r-0': !sidebarOpen
       }"
       class="fixed lg:sticky top-0 left-0 z-50 lg:z-30 h-screen bg-white border-r border-slate-200 flex flex-col justify-between transition-all duration-300 ease-in-out shrink-0 w-72 lg:w-64 select-none shadow-xl lg:shadow-none"
       aria-label="Sidebar Navigasi">

    <!-- Top Section: Header & Nav Links -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
        
        <!-- Sidebar Brand Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100 shrink-0 bg-white">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-visible:ring-2 focus-visible:ring-teal-700 rounded-lg p-1">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="" aria-hidden="true" width="32" height="32" class="h-8 w-auto object-contain" decoding="async">
                @else
                    <span class="text-2xl" aria-hidden="true">🐱</span>
                @endif
                <div class="flex flex-col">
                    <span class="font-outfit font-extrabold text-teal-800 text-lg tracking-tight leading-none">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wide uppercase mt-0.5">Sistem Terpadu</span>
                </div>
            </a>

            <!-- Close button for Mobile only -->
            <button @click="mobileSidebarOpen = false" 
                    type="button" 
                    class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-teal-700"
                    aria-label="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Active Workspace Banner -->
        <div class="p-3 mx-3 my-3 rounded-2xl bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-100/80 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="text-xl shrink-0 p-1.5 bg-white rounded-xl shadow-2xs border border-teal-100">{{ $activeMeta['icon'] }}</span>
                <div class="min-w-0">
                    <div class="text-[10px] font-bold text-teal-700 uppercase tracking-wider">Ruang Kerja</div>
                    <div class="text-xs font-bold text-slate-800 truncate">{{ $activeMeta['name'] }}</div>
                </div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-700 text-white shadow-2xs">
                {{ $activeMeta['short_name'] }}
            </span>
        </div>

        <!-- Navigation Links List -->
        <nav class="flex-1 px-3 py-2 space-y-5">
            
            <!-- Section 1: Navigasi Utama -->
            <div class="space-y-1">
                <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Utama
                </div>

                <!-- Dashboard / Kucing Saya -->
                <a href="{{ route('dashboard') }}" 
                   class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <span class="text-base {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500' }}">
                        {{ $activeRole === 'member' ? '🐱' : '📊' }}
                    </span>
                    <span>{{ $activeRole === 'member' ? 'Kucing Saya' : 'Dashboard' }}</span>
                </a>

                <!-- Hubungi Kami (for Member) -->
                @if($activeRole === 'member')
                    <a href="{{ route('contact.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('contact.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('contact.*') ? 'text-white' : 'text-slate-500' }}">💬</span>
                        <span>Hubungi Kami</span>
                    </a>
                @endif
            </div>

            <!-- Section 2: Relawan & Surveilans Medis -->
            @if($activeRole === 'volunteer' || ($authUser && $authUser->hasRole('volunteer') && $activeRole !== 'member') || $activeRole === 'dokter')
                <div class="space-y-1">
                    <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Layanan & Surveilans
                    </div>

                    <a href="{{ route('volunteer.census.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('volunteer.census.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('volunteer.census.*') ? 'text-white' : 'text-slate-500' }}">📋</span>
                        <span>Sensus PTMA</span>
                    </a>

                    <a href="{{ route('volunteer.surveillance.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('volunteer.surveillance.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('volunteer.surveillance.*') ? 'text-white' : 'text-slate-500' }}">🩺</span>
                        <span>eSurveillance Kucing</span>
                    </a>
                </div>
            @endif

            <!-- Section 3: Panel Admin & Master Data -->
            @if(in_array($activeRole, ['admin', 'superadmin']))
                <div class="space-y-1">
                    <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Administrasi & Konten
                    </div>

                    <!-- Pesan Masuk -->
                    <a href="{{ route('admin.contacts.index') }}" 
                       class="sidebar-item flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.contacts.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-base {{ request()->routeIs('admin.contacts.*') ? 'text-white' : 'text-slate-500' }}">✉️</span>
                            <span>Pesan Masuk</span>
                        </div>
                        @if($unreadContactCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.contacts.*') ? 'bg-white text-teal-900' : 'bg-rose-600 text-white' }} shadow-2xs">
                                {{ $unreadContactCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Kelola Pengguna -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-500' }}">👥</span>
                        <span>Kelola Pengguna</span>
                    </a>

                    <!-- Master Wilayah -->
                    <a href="{{ route('superadmin.wilayah.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.wilayah.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('superadmin.wilayah.*') ? 'text-white' : 'text-slate-500' }}">🗺️</span>
                        <span>Master Wilayah</span>
                    </a>

                    <!-- Album Kegiatan -->
                    <a href="{{ route('superadmin.albums.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.albums.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('superadmin.albums.*') ? 'text-white' : 'text-slate-500' }}">🖼️</span>
                        <span>Album Kegiatan</span>
                    </a>

                    <!-- Kelola Event -->
                    <a href="{{ route('admin.events.index') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.events.*') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('admin.events.*') ? 'text-white' : 'text-slate-500' }}">📅</span>
                        <span>Kelola Event</span>
                    </a>

                    <!-- Pengaturan -->
                    <a href="{{ route('admin.settings') }}" 
                       class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.settings') ? 'bg-teal-700 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="text-base {{ request()->routeIs('admin.settings') ? 'text-white' : 'text-slate-500' }}">⚙️</span>
                        <span>Pengaturan</span>
                    </a>
                </div>
            @endif

            <!-- Workspace Switcher on Sidebar (if multi-role) -->
            @if(count($allRoles) > 1)
                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Beralih Ruang Kerja
                    </div>
                    <div class="grid grid-cols-1 gap-1">
                        @foreach($allRoles as $r)
                            @php $rMeta = \App\Models\User::getWorkspaceMeta($r); @endphp
                            <form method="POST" action="{{ route('workspace.switch') }}">
                                @csrf
                                <input type="hidden" name="role" value="{{ $r }}">
                                <button type="submit" 
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold border transition cursor-pointer {{ $activeRole === $r ? 'bg-teal-50 border-teal-300 text-teal-950 font-bold' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="text-sm">{{ $rMeta['icon'] }}</span>
                                        <span class="truncate">{{ $rMeta['name'] }}</span>
                                    </div>
                                    @if($activeRole === $r)
                                        <span class="text-[9px] bg-teal-600 text-white px-1.5 py-0.5 rounded font-bold shrink-0">Aktif</span>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

        </nav>
    </div>

    <!-- Bottom User / Footer Section -->
    <div class="p-3 border-t border-slate-100 bg-slate-50/70 space-y-2 shrink-0">
        @if($authUser)
            <div class="p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between gap-2">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 group hover:opacity-80 transition" title="Lihat Profil Akun">
                    <div class="w-8 h-8 rounded-lg bg-teal-800 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800 truncate group-hover:text-teal-700 transition">{{ $authUser->name }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ $authUser->email }}</div>
                    </div>
                </a>

                <!-- Quick Logout -->
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Keluar (Log Out)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        @endif

        <div class="px-2 py-1 text-center">
            <span class="text-[10px] text-slate-400 font-medium">🌿 MLH PP Muhammadiyah</span>
        </div>
    </div>
</aside>
