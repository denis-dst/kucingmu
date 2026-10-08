<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pendaftaran Berhasil | Penjaringan Representatif {{ $app_settings['app_name'] ?? 'KucingMu' }}</title>

    @if(isset($app_settings['app_favicon']))
        <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tracking & Analytics Scripts (GTM & Google Analytics) -->
    @include('partials.tracking-head')
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-between">
    <!-- Tracking Body (GTM Noscript) -->
    @include('partials.tracking-body')

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo KucingMu"
                        class="h-8 w-auto object-contain">
                @else
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-700 text-white flex items-center justify-center font-outfit font-bold text-lg">
                        🐱
                    </div>
                @endif
                <span class="font-outfit font-extrabold text-slate-900 text-base tracking-tight">
                    {{ $app_settings['app_name'] ?? 'KucingMu' }}
                </span>
            </a>

            <a href="{{ url('/') }}" class="text-xs font-semibold text-teal-800 hover:underline">
                &larr; Beranda Utama
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 py-10 sm:py-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">

            <div
                class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8 text-center sm:text-left">

                <!-- Success Icon & Header -->
                <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5 pb-6 border-b border-slate-100">
                    <div
                        class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-3xl shrink-0 shadow-xs border border-emerald-200">
                        ✅
                    </div>
                    <div class="space-y-1">
                        <div
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold uppercase tracking-wider">
                            Pendaftaran Diterima
                        </div>
                        <h1 class="font-outfit text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                            Pendaftaran Representatif Berhasil Dikirim!
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-600">
                            Terima kasih atas kesediaan dan komitmen Anda menjadi Representatif KucingMu.
                        </p>
                    </div>
                </div>

                <!-- Registration Code Highlight Box -->
                <div
                    class="p-5 rounded-2xl bg-gradient-to-br from-teal-50 to-emerald-50 border border-teal-200 space-y-2 text-center">
                    <span class="text-xs font-bold text-teal-800 uppercase tracking-wider block">
                        Nomor Registrasi Representatif
                    </span>
                    <div class="font-mono text-2xl sm:text-3xl font-black text-teal-950 tracking-wider">
                        {{ $registration->registration_number }}
                    </div>
                    <p class="text-xs text-teal-900/80">
                        Simpan nomor ini untuk pengecekan status dan korespondensi dengan Pengurus Pusat KucingMu.
                    </p>
                </div>

                <!-- Summary Data Card -->
                <div class="space-y-3 text-left">
                    <h2 class="font-outfit font-bold text-sm text-slate-900">
                        Ringkasan Data Calon Representatif
                    </h2>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-5 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Nama Lengkap</span>
                            <span class="font-bold text-slate-900">{{ $registration->name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Nomor Baku Muhammadiyah (NBM)</span>
                            <span class="font-mono font-bold text-slate-900">{{ $registration->nbm ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Wilayah Penugasan</span>
                            <span class="font-semibold text-slate-900 text-right">
                                {{ $registration->village_name }}, Kec. {{ $registration->district_name }}<br>
                                {{ $registration->city_name }}, {{ $registration->province_name }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Pimpinan Muhammadiyah / Ortom</span>
                            <span
                                class="font-semibold text-slate-900">{{ $registration->muhammadiyah_active_leadership }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Status Saat Ini</span>
                            <span
                                class="inline-flex items-center gap-1 font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                {{ $registration->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tahapan Selanjutnya -->
                <div class="space-y-3 text-left">
                    <h2 class="font-outfit font-bold text-sm text-slate-900">
                        Langkah Selanjutnya oleh Pengurus Pusat
                    </h2>

                    <div class="space-y-2.5 text-xs text-slate-600">
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span
                                class="font-bold text-teal-800 bg-teal-100 w-5 h-5 rounded-full flex items-center justify-center shrink-0">1</span>
                            <div>
                                <strong class="text-slate-800">Verifikasi Berkas SK &amp; NBM</strong>
                                <p class="text-slate-500 mt-0.5">Pengurus pusat akan memvalidasi keaslian dokumen SK
                                    kepengurusan dan keanggotaan KTAM Anda.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span
                                class="font-bold text-teal-800 bg-teal-100 w-5 h-5 rounded-full flex items-center justify-center shrink-0">2</span>
                            <div>
                                <strong class="text-slate-800">Koordinasi via WhatsApp</strong>
                                <p class="text-slate-500 mt-0.5">Admin akan menghubungi nomor WhatsApp
                                    <strong>{{ $registration->whatsapp_number }}</strong> untuk konfirmasi dan arahan
                                    koordinasi wilayah.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span
                                class="font-bold text-teal-800 bg-teal-100 w-5 h-5 rounded-full flex items-center justify-center shrink-0">3</span>
                            <div>
                                <strong class="text-slate-800">Penerbitan Surat Mandat / Penugasan</strong>
                                <p class="text-slate-500 mt-0.5">Representatif yang lolos verifikasi akan mendapatkan
                                    surat mandat resmi dari KucingMu.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button type="button" onclick="window.print()"
                        class="button-secondary text-xs px-5 py-2.5 rounded-xl w-full sm:w-auto font-semibold inline-flex items-center justify-center gap-2">
                        <span>🖨️</span> Cetak Bukti Pendaftaran
                    </button>

                    <a href="{{ url('/') }}"
                        class="button-primary text-xs px-6 py-2.5 rounded-xl w-full sm:w-auto text-center font-bold bg-teal-700 hover:bg-teal-800 text-white">
                        Selesai &amp; Kembali ke Beranda
                    </a>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto px-4">
            {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. KucingMu.' !!}
        </div>
    </footer>

</body>

</html>