<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.mail.inbox') }}" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" title="Kembali ke Kotak Masuk">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <span class="eyebrow">Kotak Masuk &bull; Detail Pesan</span>
                    <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-0.5 truncate max-w-xl">
                        {{ $contact->subject }}
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Toggle Read/Unread Form -->
                <form action="{{ route('admin.mail.inbox.status', $contact->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="status" value="{{ $contact->status === 'read' ? 'unread' : 'read' }}">
                    <button type="submit" class="button-secondary text-xs px-3 py-1.5 rounded-xl font-semibold">
                        {{ $contact->status === 'read' ? 'Tandai Belum Dibaca' : 'Tandai Sudah Dibaca' }}
                    </button>
                </form>

                <form action="{{ route('admin.mail.inbox.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action-danger text-xs px-3 py-1.5 rounded-xl font-semibold">
                        <span>🗑</span> Hapus
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

            @if(session('warning'))
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                    <span class="text-xl">⚠️</span>
                    <p class="text-xs font-semibold text-amber-900 mt-0.5 leading-relaxed">{{ session('warning') }}</p>
                </div>
            @endif

            <!-- Incoming Message Card -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-start justify-between gap-4 bg-slate-50/50">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-teal-700 text-white font-bold flex items-center justify-center text-lg shrink-0 shadow-2xs">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="font-outfit text-base font-bold text-slate-900">{{ $contact->name }}</h2>
                                @if($contact->user)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                        Member Terdaftar (ID #{{ $contact->user->id }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Pengunjung Publik
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5 flex flex-wrap items-center gap-3">
                                <span>Email: <strong class="text-slate-700">{{ $contact->email }}</strong></span>
                                @if($contact->phone)
                                    <span>&bull;</span>
                                    <span>Telp/WA: <strong class="text-slate-700">{{ $contact->phone }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-start md:items-end gap-1 shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $contact->status_badge_class }}">
                            {{ $contact->status_label }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400">
                            {{ $contact->created_at->format('d M Y, H:i') }} WIB ({{ $contact->created_at->diffForHumans() }})
                        </span>
                    </div>
                </div>

                <!-- Message Body Content -->
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Subjek Pesan:</span>
                        <h3 class="font-outfit text-lg font-bold text-slate-900">{{ $contact->subject }}</h3>
                    </div>

                    <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line font-normal">
                        {{ $contact->message }}
                    </div>
                </div>
            </div>

            <!-- Previous Admin Responses / Outbox Log if already replied -->
            @if($contact->admin_response || $relatedOutbox->isNotEmpty())
                <div class="content-card bg-white rounded-2xl border border-teal-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-teal-100 bg-teal-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-base">💬</span>
                            <h3 class="font-outfit text-sm font-bold text-teal-900">Riwayat Balasan Resmi via SMTP</h3>
                        </div>
                        @if($contact->responded_at)
                            <span class="text-[11px] font-mono text-teal-700">
                                Dibalas: {{ $contact->responded_at->format('d M Y, H:i') }} WIB oleh <strong>{{ $contact->responder->name ?? 'Admin' }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="p-6 space-y-4">
                        @if($contact->admin_response)
                            <div class="bg-teal-50/40 p-4 rounded-xl border border-teal-200 text-xs text-teal-950 whitespace-pre-line leading-relaxed">
                                {{ $contact->admin_response }}
                            </div>
                        @endif

                        @if($relatedOutbox->isNotEmpty())
                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Log Outbox Terkait:</span>
                                @foreach($relatedOutbox as $out)
                                    <div class="flex items-center justify-between text-xs p-3 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $out->status_badge_class }}">
                                                {{ $out->status === 'sent' ? '✓ Terkirim' : '❌ Gagal' }}
                                            </span>
                                            <span class="font-semibold text-slate-800">{{ $out->subject }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] font-mono text-slate-400">{{ $out->created_at->format('d/m/Y H:i') }}</span>
                                            <a href="{{ route('admin.mail.outbox.show', $out->id) }}" class="text-teal-700 hover:underline font-bold text-[11px]">Lihat &rarr;</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Reply Form Section (Send email via SMTP) -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>✍️</span> Kirim Balasan Email ke Pengirim
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Balasan akan dikirim langsung ke <strong>{{ $contact->email }}</strong> menggunakan server SMTP (<code>{{ $smtpInfo['host'] }}:{{ $smtpInfo['port'] }}</code>).
                        </p>
                    </div>
                    <span class="text-[11px] font-mono bg-teal-50 text-teal-800 px-2.5 py-1 rounded-lg border border-teal-200 font-semibold shrink-0 self-start sm:self-auto">
                        From: {{ $smtpInfo['from_address'] }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.mail.inbox.reply', $contact->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Kepada (Email Penerima):</label>
                        <input type="text" disabled value="{{ $contact->name }} <{{ $contact->email }}>" class="w-full text-xs font-mono bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Subjek Email:</label>
                        <input type="text" disabled value="Re: [{{ config('app.name', 'KucingMu') }}] {{ $contact->subject }}" class="w-full text-xs bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label for="reply_content" class="form-label text-xs font-bold text-slate-700 flex items-center justify-between">
                            <span>Isi Pesan Balasan <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-slate-400 font-normal">Mendukung format baris baru</span>
                        </label>
                        <textarea id="reply_content" 
                                  name="response" 
                                  rows="6" 
                                  required 
                                  placeholder="Tuliskan jawaban atau tanggapan resmi Anda di sini..."
                                  class="form-input text-xs leading-relaxed">{{ old('response', $contact->admin_response) }}</textarea>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 flex-wrap gap-3">
                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                            <span>🛡️</span>
                            <span>Balasan akan dicatat secara otomatis di Kotak Keluar (Outbox).</span>
                        </div>
                        <button type="submit" class="button-primary text-xs font-bold px-5 py-2.5 shadow-sm rounded-xl inline-flex items-center gap-2">
                            <span>🚀</span> Kirim Balasan Sekarang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
