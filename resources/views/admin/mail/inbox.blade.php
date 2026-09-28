<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="eyebrow">Pusat Layanan Email & Komunikasi</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1 flex items-center gap-2">
                    <span>📬</span> Kotak Masuk (Inbox)
                </h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.mail.outbox') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>📤</span> Buka Kotak Keluar (Outbox)
                </a>
                <a href="{{ route('contact.index') }}" target="_blank" class="button-secondary text-xs font-semibold px-3 py-2 inline-flex items-center gap-1 text-slate-500 hover:text-slate-800">
                    <span>↗</span> Form Kontak Publik
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showTestModal: false, testEmail: '{{ Auth::user()->email }}' }">
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
                                ● Siap Kirim
                            </span>
                        </div>
                        <p class="text-xs text-teal-100/80 mt-0.5">
                            Pengirim: <code class="text-teal-200 font-semibold">{{ $smtpInfo['from_address'] }}</code> ({{ $smtpInfo['from_name'] }})
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="showTestModal = true" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-teal-900 hover:bg-teal-50 transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span>🧪</span> Uji Koneksi SMTP
                    </button>
                    <a href="{{ route('admin.mail.outbox') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-teal-700/70 hover:bg-teal-700 text-white border border-teal-500/50 transition inline-flex items-center gap-1">
                        <span>📤</span> Outbox ({{ $outboxCount }})
                    </a>
                </div>
            </div>

            <!-- Mailbox Navigation Tabs & Stats Header -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('admin.mail.inbox', ['status' => 'all']) }}" 
                   class="content-card p-4 transition hover:border-teal-400 hover:shadow-md bg-white block {{ $statusFilter === 'all' && !request('search') ? 'ring-2 ring-teal-500/20 border-teal-300' : '' }}">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pesan Masuk</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</span>
                        <span class="text-xs font-semibold text-slate-500">Pesan</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.inbox', ['status' => 'unread']) }}" 
                   class="content-card p-4 transition hover:border-rose-400 hover:shadow-md bg-rose-50/40 border border-rose-200 block {{ $statusFilter === 'unread' ? 'ring-2 ring-rose-500/30' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Belum Dibaca</span>
                        @if($stats['unread'] > 0)
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        @endif
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-rose-700">{{ number_format($stats['unread']) }}</span>
                        <span class="text-xs font-semibold text-rose-600">Perlu Respons</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.inbox', ['status' => 'read']) }}" 
                   class="content-card p-4 transition hover:border-amber-400 hover:shadow-md bg-amber-50/40 border border-amber-200 block {{ $statusFilter === 'read' ? 'ring-2 ring-amber-500/30' : '' }}">
                    <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Sudah Dibaca</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-amber-800">{{ number_format($stats['read']) }}</span>
                        <span class="text-xs font-semibold text-amber-700">Belum Balas</span>
                    </div>
                </a>

                <a href="{{ route('admin.mail.inbox', ['status' => 'responded']) }}" 
                   class="content-card p-4 transition hover:border-emerald-400 hover:shadow-md bg-emerald-50/40 border border-emerald-200 block {{ $statusFilter === 'responded' ? 'ring-2 ring-emerald-500/30' : '' }}">
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Sudah Dibalas (SMTP)</span>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-emerald-800">{{ number_format($stats['responded']) }}</span>
                        <span class="text-xs font-semibold text-emerald-700">Selesai</span>
                    </div>
                </a>
            </div>

            <!-- Mailbox Table & Filters -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-4">
                
                <!-- Toolbar Header -->
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- Status Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('admin.mail.inbox', array_merge(request()->except(['page']), ['status' => 'all'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'all' ? 'bg-teal-800 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('admin.mail.inbox', array_merge(request()->except(['page']), ['status' => 'unread'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'unread' ? 'bg-rose-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Belum Dibaca ({{ $stats['unread'] }})
                        </a>
                        <a href="{{ route('admin.mail.inbox', array_merge(request()->except(['page']), ['status' => 'read'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'read' ? 'bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Sudah Dibaca ({{ $stats['read'] }})
                        </a>
                        <a href="{{ route('admin.mail.inbox', array_merge(request()->except(['page']), ['status' => 'responded'])) }}" 
                           class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'responded' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Sudah Dibalas ({{ $stats['responded'] }})
                        </a>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.mail.inbox') }}" class="flex items-center gap-2">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="relative w-full sm:w-64">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, subjek..." class="w-full text-xs pl-8 pr-7 py-2 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:border-teal-600 focus:ring-2 focus:ring-teal-100 transition">
                            @if(request('search'))
                                <a href="{{ route('admin.mail.inbox', ['status' => request('status', 'all')]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold" title="Hapus pencarian">✕</a>
                            @endif
                        </div>
                        <button type="submit" class="button-primary text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs">
                            Cari
                        </button>
                    </form>
                </div>

                <!-- Messages Table (Desktop) -->
                @if($messages->isEmpty())
                    <div class="text-center py-12 px-4">
                        <div class="text-3xl mb-2">📭</div>
                        <h3 class="text-sm font-bold text-slate-800">Tidak ada pesan yang sesuai</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Kotak masuk kosong atau tidak ada pesan dengan kriteria filter saat ini.</p>
                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.mail.inbox') }}" class="inline-block mt-3 text-xs font-bold text-teal-700 hover:underline">
                                Tampilkan Semua Pesan
                            </a>
                        @endif
                    </div>
                @else
                    <div class="hidden md:block overflow-x-auto px-5">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px] bg-slate-50/70">
                                    <th class="py-3 px-4">Pengirim</th>
                                    <th class="py-3 px-4">Subjek Pesan & Cuplikan</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Waktu Kirim</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($messages as $msg)
                                    <tr class="hover:bg-teal-50/20 transition {{ $msg->status === 'unread' ? 'bg-rose-50/20 font-semibold' : '' }}">
                                        <!-- Pengirim -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">{{ $msg->name }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono">{{ $msg->email }}</div>
                                            @if($msg->phone)
                                                <div class="text-[10px] text-slate-400 font-mono">WA: {{ $msg->phone }}</div>
                                            @endif
                                        </td>

                                        <!-- Subjek & Pesan -->
                                        <td class="py-3.5 px-4">
                                            <a href="{{ route('admin.mail.inbox.show', $msg->id) }}" class="font-bold text-slate-900 hover:text-teal-800 hover:underline text-xs block leading-snug">
                                                {{ $msg->subject }}
                                            </a>
                                            <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5 font-normal max-w-md">
                                                {{ $msg->message }}
                                            </p>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border inline-flex items-center gap-1 {{ $msg->status_badge_class }}">
                                                @if($msg->status === 'unread')
                                                    <span>●</span> Belum Dibaca
                                                @elseif($msg->status === 'read')
                                                    <span>✓</span> Sudah Dibaca
                                                @elseif($msg->status === 'responded')
                                                    <span>💬</span> Dibalas
                                                @endif
                                            </span>
                                        </td>

                                        <!-- Waktu -->
                                        <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] text-slate-500">
                                            <div>{{ $msg->created_at->format('d/m/Y') }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $msg->created_at->format('H:i') }} WIB ({{ $msg->created_at->diffForHumans() }})</div>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                <a href="{{ route('admin.mail.inbox.show', $msg->id) }}" class="button-primary text-xs font-bold py-1.5 px-2.5 rounded-lg shadow-2xs" title="Buka & Balas Pesan">
                                                    <span>Balas</span>
                                                </a>
                                                
                                                <form action="{{ route('admin.mail.inbox.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pesan dari {{ $msg->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs font-semibold rounded-lg" title="Hapus Pesan">
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
                        @foreach($messages as $msg)
                            <div class="py-4 space-y-2.5 {{ $msg->status === 'unread' ? 'bg-rose-50/10' : '' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-xs">{{ $msg->name }}</h3>
                                        <p class="text-[11px] font-mono text-slate-500">{{ $msg->email }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $msg->status_badge_class }}">
                                        {{ $msg->status_label }}
                                    </span>
                                </div>

                                <a href="{{ route('admin.mail.inbox.show', $msg->id) }}" class="block font-semibold text-slate-800 text-xs hover:text-teal-800">
                                    {{ $msg->subject }}
                                </a>
                                <p class="text-[11px] text-slate-500 line-clamp-2">{{ $msg->message }}</p>

                                <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.mail.inbox.show', $msg->id) }}" class="button-primary text-xs font-bold py-1 px-2.5 rounded-lg">
                                            Buka
                                        </a>
                                        <form action="{{ route('admin.mail.inbox.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pesan ini?')">
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
                        {{ $messages->links() }}
                    </div>
                @endif
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
