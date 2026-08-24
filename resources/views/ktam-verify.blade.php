<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi KTAM KucingMu - {{ $card->ktam_number }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind compiled style -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 py-8">
        
        <div class="w-full max-w-xl bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Header Status -->
            <div class="bg-teal-900 p-6 sm:p-8 text-center text-white">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-800 border border-teal-700 text-teal-100 text-xs font-semibold uppercase tracking-wider">
                    Status: Resmi Terverifikasi
                </span>
                <h1 class="font-outfit text-2xl font-bold mt-2.5 text-white tracking-tight">KTAM KucingMu Valid</h1>
                <p class="text-teal-100 text-xs mt-1 font-mono tracking-wider">{{ $card->ktam_number }}</p>
                <p class="text-xs text-teal-200 mt-2 font-medium">
                    Diterbitkan: {{ $card->verified_at ? $card->verified_at->format('d F Y') : $card->issue_date->format('d F Y') }}
                    @if($card->verifier)
                        &bull; Verifikator: {{ $card->verifier->name }}
                    @endif
                </p>
            </div>

            <!-- Content Body -->
            <div class="p-6 sm:p-8 space-y-6">
                
                <!-- Primary Photo & Biometric Card -->
                <div class="flex flex-col sm:flex-row items-center gap-5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-24 h-24 object-cover rounded-xl border-2 border-teal-700 shadow-xs flex-shrink-0">
                    
                    <div class="space-y-1.5 text-center sm:text-left flex-1">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h2 class="font-outfit text-xl font-bold text-slate-900 leading-tight">{{ $cat->name }}</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-900 border border-teal-200">Terdaftar</span>
                        </div>
                        <p class="text-xs text-slate-600">{{ $cat->breed }} &bull; {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>

                        <!-- Biometrics Badge -->
                        @if($cat->biometric_type && $cat->biometric_type !== 'none')
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-bold bg-teal-800 text-white">
                                <span>Biometrik {{ strtoupper($cat->biometric_type) }} Terverifikasi</span>
                            </div>
                            @if($cat->biometric_code)
                                <p class="text-[11px] font-mono text-slate-600">ID Biometrik: {{ $cat->biometric_code }}</p>
                            @endif
                        @else
                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-200 text-slate-800 text-xs font-semibold">
                                <span>Data Foto Terverifikasi</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Multi-Photo Gallery (Front, Side, Top view) -->
                @if($cat->photos->count() > 0)
                    <div class="space-y-2.5">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Galeri Foto Terverifikasi</h3>
                        <div class="grid grid-cols-3 gap-2.5">
                            @foreach($cat->photos as $photo)
                                <div class="relative rounded-lg overflow-hidden border {{ $photo->is_primary ? 'border-teal-700 ring-2 ring-teal-700/20' : 'border-slate-200' }} bg-slate-100">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $photo->label }}" class="w-full h-20 object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-slate-900/80 text-white text-[10px] font-medium text-center py-0.5 truncate px-1">
                                        {{ $photo->label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Cat & Owner Information -->
                <div class="space-y-3">
                    <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-1.5">Informasi Anggota Kucing</h2>
                    
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 block font-medium">Nama Kucing</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $cat->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block font-medium">Ras / Jenis</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $cat->breed }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block font-medium">Jenis Kelamin</span>
                            <span class="font-bold text-slate-900">{{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block font-medium">Tanggal Lahir</span>
                            <span class="font-bold text-slate-900">{{ $cat->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block font-medium">Nama Pemilik</span>
                            <span class="font-bold text-slate-900">{{ $cat->owner->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block font-medium">Nomor NBM</span>
                            <span class="font-bold text-slate-900">{{ $cat->owner->muhammadiyah_id ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Medical History / Checkup Details -->
                <div class="space-y-3">
                    <h2 class="font-outfit text-base font-bold text-slate-900 border-b border-slate-200 pb-1.5">Riwayat Pemeriksaan Dokter</h2>
                    
                    @if($records->isEmpty())
                        <p class="text-xs text-slate-500 py-3 bg-slate-50 rounded-lg text-center">Belum ada catatan riwayat medis tersimpan.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($records as $rec)
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-2 text-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="text-xs font-bold text-teal-800 block">Kondisi: {{ $rec->general_condition }}</span>
                                            <span class="text-[11px] text-slate-500">{{ $rec->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <span class="font-mono text-xs bg-white px-2 py-0.5 rounded border border-slate-200 text-slate-800 font-semibold">
                                            {{ $rec->weight }}kg / {{ $rec->temperature }}°C
                                        </span>
                                    </div>

                                    <!-- Treatment logs -->
                                    <div class="flex flex-wrap gap-1.5 text-[10px]">
                                        @if($rec->deworming_given)
                                            <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-900 font-bold border border-teal-200">Obat Cacing</span>
                                        @endif
                                        @if($rec->anti_flea_given)
                                            <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-900 font-bold border border-teal-200">Obat Kutu</span>
                                        @endif
                                        @if($rec->supplement_given)
                                            <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-900 font-bold border border-teal-200">Suplemen</span>
                                        @endif
                                    </div>

                                    @if($rec->treatment_notes)
                                        <div>
                                            <span class="text-[11px] text-slate-500 block font-medium">Tindakan / Terapi:</span>
                                            <span class="text-slate-800 text-xs block bg-white p-2 rounded border border-slate-200">{{ $rec->treatment_notes }}</span>
                                        </div>
                                    @endif

                                    @if($rec->recommendation)
                                        <div>
                                            <span class="text-[11px] text-slate-500 block font-medium">Rekomendasi Dokter:</span>
                                            <span class="text-slate-800 text-xs block bg-white p-2 rounded border border-slate-200 font-semibold">{{ $rec->recommendation }}</span>
                                        </div>
                                    @endif

                                    <div class="text-[11px] text-slate-500 text-right">
                                        Pemeriksa: <strong class="text-slate-800">{{ $rec->vet->name }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Footer brand signature -->
            <div class="bg-slate-100 border-t border-slate-200 px-6 py-3.5 text-center text-xs text-slate-600">
                Pemeriksaan kesehatan kucing & penerbitan KTAM diselenggarakan oleh Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah.
            </div>
        </div>

        <div class="mt-6">
            <a href="/login" class="button-secondary text-xs font-semibold px-5 py-2.5">
                Masuk ke Dashboard
            </a>
        </div>
        
    </div>
</body>
</html>
