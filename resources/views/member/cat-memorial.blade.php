<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('dashboard') }}" class="hover:text-teal-700">Dashboard</a>
                <span>&rsaquo;</span>
                <span class="font-bold text-slate-800">Mengenang {{ $cat->name }}</span>
            </nav>

            <!-- Status Alert if any -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Memorial Tribute Hero Card -->
            <div class="relative overflow-hidden bg-gradient-to-b from-slate-900 to-slate-800 text-white rounded-3xl p-6 sm:p-10 shadow-lg border border-slate-700">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl select-none pointer-events-none">
                    🐾
                </div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10 text-center sm:text-left">
                    <!-- Cat Photo with Memorial Frame -->
                    <div class="relative shrink-0">
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-2xl overflow-hidden border-4 border-amber-400/80 shadow-2xl bg-slate-900">
                            <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                        </div>
                        <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-amber-500 text-slate-950 font-outfit text-[10px] font-extrabold uppercase px-3 py-0.5 rounded-full shadow-md whitespace-nowrap">
                            In Loving Memory
                        </span>
                    </div>

                    <!-- Cat Memorial Info -->
                    <div class="flex-1 space-y-2">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-700/80 text-amber-300 text-[11px] font-semibold border border-slate-600">
                            <span>🕊️</span>
                            <span>Mengenang Anabul Tercinta</span>
                        </div>

                        <h1 class="font-outfit text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            {{ $cat->name }}
                        </h1>

                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            {{ $cat->breed }} &bull; {{ $cat->gender === 'male' ? 'Jantan' : 'Betina' }}
                            @if($cat->color) &bull; Warna {{ $cat->color }} @endif
                        </p>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1 text-xs text-slate-400 pt-1">
                            @if($cat->date_of_birth)
                                <span>Lahir: <strong class="text-slate-200">{{ $cat->date_of_birth->format('d M Y') }}</strong></span>
                            @endif
                            @if($cat->unique_code)
                                <span>NIAKuMu: <strong class="text-amber-300 font-mono">{{ $cat->unique_code }}</strong></span>
                            @endif
                            @if($cat->owner)
                                <span>Keluarga/Pemilik: <strong class="text-slate-200">{{ $cat->owner->name }}</strong></span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                            <a href="{{ route('cat.memorial.poster', $cat->id) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-md transition cursor-pointer">
                                <span>🎨</span>
                                <span>Lihat Poster Twibbon Kenangan</span>
                            </a>
                            @if(Auth::id() === $cat->user_id)
                                <a href="{{ route('cat.edit', $cat->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-semibold text-xs transition">
                                    <span>✏️</span>
                                    <span>Kelola Data</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Layout: Condolence Form & Wall -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Form Kirim Ucapan -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span class="text-lg">💌</span>
                            <div>
                                <h2 class="font-outfit text-sm font-bold text-slate-900 leading-tight">Kirim Simpati & Doa</h2>
                                <p class="text-[11px] text-slate-500">Berikan penguatan untuk pemilik anabul.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('cat.memorial.store', $cat->id) }}" class="space-y-3.5">
                            @csrf

                            <div>
                                <label for="prayer_text" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Pilihan Doa Cepat
                                </label>
                                <select id="prayer_preset" onchange="if(this.value) document.getElementById('prayer_text').value = this.value"
                                        class="w-full text-xs py-2 px-2.5 rounded-xl border border-slate-300 bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100">
                                    <option value="">Pilih doa atau tulis sendiri di bawah</option>
                                    <option value="Semoga beristirahat dengan damai di jembatan pelangi.">Semoga beristirahat dengan damai di jembatan pelangi.</option>
                                    <option value="Turut berduka cita mendalam atas kepergian anabul tercinta.">Turut berduka cita mendalam atas kepergian anabul tercinta.</option>
                                    <option value="Terima kasih telah membawa banyak kebahagiaan dan kasih sayang.">Terima kasih telah membawa banyak kebahagiaan dan kasih sayang.</option>
                                    <option value="Kucing yang baik akan selalu dikenang di hati selamanya.">Kucing yang baik akan selalu dikenang di hati selamanya.</option>
                                </select>
                            </div>

                            <div>
                                <label for="prayer_text" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kalimat Doa / Simpati Singkat
                                </label>
                                <input type="text" id="prayer_text" name="prayer_text"
                                       placeholder="Contoh: Beristirahatlah dengan tenang, anabul manis..."
                                       value="{{ old('prayer_text') }}"
                                       maxlength="255"
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-100">
                            </div>

                            <div>
                                <label for="message" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Pesan / Kenangan Bersama <span class="text-rose-600">*</span>
                                </label>
                                <textarea id="message" name="message" rows="4" required
                                          placeholder="Tuliskan ucapan belasungkawa, simpati, atau kenangan indah bersama anabul ini..."
                                          class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-2 focus:ring-teal-100">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full button-primary text-xs py-2.5 font-bold flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>🕊️</span>
                                <span>Kirim Ucapan Belasungkawa</span>
                            </button>
                        </form>
                    </div>

                    <!-- Poster Twibbon Card Mini -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 p-4 rounded-2xl space-y-2">
                        <div class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                            <span>🖼️</span>
                            <span>Twibbon & Kartu Kenangan</span>
                        </div>
                        <p class="text-[11px] text-amber-800 leading-relaxed">
                            Unduh poster penghormatan khusus untuk dibagikan ke media sosial atau disimpan sebagai kenangan manis.
                        </p>
                        <a href="{{ route('cat.memorial.poster', $cat->id) }}" target="_blank"
                           class="inline-flex w-full items-center justify-center gap-1.5 text-xs font-bold py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white shadow-2xs transition">
                            <span>Buka Poster Twibbon</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Daftar Ucapan (Wall of Condolences) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🕯️</span>
                                <h2 class="font-outfit text-base font-bold text-slate-900">
                                    Dinding Doa & Kenangan ({{ $cat->condolences->count() }})
                                </h2>
                            </div>
                            <span class="text-xs text-slate-400">Dari Anggota KucingMu</span>
                        </div>

                        @if($cat->condolences->count() > 0)
                            <div class="space-y-3.5">
                                @foreach($cat->condolences as $item)
                                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 hover:border-slate-300 transition">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-teal-700 text-white flex items-center justify-center font-bold text-[10px]">
                                                    {{ strtoupper(substr($item->user->name ?? 'M', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-800 leading-tight">
                                                        {{ $item->user->name ?? 'Anggota KucingMu' }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-400">
                                                        {{ $item->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if($item->prayer_text)
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">
                                                    {{ $item->prayer_text }}
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-xs text-slate-700 leading-relaxed pl-9">
                                            {{ $item->message }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 px-4 space-y-2 text-slate-400">
                                <span class="text-3xl block">🕊️</span>
                                <p class="text-xs font-medium text-slate-600">Belum ada ucapan belasungkawa.</p>
                                <p class="text-[11px] text-slate-400">Jadilah yang pertama mengirimkan untaian doa dan simpati melalui formulir di samping.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
