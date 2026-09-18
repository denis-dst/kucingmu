@php
    $authUser = Auth::user();
    $allRoles = $authUser ? $authUser->getAllRoles() : ['member'];
    $activeRole = $authUser ? $authUser->getActiveRole() : 'member';
    $activeMeta = \App\Models\User::getWorkspaceMeta($activeRole);
    $unreadContactCount = in_array($activeRole, ['admin', 'superadmin']) 
        ? \App\Models\ContactMessage::where('status', 'unread')->count() 
        : 0;
@endphp

<header class="bg-white border-b border-slate-200 sticky top-0 z-30 h-16 shrink-0 flex items-center shadow-xs">
    <div class="w-full px-4 sm:px-6 flex items-center justify-between gap-4">
        
        <!-- Left Side: Hamburger Toggle (Garis 3) & Brand / Title -->
        <div class="flex items-center gap-3">
            
            <!-- 3-Line Hamburger Button (Garis 3) -->
            <button @click="toggleSidebar()" 
                    type="button" 
                    class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-700 transition cursor-pointer flex items-center justify-center"
                    aria-label="Toggle Menu Samping (Buka/Tutup Sidebar)"
                    title="Buka / Tutup Menu Samping">
                <svg class="h-6 w-6 text-slate-700" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Brand / Logo (Visible when sidebar is closed or on mobile) -->
            <div class="flex items-center gap-2" :class="{ 'flex': !sidebarOpen, 'flex lg:hidden': sidebarOpen }">
                <a href="{{ url('/') }}" class="flex items-center gap-2 focus-visible:ring-2 focus-visible:ring-teal-700 rounded-lg p-1">
                    @if(isset($app_settings['app_logo']))
                        <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="" aria-hidden="true" width="28" height="28" class="h-7 w-auto object-contain" decoding="async">
                    @else
                        <span class="text-xl" aria-hidden="true">🐱</span>
                    @endif
                    <span class="font-outfit font-extrabold text-teal-800 text-base tracking-tight hidden sm:inline">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
                </a>
            </div>

            <!-- Active Workspace Indicator -->
            <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                <span class="text-sm">{{ $activeMeta['icon'] }}</span>
                <span>{{ $activeMeta['name'] }}</span>
            </div>
        </div>

        <!-- Right Side: Switcher, Notifications, & User Profile -->
        <div class="flex items-center gap-2 sm:gap-3">
            
            <!-- Quick Link to Contact Messages (for Admin) -->
            @if(in_array($activeRole, ['admin', 'superadmin']))
                <a href="{{ route('admin.contacts.index') }}" 
                   class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus-visible:ring-2 focus-visible:ring-teal-700"
                   title="Pesan Masuk ({{ $unreadContactCount }} belum dibaca)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    @if($unreadContactCount > 0)
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white animate-pulse"></span>
                    @endif
                </a>
            @endif

            <!-- Workspace / Role Switcher (If user has multiple roles) -->
            @if(count($allRoles) > 1)
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button type="button" 
                                class="inline-flex min-h-[38px] items-center gap-1.5 px-3 py-1.5 border rounded-xl text-xs font-bold transition shadow-2xs hover:shadow-xs focus-visible:ring-2 focus-visible:ring-teal-700 cursor-pointer {{ $activeMeta['badge_class'] }}"
                                title="Klik untuk beralih ruang kerja">
                            <span class="text-sm leading-none">{{ $activeMeta['icon'] }}</span>
                            <span class="truncate max-w-[130px] hidden md:inline">{{ $activeMeta['name'] }}</span>
                            <svg class="fill-current h-3.5 w-3.5 opacity-60 ml-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3.5 py-2 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            Beralih Ruang Kerja:
                        </div>
                        @foreach($allRoles as $r)
                            @php $rMeta = \App\Models\User::getWorkspaceMeta($r); @endphp
                            <form method="POST" action="{{ route('workspace.switch') }}">
                                @csrf
                                <input type="hidden" name="role" value="{{ $r }}">
                                <button type="submit" class="w-full text-left px-3.5 py-2.5 text-xs flex items-center justify-between hover:bg-slate-50 transition cursor-pointer {{ $activeRole === $r ? 'bg-teal-50/80 font-bold text-teal-950' : 'text-slate-700' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">{{ $rMeta['icon'] }}</span>
                                        <div>
                                            <div class="leading-tight">{{ $rMeta['name'] }}</div>
                                            <div class="text-[10px] text-slate-400 font-normal">{{ $rMeta['short_name'] }}</div>
                                        </div>
                                    </div>
                                    @if($activeRole === $r)
                                        <span class="text-teal-700 font-bold text-[11px] bg-teal-100/80 px-1.5 py-0.5 rounded">Aktif</span>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </x-slot>
                </x-dropdown>
            @endif

            <!-- Settings / User Dropdown -->
            @if($authUser)
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex min-h-[38px] items-center px-2.5 sm:px-3 py-1.5 border border-slate-200 text-xs sm:text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-teal-700 shadow-2xs gap-2 cursor-pointer">
                            <div class="w-6 h-6 rounded-lg bg-teal-800 text-white font-bold flex items-center justify-center text-[10px] shrink-0">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <span class="truncate max-w-[100px] sm:max-w-[120px] hidden sm:inline">{{ $authUser->name }}</span>
                            <svg class="fill-current h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3.5 py-2.5 border-b border-slate-100">
                            <div class="font-bold text-xs text-slate-900 truncate">{{ $authUser->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">{{ $authUser->email }}</div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <span>👤</span> {{ __('Profil Akun') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50">
                                <span>🚪</span> {{ __('Keluar (Log Out)') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="button-secondary text-xs font-semibold px-3 py-1.5">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="button-primary text-xs font-bold px-3 py-1.5">
                        Daftar
                    </a>
                </div>
            @endif
        </div>

    </div>
</header>
