<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contacts.index') }}"
                    class="button-secondary text-xs font-semibold px-2.5 py-1.5" title="Kembali">
                    ← Kembali
                </a>
                <div>
                    <span class="eyebrow">Detail Pesan Masuk</span>
                    <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">
                        {{ $contact->subject }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                <form action="{{ route('admin.contacts.status', $contact->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="{{ $contact->status === 'unread' ? 'read' : 'unread' }}">
                    <button type="submit" class="button-secondary text-xs font-semibold px-3 py-1.5">
                        {{ $contact->status === 'unread' ? 'Tandai Sudah Dibaca' : 'Tandai Belum Dibaca' }}
                    </button>
                </form>

                <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button-danger text-xs font-semibold px-3 py-1.5">
                        🗑 Hapus
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3 shadow-2xs">
                    <span class="text-xl">✅</span>
                    <p class="text-xs font-medium text-emerald-900 mt-0.5">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Main Message Content -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Original Message Box -->
                    <div class="content-card space-y-4">
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3.5">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Subjek
                                    Pesan</span>
                                <h2 class="font-outfit text-lg font-bold text-slate-900 mt-0.5">{{ $contact->subject }}
                                </h2>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $contact->status_badge_class }} shrink-0">
                                {{ $contact->status_label }}
                            </span>
                        </div>

                        <!-- Message Body -->
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Isi
                                Pesan Pengirim</span>
                            <div
                                class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 leading-relaxed whitespace-pre-line font-sans">
                                {{ $contact->message }}
                            </div>
                        </div>

                        <div
                            class="flex flex-wrap items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                            <span>Diterima pada: {{ $contact->created_at->format('d F Y, H:i') }} WIB</span>
                            <span>({{ $contact->created_at->diffForHumans() }})</span>
                        </div>
                    </div>

                    <!-- Past Response History (if already responded) -->
                    @if($contact->admin_response)
                        <div class="content-card border-l-4 border-l-teal-700 bg-teal-50/20 space-y-3">
                            <div class="flex items-center justify-between border-b border-teal-100 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">💬</span>
                                    <h3 class="font-outfit font-bold text-xs text-teal-950 uppercase tracking-wider">
                                        Tanggapan / Respon Resmi yang Telah Dikirim</h3>
                                </div>
                                <span class="text-[11px] font-bold text-teal-800 bg-teal-100 px-2 py-0.5 rounded">
                                    Sudah Dibalas
                                </span>
                            </div>

                            <div
                                class="p-4 rounded-xl bg-white border border-teal-200 text-xs text-teal-950 leading-relaxed whitespace-pre-line">
                                {{ $contact->admin_response }}
                            </div>

                            <div class="text-[11px] text-slate-500 flex flex-wrap items-center justify-between pt-1">
                                <span>Dibalas oleh: <strong
                                        class="text-slate-800">{{ $contact->responder ? $contact->responder->name : 'Administrator' }}</strong></span>
                                <span>Waktu:
                                    {{ $contact->responded_at ? $contact->responded_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Reply Form -->
                    <div class="content-card space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-outfit text-base font-bold text-slate-900">
                                {{ $contact->admin_response ? 'Perbarui atau Kirim Balasan Tambahan' : 'Tulis Tanggapan Resmi ke Pengirim' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Tanggapan ini akan tersimpan di sistem dan otomatis dikirimkan ke email pengirim:
                                <strong class="text-slate-800">{{ $contact->email }}</strong>.
                            </p>
                        </div>

                        <form action="{{ route('admin.contacts.respond', $contact->id) }}" method="POST"
                            class="space-y-4">
                            @csrf

                            <div>
                                <label for="response" class="form-label text-xs font-semibold text-slate-700">Isi
                                    Balasan Admin <span class="text-rose-500">*</span></label>
                                <textarea id="response" name="response" rows="6" required
                                    placeholder="Tuliskan balasan resmi dari admin KucingMu..."
                                    class="form-input mt-1 block w-full rounded-xl border-slate-300 text-xs py-2.5 focus:border-teal-600 focus:ring-teal-600 font-sans">{{ old('response', $contact->admin_response) }}</textarea>
                                <x-input-error :messages="$errors->get('response')" class="mt-1" />
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <span>📧</span> Email notifikasi otomatis aktif
                                </span>

                                <button type="submit"
                                    onclick="return confirm('Kirim balasan resmi ke email {{ $contact->email }}?')"
                                    class="button-primary px-6 py-2.5 text-xs font-bold shadow-xs flex items-center gap-2">
                                    <span>📨</span> Kirim Balasan Email
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- Sidebar Sender Details -->
                <div class="lg:col-span-4 space-y-5">

                    <!-- Sender Profile Card -->
                    <div class="content-card space-y-3.5">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-outfit text-sm font-bold text-slate-900">Profil Pengirim Pesan</h3>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-slate-400 text-[11px] block">Nama Lengkap:</span>
                                <span class="font-bold text-slate-900 text-sm">{{ $contact->name }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 text-[11px] block">Alamat Email:</span>
                                <a href="mailto:{{ $contact->email }}"
                                    class="font-mono font-semibold text-teal-800 hover:underline">
                                    {{ $contact->email }}
                                </a>
                            </div>

                            <div>
                                <span class="text-slate-400 text-[11px] block">No. Telepon / WhatsApp:</span>
                                @if($contact->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}"
                                        target="_blank"
                                        class="font-mono font-semibold text-emerald-800 hover:underline inline-flex items-center gap-1">
                                        <span>💬</span> {{ $contact->phone }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Tidak dicantumkan</span>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <span class="text-slate-400 text-[11px] block">Status Pengguna:</span>
                                @if($contact->user)
                                    <div
                                        class="mt-1 p-2 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 text-xs space-y-1">
                                        <div class="font-bold">✓ Akun Member Terdaftar</div>
                                        <div class="text-[11px] text-teal-800">Role: {{ ucfirst($contact->user->role) }}
                                        </div>
                                        @if($contact->user->muhammadiyah_id)
                                            <div class="text-[11px] font-mono">NBM: {{ $contact->user->muhammadiyah_id }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        Pengunjung Tamu (Guest)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>