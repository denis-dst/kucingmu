<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="eyebrow">Pusat Pesan & Komunikasi</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                    Pesan Masuk Pengunjung & Member
                </h1>
            </div>
            <a href="{{ route('contact.index') }}" target="_blank" class="button-secondary text-xs font-semibold px-3.5 py-1.5 inline-flex items-center gap-1.5 self-start sm:self-auto">
                <span>↗</span> Lihat Formulir Kontak Publik
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3 shadow-2xs">
                    <span class="text-xl">✅</span>
                    <p class="text-xs font-medium text-emerald-900 mt-0.5">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pesan</span>
                    <span class="font-outfit text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($stats['total']) }}</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-rose-200 bg-rose-50/30 shadow-2xs">
                    <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Belum Dibaca</span>
                    <span class="font-outfit text-2xl font-extrabold text-rose-700 mt-1 block">{{ number_format($stats['unread']) }}</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/30 shadow-2xs">
                    <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block">Sudah Dibaca</span>
                    <span class="font-outfit text-2xl font-extrabold text-amber-700 mt-1 block">{{ number_format($stats['read']) }}</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-emerald-200 bg-emerald-50/30 shadow-2xs">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Sudah Dibalas</span>
                    <span class="font-outfit text-2xl font-extrabold text-emerald-700 mt-1 block">{{ number_format($stats['responded']) }}</span>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="content-card space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <!-- Status Tabs -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('admin.contacts.index', array_merge(request()->query(), ['status' => 'all'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'all' ? 'bg-teal-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua ({{ $stats['total'] }})
                        </a>
                        <a href="{{ route('admin.contacts.index', array_merge(request()->query(), ['status' => 'unread'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'unread' ? 'bg-rose-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Belum Dibaca ({{ $stats['unread'] }})
                        </a>
                        <a href="{{ route('admin.contacts.index', array_merge(request()->query(), ['status' => 'read'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'read' ? 'bg-amber-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Sudah Dibaca ({{ $stats['read'] }})
                        </a>
                        <a href="{{ route('admin.contacts.index', array_merge(request()->query(), ['status' => 'responded'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'responded' ? 'bg-emerald-700 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Sudah Dibalas ({{ $stats['responded'] }})
                        </a>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.contacts.index') }}" class="flex items-center gap-2">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, subjek..." class="form-input text-xs py-1.5 px-3 rounded-xl border-slate-300 w-full sm:w-64 focus:border-teal-600 focus:ring-teal-600">
                        <button type="submit" class="button-secondary text-xs font-semibold px-3 py-1.5">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.contacts.index', ['status' => request('status', 'all')]) }}" class="text-xs text-rose-600 hover:underline px-1">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Messages Table (Desktop) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4">Pengirim</th>
                                <th class="py-3 px-4">Subjek Pesan</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Waktu Kirim</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($messages as $msg)
                                <tr class="hover:bg-slate-50/70 transition {{ $msg->status === 'unread' ? 'bg-rose-50/20 font-semibold' : '' }}">
                                    <!-- Pengirim -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">{{ $msg->name }}</div>
                                        <div class="text-[11px] text-slate-500 font-normal">
                                            {{ $msg->email }}
                                            @if($msg->phone)
                                                &bull; {{ $msg->phone }}
                                            @endif
                                        </div>
                                        @if($msg->user)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                                Member Terdaftar
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Subjek & Cuplikan Pesan -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 max-w-xs truncate">{{ $msg->subject }}</div>
                                        <div class="text-[11px] text-slate-500 font-normal max-w-sm truncate mt-0.5">
                                            {{ $msg->message }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $msg->status_badge_class }} inline-flex items-center gap-1">
                                            @if($msg->status === 'unread')
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            @elseif($msg->status === 'responded')
                                                <span>✓</span>
                                            @endif
                                            {{ $msg->status_label }}
                                        </span>
                                    </td>

                                    <!-- Waktu -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 text-[11px] font-normal">
                                        <div>{{ $msg->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $msg->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.contacts.show', $msg->id) }}" class="btn-action-primary py-1.5 px-3 text-xs font-bold" title="Buka Pesan & Tulis Respon">
                                                <span>👁</span> Buka Pesan
                                            </a>
                                            <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-danger py-1.5 px-2 text-xs" title="Hapus Pesan">
                                                    🗑
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500 text-xs">
                                        <span class="text-3xl block mb-2">📭</span>
                                        Tidak ada pesan masuk yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Messages List (Mobile) -->
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($messages as $msg)
                        <div class="p-4 space-y-2.5 {{ $msg->status === 'unread' ? 'bg-rose-50/20' : 'bg-white' }}">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">{{ $msg->name }}</h3>
                                    <p class="text-[11px] text-slate-500">{{ $msg->email }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $msg->status_badge_class }}">
                                    {{ $msg->status_label }}
                                </span>
                            </div>

                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $msg->subject }}</h4>
                                <p class="text-xs text-slate-600 line-clamp-2 mt-0.5 leading-relaxed">{{ $msg->message }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[11px] text-slate-400">
                                <span>{{ $msg->created_at->format('d M Y, H:i') }} WIB</span>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.contacts.show', $msg->id) }}" class="btn-action-primary py-1 px-2.5 text-xs font-bold">
                                        Buka Pesan
                                    </a>
                                    <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pesan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-danger py-1 px-2 text-xs">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-500 text-xs">
                            <span class="text-2xl block mb-1">📭</span>
                            Tidak ada pesan masuk.
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($messages->hasPages())
                    <div class="pt-4 border-t border-slate-100">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
