<x-app-layout>
    <div class="py-10" x-data="{
        openCreateModal: false,
        openEditModal: false,
        editData: {
            id: null,
            kode: '',
            nama: '',
            singkatan: '',
            urutan: 0,
            is_active: true,
            actionUrl: ''
        },
        openEdit(item, url) {
            this.editData = {
                id: item.id,
                kode: item.kode,
                nama: item.nama,
                singkatan: item.singkatan || '',
                urutan: item.urutan || 0,
                is_active: Boolean(item.is_active),
                actionUrl: url
            };
            this.openEditModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Header Panel -->
            <div class="bg-gradient-to-r from-teal-900 via-teal-800 to-emerald-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-700/60 border border-teal-500/30 text-teal-200 text-xs font-bold uppercase tracking-wider mb-3">
                        <span>🏛️</span> Superadmin Wilayah Management
                    </div>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Master Wilayah (PWM Muhammadiyah)
                    </h1>
                    <p class="text-teal-100 text-sm max-w-2xl mt-1 leading-relaxed">
                        Kelola data master kode wilayah, singkatan PWM, dan format generator penomoran unik KTAM (<code class="bg-teal-950/60 px-1.5 py-0.5 rounded text-amber-300 font-mono text-xs">kode_wilayah.kcg.xxxx</code>).
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="openCreateModal = true" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-outfit font-bold text-sm shadow-lg hover:shadow-amber-400/20 transition active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Wilayah Baru</span>
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-xs font-bold">!</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold shadow-sm">
                    <div class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Statistics Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 text-teal-700 flex items-center justify-center text-xl font-bold">
                        🗺️
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-slate-900">{{ number_format($stats['total_wilayah']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Total Wilayah</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
                        ✅
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-emerald-700">{{ number_format($stats['active_wilayah']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Wilayah Aktif</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center text-xl font-bold">
                        ⏸️
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-slate-600">{{ number_format($stats['inactive_wilayah']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Wilayah Nonaktif</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold">
                        🐱
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-amber-800">{{ number_format($stats['total_cats_linked']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Kucing Berkode Wilayah</div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('superadmin.wilayah.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[240px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama wilayah, atau singkatan..." class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Status Filter -->
                    <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-sm rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none bg-white text-slate-700">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-teal-800 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition">
                        Filter
                    </button>

                    @if(request('search') || request('status') !== null)
                        <a href="{{ route('superadmin.wilayah.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800 underline">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="text-xs font-semibold text-slate-500">
                    Menampilkan <span class="text-slate-900">{{ $wilayahs->firstItem() ?? 0 }}-{{ $wilayahs->lastItem() ?? 0 }}</span> dari <span class="text-slate-900">{{ $wilayahs->total() }}</span> wilayah
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-4 px-5 text-center w-16">Urutan</th>
                                <th class="py-4 px-5">Kode Wilayah</th>
                                <th class="py-4 px-5">Nama Wilayah (PWM)</th>
                                <th class="py-4 px-5">Singkatan</th>
                                <th class="py-4 px-5 text-center">Kucing Terdaftar</th>
                                <th class="py-4 px-5 text-center">Status</th>
                                <th class="py-4 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($wilayahs as $wilayah)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3.5 px-5 text-center font-mono text-xs text-slate-400">
                                        {{ $wilayah->urutan }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-teal-50 border border-teal-200 font-mono font-bold text-teal-800 text-xs">
                                            {{ $wilayah->kode }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">{{ $wilayah->nama }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">Format: {{ $wilayah->kode }}.kcg.xxxx</div>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        @if($wilayah->singkatan)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold text-xs rounded-md">
                                                {{ $wilayah->singkatan }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-center">
                                        @if($wilayah->cats_count > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                                                🐱 {{ $wilayah->cats_count }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">0</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-center">
                                        <form method="POST" action="{{ route('superadmin.wilayah.toggle-status', $wilayah) }}" class="inline-block">
                                            @csrf
                                            @if($wilayah->is_active)
                                                <button type="submit" title="Klik untuk menonaktifkan" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100/70 hover:bg-emerald-200 text-emerald-800 text-xs font-bold transition">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                                </button>
                                            @else
                                                <button type="submit" title="Klik untuk mengaktifkan" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-xs font-bold transition">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                    <td class="py-3.5 px-5 text-right space-x-1">
                                        <button type="button" @click="openEdit({{ json_encode($wilayah) }}, '{{ route('superadmin.wilayah.update', $wilayah) }}')" class="p-2 rounded-lg text-slate-600 hover:text-teal-800 hover:bg-teal-50 transition" title="Edit Wilayah">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <form method="POST" action="{{ route('superadmin.wilayah.destroy', $wilayah) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wilayah [{{ $wilayah->kode }}] {{ $wilayah->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Wilayah" {{ $wilayah->cats_count > 0 ? 'disabled' : '' }}>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                                        <div class="text-3xl mb-2">🗺️</div>
                                        <div>Belum ada data wilayah yang sesuai dengan filter pencarian.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($wilayahs->hasPages())
                    <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $wilayahs->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Create Modal -->
        <div x-show="openCreateModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div @click.away="openCreateModal = false" class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col w-full max-w-lg border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h3 class="font-outfit font-bold text-slate-900 text-base">Tambah Master Wilayah Baru</h3>
                    <button type="button" @click="openCreateModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('superadmin.wilayah.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kode Wilayah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode" required placeholder="Contoh: 34 atau 00" maxlength="10" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono uppercase">
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk awalan nomor kode unik KTAM (<code class="font-mono">kode.kcg.xxxx</code>).</p>
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Wilayah / PWM <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama" required placeholder="Contoh: D.I. Yogyakarta (PWM DIY)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Singkatan
                            </label>
                            <input type="text" name="singkatan" placeholder="Contoh: DIY" maxlength="50" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Tampilan
                            </label>
                            <input type="number" name="urutan" value="0" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="create_is_active" value="1" checked class="w-4 h-4 text-teal-600 border-slate-300 rounded focus:ring-teal-500">
                        <label for="create_is_active" class="text-sm font-semibold text-slate-700 cursor-pointer">
                            Aktifkan wilayah ini (dapat dipilih oleh member saat registrasi kucing)
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-outfit font-bold text-sm shadow-md transition">
                            Simpan Wilayah
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="openEditModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div @click.away="openEditModal = false" class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col w-full max-w-lg border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h3 class="font-outfit font-bold text-slate-900 text-base">Edit Master Wilayah</h3>
                    <button type="button" @click="openEditModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" :action="editData.actionUrl" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kode Wilayah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode" x-model="editData.kode" required maxlength="10" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono uppercase">
                        <p class="text-[11px] text-slate-400 mt-1">Perubahan kode akan mempengaruhi format kode registrasi baru.</p>
                    </div>

                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Wilayah / PWM <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama" x-model="editData.nama" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Singkatan
                            </label>
                            <input type="text" name="singkatan" x-model="editData.singkatan" maxlength="50" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                        </div>

                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Tampilan
                            </label>
                            <input type="number" name="urutan" x-model="editData.urutan" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editData.is_active" class="w-4 h-4 text-teal-600 border-slate-300 rounded focus:ring-teal-500">
                        <label for="edit_is_active" class="text-sm font-semibold text-slate-700 cursor-pointer">
                            Aktifkan wilayah ini (dapat dipilih oleh member)
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-outfit font-bold text-sm shadow-md transition">
                            Perbarui Wilayah
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
