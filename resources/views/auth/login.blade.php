<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">Alamat Email</label>
            <input id="email" class="form-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="form-label mb-0">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-teal-700 hover:text-teal-800 focus:outline-none" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" class="form-input pr-10 w-full" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Captcha -->
        <div class="mt-4">
            <label for="captcha" class="form-label mb-2">Verifikasi Keamanan (Captcha)</label>
            <div class="flex items-center gap-3">
                <div class="bg-slate-100 border border-slate-200 text-teal-800 font-mono font-bold px-3 py-2 rounded text-sm select-none tracking-widest">
                    {{ session('login_captcha_question') }}
                </div>
                <input id="captcha" class="form-input flex-1" type="number" name="captcha" required placeholder="Jawab di sini" />
            </div>
            <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
                <span class="ms-2 text-sm text-slate-600 select-none">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <button type="submit" class="w-full button-primary flex justify-center items-center mt-6">
            Masuk ke Akun
        </button>
    </form>
</x-guest-layout>
