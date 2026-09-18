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

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('contact_success'))
                <div class="p-5 bg-emerald-50 border border-emerald-300 rounded-2xl flex items-start gap-3 shadow-xs">
                    <span class="text-2xl">✅</span>
                    <div>
                        <h3 class="font-outfit font-bold text-emerald-950 text-sm">Pesan Berhasil Terkirim!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5 leading-relaxed">{{ session('contact_success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Contact Details -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="content-card space-y-5">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="font-outfit text-base font-bold text-slate-900">Informasi Kontak Resmi</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Layanan administrasi, konsultasi, dan kemitraan resmi persyarikatan.</p>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-teal-100/70 border border-teal-200 text-teal-800 flex items-center justify-center text-lg shrink-0">
                                ✉️
                            </div>
                            <div>
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Alamat Email</div>
                                <div class="text-xs font-semibold text-slate-800 mt-0.5 leading-relaxed space-y-0.5">
                                    <div><a href="mailto:bidkes.immdiy@gmail.com" class="hover:text-teal-700 transition">bidkes.immdiy@gmail.com</a></div>
                                    <div><a href="mailto:kucingmuhammadiyah@gmail.com" class="hover:text-teal-700 transition">kucingmuhammadiyah@gmail.com</a></div>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat Kantor -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-teal-100/70 border border-teal-200 text-teal-800 flex items-center justify-center text-lg shrink-0">
                                📍
                            </div>
                            <div>
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Alamat Kantor Sekretariat</div>
                                <p class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">
                                    {{ $officeAddress }}
                                </p>
                            </div>
                        </div>

                        <!-- Jam Operasional -->
                        <div class="flex items-start gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-teal-100/70 border border-teal-200 text-teal-800 flex items-center justify-center text-lg shrink-0">
                                🕒
                            </div>
                            <div>
                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jam Kerja / Layanan</div>
                                <p class="text-xs font-medium text-slate-700 mt-0.5">
                                    Senin &ndash; Jumat, Pukul 08.00 &ndash; 16.00 WIB
                                </p>
                            </div>
                        </div>

                        <!-- Organization Branding -->
                        <div class="bg-teal-900 p-5 rounded-2xl text-white shadow-xs space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">🌿</span>
                                <h3 class="font-outfit font-bold text-sm text-teal-100">Majelis Lingkungan Hidup PP Muhammadiyah</h3>
                            </div>
                            <p class="text-xs text-teal-200/90 leading-relaxed">
                                Program KucingMu mengintegrasikan pencatatan identitas resmi digital, edukasi kesehatan satwa komunitas, dan surveilans populasi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right: Contact Form -->
                <div class="lg:col-span-7">
                    <div class="content-card">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Formulir Kirim Pesan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Lengkapi formulir di bawah ini beserta verifikasi keamanan untuk mengirimkan pertanyaan Anda.</p>
                        </div>

                        <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label for="name" class="form-label text-xs font-semibold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()?->name) }}" required placeholder="Nama Anda" class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="form-label text-xs font-semibold text-slate-700">Alamat Email <span class="text-rose-500">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()?->email) }}" required placeholder="nama@email.com" class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- No. Telepon / WhatsApp -->
                                <div>
                                    <label for="phone" class="form-label text-xs font-semibold text-slate-700">No. WhatsApp / Telepon <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()?->phone) }}" placeholder="08xxxxxxxxxx" class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                                </div>

                                <!-- Subjek Pesan -->
                                <div>
                                    <label for="subject" class="form-label text-xs font-semibold text-slate-700">Subjek / Topik <span class="text-rose-500">*</span></label>
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="Contoh: Pertanyaan Pembuatan KTAM" class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">
                                    <x-input-error :messages="$errors->get('subject')" class="mt-1" />
                                </div>
                            </div>

                            <!-- Pesan / Pertanyaan -->
                            <div>
                                <label for="message" class="form-label text-xs font-semibold text-slate-700">Isi Pesan <span class="text-rose-500">*</span></label>
                                <textarea id="message" name="message" rows="5" required placeholder="Tuliskan pesan, pertanyaan, atau masukan Anda di sini..." class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600">{{ old('message') }}</textarea>
                                <x-input-error :messages="$errors->get('message')" class="mt-1" />
                            </div>

                            <!-- Captcha Verification Box -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                                <label for="captcha" class="form-label text-xs font-semibold text-slate-700 flex items-center justify-between">
                                    <span>Verifikasi Keamanan (Captcha) <span class="text-rose-500">*</span></span>
                                    <span class="text-[11px] text-slate-400 font-normal">Cegah spam otomatis</span>
                                </label>
                                
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="px-4 py-2 bg-teal-50 border border-teal-200 text-teal-900 font-bold font-mono text-sm rounded-xl tracking-wider select-none shrink-0 shadow-2xs">
                                        {{ session('contact_captcha_question') }}
                                    </div>
                                    
                                    <input id="captcha" class="form-input flex-1 min-w-[140px] rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600 font-mono" type="number" name="captcha" required placeholder="Tulis hasil hitungan" />
                                </div>
                                <x-input-error :messages="$errors->get('captcha')" class="mt-1" />
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="button-primary w-full sm:w-auto px-8 py-3 text-xs font-bold shadow-xs flex items-center justify-center gap-2">
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
