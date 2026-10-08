<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kebijakan Privasi | {{ $app_settings['app_name'] ?? 'KucingMu' }}</title>
    <meta name="description"
        content="Kebijakan Privasi resmi platform KucingMu sesuai Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).">

    @if(isset($app_settings['app_favicon']))
        <link rel="shortcut icon" href="{{ asset('storage/' . $app_settings['app_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }

            .print-full {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            a {
                text-decoration: none !important;
                color: #000000 !important;
            }
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-teal-100 selection:text-teal-900 min-h-screen flex flex-col"
    x-data="{ activeSection: 'ringkasan', showBackToTop: false }"
    @scroll.window="showBackToTop = (window.pageYOffset > 400)">

    <!-- Accessibility Skip Link -->
    <a href="#konten-kebijakan"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-800 focus:text-white focus:rounded-md focus:shadow-md focus:font-semibold">
        Lewati ke konten kebijakan
    </a>

    <!-- Top Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 transition no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand Logo & Title -->
                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2 rounded-xl p-1">
                    @if(isset($app_settings['app_logo']))
                        <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="Logo KucingMu"
                            class="h-9 sm:h-10 w-auto object-contain">
                    @else
                        <div
                            class="w-10 h-10 rounded-xl bg-teal-700 text-white flex items-center justify-center font-outfit font-bold text-xl shadow-xs">
                            🐱
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span
                            class="font-outfit font-extrabold text-slate-900 text-base sm:text-lg leading-none tracking-tight group-hover:text-teal-700 transition">
                            {{ $app_settings['app_name'] ?? 'KucingMu' }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase mt-1">
                            Pusat Pelindungan Data & Privasi
                        </span>
                    </div>
                </a>

                <!-- Nav Actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <button type="button" onclick="window.print()"
                        class="button-secondary text-xs px-3 py-2 rounded-xl inline-flex items-center gap-1.5 text-slate-700 hover:text-slate-900 font-medium"
                        title="Cetak atau simpan sebagai PDF">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">Cetak Dokumen</span>
                    </button>

                    <a href="{{ route('contact.index') }}"
                        class="button-secondary text-xs px-3.5 py-2 rounded-xl text-slate-700 hover:text-slate-900 font-semibold">
                        Hubungi Kami
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="button-primary text-xs px-4 py-2 rounded-xl font-bold bg-teal-700 hover:bg-teal-800 text-white shadow-xs">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="button-primary text-xs px-4 py-2 rounded-xl font-bold bg-teal-700 hover:bg-teal-800 text-white shadow-xs">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Header Hero Banner -->
    <section
        class="bg-gradient-to-br from-teal-900 via-teal-800 to-sky-800 text-white py-12 sm:py-16 border-b border-teal-950 no-print relative overflow-hidden">
        <!-- Subtle ambient backdrop accents -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-teal-400 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-sky-400 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl space-y-4">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-teal-100 border border-white/20 text-xs font-semibold backdrop-blur">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Standar Kepatuhan UU PDP No. 27 Tahun 2022
                </div>

                <h1
                    class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    Kebijakan Privasi & Pelindungan Data Pribadi
                </h1>

                <p class="text-sm sm:text-base text-teal-100/90 leading-relaxed font-normal">
                    Komitmen resmi platform KucingMu dalam menjaga privasi, keamanan data identitas pemilik, catatan
                    biometrik hewan, serta rekam medis hewan peliharaan Anda sesuai regulasi nasional.
                </p>

                <div
                    class="flex flex-wrap items-center gap-4 text-xs text-teal-200/80 pt-2 border-t border-teal-700/50">
                    <div>
                        <span class="text-teal-200/70">Pembaruan Terakhir:</span>
                        <strong class="text-white ml-1">07 Oktober 2026</strong>
                    </div>
                    <div class="hidden sm:inline text-teal-500">&bull;</div>
                    <div>
                        <span class="text-teal-200/70">Status Dokumen:</span>
                        <strong class="text-emerald-300 ml-1">Resmi & Berlaku</strong>
                    </div>
                    <div class="hidden sm:inline text-teal-500">&bull;</div>
                    <div>
                        <span class="text-teal-200/70">Pengendali:</span>
                        <strong class="text-white ml-1">KucingMu</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area with Sticky Table of Contents -->
    <main id="konten-kebijakan" class="flex-1 py-10 sm:py-14 print-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

                <!-- Left Sidebar: Table of Contents (Sticky on Desktop) -->
                <aside class="lg:col-span-4 no-print">
                    <div class="sticky top-24 bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="font-outfit font-bold text-slate-900 text-sm flex items-center gap-2">
                                <span>📑</span> Daftar Isi Kebijakan
                            </h2>
                            <span
                                class="text-[11px] font-semibold text-teal-800 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                11 Pasal
                            </span>
                        </div>

                        <nav class="space-y-1 text-xs font-medium max-h-[calc(100vh-220px)] overflow-y-auto pr-1">
                            <a href="#pasal-1"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                1. Ketentuan Umum & Pengendali Data
                            </a>
                            <a href="#pasal-2"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                2. Kategori Data Pribadi yang Dikumpulkan
                            </a>
                            <a href="#pasal-3"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                3. Dasar Hukum & Tujuan Pemrosesan
                            </a>
                            <a href="#pasal-4"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                4. Hak-Hak Anda sebagai Subjek Data
                            </a>
                            <a href="#pasal-5"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                5. Pengamanan & Kerahasiaan Data
                            </a>
                            <a href="#pasal-6"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                6. Pembagian Data & Larangan Penjualan Data
                            </a>
                            <a href="#pasal-7"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                7. Masa Retensi & Pemusnahan Data
                            </a>
                            <a href="#pasal-8"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                8. Penggunaan Cookie & Data Sesi
                            </a>
                            <a href="#pasal-9"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                9. Pelindungan Data Anak
                            </a>
                            <a href="#pasal-10"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                10. Perubahan Kebijakan Privasi
                            </a>
                            <a href="#pasal-11"
                                class="block px-3 py-2 rounded-xl text-slate-600 hover:text-teal-800 hover:bg-teal-50/70 transition">
                                11. Kontak Petugas Pelindungan Data
                            </a>
                        </nav>

                        <!-- Quick Assistance Box -->
                        <div class="pt-3 border-t border-slate-100 bg-slate-50 p-3.5 rounded-xl space-y-2">
                            <span class="text-[11px] font-bold text-slate-800 block">Ada pertanyaan terkait data pribadi
                                Anda?</span>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Tim kepatuhan privasi KucingMu siap melayani permohonan hak akses atau koreksi data
                                Anda.
                            </p>
                            <a href="{{ route('contact.index') }}"
                                class="button-primary text-[11px] font-bold py-1.5 px-3 rounded-lg w-full text-center block bg-teal-700 hover:bg-teal-800 text-white">
                                Ajukan Pertanyaan Privasi
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Right Column: Policy Document Prose -->
                <article
                    class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-10 leading-relaxed text-slate-700 text-sm sm:text-base print-full">

                    <!-- Executive Summary Box -->
                    <div class="bg-teal-50/70 border border-teal-200 rounded-2xl p-5 sm:p-6 space-y-2.5">
                        <div class="flex items-center gap-2 text-teal-900 font-outfit font-bold text-base">
                            <span>🛡️</span> Ringkasan Komitmen Privasi
                        </div>
                        <p class="text-xs sm:text-sm text-teal-950 leading-relaxed">
                            KucingMu menghormati hak privasi setiap warga dan anggota. Data pribadi pemilik dan data
                            hewan peliharaan hanya dikumpulkan untuk tujuan legal penerbitan kartu identitas KTAKuMu,
                            surveilans kesehatan hewan, pendataan populasi kucing lingkungan, serta pelayanan medis
                            veteriner. Kami tidak pernah menjual data pribadi Anda kepada pihak ketiga untuk kepentingan
                            komersial.
                        </p>
                    </div>

                    <!-- Pasal 1 -->
                    <section id="pasal-1" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                1</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Ketentuan Umum & Identitas Pengendali Data
                            </h2>
                        </div>
                        <p>
                            Kebijakan Privasi ini mengatur tata kelola perolehan, pengumpulan, pengolahan,
                            penganalisisan, penyimpanan, perbaikan, penampilan, pengumuman, pengalihan, penyebarluasan,
                            pengungkapan, dan penghapusan atau pemusnahan data pribadi pada platform digital
                            <strong>KucingMu</strong>.
                        </p>
                        <p>
                            Penyelenggaraan platform KucingMu dilaksanakan oleh <strong>KucingMu</strong> yang
                            berkedudukan hukum di Indonesia, bertindak sebagai <strong>Pengendali Data Pribadi</strong>
                            sebagaimana diatur dalam Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang
                            Pelindungan Data Pribadi (UU PDP).
                        </p>
                        <p>
                            Dengan mendaftar, mengakses, atau menggunakan layanan KucingMu, Anda menyatakan telah
                            membaca, memahami, dan menyetujui seluruh ketentuan pemrosesan data pribadi yang tercantum
                            dalam Kebijakan Privasi ini.
                        </p>
                    </section>

                    <!-- Pasal 2 -->
                    <section id="pasal-2" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                2</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Kategori Data Pribadi yang Dikumpulkan
                            </h2>
                        </div>
                        <p>
                            KucingMu mengumpulkan data yang diberikan secara sukarela oleh Anda maupun data yang
                            dihasilkan saat menggunakan layanan:
                        </p>

                        <div class="space-y-3 pt-1">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">A. Data Identitas Pemilik Kucing (Subjek
                                    Data)</h3>
                                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1">
                                    <li>Nama lengkap pemilik.</li>
                                    <li>Alamat surel (email) aktif untuk autentikasi dan notifikasi.</li>
                                    <li>Nomor telepon / kontak WhatsApp untuk konfirmasi verifikasi dan janji temu
                                        medis.</li>
                                    <li>Nomor Baku Muhammadiyah (NBM) bagi anggota persyarikatan.</li>
                                    <li>Wilayah domisili atau Pimpinan Wilayah Muhammadiyah (PWM) terkait.</li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">B. Data Identitas & Biometrik Hewan
                                    Peliharaan</h3>
                                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1">
                                    <li>Nama kucing, ras/jenis, varian warna dan pola bulu, jenis kelamin, serta tanggal
                                        lahir atau perkiraan usia.</li>
                                    <li>Foto profil kucing tampak depan dan samping.</li>
                                    <li>Data biometrik hewan berupa foto tanda telapak (paw print) atau hidung (nose
                                        print) untuk keperluan verifikasi identitas unik KTAKuMu.</li>
                                    <li>Nomor Identitas Anggota KucingMu (NIAKuMu) yang diterbitkan secara resmi.</li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">C. Data Pelayanan Medis & Riwayat Kesehatan
                                    Hewan</h3>
                                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1">
                                    <li>Riwayat vaksinasi, sterilisasi, pemberian obat cacing, dan pemeriksaan klinis
                                        oleh dokter hewan.</li>
                                    <li>Diagnosa medis, resep obat yang diberikan, serta catatan tindakan dokter hewan
                                        terafiliasi.</li>
                                    <li>Jadwal janji temu pemeriksaan kesehatan hewan.</li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">D. Data Sensus Lingkungan Kampus PTMA &
                                    Surveilans</h3>
                                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1">
                                    <li>Titik koordinat geografis pengamatan kucing kampus (geolocation).</li>
                                    <li>Zona pengamatan lingkungan dan dokumentasi sensus lapangan oleh relawan resmi.
                                    </li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <h3 class="font-bold text-slate-900 text-sm">E. Data Teknis & Akses Platform</h3>
                                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1">
                                    <li>Alamat IP, jenis peramban (browser), sistem operasi, log waktu login, dan
                                        aktivitas sesi akun.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Pasal 3 -->
                    <section id="pasal-3" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                3</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Dasar Hukum & Tujuan Pemrosesan Data Pribadi
                            </h2>
                        </div>
                        <p>
                            Berdasarkan Pasal 20 UU PDP, pemrosesan data pribadi oleh KucingMu berlandaskan pada
                            persetujuan eksplisit dari Subjek Data, pelaksanaan kewajiban perjanjian pendaftaran
                            layanan, serta pemenuhan kepentingan yang sah dalam pengelolaan ekosistem kesehatan hewan
                            lingkungan.
                        </p>
                        <p>
                            Tujuan pemrosesan data pribadi meliputi:
                        </p>
                        <ol class="list-decimal list-inside text-xs sm:text-sm space-y-2 text-slate-700">
                            <li><strong>Verifikasi & Penerbitan KTAKuMu:</strong> Memvalidasi keabsahan data kepemilikan
                                dan menerbitkan kartu identitas resmi KTAKuMu dalam format digital maupun cetak.</li>
                            <li><strong>Penyelenggaraan Rekam Medis Hewan:</strong> Mendokumentasikan riwayat pengobatan
                                dan tindakan medis secara terstruktur agar dokter hewan dapat memberikan diagnosis yang
                                akurat dan berkelanjutan.</li>
                            <li><strong>Komunikasi & Layanan Pengguna:</strong> Mengirimkan pemberitahuan status
                                verifikasi, jadwal janji temu dokter, pembaruan sistem, dan merespons pertanyaan yang
                                diajukan melalui pusat bantuan.</li>
                            <li><strong>Riset & Manajemen Populasi Hewan:</strong> Mendukung program sensus kucing
                                kampus Perguruan Tinggi Muhammadiyah dan Aisyiyah (PTMA) untuk pengendalian populasi
                                ramah lingkungan (animal welfare).</li>
                            <li><strong>Keamanan Sistem & Pencegahan Penyalahgunaan:</strong> Menjaga integritas
                                platform, mencegah duplikasi pendaftaran, dan mendeteksi aktivitas akses yang
                                mencurigakan.</li>
                        </ol>
                    </section>

                    <!-- Pasal 4 -->
                    <section id="pasal-4" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                4</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Hak-Hak Anda sebagai Subjek Data Pribadi
                            </h2>
                        </div>
                        <p>
                            Sesuai dengan Pasal 5 sampai dengan Pasal 13 Undang-Undang Pelindungan Data Pribadi, Anda
                            sebagai Subjek Data memiliki hak-hak berikut:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">1. Hak Mendapatkan Informasi</span>
                                <p class="text-xs text-slate-600">Mengetahui kejelasan identitas pengendali data, dasar
                                    hukum, dan tujuan pemrosesan data pribadi Anda.</p>
                            </div>
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">2. Hak Akses & Salinan Data</span>
                                <p class="text-xs text-slate-600">Mengakses dan memperoleh salinan data pribadi serta
                                    identitas kucing yang tercatat dalam sistem kami.</p>
                            </div>
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">3. Hak Memperbaiki &
                                    Memperbarui</span>
                                <p class="text-xs text-slate-600">Memperbaiki kekeliruan atau memperbarui data profil
                                    pemilik dan data hewan melalui menu edit di dashboard.</p>
                            </div>
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">4. Hak Menghapus Data (Right to
                                    Erasure)</span>
                                <p class="text-xs text-slate-600">Mengajukan permohonan penghapusan data identitas
                                    kucing atau penutupan akun secara permanen.</p>
                            </div>
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">5. Hak Menarik Persetujuan</span>
                                <p class="text-xs text-slate-600">Menarik kembali persetujuan pemrosesan data pribadi
                                    yang telah diberikan sebelumnya.</p>
                            </div>
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                                <span class="font-bold text-slate-900 text-xs block">6. Hak Mengajukan Keberatan &
                                    Pengaduan</span>
                                <p class="text-xs text-slate-600">Menyampaikan keberatan atas pemrosesan data atau
                                    mengajukan aduan penanganan data pribadi.</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 pt-1">
                            Untuk menggunakan hak-hak di atas, Anda dapat mengakses dashboard akun atau menghubungi
                            Petugas Pelindungan Data melalui formulir kontak resmi kami.
                        </p>
                    </section>

                    <!-- Pasal 5 -->
                    <section id="pasal-5" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                5</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Pengamanan & Kerahasiaan Data Pribadi
                            </h2>
                        </div>
                        <p>
                            KucingMu menerapkan standar pengamanan teknis dan organisasional sesuai Pasal 35 UU PDP guna
                            melindungi data dari akses tidak sah, pengubahan, pengungkapan, atau pemusnahan yang
                            melanggar hukum:
                        </p>
                        <ul class="list-disc list-inside text-xs sm:text-sm space-y-1.5 text-slate-700">
                            <li><strong>Enkripsi Transport Layer (TLS/HTTPS):</strong> Seluruh pertukaran data antara
                                peramban pengguna dan server dienkripsi menggunakan protokol aman.</li>
                            <li><strong>Hashing Kata Sandi:</strong> Kata sandi pengguna disimpan menggunakan algoritma
                                hashing satu arah yang kuat (Bcrypt) dan tidak dapat dibaca dalam bentuk teks biasa.
                            </li>
                            <li><strong>Kontrol Akses Berbasis Peran (RBAC):</strong> Hak akses data dibatasi secara
                                ketat berdasarkan peran wewenang (Member, Dokter Hewan, Relawan, Verifikator, dan
                                Administrator).</li>
                            <li><strong>Proteksi Serangan Siber:</strong> Dilengkapi perlindungan terhadap ancaman CSRF
                                (Cross-Site Request Forgery), SQL Injection, dan Cross-Site Scripting (XSS).</li>
                            <li><strong>Prosedur Tanggap Insiden:</strong> Apabila terjadi kegagalan pelindungan data
                                pribadi, kami akan memberitahukan kepada Anda dan lembaga pengawas terkait dalam waktu
                                maksimal 3x24 jam sebagaimana diwajibkan regulasi.</li>
                        </ul>
                    </section>

                    <!-- Pasal 6 -->
                    <section id="pasal-6" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                6</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Pembagian Data & Larangan Penjualan Data
                            </h2>
                        </div>
                        <p class="font-semibold text-slate-900">
                            Prinsip Non-Komersialisasi: KucingMu TIDAK PERNAH menjual, menyewakan, memperdagangkan, atau
                            membagikan data pribadi Anda kepada pihak ketiga untuk kepentingan pemasaran pihak ketiga.
                        </p>
                        <p>
                            Pengungkapan data hanya dilakukan dalam lingkup terbatas dan terkendali:
                        </p>
                        <ul class="list-disc list-inside text-xs sm:text-sm space-y-1.5 text-slate-700">
                            <li><strong>Dokter Hewan Terafiliasi:</strong> Untuk keperluan diagnosis, rekam medis
                                klinis, dan pelayanan kesehatan hewan peliharaan Anda.</li>
                            <li><strong>Verifikator Resmi Persyarikatan:</strong> Untuk memverifikasi kesesuaian dokumen
                                pendaftaran dan penerbitan nomor resmi KTAKuMu.</li>
                            <li><strong>Kewajiban Hukum:</strong> Jika diwajibkan berdasarkan perintah pengadilan,
                                peraturan perundang-undangan, atau permintaan resmi instansi penegak hukum Republik
                                Indonesia yang sah.</li>
                        </ul>
                    </section>

                    <!-- Pasal 7 -->
                    <section id="pasal-7" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                7</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Masa Retensi & Pemusnahan Data
                            </h2>
                        </div>
                        <p>
                            Data pribadi Anda akan disimpan selama akun Anda aktif dan terdaftar pada platform KucingMu.
                        </p>
                        <p>
                            Jika Anda mengajukan penutupan akun atau penghapusan data:
                        </p>
                        <ul class="list-disc list-inside text-xs sm:text-sm space-y-1.5 text-slate-700">
                            <li>Data identitas pemilik dan foto profil akan dihapus atau dianonimkan dari basis data
                                operasional.</li>
                            <li>Catatan rekam medis hewan dapat disimpan dalam bentuk data terarsip yang
                                didepersonalisasi (tanpa identitas pemilik) semata-mata untuk riwayat epidemiologi
                                veteriner dan pelaporan statistik lingkungan hidup.</li>
                            <li>Pemusnahan data dilakukan dengan metode penghapusan digital yang aman sehingga data
                                tidak dapat dipulihkan kembali.</li>
                        </ul>
                    </section>

                    <!-- Pasal 8 -->
                    <section id="pasal-8" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                8</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Penggunaan Cookie & Data Sesi
                            </h2>
                        </div>
                        <p>
                            Platform KucingMu menggunakan cookie teknis esensial untuk:
                        </p>
                        <ul class="list-disc list-inside text-xs sm:text-sm space-y-1 text-slate-700">
                            <li>Menjaga sesi masuk (login authentication session) Anda agar tetap aman.</li>
                            <li>Menyimpan token keamanan CSRF untuk mencegah serangan pemalsuan permintaan.</li>
                            <li>Mengingat preferensi tampilan dasar (seperti status buka-tutup bilah navigasi).</li>
                        </ul>
                        <p class="text-xs text-slate-600">
                            Kami tidak menggunakan cookie pihak ketiga (third-party tracking cookies) untuk pelacakan
                            lintas situs atau profil iklan komersial. Anda dapat mengatur peramban untuk menolak cookie,
                            namun beberapa fitur autentikasi mungkin tidak berjalan optimal.
                        </p>
                    </section>

                    <!-- Pasal 9 -->
                    <section id="pasal-9" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                9</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Pelindungan Data Anak di Bawah Umur
                            </h2>
                        </div>
                        <p>
                            Layanan KucingMu ditujukan bagi pengguna yang telah berusia minimal 18 tahun atau telah
                            cakap hukum menurut peraturan perundang-undangan Republik Indonesia.
                        </p>
                        <p>
                            Bagi anak di bawah umur yang ingin mendaftarkan hewan peliharaannya, pendaftaran akun dan
                            pemrosesan data pribadi wajib dilakukan dengan persetujuan atau pendampingan dari orang tua
                            atau wali yang sah sebagaimana diatur dalam Pasal 25 UU PDP.
                        </p>
                    </section>

                    <!-- Pasal 10 -->
                    <section id="pasal-10" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                10</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Perubahan & Pembaruan Kebijakan Privasi
                            </h2>
                        </div>
                        <p>
                            KucingMu dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu untuk menyesuaikan
                            dengan perkembangan layanan teknis atau perubahan peraturan perundang-undangan yang berlaku.
                        </p>
                        <p>
                            Setiap perubahan akan dicantumkan pada halaman ini dengan memperbarui tanggal "Pembaruan
                            Terakhir" pada bagian atas dokumen. Apabila terdapat perubahan mendasar yang memengaruhi
                            hak-hak Anda, kami akan memberikan pemberitahuan yang wajar melalui situs web atau surel
                            resmi.
                        </p>
                    </section>

                    <!-- Pasal 11 -->
                    <section id="pasal-11" class="space-y-3.5 scroll-mt-24">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-teal-50 text-teal-800 border border-teal-200/80">Pasal
                                11</span>
                            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900">
                                Kontak Petugas Pelindungan Data Pribadi
                            </h2>
                        </div>
                        <p>
                            Jika Anda memiliki pertanyaan, permohonan penggunaan hak subjek data, masukan, atau keluhan
                            terkait penerapan Kebijakan Privasi ini, Anda dapat menghubungi kami melalui saluran resmi
                            berikut:
                        </p>

                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-3 text-xs sm:text-sm">
                            <div class="flex items-start gap-3">
                                <span class="text-base shrink-0">🏢</span>
                                <div>
                                    <strong class="text-slate-900 block">Pengendali Data Pribadi:</strong>
                                    <span class="text-slate-600">KucingMu</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="text-base shrink-0">📍</span>
                                <div>
                                    <strong class="text-slate-900 block">Alamat Kantor Sekretariat:</strong>
                                    <span
                                        class="text-slate-600">{{ $app_settings['office_address'] ?? 'Jl. Gedongkuning No.130 B, Rejowinangun, Kec. Kotagede, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55171' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="text-base shrink-0">✉️</span>
                                <div>
                                    <strong class="text-slate-900 block">Surel Kontak Privasi:</strong>
                                    <a href="mailto:bidkes.immdiy@gmail.com"
                                        class="text-teal-700 hover:underline">bidkes.immdiy@gmail.com</a> /
                                    <a href="mailto:kucingmuhammadiyah@gmail.com"
                                        class="text-teal-700 hover:underline">kucingmuhammadiyah@gmail.com</a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="text-base shrink-0">🌐</span>
                                <div>
                                    <strong class="text-slate-900 block">Formulir Pesan Online:</strong>
                                    <a href="{{ route('contact.index') }}"
                                        class="text-teal-700 hover:underline font-semibold">Halaman Pusat Bantuan &
                                        Kontak KucingMu</a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Document End Signature -->
                    <div class="pt-6 border-t border-slate-200 text-center text-xs text-slate-500 space-y-1">
                        <p class="font-semibold text-slate-700">Ditetapkan oleh Pengurus Platform KucingMu</p>
                        <p>KucingMu</p>
                    </div>

                </article>

            </div>

        </div>
    </main>

    <!-- Floating Back to Top Button -->
    <div class="fixed bottom-6 right-6 z-30 no-print" x-show="showBackToTop" x-cloak x-transition>
        <button type="button" @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="w-11 h-11 rounded-full bg-teal-700 text-white shadow-lg hover:bg-teal-800 flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
            title="Kembali ke atas">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18">
                </path>
            </svg>
        </button>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-10 border-t border-slate-800 no-print mt-auto">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="flex items-center gap-2">
                @if(isset($app_settings['app_logo']))
                    <img src="{{ asset('storage/' . $app_settings['app_logo']) }}" alt="" aria-hidden="true" width="28"
                        height="28" class="h-7 w-auto object-contain">
                @else
                    <span class="text-2xl" aria-hidden="true">🐱</span>
                @endif
                <span
                    class="font-outfit font-extrabold text-white text-base tracking-tight">{{ $app_settings['app_name'] ?? 'KucingMu' }}</span>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('privacy.policy') }}" class="text-teal-400 font-semibold hover:underline">
                    Kebijakan Privasi
                </a>
                <span class="text-slate-700">&bull;</span>
                <a href="{{ route('contact.index') }}" class="text-slate-400 hover:text-white transition">
                    Pusat Bantuan
                </a>
                <span class="text-slate-700">&bull;</span>
                <a href="{{ url('/') }}" class="text-slate-400 hover:text-white transition">
                    Beranda
                </a>
            </div>

            <p class="text-xs text-slate-400 footer-text">
                {!! $app_settings['app_footer'] ?? '&copy; ' . date('Y') . ' KucingMu. KucingMu.' !!}
            </p>
        </div>
    </footer>

    @include('partials.accessibility-widget')
</body>

</html>