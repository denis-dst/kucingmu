<x-app-layout>
    <div class="py-8" x-data="{ activeAppointment: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Hero Panel -->
            <div class="content-card bg-teal-900 text-white p-6 sm:p-8">
                <div class="max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-200">Portal Dokter Hewan</span>
                    <h1 class="font-outfit text-2xl sm:text-3xl font-bold text-white mt-1">
                        Selamat Bertugas, {{ Auth::user()->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-teal-100 mt-2 leading-relaxed">
                        Kelola antrian pemeriksaan fisik kucing, berikan diagnosa dan resep tindakan, lalu simpan rekam medis untuk proses verifikasi KTAM resmi.
                    </p>
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="p-4 rounded-lg bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center gap-2" role="alert">
                    <span aria-hidden="true">✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Dashboard Content Grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Left Queue Column -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Today's Examination Queue -->
                    <div class="content-card">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                            <div>
                                <h2 class="font-outfit text-lg font-bold text-slate-900">Antrian Pemeriksaan Hari Ini</h2>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::today()->format('d F Y') }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded text-xs font-bold bg-teal-100 text-teal-900 border border-teal-200">{{ $queue->count() }} Pasien</span>
                        </div>

                        @if($queue->isEmpty())
                            <div class="text-center py-10 text-slate-600 bg-slate-50 rounded-lg border border-slate-200">
                                <h3 class="text-sm font-bold text-slate-800">Tidak ada antrian pemeriksaan untuk hari ini</h3>
                                <p class="text-xs text-slate-500 mt-1">Pasien akan otomatis masuk ke daftar ini setelah relawan mengonfirmasi check-in di lokasi.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($queue as $app)
                                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50 hover:bg-white transition flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="flex items-start gap-3.5">
                                            <div class="h-11 w-11 rounded-lg bg-teal-100 text-teal-900 text-base font-bold flex items-center justify-center flex-shrink-0">
                                                {{ substr($app->cat->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $app->cat->name }}</h3>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $app->status == 'checked_in' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                                                        {{ $app->status == 'checked_in' ? 'Siap Periksa (Checked-in)' : 'Terjadwal' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-600 mt-0.5">{{ $app->cat->breed }} &bull; {{ $app->cat->gender == 'male' ? 'Jantan' : 'Betina' }}</p>
                                                <p class="text-xs text-slate-600 mt-0.5">Pemilik: <span class="font-semibold text-slate-800">{{ $app->cat->owner->name }}</span> (NBM: {{ $app->cat->owner->muhammadiyah_id ?? '-' }})</p>
                                                @if($app->notes)
                                                    <p class="text-xs text-slate-700 mt-1.5 bg-white p-2 rounded border border-slate-200">Catatan Pemilik: "{{ $app->notes }}"</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 pt-2 md:pt-0">
                                            <button type="button" @click="activeAppointment = {{ $app }}" class="w-full md:w-auto button-primary px-4 py-2 text-xs font-semibold min-h-[38px]">
                                                Buka Form Pemeriksaan
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Examination Input Column -->
                <div class="space-y-6">
                    
                    <!-- Examination Entry Form -->
                    <div class="content-card bg-slate-50" x-show="activeAppointment" x-transition>
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2.5 mb-3.5">
                            <h2 class="font-outfit text-base font-bold text-slate-900">Input Rekam Medis</h2>
                            <button type="button" @click="activeAppointment = null" class="text-slate-500 hover:text-slate-800 font-semibold text-xs p-1">Batal</button>
                        </div>

                        <!-- Info Header -->
                        <div class="mb-3.5 bg-white p-3.5 rounded-lg border border-slate-200">
                            <div class="text-[11px] text-slate-500 uppercase tracking-wide font-semibold">Pasien Kucing:</div>
                            <div class="font-outfit text-base font-bold text-slate-900 mt-0.5" x-text="activeAppointment ? activeAppointment.cat.name : ''"></div>
                            <div class="text-xs text-slate-600 mt-0.5" x-text="activeAppointment ? activeAppointment.cat.breed + ' (' + (activeAppointment.cat.gender == 'male' ? 'Jantan' : 'Betina') + ')' : ''"></div>
                        </div>

                        <form method="POST" :action="activeAppointment ? `/checkup/${activeAppointment.id}` : '#'" class="space-y-3.5">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label text-xs">Berat Badan (Kg)</label>
                                    <input type="number" step="0.01" name="weight" required class="form-input text-xs" placeholder="Contoh: 3.5">
                                </div>
                                <div>
                                    <label class="form-label text-xs">Suhu Tubuh (°C)</label>
                                    <input type="number" step="0.1" name="temperature" required class="form-input text-xs" placeholder="Contoh: 38.5">
                                </div>
                            </div>

                            <div>
                                <label class="form-label text-xs">Kondisi Umum Klinis</label>
                                <select name="general_condition" required class="form-input text-xs">
                                    <option value="Sehat">Sehat</option>
                                    <option value="Lemas / Dehidrasi">Lemas / Dehidrasi</option>
                                    <option value="Sakit / Demam">Sakit / Demam</option>
                                    <option value="Flu Kucing / Bersin">Flu Kucing / Bersin</option>
                                    <option value="Gangguan Kulit / Jamur">Gangguan Kulit / Jamur</option>
                                </select>
                            </div>

                            <!-- Treatment Checkboxes -->
                            <div class="p-3 bg-white rounded-lg border border-slate-200 space-y-2">
                                <span class="form-label text-xs mb-1 block">Tindakan Medis & Profilaksis:</span>
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer min-h-[30px]">
                                    <input type="checkbox" name="deworming_given" value="1" class="rounded text-teal-700 focus:ring-teal-700">
                                    Pemberian Obat Cacing (Deworming)
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer min-h-[30px]">
                                    <input type="checkbox" name="anti_flea_given" value="1" class="rounded text-teal-700 focus:ring-teal-700">
                                    Pengobatan Kutu (Anti-Flea)
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer min-h-[30px]">
                                    <input type="checkbox" name="supplement_given" value="1" class="rounded text-teal-700 focus:ring-teal-700">
                                    Pemberian Vitamin / Suplemen
                                </label>
                            </div>

                            <div>
                                <label class="form-label text-xs">Catatan Tindakan / Terapi</label>
                                <textarea name="treatment_notes" rows="2" class="form-input text-xs" placeholder="Tuliskan tindakan medis non-invasif yang diberikan..."></textarea>
                            </div>

                            <div>
                                <label class="form-label text-xs">Rekomendasi untuk Pemilik</label>
                                <textarea name="recommendation" rows="2" class="form-input text-xs" placeholder="Contoh: Istirahat cukup, jadwal kontrol ulang..."></textarea>
                            </div>

                            <div class="bg-teal-50 border border-teal-200 rounded-md p-2.5 text-xs text-teal-900">
                                Rekam medis yang disimpan akan otomatis masuk ke antrian verifikasi KTAM administrator.
                            </div>

                            <button type="submit" class="w-full button-primary text-xs font-semibold py-2.5">
                                Simpan Rekam Medis Pasien
                            </button>
                        </form>
                    </div>

                    <!-- Placeholder when no active examine is selected -->
                    <div class="content-card text-center py-10 text-slate-500 bg-slate-50" x-show="!activeAppointment">
                        <h3 class="text-sm font-bold text-slate-800">Pilih Pasien untuk Diperiksa</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">Klik tombol "Buka Form Pemeriksaan" pada antrian di sebelah kiri untuk mencatat hasil rekam medis.</p>
                    </div>

                </div>
            </div>

            <!-- Recent Records Section -->
            <div class="content-card">
                <div class="border-b border-slate-200 pb-3 mb-4">
                    <h2 class="font-outfit text-lg font-bold text-slate-900">Riwayat Pemeriksaan Terakhir</h2>
                </div>

                @if($recentRecords->isEmpty())
                    <p class="text-xs text-slate-500 py-6 text-center bg-slate-50 rounded-lg border border-slate-200">Belum ada pemeriksaan medis yang tersimpan di akun Anda.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs" aria-label="Riwayat Rekam Medis Terakhir">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-700 font-bold bg-slate-50">
                                    <th class="py-2.5 px-3">Kucing</th>
                                    <th class="py-2.5 px-3">Pemilik</th>
                                    <th class="py-2.5 px-3">Tanggal Periksa</th>
                                    <th class="py-2.5 px-3">Kondisi Klinis</th>
                                    <th class="py-2.5 px-3">Berat / Suhu</th>
                                    <th class="py-2.5 px-3">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-700">
                                @foreach($recentRecords as $rec)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $rec->cat->name }}</td>
                                        <td class="py-3 px-3">{{ $rec->cat->owner->name }}</td>
                                        <td class="py-3 px-3">{{ $rec->created_at->format('d M Y, H:i') }}</td>
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 border border-teal-200 text-teal-900">
                                                {{ $rec->general_condition }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 font-mono text-[11px]">{{ $rec->weight }}kg / {{ $rec->temperature }}°C</td>
                                        <td class="py-3 px-3 text-[11px]">
                                            @php
                                                $treatments = [];
                                                if ($rec->deworming_given) $treatments[] = 'Obat Cacing';
                                                if ($rec->anti_flea_given) $treatments[] = 'Obat Kutu';
                                                if ($rec->supplement_given) $treatments[] = 'Vitamin';
                                            @endphp
                                            {{ !empty($treatments) ? implode(', ', $treatments) : '-' }}
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
