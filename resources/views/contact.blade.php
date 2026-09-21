<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <span class="eyebrow">Pusat Bantuan & Kemitraan</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                    Hubungi Pengurus & Administrator KucingMu
                </h1>
            </div>
            <a href="{{ url('/') }}" class="button-secondary text-xs font-semibold px-3.5 py-1.5 hidden sm:inline-flex">
                ← Kembali ke Beranda
            </a>
        </div>
    </x-slot>

    @php
        $contactEmail = $app_settings['contact_email'] ?? 'bidkes.immdiy@gmail.com / kucingmuhammadiyah@gmail.com';
        $officeAddress = $app_settings['office_address'] ?? 'Jl. Gedongkuning No.130 B, Rejowinangun, Kec. Kotagede, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55171';
    @endphp

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('contact_success'))
                <div class="p-5 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-start gap-3 shadow-xs">
                    <span class="text-2xl shrink-0">✅</span>
                    <div>
                        <h3 class="font-outfit font-bold text-emerald-950 text-sm">Pesan Berhasil Terkirim!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">{{ session('contact_success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Symmetrical 2-Column Responsive Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

                <!-- Left Column: Official Contact Information Card -->
                <div class="lg:col-span-5 rounded-3xl p-6 sm:p-8 flex flex-col justify-between space-y-6 shadow-md"
                    style="background-color: #0f3e36; color: #ffffff;">

                    <!-- Top: Title & Description -->
                    <div class="space-y-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase"
                            style="background-color: rgba(255,255,255,0.12); color: #a7f3d0; border: 1px solid rgba(255,255,255,0.2);">
                            <span>💬</span> Informasi & Bantuan
                        </span>
                        <h2
                            class="font-outfit text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight">
                            Layanan Terpadu & Kemitraan KucingMu
                        </h2>
                        <p class="text-xs sm:text-sm leading-relaxed" style="color: #ccfbf1;">
                            Layanan resmi persyarikatan untuk konsultasi, bantuan data identitas KTAKuMu, surveilans
                            kesehatan kucing, dan kemitraan dokter hewan/relawan.
                        </p>
                    </div>

                    <!-- Middle: Contact Info Badges -->
                    <div class="space-y-3.5">

                        <!-- Email Resmi -->
                        <div class="flex items-start gap-3.5 p-4 rounded-2xl"
                            style="background-color: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.15); color: #ffffff;">
                                ✉️
                            </div>
                            <div class="space-y-1 min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider block"
                                    style="color: #6ee7b7;">Email Resmi</span>
                                <div class="text-xs font-semibold leading-relaxed space-y-0.5">
                                    <div><a href="mailto:bidkes.immdiy@gmail.com"
                                            class="text-white hover:underline transition">bidkes.immdiy@gmail.com</a>
                                    </div>
                                    <div><a href="mailto:kucingmuhammadiyah@gmail.com"
                                            class="text-white hover:underline transition">kucingmuhammadiyah@gmail.com</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat Kantor -->
                        <div class="flex items-start gap-3.5 p-4 rounded-2xl"
                            style="background-color: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.15); color: #ffffff;">
                                📍
                            </div>
                            <div class="space-y-1 min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider block"
                                    style="color: #6ee7b7;">Alamat Kantor Sekretariat</span>
                                <p class="text-xs leading-relaxed text-white font-medium">
                                    {{ $officeAddress }}
                                </p>
                            </div>
                        </div>

                        <!-- Jam Pelayanan -->
                        <div class="flex items-start gap-3.5 p-4 rounded-2xl"
                            style="background-color: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.15); color: #ffffff;">
                                🕒
                            </div>
                            <div class="space-y-1 min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider block"
                                    style="color: #6ee7b7;">Waktu Pelayanan</span>
                                <p class="text-xs text-white font-medium">
                                    Senin &ndash; Jumat, 08.00 &ndash; 16.00 WIB
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Organization Badge -->
                    <div class="p-4 rounded-2xl space-y-1.5"
                        style="background-color: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.12);">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🌿</span>
                            <span class="font-outfit font-bold text-xs" style="color: #a7f3d0;"></span>
                        </div>
                        <p class="text-[11px] leading-relaxed" style="color: #ccfbf1;">
                            Mewujudkan ekosistem lingkungan hidup yang ramah satwa melalui pendekatan kesejahteraan
                            hewan (Animal Welfare) berbasis nilai-nilai Islam.
                        </p>
                    </div>
                </div>

                <!-- Right Column: Interactive Form Card -->
                <div
                    class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 sm:p-10 shadow-md flex flex-col justify-between space-y-6">
                    <div>
                        <div class="border-b border-slate-100 pb-4 mb-5">
                            <h3 class="font-outfit text-xl sm:text-2xl font-extrabold text-slate-900">Kirimkan Pesan
                                atau Pertanyaan</h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi formulir di bawah ini, tim
                                administrator KucingMu akan merespon melalui email sesegera mungkin.</p>
                        </div>

                        <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label for="name" class="form-label text-xs font-semibold text-slate-700">Nama
                                        Lengkap <span class="text-rose-500">*</span></label>
                                    <input type="text" id="name" name="name"
                                        value="{{ old('name', Auth::user()?->name) }}" required
                                        placeholder="Nama Lengkap Anda"
                                        class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="form-label text-xs font-semibold text-slate-700">Alamat
                                        Email <span class="text-rose-500">*</span></label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email', Auth::user()?->email) }}" required
                                        placeholder="nama@email.com"
                                        class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- No. Telepon / WhatsApp -->
                                <div>
                                    <label for="phone" class="form-label text-xs font-semibold text-slate-700">No.
                                        WhatsApp / Telepon <span
                                            class="text-slate-400 font-normal">(Opsional)</span></label>
                                    <input type="text" id="phone" name="phone"
                                        value="{{ old('phone', Auth::user()?->phone) }}" placeholder="08xxxxxxxxxx"
                                        class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                                </div>

                                <!-- Subjek Pesan -->
                                <div>
                                    <label for="subject" class="form-label text-xs font-semibold text-slate-700">Subjek
                                        / Topik <span class="text-rose-500">*</span></label>
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                                        placeholder="Contoh: Kemitraan Relawan / Pertanyaan KTAM"
                                        class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('subject')" class="mt-1" />
                                </div>
                            </div>

                            <!-- Pesan / Pertanyaan -->
                            <div>
                                <label for="message" class="form-label text-xs font-semibold text-slate-700">Isi Pesan
                                    <span class="text-rose-500">*</span></label>
                                <textarea id="message" name="message" rows="4" required
                                    placeholder="Tuliskan pesan, pertanyaan, atau masukan Anda di sini..."
                                    class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">{{ old('message') }}</textarea>
                                <x-input-error :messages="$errors->get('message')" class="mt-1" />
                            </div>

                            <!-- Captcha Verification Box -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                                <label for="captcha"
                                    class="form-label text-xs font-semibold text-slate-700 flex items-center justify-between">
                                    <span>Verifikasi Keamanan (Captcha) <span class="text-rose-500">*</span></span>
                                    <span class="text-[11px] text-slate-400 font-normal">Cegah bot otomatis</span>
                                </label>

                                <div class="flex flex-wrap items-center gap-3">
                                    <div
                                        class="px-4 py-2.5 bg-teal-50 border border-teal-200 text-teal-900 font-bold font-mono text-sm rounded-xl tracking-wider select-none shrink-0 shadow-2xs">
                                        {{ session('contact_captcha_question') }}
                                    </div>

                                    <input id="captcha"
                                        class="form-input flex-1 min-w-[140px] rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600 font-mono"
                                        type="number" name="captcha" required placeholder="Tulis hasil hitungan" />
                                </div>
                                <x-input-error :messages="$errors->get('captcha')" class="mt-1" />
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit"
                                    class="button-primary w-full py-3.5 text-xs sm:text-sm font-bold shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                                    <span>📨</span> Kirim Pesan Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>