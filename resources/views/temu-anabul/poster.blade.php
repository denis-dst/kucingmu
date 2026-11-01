<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poster Pencarian: {{ $temuAnabul->cat_name }} - KucingMU</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&family=Outfit:wght@700;800;900&display=swap">

    @vite(['resources/css/app.css'])

    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .poster-card {
                box-shadow: none !important;
                border: 2px solid #e11d48 !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans antialiased text-slate-800">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-xl w-full mb-4 flex items-center justify-between gap-3">
        <a href="{{ route('temu-anabul.show', $temuAnabul->id) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
            &larr; Kembali ke Laporan
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Poster Canvas (A4 Aspect Ratio Optimized) -->
    <div class="poster-card max-w-xl w-full bg-white rounded-3xl border-4 border-rose-600 shadow-2xl overflow-hidden flex flex-col justify-between">
        
        <!-- Top Banner Header -->
        <div class="bg-rose-600 text-white py-6 px-6 text-center space-y-1">
            <div class="text-xs font-black uppercase tracking-[0.25em] text-rose-200">
                KOMUNITAS KUCINGMU PEDULI
            </div>
            <h1 class="text-3xl sm:text-4xl font-outfit font-black tracking-tight leading-none uppercase">
                DICARI: KUCING HILANG!
            </h1>
            <p class="text-xs text-rose-100 font-semibold pt-1">
                Mohon bantuan warga & pecinta kucing yang melihat anabul berikut
            </p>
        </div>

        <!-- Poster Body -->
        <div class="p-6 sm:p-8 space-y-6 flex-1">

            <!-- Photo & Name -->
            <div class="space-y-3 text-center">
                <div class="w-full aspect-4/3 rounded-2xl overflow-hidden border-2 border-slate-200 shadow-md bg-slate-50">
                    <img src="{{ $temuAnabul->thumbnail_url }}" alt="{{ $temuAnabul->cat_name }}" class="w-full h-full object-cover">
                </div>

                <div>
                    <h2 class="text-3xl font-outfit font-black text-slate-900 leading-tight">
                        "{{ $temuAnabul->cat_name }}"
                    </h2>
                    <div class="flex flex-wrap items-center justify-center gap-2 text-xs font-bold text-slate-600 mt-1">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 capitalize">
                            {{ $temuAnabul->gender === 'jantan' ? '♂ Jantan' : ($temuAnabul->gender === 'betina' ? '♀ Betina' : 'Kucing') }}
                        </span>
                        @if($temuAnabul->coat_color)
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200">
                                Warna: {{ $temuAnabul->coat_color }}
                            </span>
                        @endif
                        @if($temuAnabul->age_estimate)
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200">
                                Usia: {{ $temuAnabul->age_estimate }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reward Banner (If Any) -->
            @if($temuAnabul->reward_info)
                <div class="p-3.5 bg-amber-400 border-2 border-amber-500 rounded-2xl text-center shadow-xs">
                    <span class="text-xs uppercase font-extrabold tracking-wider text-amber-950 block">HADIAH / IMBALAN</span>
                    <span class="text-base font-outfit font-black text-amber-950">{{ $temuAnabul->reward_info }}</span>
                </div>
            @endif

            <!-- Distinctive Traits -->
            <div class="bg-rose-50/60 border border-rose-200/80 rounded-2xl p-4 space-y-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800 block">
                    Ciri-Ciri Khas Fisik:
                </span>
                <p class="text-xs sm:text-sm text-slate-800 leading-relaxed font-semibold">
                    {{ $temuAnabul->distinctive_features }}
                </p>
            </div>

            <!-- Incident Location & Time -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-1">
                <div class="flex items-start gap-2">
                    <span class="font-bold text-slate-700 shrink-0">📍 Terakhir Terlihat:</span>
                    <span class="text-slate-800 font-semibold">{{ $temuAnabul->formatted_address ?: ($temuAnabul->district . ', ' . $temuAnabul->city) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-700 shrink-0">⏰ Waktu:</span>
                    <span class="text-slate-800">{{ $temuAnabul->incident_date ? $temuAnabul->incident_date->translatedFormat('l, d F Y - H:i WIB') : '-' }}</span>
                </div>
            </div>

            <!-- Contact Box -->
            <div class="bg-slate-900 text-white rounded-2xl p-5 text-center space-y-2 shadow-md">
                <span class="text-xs font-bold text-rose-400 uppercase tracking-widest block">
                    BILA MELIHAT ATAU MENEMUKAN, HUBUNGI:
                </span>
                <div class="text-2xl sm:text-3xl font-outfit font-black tracking-tight text-white">
                    {{ $temuAnabul->contact_phone }}
                </div>
                <div class="text-xs text-slate-300 font-medium">
                    Kontak: <span class="font-bold text-white">{{ $temuAnabul->contact_name }}</span> (WhatsApp / Telepon)
                </div>
            </div>

        </div>

        <!-- Poster Footer -->
        <div class="bg-slate-100 border-t border-slate-200 px-6 py-4 flex items-center justify-between text-slate-500 text-[11px]">
            <div class="flex items-center gap-2">
                <span class="text-base">🐱</span>
                <span class="font-bold text-slate-700">KucingMU Community</span>
            </div>
            <div>
                kucingmu.id/temu-anabul/{{ $temuAnabul->id }}
            </div>
        </div>

    </div>

</body>
</html>

