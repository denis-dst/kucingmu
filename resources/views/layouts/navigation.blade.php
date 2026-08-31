<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-40" aria-label="Navigasi Utama">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 focus-visible:ring-2 focus-visible:ring-teal-700 rounded-lg p-1">
                        @if(isset($app_settings['app_logo']))
                            <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="{{ $app_settings['app_name'] ?? 'KucingMu' }}" class="h-8 w-auto object-contain">
                        @else
                            <span class="text-2xl" aria-hidden="true">🐱</span>
                        @endif
                        <span class="font-outfit font-extrabold text-teal-800 text-lg tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-8 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(Auth::user()->role === 'volunteer')
                        <x-nav-link :href="route('volunteer.census.index')" :active="request()->routeIs('volunteer.census.*')">
                            {{ __('Sensus PTMA') }}
                        </x-nav-link>
                        <x-nav-link :href="route('volunteer.surveillance.index')" :active="request()->routeIs('volunteer.surveillance.*')">
                            {{ __('eSurveillance Kucing') }}
                        </x-nav-link>
                    @endif
                    @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
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

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex min-h-[44px] items-center px-3.5 py-2 border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-teal-700">
                            <div class="flex items-center gap-2">
                                <span>{{ Auth::user()->name }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 border border-teal-200 text-teal-800 uppercase tracking-wider">{{ Auth::user()->role }}</span>
                            </div>

                            <div class="ms-1.5">
                                <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-slate-200 px-4 pt-3 pb-4 space-y-2">
        <div class="space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(Auth::user()->role === 'volunteer')
                <x-responsive-nav-link :href="route('volunteer.census.index')" :active="request()->routeIs('volunteer.census.*')">
                    {{ __('Sensus PTMA') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('volunteer.surveillance.index')" :active="request()->routeIs('volunteer.surveillance.*')">
                    {{ __('eSurveillance Kucing') }}
                </x-responsive-nav-link>
            @endif
            @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
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
        <div class="pt-4 border-t border-slate-200">
            <div class="px-2 pb-2">
                <div class="font-semibold text-sm text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-500">{{ Auth::user()->email }} ({{ Auth::user()->role }})</div>
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
        </div>
    </div>
</nav>
