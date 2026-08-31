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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50 min-h-screen">
        <!-- Skip link for keyboard accessibility -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-800 focus:text-white focus:rounded-md focus:shadow-md focus:font-semibold">
            Lewati ke konten utama
        </a>

        <div class="min-h-screen bg-slate-50 flex flex-col justify-between">
            <div>
                @include('partials.impersonation-banner')
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-b border-slate-200">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main id="main-content" tabindex="-1" class="focus:outline-none">
                    {{ $slot }}
                </main>
            </div>

            <footer class="mt-auto py-6 border-t border-slate-200 text-center text-xs text-slate-500">
                <div class="max-w-7xl mx-auto px-4">
                    {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah.' !!}
                </div>
            </footer>
        </div>

        @include('partials.accessibility-widget')
    </body>
</html>
