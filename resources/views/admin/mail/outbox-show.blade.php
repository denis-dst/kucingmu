<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.mail.outbox') }}" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" title="Kembali ke Kotak Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <span class="eyebrow">Kotak Keluar &bull; Detail Email Terkirim</span>
                    <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 truncate max-w-xl">
                        {{ $outbox->subject }}
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-2 flex-wrap">
                <form action="{{ route('admin.mail.outbox.resend', $outbox->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="button-primary text-xs px-3.5 py-2 rounded-xl font-bold shadow-2xs inline-flex items-center gap-1.5">
                        <span>🔄</span> Kirim Ulang via SMTP
                    </button>
                </form>

                <form action="{{ route('admin.mail.outbox.destroy', $outbox->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log email keluar ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action-danger text-xs px-3 py-2 rounded-xl font-semibold">
                        <span>🗑</span> Hapus Log
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                    <span class="text-xl">✅</span>
                    <p class="text-xs font-semibold text-emerald-900 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                    <span class="text-xl">❌</span>
                    <p class="text-xs font-semibold text-rose-900 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Outbox Detail Card -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-start justify-between gap-4 bg-slate-50/50">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-700 text-white font-bold flex items-center justify-center text-lg shrink-0 shadow-2xs">
                            📤
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="font-outfit text-base font-bold text-slate-900">
                                    To: {{ $outbox->recipient_name ?: $outbox->recipient_email }}
                                </h2>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border uppercase {{ $outbox->type_badge_class }}">
                                    {{ $outbox->type_label }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5 flex flex-wrap items-center gap-3">
                                <span>Email: <strong class="text-slate-700">{{ $outbox->recipient_email }}</strong></span>
                                <span>&bull;</span>
                                <span>Pengirim: <strong>{{ $outbox->sender->name ?? 'Sistem Otomatis' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-start md:items-end gap-1 shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $outbox->status_badge_class }}">
                            {{ $outbox->status_label }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400">
                            {{ $outbox->created_at->format('d M Y, H:i') }} WIB ({{ $outbox->created_at->diffForHumans() }})
                        </span>
                    </div>
                </div>

                <!-- Error Box if Failed -->
                @if($outbox->error_message)
                    <div class="m-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs space-y-1">
                        <div class="font-bold text-rose-900 flex items-center gap-1.5">
                            <span>⚠️</span> Rincian Pesan Error SMTP:
                        </div>
                        <p class="font-mono text-rose-800 text-[11px] leading-relaxed break-all">
                            {{ $outbox->error_message }}
                        </p>
                    </div>
                @endif

                <!-- Email Message Details -->
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Subjek Email:</span>
                        <h3 class="font-outfit text-lg font-bold text-slate-900">{{ $outbox->subject }}</h3>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Isi Pesan Email:</span>
                        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line font-normal">
                            {{ $outbox->body }}
                        </div>
                    </div>

                    <!-- Meta details table -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Mailer Driver</span>
                                <span class="font-mono font-bold text-slate-700 text-xs">{{ $outbox->mailer }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Waktu Terkirim</span>
                                <span class="font-mono font-bold text-slate-700 text-xs">
                                    {{ $outbox->sent_at ? $outbox->sent_at->format('d/m/Y H:i:s') : 'Belum Berhasil' }}
                                </span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tautan Pesan Kontak</span>
                                @if($outbox->contactMessage)
                                    <a href="{{ route('admin.mail.inbox.show', $outbox->contact_message_id) }}" class="font-bold text-teal-700 hover:underline text-xs inline-flex items-center gap-1">
                                        <span>Buka Pesan Masuk #{{ $outbox->contact_message_id }}</span> &rarr;
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
