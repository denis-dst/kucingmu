<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password fields wrapped in AlpineJS showPassword data -->
        <div x-data="{ showPassword: false }">
            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <input id="password" :type="showPassword ? 'text' : 'password'" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <input id="password_confirmation" :type="showPassword ? 'text' : 'password'" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Show Password Checkbox -->
            <div class="mt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" @change="showPassword = $event.target.checked" class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500">
                    <span class="ms-2 text-xs text-slate-600 select-none">Tampilkan Kata Sandi</span>
                </label>
            </div>
        </div>

        <!-- Captcha -->
        <div class="mt-4">
            <x-input-label for="captcha" :value="__('Verifikasi Keamanan (Captcha)')" />
            <div class="flex items-center gap-3 mt-1">
                <div class="bg-slate-100 border border-slate-200 text-teal-800 font-mono font-bold px-3 py-2 rounded text-sm select-none tracking-widest">
                    {{ session('reset_captcha_question') }}
                </div>
                <input id="captcha" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1" type="number" name="captcha" required placeholder="Jawab di sini" />
            </div>
            <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
