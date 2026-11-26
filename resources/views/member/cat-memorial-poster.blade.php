<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poster Kenangan &mdash; {{ $cat->name }} | KucingMu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
            }

            .poster-card {
                box-shadow: none !important;
                border-color: #334155 !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body
    class="bg-slate-900 text-slate-100 min-h-screen py-6 px-4 flex flex-col items-center justify-center font-sans antialiased">

    <!-- Top Action Bar (No Print) -->
    <header class="no-print w-full max-w-md mb-6 flex items-center justify-between gap-3">
        <a href="{{ route('cat.memorial', $cat->id) }}"
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-3 py-2 rounded-xl border border-slate-700 transition">
            <span>&larr;</span>
            <span>Kembali ke Halaman Kenangan</span>
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 px-4 py-2 rounded-xl shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak / Simpan Poster</span>
        </button>
    </header>

    <!-- Poster Canvas Container (4:5 Ratio Optimized for Story & Social Share) -->
    <main
        class="poster-card relative w-full max-w-md bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 rounded-3xl border-2 border-amber-400/40 p-7 sm:p-9 shadow-2xl overflow-hidden text-center flex flex-col justify-between">

        <!-- Subtle Background Decorative Patterns -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-amber-400/5 rounded-full blur-2xl pointer-events-none">
        </div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-teal-500/5 rounded-full blur-2xl pointer-events-none">
        </div>

        <!-- Inner Ornate Border -->
        <div class="absolute inset-3 rounded-2xl border border-amber-400/20 pointer-events-none"></div>

        <!-- Poster Header -->
        <div class="relative z-10 space-y-2 pt-2">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/90 border border-amber-400/30 text-amber-300 text-[10px] font-bold tracking-widest uppercase">
                <span>🕊️</span>
                <span>In Loving Memory</span>
            </div>
            <h2 class="font-serif italic text-2xl text-amber-200 font-normal tracking-wide">
                Mengenang Anabul Tercinta
            </h2>
        </div>

        <!-- Cat Photo in Elegant Frame -->
        <div class="relative z-10 my-6 flex justify-center">
            <div
                class="relative w-48 h-48 sm:w-56 sm:h-56 rounded-full p-1.5 bg-gradient-to-tr from-amber-500 via-amber-200 to-amber-500 shadow-2xl">
                <div class="w-full h-full rounded-full overflow-hidden bg-slate-900 border-2 border-slate-950">
                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                </div>
                <div
                    class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-slate-900 border border-amber-400 text-amber-300 text-[10px] font-bold px-3 py-0.5 rounded-full shadow-lg whitespace-nowrap">
                    🐾 Anabul Kesayangan
                </div>
            </div>
        </div>

        <!-- Cat Identity & Tribute -->
        <div class="relative z-10 space-y-3 pb-2">
            <div>
                <h1 class="font-outfit text-3xl font-extrabold text-white tracking-tight">
                    {{ $cat->name }}
                </h1>
                <p class="text-xs text-amber-300 font-medium mt-0.5">
                    {{ $cat->breed }} &bull; {{ $cat->gender === 'male' ? 'Jantan' : 'Betina' }}
                </p>
            </div>

            <div
                class="inline-flex items-center gap-3 text-[11px] text-slate-300 bg-slate-900/80 px-4 py-1.5 rounded-xl border border-slate-800">
                @if($cat->date_of_birth)
                    <span>Lahir: {{ $cat->date_of_birth->format('d M Y') }}</span>
                @endif
                @if($cat->unique_code)
                    <span class="text-slate-500">&bull;</span>
                    <span class="font-mono text-amber-300 font-bold">NIA: {{ $cat->unique_code }}</span>
                @endif
            </div>

            <!-- Compassionate Quote / Prayer -->
            <blockquote class="text-xs text-slate-300 italic max-w-xs mx-auto leading-relaxed pt-1">
                "Terima kasih telah menghiasi hari-hari dengan kehangatan dan dengkuran manis. Beristirahatlah dengan
                tenang di seberang jembatan pelangi."
            </blockquote>

            @if($cat->owner)
                <p class="text-[11px] text-slate-400 pt-1">
                    Keluarga: <span class="text-slate-200 font-semibold">{{ $cat->owner->name }}</span>
                </p>
            @endif
        </div>

        <!-- Poster Footer Branding -->
        <footer
            class="relative z-10 pt-4 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400">
            <div class="flex items-center gap-1.5 font-outfit font-bold text-teal-400">
                <span>🐱</span>
                <span>KucingMu Komunitas</span>
            </div>
            <span>kucingmu.online</span>
        </footer>

    </main>

    <!-- Social Share Help (No Print) -->
    <div class="no-print mt-6 text-center text-xs text-slate-400 max-w-md leading-relaxed">
        Tip: Gunakan tombol <strong class="text-amber-300">Cetak / Simpan Poster</strong> di atas, lalu pilih opsi
        <em>Save as PDF</em> atau tangkap layar (screenshot) untuk membagikannya ke media sosial.
    </div>

</body>

</html>