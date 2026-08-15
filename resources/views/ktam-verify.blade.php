<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi KTAM KucingMu - {{ $card->ktam_number }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Tailwind compiled style -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        
        <div class="w-full max-w-xl bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            <!-- Header Status -->
            <div class="bg-gradient-to-br from-teal-800 to-teal-600 p-8 text-center text-white relative">
                <div class="mx-auto w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-3xl border border-white/30 shadow-inner">
                    🛡️
                </div>
                <h1 class="font-outfit text-2xl font-bold mt-4 tracking-tight">KTAM KucingMu Terverifikasi</h1>
                <p class="text-teal-100 text-xs mt-1 font-mono tracking-wider">{{ $card->ktam_number }}</p>
                <p class="text-[10px] text-teal-200/80 mt-1 uppercase tracking-widest font-bold">
                    Diterbitkan & Diverifikasi: {{ $card->verified_at ? $card->verified_at->format('d F Y') : $card->issue_date->format('d F Y') }}
                    @if($card->verifier)
                        • Oleh Admin {{ $card->verifier->name }}
                    @endif
                </p>
            </div>

            <!-- Content Body -->
            <div class="p-6 md:p-8 space-y-8">
                
                <!-- Primary Photo & Biometric Card -->
                <div class="flex flex-col sm:flex-row items-center gap-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-24 h-24 object-cover rounded-2xl border-2 border-teal-500 shadow-md flex-shrink-0">
                    
                    <div class="space-y-2 text-center sm:text-left flex-1">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h2 class="font-outfit text-xl font-bold text-slate-900 leading-tight">{{ $cat->name }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">Terdaftar</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan ♂' : 'Betina ♀' }}</p>

                        <!-- Biometrics Badge -->
                        @if($cat->biometric_type && $cat->biometric_type !== 'none')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-teal-600 text-white text-xs font-bold shadow-xs">
                                <span>🐾 Biometrik {{ strtoupper($cat->biometric_type) }} Verified</span>
                            </div>
                            @if($cat->biometric_code)
                                <p class="text-[10px] font-mono text-slate-500">ID Biometrik: {{ $cat->biometric_code }}</p>
                            @endif
                        @else
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-slate-200 text-slate-700 text-[11px] font-semibold">
                                <span>⚪ Data Foto Terverifikasi</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Multi-Photo Gallery (Front, Side, Top view) -->
                @if($cat->photos->count() > 0)
                    <div class="space-y-3">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Galeri Foto Kucing (Semua Sudut Pandang)</h3>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($cat->photos as $photo)
                                <div class="relative rounded-xl overflow-hidden border {{ $photo->is_primary ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-200' }} bg-slate-100">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $photo->label }}" class="w-full h-20 object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-slate-900/70 text-white text-[9px] font-bold text-center py-0.5 truncate px-1">
                                        {{ $photo->label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Cat & Owner Information -->
                <div class="space-y-4">
                    <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Informasi Anggota Kucing</h2>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">Nama Kucing</span>
                            <span class="font-bold text-slate-900">{{ $cat->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">Ras Kucing</span>
                            <span class="font-bold text-slate-900">{{ $cat->breed }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">Jenis Kelamin</span>
                            <span class="font-bold text-slate-900">{{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">Tanggal Lahir</span>
                            <span class="font-bold text-slate-900">{{ $cat->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">Nama Pemilik</span>
                            <span class="font-bold text-slate-900">{{ $cat->owner->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block uppercase tracking-wider font-semibold">NBM Pemilik</span>
                            <span class="font-bold text-slate-900">{{ $cat->owner->muhammadiyah_id ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Medical History / Checkup Details -->
                <div class="space-y-4">
                    <h2 class="font-outfit text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Riwayat Pemeriksaan Kesehatan</h2>
                    
                    @if($records->isEmpty())
                        <p class="text-xs text-slate-400">Belum ada riwayat pemeriksaan medis tercatat.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($records as $rec)
                                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-3 leading-relaxed text-sm">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="text-xs font-bold text-teal-700 block">Kondisi: {{ $rec->general_condition }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $rec->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <span class="font-mono text-xs bg-white px-2 py-0.5 rounded border border-slate-200 text-slate-700">
                                            {{ $rec->weight }}kg / {{ $rec->temperature }}°C
                                        </span>
                                    </div>

                                    <!-- Treatment logs -->
                                    <div class="flex flex-wrap gap-2 text-[10px]">
                                        @if($rec->deworming_given)
                                            <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-bold border border-teal-200">Obat Cacing</span>
                                        @endif
                                        @if($rec->anti_flea_given)
                                            <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-bold border border-teal-200">Obat Kutu</span>
                                        @endif
                                        @if($rec->supplement_given)
                                            <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 font-bold border border-teal-200">Suplemen</span>
                                        @endif
                                    </div>

                                    @if($rec->treatment_notes)
                                        <div>
                                            <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Tindakan / Terapi:</span>
                                            <span class="text-slate-600 text-xs block bg-white p-2 rounded border border-slate-100">{{ $rec->treatment_notes }}</span>
                                        </div>
                                    @endif

                                    @if($rec->recommendation)
                                        <div>
                                            <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Rekomendasi Dokter:</span>
                                            <span class="text-slate-600 text-xs block bg-white p-2 rounded border border-slate-100 font-semibold">{{ $rec->recommendation }}</span>
                                        </div>
                                    @endif

                                    <div class="text-[10px] text-slate-400 text-right">
                                        Pemeriksa: <span class="font-bold text-slate-600">{{ $rec->vet->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Footer brand signature -->
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 text-center text-xs text-slate-400">
                Pemeriksaan kesehatan kucing & penerbitan KTAM ini diselenggarakan oleh komunitas KucingMu.
            </div>
        </div>

        <div class="mt-6">
            <a href="/login" class="text-xs font-bold text-teal-700 hover:text-teal-800 bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm hover:shadow transition">
                Masuk ke Dashboard
            </a>
        </div>
        
    </div>
</body>
</html>
