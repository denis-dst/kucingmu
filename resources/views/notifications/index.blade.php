<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('dashboard') }}" class="hover:text-teal-700">Dashboard</a>
                <span>&rsaquo;</span>
                <span class="font-bold text-slate-800">Pemberitahuan Sistem</span>
            </nav>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-2xl text-teal-700">
                        🔔
                    </div>
                    <div>
                        <h1 class="font-outfit text-xl font-bold text-slate-900 leading-tight">Pemberitahuan & Notifikasi</h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Semua pembaruan status resep, ucapan belasungkawa, dan informasi anabul Anda.
                        </p>
                    </div>
                </div>

                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="button-secondary text-xs px-3.5 py-2 font-bold text-teal-800 border-teal-300 hover:bg-teal-50 cursor-pointer">
                            ✓ Tandai Semua Telah Dibaca ({{ $unreadCount }})
                        </button>
                    </form>
                @endif
            </div>

            <!-- Notification List -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                @if($notifications->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($notifications as $notif)
                            <div class="p-4 sm:p-5 flex items-start gap-3.5 transition hover:bg-slate-50/80 {{ !$notif->read_at ? 'bg-teal-50/40' : '' }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 border {{ !$notif->read_at ? 'bg-white border-teal-300 shadow-2xs' : 'bg-slate-100 border-slate-200' }}">
                                    @if($notif->icon === 'heart' || in_array($notif->type, ['cat_memorial', 'cat_deceased', 'cat_condolence'])) 🕊️
                                    @elseif($notif->icon === 'pill' || $notif->type === 'prescription_dispensed') 💊
                                    @else 🔔
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h3 class="text-sm font-bold text-slate-900 {{ !$notif->read_at ? 'text-teal-900' : '' }}">
                                            {{ $notif->title }}
                                        </h3>
                                        <span class="text-[11px] text-slate-400 whitespace-nowrap">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                        {{ $notif->message }}
                                    </p>
                                    @if($notif->sender)
                                        <p class="text-[11px] text-slate-400 mt-1 font-medium">
                                            Dari: {{ $notif->sender->name }}
                                        </p>
                                    @endif
                                    <div class="mt-3 flex items-center gap-2">
                                        @if($notif->url)
                                            <a href="{{ route('notifications.read', $notif->id) }}" class="button-primary text-xs py-1.5 px-3 font-semibold inline-flex items-center gap-1.5">
                                                <span>Buka Tautan</span>
                                                <span>&rarr;</span>
                                            </a>
                                        @endif
                                        @if(!$notif->read_at)
                                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="button-secondary text-xs py-1.5 px-3 font-semibold text-slate-600 hover:text-slate-900 cursor-pointer">
                                                    Tandai Sudah Dibaca
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-slate-400 font-medium">✓ Sudah dibaca</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 border-t border-slate-100">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12 px-4 space-y-3">
                        <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-2xl text-slate-400">
                            📭
                        </div>
                        <h2 class="text-base font-bold text-slate-700">Belum Ada Pemberitahuan</h2>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                            Pemberitahuan terkait penyerahan resep obat atau ucapan belasungkawa dari sesama anggota akan muncul di halaman ini.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
