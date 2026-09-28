<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="eyebrow">Pusat Layanan Email & Komunikasi</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1 flex items-center gap-2">
                    <span>📤</span> Kotak Keluar (Outbox)
                </h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="showBroadcastModal = true" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 transition shadow-xs inline-flex items-center gap-2 cursor-pointer">
                    <span>📢</span> Mailing List KTAKuMu
                </button>
                <button type="button" @click="showComposeModal = true" class="button-primary text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-2 cursor-pointer">
                    <span>✍️</span> Tulis Email Baru
                </button>
                <a href="{{ route('admin.mail.inbox') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>📬</span> Buka Kotak Masuk
                    @if($unreadInboxCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-600 text-white">
                            {{ $unreadInboxCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        showComposeModal: false, 
        showBroadcastModal: false,
        showTestModal: false, 
        testEmail: '{{ Auth::user()->email }}',
        recipientEmail: '',
        recipientName: '',
        subject: '',
        body: '',
        broadcastWilayah: 'all',
        broadcastSubject: '[KucingMu] Pemberitahuan Penyesuaian Nomor & Versi KTAKuMu Terbaru',
        broadcastNote: '',
        selectUser(u) {
            this.recipientEmail = u.email;
            this.recipientName = u.name;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                    <span class="text-xl">❌</span>
                    <p class="text-xs font-semibold text-rose-900 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Active SMTP Server Status Bar -->
            <div class="bg-gradient-to-r from-teal-900 via-teal-800 to-slate-900 text-white rounded-2xl p-4 sm:p-5 shadow-sm border border-teal-700/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-700/60 border border-teal-500/40 flex items-center justify-center text-xl shrink-0">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Server SMTP Aktif:</span>
                            <span class="font-mono text-xs bg-teal-800/80 px-2 py-0.5 rounded border border-teal-600 font-semibold text-teal-100">
                                {{ $smtpInfo['host'] }}:{{ $smtpInfo['port'] }} ({{ strtoupper($smtpInfo['scheme']) }})
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                ● Online
                            </span>
                        </div>
                        <p class="text-xs text-teal-100/80 mt-0.5">
                            Pengirim Resmi: <code class="text-teal-200 font-semibold">{{ $smtpInfo['from_address'] }}</code> ({{ $smtpInfo['from_name'] }})
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <button type="button" @click="showBroadcastModal = true" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-400 hover:bg-amber-300 text-slate-950 transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span>📢</span> Broadcast KTAKuMu
                    </button>
                    <button type="button" @click="showTestModal = true" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-teal-900 hover:bg-teal-50 transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span>🧪</span> Uji SMTP
                    </button>
                    <button type="button" @click="showComposeModal = true" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span>✍️</span> Tulis Email
                    </button>
                </div>
            </div>

            <!-- Outbox Stat Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <a href="{{ route('admin.mail.outbox', ['status' => 'all', 'type' => 'all']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-white block {{ ($statusFilter === 'all' && $typeFilter === 'all') && !request('search') ? 'ring-2 ring-teal-500/20 border-teal-300' : '' }}">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Email Keluar</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</span>
                        <span class="text-xs font-semibold text-slate-500">Log</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.outbox', ['status' => 'sent']) }}" 
                   class="content-card p-4 transition hover:border-emerald-400 hover:shadow-md bg-emerald-50/40 border border-emerald-200 block {{ $statusFilter === 'sent' ? 'ring-2 ring-emerald-500/30' : '' }}">
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Berhasil Terkirim</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-emerald-800">{{ number_format($stats['sent']) }}</span>
                        <span class="text-xs font-semibold text-emerald-700">Terkirim</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.outbox', ['status' => 'failed']) }}" 
                   class="content-card p-4 transition hover:border-rose-400 hover:shadow-md bg-rose-50/40 border border-rose-200 block {{ $statusFilter === 'failed' ? 'ring-2 ring-rose-500/30' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Gagal Terkirim</span>
                        @if($stats['failed'] > 0)
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        @endif
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-rose-700">{{ number_format($stats['failed']) }}</span>
                        <span class="text-xs font-semibold text-rose-600">Perlu Dicek</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.outbox', ['type' => 'ktam_broadcast']) }}" 
                   class="content-card p-4 transition hover:border-amber-400 hover:shadow-md bg-amber-50/50 border border-amber-200 block {{ $typeFilter === 'ktam_broadcast' ? 'ring-2 ring-amber-500/30' : '' }}">
                    <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider block">Mailing List KTA</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-amber-900">{{ number_format($stats['broadcasts'] ?? 0) }}</span>
                        <span class="text-xs font-semibold text-amber-700">Broadcast</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.outbox', ['type' => 'contact_reply']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-teal-50/40 border border-teal-200 block {{ $typeFilter === 'contact_reply' ? 'ring-2 ring-teal-500/30' : '' }}">
                    <span class="text-[11px] font-bold text-teal-800 uppercase tracking-wider block">Balasan Kontak</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-teal-800">{{ number_format($stats['replies']) }}</span>
                        <span class="text-xs font-semibold text-teal-700">Respons</span>
                    </div>
                </a>
            </div>

            <!-- Outbox Table & Filters -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-4">
                
                <!-- Toolbar Header -->
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- Type and Status Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('admin.mail.outbox', array_merge(request()->except(['page']), ['status' => 'all', 'type' => 'all'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'all' && $typeFilter === 'all' ? 'bg-teal-800 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('admin.mail.outbox', array_merge(request()->except(['page']), ['status' => 'sent'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'sent' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            ✓ Terkirim ({{ $stats['sent'] }})
                        </a>
                        <a href="{{ route('admin.mail.outbox', array_merge(request()->except(['page']), ['status' => 'failed'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'failed' ? 'bg-rose-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            ❌ Gagal ({{ $stats['failed'] }})
                        </a>
                        <a href="{{ route('admin.mail.outbox', array_merge(request()->except(['page']), ['type' => 'ktam_broadcast'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $typeFilter === 'ktam_broadcast' ? 'bg-amber-600 text-white shadow-xs font-bold' : 'bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100' }}">
                            📢 Mailing List KTA ({{ $stats['broadcasts'] ?? 0 }})
                        </a>
                        <a href="{{ route('admin.mail.outbox', array_merge(request()->except(['page']), ['type' => 'direct_compose'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $typeFilter === 'direct_compose' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            ✍️ Tulis Langsung ({{ $stats['direct'] }})
                        </a>
                        <a href="{{ route('admin.mail.outbox', array_merge(request()->except(['page']), ['type' => 'contact_reply'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $typeFilter === 'contact_reply' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            💬 Balasan Kontak ({{ $stats['replies'] }})
                        </a>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.mail.outbox') }}" class="flex items-center gap-2">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        <div class="relative w-full sm:w-64">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari penerima, subjek..." class="w-full text-xs pl-8 pr-7 py-2 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 transition">
                            @if(request('search'))
                                <a href="{{ route('admin.mail.outbox', ['status' => request('status', 'all'), 'type' => request('type', 'all')]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold" title="Hapus pencarian">✕</a>
                            @endif
                        </div>
                        <button type="submit" class="button-primary text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs">
                            Cari
                        </button>
                    </form>
                </div>

                <!-- Messages Table (Desktop) -->
                @if($outboxes->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="text-3xl mb-2">📤</div>
                        <h3 class="text-sm font-bold text-slate-800">Tidak ada log email keluar yang sesuai</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Kotak keluar kosong atau belum ada email terkirim dengan filter saat ini.</p>
                        <div class="mt-3 flex items-center justify-center gap-3">
                            <button type="button" @click="showBroadcastModal = true" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-xs">
                                📢 Kirim Broadcast KTAKuMu
                            </button>
                            <button type="button" @click="showComposeModal = true" class="button-primary text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs">
                                ✍️ Tulis Email Sekarang
                            </button>
                            @if(request('search') || request('status') || request('type'))
                                <a href="{{ route('admin.mail.outbox') }}" class="text-xs font-bold text-teal-700 hover:underline">
                                    Reset Filter
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="hidden md:block overflow-x-auto px-5">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px] bg-slate-50/70">
                                    <th class="py-3 px-4">Penerima (To)</th>
                                    <th class="py-3 px-4">Subjek & Tipe Email</th>
                                    <th class="py-3 px-4">Status Pengiriman</th>
                                    <th class="py-3 px-4">Pengirim (Admin)</th>
                                    <th class="py-3 px-4">Waktu Kirim</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($outboxes as $out)
                                    <tr class="hover:bg-teal-50/20 transition {{ $out->status === 'failed' ? 'bg-rose-50/20' : '' }}">
                                        <!-- Penerima -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                             <div class="font-bold text-slate-900">{{ $out->recipient_name ?: '-' }}</div>
                                             <div class="text-[11px] text-slate-500 font-mono">{{ $out->recipient_email }}</div>
                                        </td>

                                        <!-- Subjek & Tipe -->
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold border uppercase {{ $out->type_badge_class }}">
                                                    {{ $out->type_label }}
                                                </span>
                                            </div>
                                            <a href="{{ route('admin.mail.outbox.show', $out->id) }}" class="font-bold text-slate-900 hover:text-teal-800 hover:underline text-xs block mt-1 leading-snug">
                                                {{ $out->subject }}
                                            </a>
                                            <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5 font-normal max-w-md">
                                                {{ strip_tags($out->body) }}
                                            </p>
                                        </td>

                                        <!-- Status Pengiriman -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border inline-flex items-center gap-1 {{ $out->status_badge_class }}">
                                                    @if($out->status === 'sent')
                                                        <span>✓</span> Terkirim (SMTP)
                                                    @elseif($out->status === 'failed')
                                                        <span>❌</span> Gagal Kirim
                                                    @else
                                                        <span>⏳</span> {{ $out->status_label }}
                                                    @endif
                                                </span>
                                            </div>
                                            @if($out->error_message)
                                                <div class="text-[10px] text-rose-600 max-w-xs truncate mt-0.5" title="{{ $out->error_message }}">
                                                    Err: {{ $out->error_message }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Pengirim -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-semibold text-slate-800 text-xs">{{ $out->sender->name ?? 'Sistem Otomatis' }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">Driver: {{ $out->mailer }}</div>
                                        </td>

                                        <!-- Waktu -->
                                        <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] text-slate-500">
                                            <div>{{ $out->created_at->format('d/m/Y') }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $out->created_at->format('H:i') }} WIB ({{ $out->created_at->diffForHumans() }})</div>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                <a href="{{ route('admin.mail.outbox.show', $out->id) }}" class="button-secondary text-xs font-semibold py-1.5 px-2.5 rounded-lg" title="Lihat Detail Pesan">
                                                    <span>Lihat</span>
                                                </a>

                                                @if($out->status === 'failed')
                                                    <form action="{{ route('admin.mail.outbox.resend', $out->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="button-primary text-xs font-bold py-1.5 px-2.5 rounded-lg shadow-2xs" title="Coba Kirim Ulang via SMTP">
                                                            <span>🔄</span> Kirim Ulang
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.mail.outbox.destroy', $out->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log email keluar ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs font-semibold rounded-lg" title="Hapus Log">
                                                        <span>🗑</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="block md:hidden divide-y divide-slate-100 px-4">
                        @foreach($outboxes as $out)
                            <div class="py-4 space-y-2.5 {{ $out->status === 'failed' ? 'bg-rose-50/10' : '' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-xs">To: {{ $out->recipient_name ?: $out->recipient_email }}</h3>
                                        <p class="text-[11px] font-mono text-slate-500">{{ $out->recipient_email }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $out->status_badge_class }}">
                                        {{ $out->status === 'sent' ? '✓ Terkirim' : '❌ Gagal' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold border {{ $out->type_badge_class }}">
                                        {{ $out->type_label }}
                                    </span>
                                </div>

                                <a href="{{ route('admin.mail.outbox.show', $out->id) }}" class="block font-semibold text-slate-800 text-xs hover:text-teal-800">
                                    {{ $out->subject }}
                                </a>
                                <p class="text-[11px] text-slate-500 line-clamp-2">{{ strip_tags($out->body) }}</p>

                                <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $out->created_at->format('d/m/Y H:i') }}</span>
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.mail.outbox.show', $out->id) }}" class="button-secondary text-xs font-semibold py-1 px-2.5 rounded-lg">
                                            Lihat
                                        </a>
                                        @if($out->status === 'failed')
                                            <form action="{{ route('admin.mail.outbox.resend', $out->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="button-primary text-xs font-bold py-1 px-2 rounded-lg">
                                                    🔄
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.mail.outbox.destroy', $out->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-danger py-1 px-2 text-xs font-semibold rounded-lg">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $outboxes->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Modal Mailing List / Broadcast KTAKuMu -->
        <div x-show="showBroadcastModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 my-8" @click.away="showBroadcastModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📢</span>
                        <div>
                            <h3 class="font-outfit text-base font-bold text-slate-900">Mailing List / Broadcast Pemilik KTAKuMu</h3>
                            <p class="text-[11px] text-slate-500">Kirim email pemberitahuan ke seluruh member pemilik KTAKuMu terbit untuk memeriksa versi kartu terbarunya.</p>
                        </div>
                    </div>
                    <button type="button" @click="showBroadcastModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-sm">✕</button>
                </div>

                <!-- Summary Badge Info -->
                <div class="p-3.5 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200 flex items-center justify-between gap-3 text-xs text-amber-900">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">🐱</span>
                        <div>
                            <div class="font-bold text-slate-900">Target Penerima: {{ $ktamMembersCount ?? 0 }} Member Terdaftar</div>
                            <div class="text-[11px] text-amber-800 font-medium">Total {{ $ktamIssuedCatsCount ?? 0 }} kucing ber-KTAKuMu resmi aktif di sistem</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-200/80 text-amber-950 shrink-0">
                        SMTP Otomatis
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.mail.broadcast-ktam') }}" class="space-y-4" onsubmit="return confirm('Kirim email broadcast ke seluruh pemilik KTAKuMu terpilih via SMTP sekarang?')">
                    @csrf

                    <!-- Filter Wilayah -->
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Filter Wilayah Penerima:</label>
                        <select name="wilayah_code" x-model="broadcastWilayah" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-800 font-medium">
                            <option value="all">🌐 Semua Wilayah (Seluruh Member Pemilik KTAKuMu)</option>
                            @if(isset($wilayahList))
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Pilih wilayah tertentu (misal kode 11) atau biarkan Semua Wilayah.</span>
                    </div>

                    <!-- Subjek Email -->
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Subjek Email:</label>
                        <input type="text" name="subject" x-model="broadcastSubject" required class="form-input text-xs" placeholder="Subjek email pemberitahuan...">
                    </div>

                    <!-- Catatan Tambahan Kustom -->
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Catatan Tambahan Admin (Opsional):</label>
                        <textarea name="custom_note" x-model="broadcastNote" rows="3" class="form-input text-xs leading-relaxed" placeholder="Contoh: Silakan login ke portal dan buka menu Kucing Saya untuk melihat serta mengunduh versi KTAKuMu terbaru Anda..."></textarea>
                    </div>

                    <!-- Preview Isi Template Email -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
                        <div class="font-bold text-slate-700 flex items-center gap-1">
                            <span>👁️</span> Format Email Otomatis:
                        </div>
                        <ul class="text-[11px] text-slate-500 list-disc list-inside space-y-0.5">
                            <li>Menyapa nama masing-masing pemilik secara personal.</li>
                            <li>Menyertakan rincian nama kucing dan nomor KTAKuMu terbaru milik member tersebut.</li>
                            <li>Menyertakan tombol langsung (CTA) menuju portal <strong>KTAKuMu Saya</strong>.</li>
                            <li>Semua email keluar akan dicatat otomatis di Kotak Keluar (Outbox).</li>
                        </ul>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showBroadcastModal = false" class="button-secondary text-xs px-4 py-2.5 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="button-primary text-xs font-bold px-5 py-2.5 shadow-sm rounded-xl bg-amber-600 hover:bg-amber-700 text-white inline-flex items-center gap-1.5">
                            <span>🚀</span> Kirim Broadcast via SMTP
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tulis Email Baru (Compose Modal) -->
        <div x-show="showComposeModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 my-8" @click.away="showComposeModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">✍️</span>
                        <div>
                            <h3 class="font-outfit text-base font-bold text-slate-900">Tulis Email Baru (Outbox)</h3>
                            <p class="text-[11px] text-slate-500">Kirim email langsung melalui server SMTP resmi KucingMu.</p>
                        </div>
                    </div>
                    <button type="button" @click="showComposeModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-sm">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.mail.compose') }}" class="space-y-4">
                    @csrf

                    <!-- Quick User Selector / Autocomplete -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="form-label text-xs font-bold text-slate-700 m-0">Pilih Cepat Pengguna Terdaftar (Opsional):</label>
                            <span class="text-[10px] text-slate-400">Klik untuk isi otomatis</span>
                        </div>
                        <select @change="if($event.target.value) { 
                            const val = JSON.parse($event.target.value); 
                            selectUser(val); 
                        }" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-700 font-medium">
                            <option value="">-- Pilih dari daftar pengguna terdaftar (Member/Dokter/Relawan) --</option>
                            @foreach($usersList as $u)
                                <option value="{{ json_encode(['name' => $u->name, 'email' => $u->email]) }}">
                                    [{{ strtoupper($u->role) }}] {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Email Penerima (To) <span class="text-rose-500">*</span></label>
                            <input type="email" name="recipient_email" x-model="recipientEmail" required class="form-input text-xs font-mono" placeholder="penerima@email.com">
                        </div>
                        <div>
                            <label class="form-label text-xs font-bold text-slate-700">Nama Penerima</label>
                            <input type="text" name="recipient_name" x-model="recipientName" class="form-input text-xs" placeholder="Nama Lengkap Penerima">
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Subjek Email <span class="text-rose-500">*</span></label>
                        <input type="text" name="subject" x-model="subject" required class="form-input text-xs" placeholder="Contoh: Informasi Pemeriksaan Kesehatan & KTAKuMu">
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Isi Pesan Email <span class="text-rose-500">*</span></label>
                        <textarea name="body" x-model="body" rows="7" required class="form-input text-xs leading-relaxed" placeholder="Tuliskan isi pesan email resmi Anda di sini..."></textarea>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Pengirim: <strong>{{ $smtpInfo['from_address'] }}</strong> ({{ $smtpInfo['from_name'] }})</span>
                        <span class="font-mono text-teal-700 font-semibold">{{ $smtpInfo['host'] }}:{{ $smtpInfo['port'] }}</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showComposeModal = false" class="button-secondary text-xs px-4 py-2.5 rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="button-primary text-xs font-bold px-5 py-2.5 shadow-sm rounded-xl inline-flex items-center gap-1.5">
                            <span>🚀</span> Kirim Email via SMTP
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Uji Coba SMTP -->
        <div x-show="showTestModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl border border-slate-200" @click.away="showTestModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🧪</span>
                        <h3 class="font-outfit text-base font-bold text-slate-900">Uji Coba Koneksi SMTP</h3>
                    </div>
                    <button type="button" @click="showTestModal = false" class="text-slate-400 hover:text-slate-700 font-bold text-sm">✕</button>
                </div>

                <p class="text-xs text-slate-500">
                    Sistem akan mengirimkan email diagnostik melalui server SMTP yang sedang aktif (<code>{{ $smtpInfo['host'] }}:{{ $smtpInfo['port'] }}</code>).
                </p>

                <form method="POST" action="{{ route('admin.mail.test-smtp') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Kirim Email Uji Coba Ke:</label>
                        <input type="email" name="test_email" x-model="testEmail" required class="form-input text-xs font-mono" placeholder="nama@email.com">
                        <span class="text-[10px] text-slate-400 mt-1 block">Pastikan alamat email tujuan dapat diakses untuk memeriksa inbox.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="showTestModal = false" class="button-secondary text-xs px-3.5 py-2">
                            Batal
                        </button>
                        <button type="submit" class="button-primary text-xs font-bold px-4 py-2 shadow-sm">
                            <span>🚀</span> Kirim Email Tes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
