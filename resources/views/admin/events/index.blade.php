<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Header Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Manajemen Kegiatan</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Kelola Event KucingMu
                    </h1>
                    <p class="card-copy max-w-xl">
                        Buat dan kelola jadwal sosialisasi kegiatan pemeriksaan kesehatan kucing, seminar, vaksinasi massal, dsb. Lengkap dengan link pendaftaran luar.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('admin.events.create') }}" class="button-primary px-5 py-2.5 inline-flex items-center gap-2">
                            <span>➕</span> Buat Kegiatan Baru
                        </a>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    📅
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center gap-2">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Events List Card -->
            <div class="content-card">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="font-outfit text-xl font-bold text-slate-900">Daftar Kegiatan Aktif & Arsip</h2>
                </div>

                @if($events->isEmpty())
                    <div class="text-center py-12 text-slate-500">
                        <span class="text-4xl block mb-3">📅</span>
                        <p class="font-bold">Belum ada kegiatan yang terdaftar.</p>
                        <p class="text-xs text-slate-400 mt-1">Klik tombol di atas untuk membuat kegiatan sosialisasi pertama Anda.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 font-bold">
                                    <th class="py-3 px-1">Banner</th>
                                    <th class="py-3 px-1">Nama Kegiatan</th>
                                    <th class="py-3 px-1">Tanggal & Lokasi</th>
                                    <th class="py-3 px-1">Link Pendaftaran</th>
                                    <th class="py-3 px-1">Status</th>
                                    <th class="py-3 px-1 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($events as $event)
                                    <tr>
                                        <td class="py-4 px-1">
                                            <div class="h-12 w-20 bg-slate-100 border border-slate-200 rounded-lg overflow-hidden flex items-center justify-center">
                                                @if($event->banner_path)
                                                    <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-xs text-slate-400 uppercase font-semibold">No Banner</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-1 font-bold text-slate-900">
                                            <div>{{ $event->title }}</div>
                                            <div class="text-xs text-slate-400 font-normal mt-0.5 line-clamp-1 max-w-xs">{{ $event->description }}</div>
                                        </td>
                                        <td class="py-4 px-1">
                                            <div class="font-semibold">{{ $event->date->format('d M Y') }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $event->location }}</div>
                                        </td>
                                        <td class="py-4 px-1">
                                            @if($event->registration_link)
                                                <a href="{{ str_starts_with($event->registration_link, 'http') ? $event->registration_link : 'https://' . $event->registration_link }}" target="_blank" class="text-xs font-bold text-teal-700 hover:underline max-w-xs block truncate">
                                                    {{ $event->registration_link }} ↗
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-1">
                                            @if($event->status === 'active')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">Aktif</span>
                                            @elseif($event->status === 'draft')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Draft</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Selesai</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-1 text-right">
                                            <div class="flex justify-end items-center gap-2">
                                                <a href="{{ route('admin.events.edit', $event->id) }}" class="text-xs font-bold text-teal-700 hover:underline">
                                                    Ubah
                                                </a>
                                                <span class="text-slate-300">|</span>
                                                <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
