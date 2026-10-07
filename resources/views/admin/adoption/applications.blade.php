<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="eyebrow">Panel Mediasi Komunitas</span>
                <h1 class="font-outfit text-xl sm:text-2xl font-bold text-slate-900 mt-1 flex items-center gap-2">
                    <span>📬</span> Daftar Permohonan Adopsi
                </h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.adoptions.index') }}" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>🐱</span> Kelola Data Kucing
                </a>
                <a href="{{ route('adoption.index') }}" target="_blank" class="button-secondary text-xs font-semibold px-3.5 py-2 inline-flex items-center gap-1.5 shadow-2xs">
                    <span>🌐</span> Etalase Publik ↗
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
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

            <!-- Status Metric Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                <a href="{{ route('admin.adoptions.applications', ['status' => 'all']) }}" 
                   class="content-card p-3.5 bg-white border border-slate-200 block hover:border-teal-400 transition {{ $statusFilter === 'all' ? 'ring-2 ring-teal-500/30' : '' }}">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Masuk</div>
                    <div class="mt-1 font-outfit text-2xl font-bold text-slate-900">{{ number_format($stats['total']) }}</div>
                </a>

                <a href="{{ route('admin.adoptions.applications', ['status' => 'pending']) }}" 
                   class="content-card p-3.5 bg-rose-50/50 border border-rose-200 block hover:border-rose-400 transition {{ $statusFilter === 'pending' ? 'ring-2 ring-rose-500/40' : '' }}">
                    <div class="text-[11px] font-bold text-rose-800 uppercase tracking-wider flex items-center justify-between">
                        <span>Menunggu</span>
                        <span class="text-xs">🔴</span>
                    </div>
                    <div class="mt-1 font-outfit text-2xl font-bold text-rose-900">{{ number_format($stats['pending']) }}</div>
                </a>

                <a href="{{ route('admin.adoptions.applications', ['status' => 'reviewing']) }}" 
                   class="content-card p-3.5 bg-amber-50/50 border border-amber-200 block hover:border-amber-400 transition {{ $statusFilter === 'reviewing' ? 'ring-2 ring-amber-500/40' : '' }}">
                    <div class="text-[11px] font-bold text-amber-800 uppercase tracking-wider flex items-center justify-between">
                        <span>Ditinjau</span>
                        <span class="text-xs">🟡</span>
                    </div>
                    <div class="mt-1 font-outfit text-2xl font-bold text-amber-900">{{ number_format($stats['reviewing']) }}</div>
                </a>

                <a href="{{ route('admin.adoptions.applications', ['status' => 'approved']) }}" 
                   class="content-card p-3.5 bg-emerald-50/50 border border-emerald-200 block hover:border-emerald-400 transition {{ $statusFilter === 'approved' ? 'ring-2 ring-emerald-500/40' : '' }}">
                    <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider flex items-center justify-between">
                        <span>Disetujui</span>
                        <span class="text-xs">🟢</span>
                    </div>
                    <div class="mt-1 font-outfit text-2xl font-bold text-emerald-900">{{ number_format($stats['approved']) }}</div>
                </a>

                <a href="{{ route('admin.adoptions.applications', ['status' => 'completed']) }}" 
                   class="content-card p-3.5 bg-purple-50/50 border border-purple-200 block hover:border-purple-400 transition {{ $statusFilter === 'completed' ? 'ring-2 ring-purple-500/40' : '' }}">
                    <div class="text-[11px] font-bold text-purple-800 uppercase tracking-wider flex items-center justify-between">
                        <span>Selesai Adopsi</span>
                        <span class="text-xs">🏠</span>
                    </div>
                    <div class="mt-1 font-outfit text-2xl font-bold text-purple-900">{{ number_format($stats['completed']) }}</div>
                </a>

                <a href="{{ route('admin.adoptions.applications', ['status' => 'rejected']) }}" 
                   class="content-card p-3.5 bg-slate-50 border border-slate-200 block hover:border-slate-400 transition {{ $statusFilter === 'rejected' ? 'ring-2 ring-slate-500/40' : '' }}">
                    <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center justify-between">
                        <span>Ditolak / Batal</span>
                        <span class="text-xs">⚪</span>
                    </div>
                    <div class="mt-1 font-outfit text-2xl font-bold text-slate-700">{{ number_format($stats['rejected']) }}</div>
                </a>
            </div>

            <!-- Search & Filter Toolbar -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-3">
                <form method="GET" action="{{ route('admin.adoptions.applications') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-96">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari kode (ADP-...), nama, nomor WA, atau kucing..." 
                               class="form-input text-xs pl-9 py-2 w-full">
                    </div>

                    <!-- Status Dropdown & Actions -->
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <select name="status" onchange="this.form.submit()" class="form-input text-xs py-2 px-3">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>🔴 Menunggu Review (Pending)</option>
                            <option value="reviewing" {{ $statusFilter === 'reviewing' ? 'selected' : '' }}>🟡 Sedang Ditinjau Admin</option>
                            <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>🟢 Disetujui (Tahap Wawancara/Serah Terima)</option>
                            <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>🏠 Selesai Diadopsi (Completed)</option>
                            <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>⚪ Ditolak / Dibatalkan</option>
                        </select>

                        @if(request('search') || $statusFilter !== 'all')
                            <a href="{{ route('admin.adoptions.applications') }}" class="button-secondary text-xs px-3 py-2">
                                Reset
                            </a>
                        @endif

                        <button type="submit" class="button-primary text-xs font-bold px-4 py-2 shadow-xs">
                            Cari
                        </button>
                    </div>

                </form>
            </div>

            <!-- Applications Table -->
            <div class="content-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                @if($applications->isEmpty())
                    <div class="text-center py-16 text-slate-400 space-y-2">
                        <span class="text-5xl">📬</span>
                        <h3 class="font-outfit text-base font-bold text-slate-700">Belum Ada Permohonan Adopsi</h3>
                        <p class="text-xs max-w-sm mx-auto">Permohonan yang dikirimkan oleh peminat anabul melalui etalase "Adopsi Aku" akan otomatis muncul di halaman ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-3.5">Kode & Tanggal</th>
                                    <th class="p-3.5">Anabul yang Diminati</th>
                                    <th class="p-3.5">Calon Adopter (Pemohon)</th>
                                    <th class="p-3.5">Domisili & Hunian</th>
                                    <th class="p-3.5">Status Mediasi</th>
                                    <th class="p-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($applications as $app)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        
                                        <!-- Code & Date -->
                                        <td class="p-3.5">
                                            <div class="font-mono text-xs font-bold text-teal-800">
                                                {{ $app->application_code }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">
                                                {{ $app->created_at->format('d M Y, H:i') }}
                                            </div>
                                        </td>

                                        <!-- Cat Requested -->
                                        <td class="p-3.5">
                                            <div class="flex items-center gap-2.5">
                                                @if($app->cat)
                                                    <div class="w-9 h-9 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                                        <img src="{{ $app->cat->primary_photo_url }}" alt="" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-900">{{ $app->cat->name }}</div>
                                                        <div class="text-[10px] text-slate-500">Kucing Member • {{ $app->cat->unique_code ?? 'Tanpa KTA' }}</div>
                                                    </div>
                                                @elseif($app->strayCatSurvey)
                                                    <div class="w-9 h-9 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                                        <img src="{{ $app->strayCatSurvey->photo_url }}" alt="" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-900">{{ $app->strayCatSurvey->physical_cat_name ?: 'Kucing Rescue' }}</div>
                                                        <div class="text-[10px] text-indigo-600 font-semibold">Hasil Sensus Relawan</div>
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic">Data Kucing Dihapus</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Applicant Details (Unmasked for Admin) -->
                                        <td class="p-3.5">
                                            <div class="space-y-0.5">
                                                <div class="font-bold text-slate-900">{{ $app->applicant_name }}</div>
                                                <div class="text-[11px] text-slate-500 font-mono">{{ $app->applicant_email }}</div>
                                                
                                                <!-- Direct Admin WhatsApp to Adopter -->
                                                @if($app->applicant_phone)
                                                    @php
                                                        $waClean = preg_replace('/[^0-9]/', '', $app->applicant_phone);
                                                        if (str_starts_with($waClean, '0')) {
                                                            $waClean = '62' . substr($waClean, 1);
                                                        }
                                                        $catName = $app->cat ? $app->cat->name : ($app->strayCatSurvey ? $app->strayCatSurvey->physical_cat_name : 'anabul');
                                                        $waApplicantText = "Halo kak {$app->applicant_name}, kami dari Tim Admin KucingMu terkait permohonan adopsi Anda [{$app->application_code}] untuk anabul '{$catName}'.";
                                                        $waAppUrl = "https://wa.me/{$waClean}?text=" . urlencode($waApplicantText);
                                                    @endphp
                                                    <a href="{{ $waAppUrl }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 hover:bg-emerald-100 mt-0.5">
                                                        <span>💬</span> WA: {{ $app->applicant_phone }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Domisili & Housing -->
                                        <td class="p-3.5">
                                            <div class="font-medium text-slate-800">{{ $app->applicant_city ?: '-' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $app->housing_type }}</div>
                                            <div class="text-[10px] text-teal-700 mt-0.5">Hewan lain: {{ $app->has_other_pets ?: 'Tidak Ada' }}</div>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="p-3.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $app->status_badge_class }}">
                                                {{ $app->status_label }}
                                            </span>
                                            @if($app->reviewer)
                                                <div class="text-[10px] text-slate-400 mt-1">
                                                    Review: {{ $app->reviewer->name }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="p-3.5 text-right">
                                            <a href="{{ route('admin.adoptions.show-application', $app->id) }}" class="button-primary text-xs font-bold px-3 py-1.5 rounded-xl shadow-2xs inline-flex items-center gap-1">
                                                <span>🔍</span> Tinjau Mediasi
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($applications->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $applications->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
