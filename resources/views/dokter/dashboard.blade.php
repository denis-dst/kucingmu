<x-app-layout>
    <div class="py-12" x-data="{ activeAppointment: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Panel -->
            <div class="hero-card">
                <div>
                    <span class="card-kicker">Portal Dokter Hewan</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">
                        Selamat Tugas, {{ Auth::user()->name }}!
                    </h1>
                    <p class="card-copy max-w-xl">
                        Akses rekam medis, kelola antrian pemeriksaan kucing hari ini, dan catat hasil konsultasi. Platform akan secara otomatis menerbitkan kartu KTAM setelah pemeriksaan selesai dilakukan.
                    </p>
                </div>
                <div class="hidden md:block text-5xl">
                    🩺
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold flex items-center gap-2">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Dashboard Content Grid -->
            <div class="grid gap-8 lg:grid-cols-3">
                
                <!-- Left Queue Column -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Today's Examination Queue -->
                    <div class="content-card">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                            <h2 class="font-outfit text-xl font-bold text-slate-900">Antrian Pemeriksaan Hari Ini</h2>
                            <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-bold border border-teal-100">{{ $queue->count() }} Antrian</span>
                        </div>

                        @if($queue->isEmpty())
                            <div class="text-center py-12 text-slate-500">
                                <span class="text-3xl">📭</span>
                                <p class="mt-4 text-sm font-semibold text-slate-700">Tidak ada antrian pemeriksaan untuk hari ini.</p>
                                <p class="text-xs text-slate-400 mt-1">Kucing akan masuk ke daftar ini setelah volunteer melakukan check-in.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($queue as $app)
                                    <div class="rounded-xl border border-slate-200 p-5 bg-slate-50/50 hover:bg-white hover:shadow-md transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <div class="h-12 w-12 rounded-xl bg-teal-50 text-teal-700 text-xl font-bold flex items-center justify-center flex-shrink-0">
                                                {{ substr($app->cat->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $app->cat->name }}</h3>
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $app->status == 'checked_in' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ $app->status == 'checked_in' ? 'Siap Periksa' : 'Direncanakan' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-0.5">Ras: {{ $app->cat->breed }} &bull; Kelamin: {{ $app->cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">Pemilik: <span class="font-semibold text-slate-700">{{ $app->cat->owner->name }}</span> (NBM: {{ $app->cat->owner->muhammadiyah_id ?? '-' }})</p>
                                                @if($app->notes)
                                                    <p class="text-xs text-slate-400 mt-2 bg-white p-2 rounded border border-slate-100 italic">"{{ $app->notes }}"</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button @click="activeAppointment = {{ $app }}" class="button-primary px-4 py-2 text-xs">
                                                Mulai Periksa
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Examination Input Column -->
                <div class="space-y-8">
                    
                    <!-- Examination Entry Form -->
                    <div class="content-card bg-slate-50/50" x-show="activeAppointment" x-transition>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h2 class="font-outfit text-lg font-bold text-slate-900">Pemeriksaan Medis</h2>
                            <button @click="activeAppointment = null" class="text-slate-400 hover:text-slate-600 font-bold text-xs">Tutup</button>
                        </div>

                        <!-- Info Header -->
                        <div class="mb-4 bg-white p-4 rounded-xl border border-slate-200">
                            <div class="text-xs text-slate-400 uppercase tracking-wider font-bold">Kucing yang Diperiksa:</div>
                            <div class="font-outfit text-base font-bold text-slate-900 mt-0.5" x-text="activeAppointment ? activeAppointment.cat.name : ''"></div>
                            <div class="text-xs text-slate-500 mt-0.5" x-text="activeAppointment ? activeAppointment.cat.breed + ' (' + (activeAppointment.cat.gender == 'male' ? 'Jantan' : 'Betina') + ')' : ''"></div>
                        </div>

                        <form method="POST" :action="activeAppointment ? `/checkup/${activeAppointment.id}` : '#'" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Berat Badan (Kg)</label>
                                    <input type="number" step="0.01" name="weight" required class="form-input" placeholder="e.g. 3.5">
                                </div>
                                <div>
                                    <label class="form-label">Suhu Tubuh (°C)</label>
                                    <input type="number" step="0.1" name="temperature" required class="form-input" placeholder="e.g. 38.5">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Kondisi Umum</label>
                                <select name="general_condition" required class="form-input py-2">
                                    <option value="Sehat">Sehat</option>
                                    <option value="Lemas / Dehidrasi">Lemas / Dehidrasi</option>
                                    <option value="Sakit / Demam">Sakit / Demam</option>
                                    <option value="Flu Kucing / Bersin">Flu Kucing / Bersin</option>
                                    <option value="Gangguan Kulit / Jamur">Gangguan Kulit / Jamur</option>
                                </select>
                            </div>

                            <!-- Treatment Checkboxes -->
                            <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-3">
                                <span class="form-label mb-1">Tindakan & Obat Tambahan:</span>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="deworming_given" value="1" class="rounded text-teal-600 focus:ring-teal-500">
                                    Pemberian Obat Cacing (Deworming)
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="anti_flea_given" value="1" class="rounded text-teal-600 focus:ring-teal-500">
                                    Pengobatan Kutu (Anti-Flea)
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="supplement_given" value="1" class="rounded text-teal-600 focus:ring-teal-500">
                                    Pemberian Vitamin / Suplemen
                                </label>
                            </div>

                            <div>
                                <label class="form-label">Catatan Tindakan / Resep</label>
                                <textarea name="treatment_notes" rows="2" class="form-input" placeholder="Tuliskan tindakan medis noninvasif yang diberikan..."></textarea>
                            </div>

                            <div>
                                <label class="form-label">Rekomendasi Perawatan</label>
                                <textarea name="recommendation" rows="2" class="form-input" placeholder="e.g. Istirahat yang cukup, bersihkan telinga rutin..."></textarea>
                            </div>

                            <button type="submit" class="w-full button-primary flex justify-center py-2.5">
                                Simpan Hasil & Terbitkan KTAM
                            </button>
                        </form>
                    </div>

                    <!-- Placeholder when no active examine is selected -->
                    <div class="content-card text-center py-12 text-slate-400" x-show="!activeAppointment">
                        <span class="text-4xl">🩺</span>
                        <h3 class="mt-4 text-sm font-semibold text-slate-700">Pilih Kucing untuk Diperiksa</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Silakan klik tombol "Mulai Periksa" di sebelah kiri untuk mengisi lembar rekam medis kucing.</p>
                    </div>

                </div>
            </div>

            <!-- Recent Records Section -->
            <div class="content-card">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="font-outfit text-xl font-bold text-slate-900">Riwayat Pemeriksaan Terakhir Anda</h2>
                </div>

                @if($recentRecords->isEmpty())
                    <p class="text-sm text-slate-500 py-4 text-center">Belum ada pemeriksaan medis yang tersimpan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 font-bold">
                                    <th class="py-3 px-1">Kucing</th>
                                    <th class="py-3 px-1">Pemilik</th>
                                    <th class="py-3 px-1">Tanggal</th>
                                    <th class="py-3 px-1">Kondisi</th>
                                    <th class="py-3 px-1">Berat/Suhu</th>
                                    <th class="py-3 px-1">Obat Diberikan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($recentRecords as $rec)
                                    <tr>
                                        <td class="py-4 px-1 font-bold">{{ $rec->cat->name }}</td>
                                        <td class="py-4 px-1 text-slate-500">{{ $rec->cat->owner->name }}</td>
                                        <td class="py-4 px-1">{{ $rec->created_at->format('d M Y, H:i') }}</td>
                                        <td class="py-4 px-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 border border-teal-100 text-teal-800">
                                                {{ $rec->general_condition }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-1 font-mono text-xs">{{ $rec->weight }}kg / {{ $rec->temperature }}°C</td>
                                        <td class="py-4 px-1 text-xs">
                                            <ul class="list-disc list-inside">
                                                @if($rec->deworming_given) <li>Obat Cacing</li> @endif
                                                @if($rec->anti_flea_given) <li>Obat Kutu</li> @endif
                                                @if($rec->supplement_given) <li>Suplemen</li> @endif
                                                @if(!$rec->deworming_given && !$rec->anti_flea_given && !$rec->supplement_given) <span class="text-slate-400">-</span> @endif
                                            </ul>
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
</x-app-layout>
