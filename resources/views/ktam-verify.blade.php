<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi KTAKuMu KucingMu - {{ $card->ktam_number }}</title>
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
            
            @if($isDeleted ?? false)
                <!-- Header Status for DELETED cat -->
                <div class="bg-rose-950 p-6 sm:p-8 text-center text-white border-b-4 border-rose-600">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-rose-900 border border-rose-700 text-rose-100 text-xs font-bold uppercase tracking-wider">
                        ⚠️ Status: Sudah Dihapus / Tidak Berlaku
                    </span>
                    <h1 class="font-outfit text-2xl font-bold mt-2.5 text-white tracking-tight">Data Kucing Telah Dihapus</h1>
                    <p class="text-rose-200 text-xs mt-1 font-mono tracking-wider">{{ $card->ktam_number }}</p>
                </div>

                <!-- Body for DELETED cat (all photos and sensitive data hidden) -->
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center space-y-3">
                        <div class="w-14 h-14 bg-rose-100 text-rose-700 rounded-full flex items-center justify-center mx-auto text-2xl shadow-inner">
                            🚫
                        </div>
                        <h2 class="font-outfit text-lg font-bold text-rose-950">Kartu Tanda Anggota Ini Tidak Lagi Aktif</h2>
                        <div class="p-4 bg-white rounded-xl border border-rose-200 text-xs text-rose-900 leading-relaxed text-left space-y-2">
                            <div class="flex items-start gap-2">
                                <span class="font-semibold text-rose-950 min-w-[90px]">Nomor NIAKuMu:</span>
                                <span class="font-mono font-bold text-slate-800">{{ $card->ktam_number }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="font-semibold text-rose-950 min-w-[90px]">Dihapus Oleh:</span>
                                <span class="font-bold text-rose-800">{{ $deletedByName ?? 'Administrator' }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="font-semibold text-rose-950 min-w-[90px]">Waktu Hapus:</span>
                                <span class="font-bold text-slate-700">{{ $deletedAtFormatted ?? '-' }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 pt-1">
                            Sesuai dengan ketentuan sistem, seluruh informasi detail, galeri foto, dan riwayat pemeriksaan medis untuk data yang telah dihapus disembunyikan.
                        </p>
                    </div>
                </div>
            @else
                <!-- Header Status for VALID cat -->
                <div class="bg-teal-900 p-6 sm:p-8 text-center text-white">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-800 border border-teal-700 text-teal-100 text-xs font-semibold uppercase tracking-wider">
                        Status: Resmi Terverifikasi
                    </span>
                    <h1 class="font-outfit text-2xl font-bold mt-2.5 text-white tracking-tight">KTAKuMu KucingMu Valid</h1>
                    <p class="text-teal-100 text-xs mt-1 font-mono tracking-wider">{{ $card->ktam_number }}</p>
                    <p class="text-xs text-teal-200 mt-2 font-medium">
                        Diterbitkan: {{ $card->verified_at ? $card->verified_at->format('d F Y') : $card->issue_date->format('d F Y') }}
                        @if($card->verifier)
                            &bull; Verifikator: {{ $card->verifier->name }}
                        @endif
                    </p>
                </div>

                <!-- Content Body for VALID cat -->
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
                                <span class="text-slate-500 block font-medium">Nomor NIAKuMu</span>
                                <span class="font-bold font-mono text-teal-800 text-sm">{{ $cat->formatted_unique_code }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block font-medium">Ras / Jenis</span>
                                <span class="font-bold text-slate-900">{{ $cat->breed }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block font-medium">Warna / Pola</span>
                                <span class="font-bold text-slate-900">{{ $cat->color ?: 'Campuran / Ras' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block font-medium">Jenis Kelamin</span>
                                <span class="font-bold text-slate-900">{{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block font-medium">Tanggal Lahir</span>
                                <span class="font-bold text-slate-900">{{ $cat->date_of_birth ? $cat->date_of_birth->format('d M Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block font-medium">Wilayah Muhammadiyah</span>
                                <span class="font-bold text-slate-900">{{ $cat->wilayah ? $cat->wilayah->nama : 'PWM D.I. Yogyakarta' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block font-medium">Nama Pemilik</span>
                                <span class="font-bold text-slate-900">{{ $cat->owner ? $cat->owner->name : '-' }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-slate-500 block font-medium">Nomor NBM Pemilik</span>
                                <span class="font-bold font-mono text-slate-900">{{ $cat->owner ? ($cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?? '-')) : '-' }}</span>
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
            @endif

            <!-- Footer brand signature -->
            <div class="bg-slate-100 border-t border-slate-200 px-6 py-3.5 text-center text-xs text-slate-600">
                Pemeriksaan kesehatan kucing & penerbitan KTAKuMu diselenggarakan oleh Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah.
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

