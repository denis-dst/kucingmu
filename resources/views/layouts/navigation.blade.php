@php
    $authUser = Auth::user();
    $allRoles = $authUser ? $authUser->getAllRoles() : ['member'];
    $activeRole = $authUser ? $authUser->getActiveRole() : 'member';
    $activeMeta = \App\Models\User::getWorkspaceMeta($activeRole);
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40" aria-label="Navigasi Utama">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 focus-visible:ring-2 focus-visible:ring-teal-700 rounded-lg p-1">
                        @if(isset($app_settings['app_logo']))
                            <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="" aria-hidden="true" width="32" height="32" class="h-8 w-auto object-contain" decoding="async">
                        @else
                            <span class="text-2xl" aria-hidden="true">🐱</span>
                        @endif
                        <span class="font-outfit font-extrabold text-teal-800 text-lg tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
                    </a>
                </div>

                <!-- Navigation Links based on Active Workspace Role -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-8 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ $activeRole === 'member' ? __('Kucing Saya') : __('Dashboard') }}
                    </x-nav-link>

                    @if($activeRole === 'volunteer' || ($authUser && $authUser->hasRole('volunteer') && $activeRole !== 'member'))
                        <x-nav-link :href="route('volunteer.census.index')" :active="request()->routeIs('volunteer.census.*')">
                            {{ __('Sensus PTMA') }}
                        </x-nav-link>
                        <x-nav-link :href="route('volunteer.surveillance.index')" :active="request()->routeIs('volunteer.surveillance.*')">
                            {{ __('eSurveillance Kucing') }}
                        </x-nav-link>
                    @endif

                    @if($activeRole === 'member')
                        <x-nav-link :href="route('contact.index')" :active="request()->routeIs('contact.*')">
                            {{ __('Hubungi Kami') }}
                        </x-nav-link>
                    @endif

                    @if(in_array($activeRole, ['admin', 'superadmin']))
                        @php
                            $unreadContactCount = \App\Models\ContactMessage::where('status', 'unread')->count();
                        @endphp
                        <x-nav-link :href="route('admin.contacts.index')" :active="request()->routeIs('admin.contacts.*')" class="inline-flex items-center gap-1.5">
                            <span>{{ __('Pesan Masuk') }}</span>
                            @if($unreadContactCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white leading-none">
                                    {{ $unreadContactCount }}
                                </span>
                            @endif
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Kelola Pengguna') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.wilayah.index')" :active="request()->routeIs('superadmin.wilayah.*')">
                            {{ __('Master Wilayah') }}
                        </x-nav-link>
                        <x-nav-link :href="route('superadmin.albums.index')" :active="request()->routeIs('superadmin.albums.*')">
                            {{ __('Album Kegiatan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                            {{ __('Kelola Event') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
                            {{ __('Pengaturan') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <!-- Workspace / Role Switcher (If user has multiple roles) -->
                @if(count($allRoles) > 1)
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" 
                                    class="inline-flex min-h-[40px] items-center gap-1.5 px-3 py-1.5 border rounded-xl text-xs font-bold transition shadow-2xs hover:shadow-xs focus-visible:ring-2 focus-visible:ring-teal-700 cursor-pointer {{ $activeMeta['badge_class'] }}"
                                    title="Klik untuk beralih ruang kerja">
                                <span class="text-sm leading-none">{{ $activeMeta['icon'] }}</span>
                                <span class="truncate max-w-[150px]">{{ $activeMeta['name'] }}</span>
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

                <!-- Settings Dropdown or Guest Login -->
                @if($authUser)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex min-h-[40px] items-center px-3 py-1.5 border border-slate-200 text-sm font-semibold rounded-xl text-slate-700 bg-white hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-teal-700 shadow-2xs">
                                <div class="flex items-center gap-2">
                                    <span class="truncate max-w-[120px]">{{ $authUser->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 border border-teal-200 text-teal-800 uppercase tracking-wider">{{ $activeMeta['short_name'] }}</span>
                                </div>

                                <div class="ms-1.5">
                                    <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil Akun') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="button-secondary text-xs font-semibold px-3.5 py-2">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="button-primary text-xs font-bold px-3.5 py-2">
                            Daftar
                        </a>
                    </div>
                @endif
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" type="button" aria-label="Buka navigasi menu" :aria-expanded="open.toString()" class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-teal-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-slate-200 px-4 pt-3 pb-4 space-y-3">
        
        <!-- Mobile Workspace Switcher -->
        @if(count($allRoles) > 1)
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                    <span>Ruang Kerja:</span>
                    <span class="text-teal-700">{{ $activeMeta['name'] }}</span>
                </div>
                <div class="grid grid-cols-1 gap-1.5">
                    @foreach($allRoles as $r)
                        @php $rMeta = \App\Models\User::getWorkspaceMeta($r); @endphp
                        <form method="POST" action="{{ route('workspace.switch') }}">
                            @csrf
                            <input type="hidden" name="role" value="{{ $r }}">
                            <button type="submit" class="w-full flex items-center justify-between p-2.5 rounded-lg text-xs font-semibold border transition cursor-pointer {{ $activeRole === $r ? 'bg-teal-700 text-white border-teal-700 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                <span class="flex items-center gap-2">
                                    <span>{{ $rMeta['icon'] }}</span>
                                    <span>{{ $rMeta['name'] }}</span>
                                </span>
                                @if($activeRole === $r)
                                    <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded font-bold">Aktif ✓</span>
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ $activeRole === 'member' ? __('Kucing Saya') : __('Dashboard') }}
            </x-responsive-nav-link>

            @if($activeRole === 'volunteer' || ($authUser && $authUser->hasRole('volunteer') && $activeRole !== 'member'))
                <x-responsive-nav-link :href="route('volunteer.census.index')" :active="request()->routeIs('volunteer.census.*')">
                    {{ __('Sensus PTMA') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('volunteer.surveillance.index')" :active="request()->routeIs('volunteer.surveillance.*')">
                    {{ __('eSurveillance Kucing') }}
                </x-responsive-nav-link>
            @endif

            @if($activeRole === 'member')
                <x-responsive-nav-link :href="route('contact.index')" :active="request()->routeIs('contact.*')">
                    {{ __('Hubungi Kami') }}
                </x-responsive-nav-link>
            @endif

            @if(in_array($activeRole, ['admin', 'superadmin']))
                @php
                    $unreadContactCount = $unreadContactCount ?? \App\Models\ContactMessage::where('status', 'unread')->count();
                @endphp
                <x-responsive-nav-link :href="route('admin.contacts.index')" :active="request()->routeIs('admin.contacts.*')" class="flex justify-between items-center">
                    <span>{{ __('Pesan Masuk') }}</span>
                    @if($unreadContactCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white">
                            {{ $unreadContactCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Kelola Pengguna') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('superadmin.wilayah.index')" :active="request()->routeIs('superadmin.wilayah.*')">
                    {{ __('Master Wilayah') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('superadmin.albums.index')" :active="request()->routeIs('superadmin.albums.*')">
                    {{ __('Album Kegiatan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                    {{ __('Kelola Event') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
                    {{ __('Pengaturan') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-3 border-t border-slate-200">
            @if($authUser)
                <div class="px-2 pb-2">
                    <div class="font-semibold text-sm text-slate-800">{{ $authUser->name }}</div>
                    <div class="text-xs text-slate-500">{{ $authUser->email }}</div>
                </div>

                <div class="mt-2 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profil Akun') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar (Log Out)') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="grid grid-cols-2 gap-2 p-2">
                    <a href="{{ route('login') }}" class="button-secondary text-center text-xs font-semibold py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="button-primary text-center text-xs font-bold py-2">
                        Daftar
                    </a>
                </div>
            @endif
        </div>
    </div>
</nav>
