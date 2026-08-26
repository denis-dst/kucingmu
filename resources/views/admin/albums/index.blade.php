<x-app-layout>
    <div class="py-10" x-data="{
        openCreateModal: false,
        openEditModal: false,
        uploadMode: 'file', // 'file' or 'existing'
        editData: {
            id: null,
            title: '',
            caption: '',
            category: 'Kegiatan',
            activity_date: '',
            order: 0,
            is_active: true,
            image_url: '',
            existing_image: '',
            actionUrl: ''
        },
        openEdit(item, url, imgUrl) {
            this.editData = {
                id: item.id,
                title: item.title,
                caption: item.caption || '',
                category: item.category || 'Kegiatan',
                activity_date: item.activity_date ? item.activity_date.substring(0, 10) : '',
                order: item.order || 0,
                is_active: Boolean(item.is_active),
                image_url: imgUrl,
                existing_image: item.image_path ? item.image_path.replace('images/albums/', '') : '',
                actionUrl: url
            };
            this.uploadMode = 'file';
            this.openEditModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Superadmin Galeri & Publikasi</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                        Album Foto Kegiatan KucingMu
                    </h1>
                    <p class="card-copy max-w-2xl">
                        Kelola galeri dan stok foto dokumentasi kegiatan pemeriksaan, sensus liar PTMA, serta edukasi komunitas untuk ditampilkan sebagai slide beranda. Stok foto disimpan di <code class="font-mono text-teal-800 bg-teal-100/70 px-1.5 py-0.5 rounded text-xs">public/images/albums</code>.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="button" @click="openCreateModal = true" class="button-primary px-5 py-2.5 inline-flex items-center gap-2 text-xs font-bold shadow-md bg-teal-800 hover:bg-teal-900">
                            <span>➕</span> Tambah Foto Kegiatan
                        </button>
                        <form method="POST" action="{{ route('superadmin.albums.seed-default') }}" class="inline-block" onsubmit="return confirm('Apakah Anda ingin memuat / memperbarui daftar stok 250 foto album kegiatan?')">
                            @csrf
                            <button type="submit" class="button-secondary px-4 py-2.5 inline-flex items-center gap-2 text-xs font-semibold bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 shadow-xs">
                                <span>📥</span> Muat Stok 250 Foto Default
                            </button>
                        </form>
                    </div>
                </div>
                <div class="hidden md:block text-5xl">
                    📸
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
                        🖼️
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-slate-900">{{ number_format($stats['total_photos']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Total Foto Terdaftar</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
                        ✨
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-emerald-700">{{ number_format($stats['active_photos']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Tampil di Slide Beranda</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center text-xl font-bold">
                        👁️‍🗨️
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-slate-600">{{ number_format($stats['inactive_photos']) }}</div>
                        <div class="text-xs font-medium text-slate-500">Disembunyikan</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold">
                        📁
                    </div>
                    <div>
                        <div class="text-2xl font-outfit font-extrabold text-amber-800">{{ number_format($stats['total_files_in_dir']) }}</div>
                        <div class="text-xs font-medium text-slate-500">File di /images/albums</div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('superadmin.albums.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-[240px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, keterangan, atau kategori..." class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Category Filter -->
                    <select name="category" onchange="this.form.submit()" class="py-2 px-3 text-sm rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none bg-white text-slate-700">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-sm rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 outline-none bg-white text-slate-700">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-teal-800 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl transition">
                        Filter
                    </button>

                    @if(request('search') || request('category') || request('status') !== null)
                        <a href="{{ route('superadmin.albums.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800 underline">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="text-xs font-semibold text-slate-500">
                    Menampilkan <span class="text-slate-900">{{ $albums->firstItem() ?? 0 }}-{{ $albums->lastItem() ?? 0 }}</span> dari <span class="text-slate-900">{{ $albums->total() }}</span> foto
                </div>
            </div>

            <!-- Albums Card Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($albums as $album)
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-md hover:border-teal-200 transition duration-200">
                        <!-- Image Preview -->
                        <div class="relative aspect-[4/3] bg-slate-900 overflow-hidden">
                            <img src="{{ $album->image_url }}" alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            
                            <!-- Badges Overlay -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-900/80 backdrop-blur-xs text-white border border-white/20">
                                    {{ $album->category }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <form method="POST" action="{{ route('superadmin.albums.toggle-status', $album) }}">
                                    @csrf
                                    @if($album->is_active)
                                        <button type="submit" title="Klik untuk menyembunyikan dari slide beranda" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-md hover:bg-emerald-600 transition flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Tampil
                                        </button>
                                    @else
                                        <button type="submit" title="Klik untuk menampilkan di slide beranda" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-700/90 text-slate-300 hover:bg-slate-800 transition">
                                            Nonaktif
                                        </button>
                                    @endif
                                </form>
                            </div>

                            @if($album->order > 0)
                                <div class="absolute bottom-2 left-3">
                                    <span class="px-2 py-0.5 rounded bg-black/60 backdrop-blur-xs text-[10px] font-mono text-amber-300 font-bold">
                                        Urutan: #{{ $album->order }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <div class="text-[11px] font-semibold text-teal-700 mb-1 flex items-center gap-1">
                                    <span>📅</span> {{ $album->activity_date ? $album->activity_date->translatedFormat('d M Y') : '-' }}
                                </div>
                                <h3 class="font-outfit font-bold text-slate-900 text-sm leading-snug line-clamp-2" title="{{ $album->title }}">
                                    {{ $album->title }}
                                </h3>
                                @if($album->caption)
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $album->caption }}
                                    </p>
                                @endif
                                <div class="mt-2 text-[10px] font-mono text-slate-400 truncate" title="{{ $album->image_path }}">
                                    📂 {{ $album->image_path }}
                                </div>
                            </div>

                            <!-- Actions Footer -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                <button type="button" @click="openEdit({{ json_encode($album) }}, '{{ route('superadmin.albums.update', $album) }}', '{{ $album->image_url }}')" class="flex-1 py-1.5 px-3 rounded-xl bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-800 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit</span>
                                </button>

                                <form method="POST" action="{{ route('superadmin.albums.destroy', $album) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto kegiatan \"{{ $album->title }}\"?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Foto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200 p-8 shadow-xs">
                        <div class="text-5xl mb-3">📸</div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">Belum Ada Foto Kegiatan</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mb-5">
                            Tambahkan foto dokumentasi kegiatan untuk ditampilkan di slide beranda website KucingMu. Anda dapat mengunggah file foto baru atau memilih foto dari folder <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">public/images/albums</code>.
                        </p>
                        <button type="button" @click="openCreateModal = true" class="button-primary px-5 py-2.5 inline-flex items-center gap-2 text-xs font-bold shadow-md bg-teal-800 hover:bg-teal-900">
                            <span>➕</span> Tambah Foto Kegiatan Baru
                        </button>
                    </div>
                @endforelse
            </div>

            @if($albums->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white rounded-2xl shadow-xs">
                    {{ $albums->links() }}
                </div>
            @endif

        </div>

        <!-- Create Modal -->
        <div x-show="openCreateModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div @click.away="openCreateModal = false" class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col w-full max-w-xl max-h-[90vh] border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h3 class="font-outfit font-bold text-slate-900 text-base">Tambah Foto Kegiatan Baru</h3>
                    <button type="button" @click="openCreateModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('superadmin.albums.store') }}" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1">
                    @csrf

                    <!-- Source Selection: Upload New vs Pick Existing File -->
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Pilihan Sumber Foto <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl mb-3">
                            <button type="button" @click="uploadMode = 'file'" :class="uploadMode === 'file' ? 'bg-white text-teal-800 shadow-xs font-bold' : 'text-slate-600 font-medium'" class="py-2 text-xs rounded-lg transition text-center">
                                📤 Unggah File Baru
                            </button>
                            <button type="button" @click="uploadMode = 'existing'" :class="uploadMode === 'existing' ? 'bg-white text-teal-800 shadow-xs font-bold' : 'text-slate-600 font-medium'" class="py-2 text-xs rounded-lg transition text-center">
                                📁 Pilih Dari /images/albums ({{ count($existingFiles) }})
                            </button>
                        </div>

                        <!-- Mode 1: Upload File -->
                        <div x-show="uploadMode === 'file'" class="space-y-2">
                            <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-300 rounded-xl p-2 cursor-pointer">
                            <p class="text-[11px] text-slate-400">Format: JPG, PNG, WEBP (Maks. 10MB). Otomatis disimpan ke <code class="font-mono">public/images/albums</code>.</p>
                        </div>

                        <!-- Mode 2: Existing File Selector -->
                        <div x-show="uploadMode === 'existing'" class="space-y-2">
                            @if(count($existingFiles) > 0)
                                <select name="existing_image" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono bg-white">
                                    <option value="">-- Pilih file foto yang tersedia di public/images/albums --</option>
                                    @foreach($existingFiles as $file)
                                        <option value="{{ $file }}">{{ $file }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-400">File dibaca langsung dari folder <code class="font-mono">public/images/albums</code>.</p>
                            @else
                                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                    Belum ada file di folder <code class="font-mono font-bold">public/images/albums</code>. Silakan gunakan opsi "Unggah File Baru" di samping.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Judul Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" required placeholder="Contoh: Pemeriksaan Kesehatan Kucing di Kampus UMY" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                    </div>

                    <!-- Caption -->
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Deskripsi / Keterangan Singkat
                        </label>
                        <textarea name="caption" rows="2" placeholder="Contoh: Tim dokter hewan dan relawan melakukan pemeriksaan fisik dan pemberian vitamin gratis..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Category -->
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Kategori
                            </label>
                            <input type="text" name="category" list="category-suggestions" placeholder="Pemeriksaan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                            <datalist id="category-suggestions">
                                <option value="Pemeriksaan">
                                <option value="Sensus PTMA">
                                <option value="Edukasi & Sosialisasi">
                                <option value="Vaksinasi & Sterilisasi">
                                <option value="Kegiatan Komunitas">
                            </datalist>
                        </div>

                        <!-- Activity Date -->
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Kegiatan
                            </label>
                            <input type="date" name="activity_date" value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Slide
                            </label>
                            <input type="number" name="order" value="0" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="create_album_active" value="1" checked class="w-4 h-4 text-teal-600 border-slate-300 rounded focus:ring-teal-500">
                        <label for="create_album_active" class="text-sm font-semibold text-slate-700 cursor-pointer">
                            Tampilkan di Slide Beranda Website
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-outfit font-bold text-sm shadow-md transition">
                            Simpan ke Album
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="openEditModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div @click.away="openEditModal = false" class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col w-full max-w-xl max-h-[90vh] border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h3 class="font-outfit font-bold text-slate-900 text-base">Edit Foto Kegiatan</h3>
                    <button type="button" @click="openEditModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" :action="editData.actionUrl" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1">
                    @csrf
                    @method('PUT')

                    <!-- Current Image Preview -->
                    <div class="flex items-center gap-4 p-3 rounded-2xl bg-slate-50 border border-slate-200">
                        <img :src="editData.image_url" alt="Preview" class="w-20 h-16 object-cover rounded-xl border border-slate-300">
                        <div class="text-xs">
                            <div class="font-bold text-slate-800">Foto Saat Ini:</div>
                            <div class="text-slate-500 font-mono text-[11px] truncate max-w-xs" x-text="editData.existing_image"></div>
                            <div class="text-slate-400 text-[10px] mt-0.5">Biarkan kosong di bawah jika tidak ingin mengganti foto.</div>
                        </div>
                    </div>

                    <!-- Change Image Option -->
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Ganti Foto (Opsional)
                        </label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl mb-2">
                            <button type="button" @click="uploadMode = 'file'" :class="uploadMode === 'file' ? 'bg-white text-teal-800 shadow-xs font-bold' : 'text-slate-600 font-medium'" class="py-1.5 text-xs rounded-lg transition text-center">
                                📤 Unggah File Baru
                            </button>
                            <button type="button" @click="uploadMode = 'existing'" :class="uploadMode === 'existing' ? 'bg-white text-teal-800 shadow-xs font-bold' : 'text-slate-600 font-medium'" class="py-1.5 text-xs rounded-lg transition text-center">
                                📁 Pilih Dari Stok
                            </button>
                        </div>

                        <div x-show="uploadMode === 'file'">
                            <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-300 rounded-xl p-2 cursor-pointer">
                        </div>

                        <div x-show="uploadMode === 'existing'">
                            <select name="existing_image" x-model="editData.existing_image" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono bg-white">
                                <option value="">-- Pilih foto dari public/images/albums --</option>
                                @foreach($existingFiles as $file)
                                    <option value="{{ $file }}">{{ $file }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Judul Kegiatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" x-model="editData.title" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                    </div>

                    <!-- Caption -->
                    <div>
                        <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Deskripsi / Keterangan Singkat
                        </label>
                        <textarea name="caption" x-model="editData.caption" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Category -->
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Kategori
                            </label>
                            <input type="text" name="category" x-model="editData.category" list="category-suggestions" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                        </div>

                        <!-- Activity Date -->
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Kegiatan
                            </label>
                            <input type="date" name="activity_date" x-model="editData.activity_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm">
                        </div>

                        <!-- Order -->
                        <div>
                            <label class="block font-outfit text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Slide
                            </label>
                            <input type="number" name="order" x-model="editData.order" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600 text-sm font-mono">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="edit_album_active" value="1" :checked="editData.is_active" class="w-4 h-4 text-teal-600 border-slate-300 rounded focus:ring-teal-500">
                        <label for="edit_album_active" class="text-sm font-semibold text-slate-700 cursor-pointer">
                            Tampilkan di Slide Beranda Website
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-outfit font-bold text-sm shadow-md transition">
                            Perbarui Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
