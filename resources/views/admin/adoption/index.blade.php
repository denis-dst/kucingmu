<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="eyebrow">Panel Manajemen Adopsi</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1 flex items-center gap-2">
                    <span>💖</span> Kelola Etalase "Adopsi Aku"
                </h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('adoption.index') }}" target="_blank" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>🌐</span> Buka Etalase Publik ↗
                </a>
                <a href="{{ route('admin.adoptions.applications') }}" class="button-primary text-xs font-bold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>📬</span> Daftar Permohonan Adopsi
                    @if($stats['pending_applications'] > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-rose-500 text-white font-mono font-bold">
                            {{ $stats['pending_applications'] }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        activeTab: 'member',
        editModalOpen: false,
        modalCat: null,
        modalActionUrl: '',
        modalStatus: 'available',
        modalNotes: '',
        modalLocation: '',
        openEditModal(cat, url) {
            this.modalCat = cat;
            this.modalActionUrl = url;
            this.modalStatus = cat.adoption_status || 'available';
            this.modalNotes = cat.adoption_notes || '';
            this.modalLocation = cat.adoption_location || '';
            this.editModalOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
                <div class="content-card p-4 bg-white border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Etalase</span>
                        <span class="text-sm">🐾</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-slate-900">{{ number_format($stats['total_listed']) }}</span>
                        <span class="text-xs text-slate-500">Ekor</span>
                    </div>
                </div>

                <div class="content-card p-4 bg-emerald-50/50 border border-emerald-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Siap Adopsi</span>
                        <span class="text-sm">🟢</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-emerald-900">{{ number_format($stats['available']) }}</span>
                        <span class="text-xs text-emerald-700">Ekor</span>
                    </div>
                </div>

                <div class="content-card p-4 bg-amber-50/50 border border-amber-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Sedang Proses</span>
                        <span class="text-sm">🟡</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-amber-900">{{ number_format($stats['in_process']) }}</span>
                        <span class="text-xs text-amber-700">Ekor</span>
                    </div>
                </div>

                <div class="content-card p-4 bg-purple-50/50 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-purple-800 uppercase tracking-wider">Sudah Diadopsi</span>
                        <span class="text-sm">🏠</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-purple-900">{{ number_format($stats['adopted']) }}</span>
                        <span class="text-xs text-purple-700">Ekor</span>
                    </div>
                </div>

                <a href="{{ route('admin.adoptions.applications', ['status' => 'pending']) }}" class="content-card p-4 bg-rose-50/50 border border-rose-200 hover:border-rose-400 transition block">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Menunggu Review</span>
                        <span class="text-sm">📬</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-1.5">
                        <span class="font-outfit text-2xl font-bold text-rose-900">{{ number_format($stats['pending_applications']) }}</span>
                        <span class="text-xs text-rose-700">Permohonan</span>
                    </div>
                </a>
            </div>

            <!-- Tab Navigation & Filters -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    
                    <!-- Source Tabs -->
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="activeTab = 'member'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                :class="activeTab === 'member' ? 'bg-teal-800 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                            <span>🐱</span>
                            <span>Kucing dari Member ({{ $memberCats->count() }})</span>
                        </button>
                        <button type="button" 
                                @click="activeTab = 'rescue'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                :class="activeTab === 'rescue' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                            <span>📋</span>
                            <span>Kucing Sensus & Rescue ({{ $rescueCats->count() }})</span>
                        </button>
                    </div>

                    <!-- Status Filter Dropdown -->
                    <form method="GET" action="{{ route('admin.adoptions.index') }}" class="flex items-center gap-2">
                        <select name="status" onchange="this.form.submit()" class="form-input text-xs py-1.5 px-3">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="available" {{ $statusFilter === 'available' ? 'selected' : '' }}>🟢 Siap Diadopsi</option>
                            <option value="in_process" {{ $statusFilter === 'in_process' ? 'selected' : '' }}>🟡 Sedang Proses</option>
                            <option value="adopted" {{ $statusFilter === 'adopted' ? 'selected' : '' }}>🟣 Sudah Diadopsi</option>
                        </select>
                    </form>
                </div>

                <!-- TAB 1: KUCING DARI MEMBER -->
                <div x-show="activeTab === 'member'" class="space-y-3">
                    @if($memberCats->isEmpty())
                        <div class="text-center py-12 text-slate-400 space-y-2">
                            <span class="text-4xl">🐱</span>
                            <p class="text-xs font-medium">Belum ada data kucing member yang didaftarkan untuk adopsi.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="w-full text-left text-xs text-slate-600">
                                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="p-3">Anabul</th>
                                        <th class="p-3">Pemilik (Member)</th>
                                        <th class="p-3">Wilayah / Lokasi</th>
                                        <th class="p-3">Status Adopsi</th>
                                        <th class="p-3">Tgl Masuk Etalase</th>
                                        <th class="p-3 text-right">Aksi Admin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($memberCats as $cat)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                                        <img src="{{ $cat->primary_photo_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('adoption.show', ['member', $cat->id]) }}" target="_blank" class="font-bold text-slate-900 hover:text-teal-800 text-sm">
                                                            {{ $cat->name }} ↗
                                                        </a>
                                                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                            <span>{{ $cat->breed ?? 'Domestik' }}</span> • 
                                                            <span>{{ $cat->gender === 'male' ? 'Jantan ♂' : 'Betina ♀' }}</span> •
                                                            <span class="font-mono text-[10px] text-teal-700 font-semibold">{{ $cat->unique_code ?? 'Tanpa KTA' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                @if($cat->owner)
                                                    <div class="space-y-0.5">
                                                        <div class="font-semibold text-slate-900">{{ $cat->owner->name }}</div>
                                                        <div class="text-[11px] text-slate-500 font-mono">{{ $cat->owner->email }}</div>
                                                        @if($cat->owner->phone)
                                                            @php
                                                                $cleanPhone = preg_replace('/[^0-9]/', '', $cat->owner->phone);
                                                                if (str_starts_with($cleanPhone, '0')) {
                                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                                }
                                                                $waOwnerUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode("Halo {$cat->owner->name}, kami dari Admin KucingMu terkait program Adopsi Aku untuk anabul Anda '{$cat->name}'.");
                                                            @endphp
                                                            <a href="{{ $waOwnerUrl }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 hover:bg-emerald-100 mt-1">
                                                                <span>💬</span> WA: {{ $cat->owner->phone }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic">Tidak Diketahui</span>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                <div class="space-y-0.5">
                                                    <div class="font-medium text-slate-800">{{ $cat->wilayah ? $cat->wilayah->nama : 'Wilayah Tidak Terpilih' }}</div>
                                                    <div class="text-[11px] text-slate-500">{{ $cat->adoption_location ?: '-' }}</div>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $cat->adoption_badge_class }}">
                                                    {{ $cat->adoption_status_label }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-[11px] text-slate-500 font-mono">
                                                {{ $cat->adoption_listed_at ? $cat->adoption_listed_at->format('d/m/Y H:i') : $cat->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" 
                                                            @click="openEditModal({{ json_encode($cat) }}, '{{ route('admin.adoptions.toggle-cat', $cat->id) }}')"
                                                            class="button-secondary text-[11px] font-bold px-2.5 py-1.5 rounded-lg">
                                                        ⚙️ Ubah Status
                                                    </button>
                                                    <a href="{{ route('cat.edit', $cat->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-teal-800 hover:bg-slate-100 transition" title="Edit Data Kucing">
                                                        ✏️
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- TAB 2: KUCING RESCUE & SENSUS -->
                <div x-show="activeTab === 'rescue'" class="space-y-3" style="display: none;">
                    @if($rescueCats->isEmpty())
                        <div class="text-center py-12 text-slate-400 space-y-2">
                            <span class="text-4xl">📋</span>
                            <p class="text-xs font-medium">Belum ada data kucing hasil sensus/rescue yang dibuka untuk adopsi.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="w-full text-left text-xs text-slate-600">
                                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="p-3">Anabul Rescue</th>
                                        <th class="p-3">Relawan Pencatat</th>
                                        <th class="p-3">Lokasi Rescue / Kampus</th>
                                        <th class="p-3">Status Adopsi</th>
                                        <th class="p-3">Tgl Sensus</th>
                                        <th class="p-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($rescueCats as $survey)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                                        <img src="{{ $survey->photo_url }}" alt="Rescue" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('adoption.show', ['rescue', $survey->id]) }}" target="_blank" class="font-bold text-slate-900 hover:text-indigo-800 text-sm">
                                                            {{ $survey->physical_cat_name ?: 'Kucing Rescue #' . $survey->id }} ↗
                                                        </a>
                                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                                            <span>{{ $survey->coat_condition ?: 'Kondisi Normal' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                @if($survey->volunteer)
                                                    <div class="space-y-0.5">
                                                        <div class="font-semibold text-slate-900">{{ $survey->volunteer->name }}</div>
                                                        <div class="text-[11px] text-slate-500 font-mono">{{ $survey->volunteer->email }}</div>
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic">Relawan Umum</span>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                <span class="font-medium text-slate-800">{{ $survey->campus_location ?: 'Lokasi Belum Terdata' }}</span>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $survey->adoption_badge_class }}">
                                                    {{ $survey->adoption_status_label }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-[11px] text-slate-500 font-mono">
                                                {{ $survey->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="p-3 text-right">
                                                <a href="{{ route('adoption.show', ['rescue', $survey->id]) }}" target="_blank" class="button-secondary text-[11px] font-bold px-2.5 py-1.5 rounded-lg">
                                                    👁️ Lihat Profil
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

        </div>

        <!-- MODAL UBAH STATUS ADOPSI KUCING MEMBER -->
        <div x-show="editModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-outfit text-base font-bold text-slate-900 flex items-center gap-2">
                        <span>⚙️</span> Update Status Adopsi
                    </h3>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-700 font-bold">✕</button>
                </div>

                <form method="POST" :action="modalActionUrl" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Tampilkan di Etalase "Adopsi Aku":</label>
                        <select name="is_for_adoption" class="form-input text-xs py-2">
                            <option value="1">Ya, Tampilkan di Etalase Adopsi</option>
                            <option value="0">Tidak (Tutup / Hapus dari Etalase)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Status Ketersediaan:</label>
                        <select name="adoption_status" x-model="modalStatus" class="form-input text-xs py-2">
                            <option value="available">🟢 Siap Diadopsi (Available)</option>
                            <option value="in_process">🟡 Sedang Proses Seleksi / Trial (In Process)</option>
                            <option value="adopted">🟣 Sudah Berhasil Diadopsi (Adopted)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Catatan Khusus Adopsi / Syarat:</label>
                        <textarea name="adoption_notes" x-model="modalNotes" rows="3" class="form-input text-xs" placeholder="Misal: Siap antar area Sleman, wajib steril, dll."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="button-secondary text-xs px-3.5 py-2">Batal</button>
                        <button type="submit" class="button-primary text-xs font-bold px-4 py-2 shadow-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
