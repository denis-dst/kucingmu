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
    <body class="font-sans text-slate-800 antialiased">
        <div class="auth-shell">
            <!-- Left brand panel (visible on large screens) -->
            <div class="auth-brand-panel">
                <div class="brand-mark">
                    @if(isset($app_settings['app_logo']))
                        <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo" class="h-12 w-auto mx-auto object-contain">
                    @else
                        🐱
                    @endif
                </div>
                
                <div class="my-auto">
                    <h1 class="font-outfit text-4xl font-extrabold tracking-tight text-white lg:text-5xl">
                        {{ $app_settings['app_name'] ?? 'KucingMu' }}
                    </h1>
                    <p class="mt-4 text-base text-teal-100/90 leading-relaxed max-w-md">
                        {{ $app_settings['app_description'] ?? 'Platform komunitas pecinta kucing Muhammadiyah yang menggabungkan kepedulian kesehatan hewan dengan inisiatif dakwah yang berdampak nyata.' }}
                    </p>
                </div>
                
                <div class="text-xs text-teal-200/60 font-semibold tracking-wide">
                    {!! $app_settings['app_footer'] ?? '&copy; 2026 KucingMu. Warga Muhammadiyah Peduli Hewan.' !!}
                </div>
            </div>

            <!-- Right form panel -->
            <div class="auth-form-panel">
                <div class="w-full max-w-md">
                    <!-- Small logo for mobile -->
                    <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                        <div class="brand-mark brand-mark-small">
                            @if(isset($app_settings['app_logo']))
                                <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo" class="h-6 w-auto mx-auto object-contain">
                            @else
                                🐱
                            @endif
                        </div>
                        <span class="font-outfit text-xl font-bold tracking-tight text-slate-900">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
                    </div>

                    <div class="mb-6">
                        <span class="eyebrow">Akses Platform</span>
                        <h2 class="mt-2 text-2xl font-bold text-slate-900">Selamat Datang Kembali</h2>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
