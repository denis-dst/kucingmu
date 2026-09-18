<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $app_settings['app_name'] ?? config('app.name', 'KucingMu') }}</title>

        @if(isset($app_settings['app_description']))
            <meta name="description" content="{{ $app_settings['app_description'] }}">
        @endif

        @if(isset($app_settings['app_favicon']))
            <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
        @endif

        <!-- Google Fonts DNS & Preconnect -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;800&display=swap">

        <style>
            [x-cloak] { display: none !important; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50 min-h-screen"
          x-data="{ 
              sidebarOpen: localStorage.getItem('sidebar_open') === null ? true : localStorage.getItem('sidebar_open') === 'true',
              mobileSidebarOpen: false,
              toggleSidebar() {
                  if (window.innerWidth >= 1024) {
                      this.sidebarOpen = !this.sidebarOpen;
                      localStorage.setItem('sidebar_open', this.sidebarOpen);
                  } else {
                      this.mobileSidebarOpen = !this.mobileSidebarOpen;
                  }
              }
          }">
        <!-- Skip link for keyboard accessibility -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-800 focus:text-white focus:rounded-md focus:shadow-md focus:font-semibold">
            Lewati ke konten utama
        </a>

        @include('partials.impersonation-banner')

        <div class="min-h-screen bg-slate-50 flex flex-row">
            
            <!-- Left Sidebar Navigation -->
            @include('layouts.sidebar')

            <!-- Main Application Content Area -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                
                <!-- Top Navbar with 3-Line Toggle Button -->
                @include('layouts.navigation')

                <!-- Page Heading (Optional) -->
                @isset($header)
                    <header class="bg-white border-b border-slate-200">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main id="main-content" tabindex="-1" class="flex-1 focus:outline-none">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="mt-auto py-5 border-t border-slate-200 bg-white/60 text-center text-xs text-slate-500">
                    <div class="max-w-7xl mx-auto px-4 footer-text">
                        {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah.' !!}
                    </div>
                </footer>
            </div>
        </div>

        @include('partials.accessibility-widget')
    </body>
</html>
