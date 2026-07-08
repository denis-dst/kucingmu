<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Header Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Pengaturan Sistem</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Settings Apps KucingMu
                    </h1>
                    <p class="card-copy max-w-xl">
                        Kelola konfigurasi global aplikasi, status registrasi kucing baru, email pengirim, dan setelan operasional sistem lainnya secara real-time.
                    </p>
                </div>
                <div class="hidden md:block text-5xl">
                    ⚙️
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center gap-2">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Settings Form Card -->
            <div class="content-card bg-white border border-slate-200 rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="divide-y divide-slate-100 space-y-6">
                        @foreach($settings as $setting)
                            <div class="pt-6 {{ $loop->first ? 'pt-0' : '' }}">
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div class="md:col-span-1">
                                        <label for="setting_{{ $setting->key }}" class="block font-outfit text-sm font-bold text-slate-800">
                                            {{ $setting->label }}
                                        </label>
                                        <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider block mt-1">
                                            Key: {{ $setting->key }}
                                        </span>
                                    </div>
                                    <div class="md:col-span-2">
                                        @if($setting->type === 'boolean')
                                            <div class="flex items-center mt-1">
                                                <select id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" class="form-input w-full max-w-xs rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2">
                                                    <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Aktif (Ya)</option>
                                                    <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Nonaktif (Tidak)</option>
                                                </select>
                                            </div>
                                        @else
                                            <input type="text" id="setting_{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ old('settings.' . $setting->key, $setting->value) }}" class="form-input mt-1 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" required>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('dashboard') }}" class="button-secondary px-5 py-2.5">
                            Batal
                        </a>
                        <button type="submit" class="button-primary px-6 py-2.5">
                            Simpan Semua Pengaturan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
