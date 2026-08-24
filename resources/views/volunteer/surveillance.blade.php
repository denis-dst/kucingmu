<x-app-layout>
<style>
/* ── eSurveillance custom styles ── */
:root {
    --sur-primary: #115e59;
    --sur-accent:  #0f766e;
    --sur-accent2: #14b8a6;
    --sur-success: #047857;
    --sur-warning: #b45309;
    --sur-danger:  #be123c;
    --sur-bg:      #f8fafc;
    --sur-border:  #cbd5e1;
    --sur-muted:   #475569;
}

/* wrapper */
.sur-wrap { max-width: 860px; margin: 0 auto; padding: 0 1rem 5rem; }

/* progress bar */
.sur-prog-wrap { background: #e8edf3; height: 6px; }
.sur-prog-bar  { height: 100%; background: linear-gradient(90deg, var(--sur-accent), var(--sur-accent2)); transition: width .4s ease; border-radius: 0 3px 3px 0; }
.sur-prog-lbl  { text-align: center; font-size: 11px; color: var(--sur-muted); padding: 4px 0; background: #fff; border-bottom: 1px solid var(--sur-border); }

/* step nav */
.sur-steps-nav { display: flex; overflow-x: auto; background:#fff; border-bottom: 1px solid var(--sur-border); padding: 0 6px; gap: 0; }
.sur-step-btn  { display: flex; flex-direction: column; align-items: center; padding: 10px 12px 8px; border: none; background: none; cursor: pointer; font-size: 10px; color: var(--sur-muted); border-bottom: 3px solid transparent; white-space: nowrap; gap: 3px; transition: color .2s, border-color .2s; }
.sur-step-btn .si { font-size: 17px; }
.sur-step-btn.active { color: var(--sur-accent); border-bottom-color: var(--sur-accent); font-weight: 700; }
.sur-step-btn.done   { color: var(--sur-success); }

/* form sections */
.sur-section         { display: none; animation: surFade .3s ease; }
.sur-section.active  { display: block; }
@keyframes surFade { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

/* card */
.sur-card { background:#fff; border: 1px solid var(--sur-border); border-radius: 14px; padding: 22px; margin-bottom: 14px; box-shadow: 0 2px 6px rgba(0,0,0,.05); }
.sur-card-title { font-size: 14px; font-weight: 700; color: var(--sur-primary); display: flex; align-items: center; gap: 7px; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid var(--sur-accent2); }
.sur-card-title .si { font-size: 18px; }

/* grid */
.sur-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px,1fr)); gap: 13px; }
.sur-full  { grid-column: 1/-1; }

/* labels & inputs */
.sur-label { font-size: 11px; font-weight: 700; color: #2c3e50; letter-spacing: .2px; display: block; margin-bottom: 4px; }
.sur-label .req { color: var(--sur-danger); margin-left: 2px; }
.sur-input, .sur-select, .sur-textarea {
    width: 100%; border: 1.5px solid var(--sur-border); border-radius: 8px; padding: 8px 11px; font-size: 13px; color: #1e2a38; background: #fafdff;
    transition: border-color .2s, box-shadow .2s;
}
.sur-input:focus, .sur-select:focus, .sur-textarea:focus { outline: none; border-color: var(--sur-accent); box-shadow: 0 0 0 3px rgba(0,119,182,.12); background: #fff; }
.sur-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7a8d' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 28px; }
.sur-textarea { min-height: 70px; resize: vertical; }

/* radio/check pill groups */
.sur-radio-group, .sur-check-group { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 3px; }
.sur-radio-group label, .sur-check-group label { display: flex; align-items: center; gap: 5px; font-size: 12px; cursor: pointer; background: #f4f7fb; border: 1.5px solid var(--sur-border); border-radius: 7px; padding: 6px 10px; transition: border-color .15s; }
.sur-radio-group label:hover, .sur-check-group label:hover { border-color: var(--sur-accent); }
.sur-radio-group input[type=radio], .sur-check-group input[type=checkbox] { accent-color: var(--sur-accent); }

/* info box */
.sur-info { background: #e8f4fd; border-left: 4px solid var(--sur-accent); border-radius: 0 8px 8px 0; padding: 9px 13px; font-size: 12px; color: #1b4f72; margin-bottom: 14px; }

/* sub label */
.sur-sub { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--sur-accent); margin: 14px 0 7px; }

/* BCS buttons */
.bcs-grid { display: grid; grid-template-columns: repeat(9,1fr); gap: 5px; margin-bottom: 6px; }
.bcs-btn { height: 36px; border: 1.5px solid var(--sur-border); border-radius: 7px; background: #f8fafc; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .15s; }
.bcs-btn:hover { border-color: var(--sur-accent); color: var(--sur-accent); }
.bcs-btn.sel-low   { background: var(--sur-danger);  color:#fff; border-color: var(--sur-danger);  }
.bcs-btn.sel-ideal { background: var(--sur-success); color:#fff; border-color: var(--sur-success); }
.bcs-btn.sel-high  { background: var(--sur-warning); color:#fff; border-color: var(--sur-warning); }
#sur-bcs-desc { font-size: 11px; color: var(--sur-muted); min-height: 18px; }

/* welfare toggle */
.sur-welfare-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f3f7; gap: 10px; }
.sur-welfare-row:last-child { border-bottom: none; }
.sur-welfare-row span { font-size: 12px; flex: 1; }
.sur-tog-wrap { display: flex; gap: 5px; }
.sur-tog { padding: 4px 10px; border-radius: 6px; border: 1.5px solid var(--sur-border); font-size: 11px; font-weight: 700; cursor: pointer; background: #f4f7fb; transition: all .15s; }
.sur-tog.yes.active { background: var(--sur-danger);  border-color: var(--sur-danger);  color:#fff; }
.sur-tog.no.active  { background: var(--sur-success); border-color: var(--sur-success); color:#fff; }

/* k3l toggle (3 options) */
.k3l-tog.ada.active  { background: var(--sur-success); border-color: var(--sur-success); color:#fff; }
.k3l-tog.tidak.active{ background: var(--sur-danger);  border-color: var(--sur-danger);  color:#fff; }
.k3l-tog.proses.active{ background: var(--sur-warning); border-color: var(--sur-warning); color:#fff; }

/* likert table */
.sur-table { width:100%; border-collapse: collapse; font-size: 12px; margin-bottom: 14px; }
.sur-table th { background: var(--sur-primary); color: #fff; padding: 7px 5px; text-align: center; }
.sur-table th:first-child { text-align: left; border-radius: 8px 0 0 0; }
.sur-table th:last-child  { border-radius: 0 8px 0 0; }
.sur-table td { border: 1px solid var(--sur-border); padding: 7px 5px; vertical-align: middle; }
.sur-table tr:nth-child(even) td { background: #f8fafc; }
.sur-table td:not(:first-child) { text-align: center; }
.sur-table input[type=radio] { accent-color: var(--sur-accent); width:15px; height:15px; }

/* cat entry */
.sur-cat-entry { background: #f8fafc; border: 1px solid var(--sur-border); border-radius: 10px; padding: 14px; margin-bottom: 10px; }
.sur-cat-id    { background: #eaf4fd; border: 1.5px solid var(--sur-accent2); border-radius: 7px; padding: 6px 12px; font-family: monospace; font-size: 15px; font-weight: 700; color: var(--sur-primary); letter-spacing: 2px; }

/* nav buttons */
.sur-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; gap: 10px; }
.sur-btn { padding: 10px 20px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; }
.sur-btn-prev   { background: #edf2f7; color: var(--sur-muted); }
.sur-btn-prev:hover { background: #dde3ea; color: #1e2a38; }
.sur-btn-next   { background: var(--sur-accent); color: #fff; }
.sur-btn-next:hover { background: #005f8e; }
.sur-btn-submit { background: var(--sur-success); color: #fff; padding: 11px 28px; font-size: 14px; }
.sur-btn-submit:hover { background: #1b4332; }
.sur-divider    { border: none; border-top: 1px solid var(--sur-border); margin: 16px 0; }

/* readonly inputs */
.sur-readonly { background: #f0f4f8 !important; font-weight: 700; }

/* history table */
.sur-hist-table { width: 100%; text-align: left; font-size: 13px; border-collapse: collapse; }
.sur-hist-table th { font-size: 11px; text-transform: uppercase; color: #9ca3af; padding: 0 0 10px; font-weight: 600; }
.sur-hist-table td { padding: 10px 0; border-top: 1px solid #f1f5f9; color: #475569; }
.sur-hist-table td:first-child { font-weight: 600; color: #1e293b; }
</style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Hero --}}
            <div class="hero-card">
                <div>
                    <span class="card-kicker">eSurveillance Kucing Liar</span>
                    <h1 class="font-outfit text-3xl font-bold text-slate-900 mt-1">Laporan Surveilans Lapangan</h1>
                    <p class="card-copy max-w-2xl">Formulir 7 langkah berbasis One Health: sensus populasi, pemeriksaan fisik, parasitologi, sampling tanah, survei KAP, hingga evaluasi kebijakan K3L kampus.</p>
                </div>
                <div class="hidden md:block text-5xl">🐾</div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-semibold">✓ {{ session('success') }}</div>
            @endif

            {{-- Form card --}}
            <div class="content-card !p-0 overflow-hidden">

                {{-- Progress --}}
                <div class="sur-prog-wrap"><div class="sur-prog-bar" id="surProgBar" style="width:14%"></div></div>
                <div class="sur-prog-lbl" id="surProgLbl">Langkah 1 dari 7: Identitas Surveyor & Lokasi</div>

                {{-- Step nav --}}
                <div class="sur-steps-nav">
                    <button type="button" class="sur-step-btn active" data-step="0" onclick="surGoStep(0)"><span class="si">📋</span>Identitas</button>
                    <button type="button" class="sur-step-btn" data-step="1" onclick="surGoStep(1)"><span class="si">📷</span>Sensus Visual</button>
                    <button type="button" class="sur-step-btn" data-step="2" onclick="surGoStep(2)"><span class="si">🐈</span>Pemeriksaan Fisik</button>
                    <button type="button" class="sur-step-btn" data-step="3" onclick="surGoStep(3)"><span class="si">🔬</span>Ekto &amp; Endoparasit</button>
                    <button type="button" class="sur-step-btn" data-step="4" onclick="surGoStep(4)"><span class="si">🌱</span>Sampel Tanah</button>
                    <button type="button" class="sur-step-btn" data-step="5" onclick="surGoStep(5)"><span class="si">📊</span>KAP Civitas</button>
                    <button type="button" class="sur-step-btn" data-step="6" onclick="surGoStep(6)"><span class="si">🏢</span>K3L &amp; SOP</button>
                </div>

                <form method="POST" action="{{ route('volunteer.surveillance.store') }}" enctype="multipart/form-data" id="surForm" class="sur-wrap pt-6">
                    @csrf

                    {{-- Hidden JSON fields (populated by JS) --}}
                    <input type="hidden" name="cat_individuals"       id="hidCatIndividuals">
                    <input type="hidden" name="welfare_flags"         id="hidWelfareFlags">
                    <input type="hidden" name="kap_knowledge"         id="hidKapKnowledge">
                    <input type="hidden" name="kap_attitude"          id="hidKapAttitude">
                    <input type="hidden" name="k3l_document_checklist" id="hidK3LChecklist">

                    {{-- ═══════════════ STEP 0: IDENTITAS ═══════════════ --}}
                    <div class="sur-section active" id="surStep0">
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">📋</span> Identitas Surveyor &amp; Lokasi</div>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label" for="surveyed_at">Tanggal Survei <span class="req">*</span></label>
                                    <input class="sur-input" type="date" id="surveyed_at" name="surveyed_at" value="{{ old('surveyed_at', date('Y-m-d')) }}" required>
                                    @error('surveyed_at')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="sur-label" for="start_time">Waktu Mulai</label>
                                    <input class="sur-input" type="time" id="start_time" name="start_time" value="{{ old('start_time') }}">
                                </div>
                                <div>
                                    <label class="sur-label" for="surveyor_name">Nama Surveyor</label>
                                    <input class="sur-input" type="text" id="surveyor_name" name="surveyor_name" value="{{ old('surveyor_name', Auth::user()->name) }}" placeholder="Nama lengkap">
                                </div>
                                <div>
                                    <label class="sur-label" for="surveyor_role">Profesi / Peran</label>
                                    <select class="sur-select" id="surveyor_role" name="surveyor_role">
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Peneliti','Dokter Hewan','Mahasiswa','Staf K3L Kampus','Tenaga Kependidikan','Lainnya'] as $r)
                                            <option value="{{ $r }}" @selected(old('surveyor_role')===$r)>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="sur-label" for="institution">Nama Institusi / Kampus <span class="req">*</span></label>
                                    <select class="sur-select" id="institution" name="institution">
                                        <option value="">-- Pilih Institusi --</option>
                                        @foreach(['PP Muhammadiyah (Pusat)','Universitas Muhammadiyah Yogyakarta (UMY)','Universitas Ahmad Dahlan (UAD)','Universitas Muhammadiyah Purwokerto (UMP)','Universitas Muhammadiyah Surakarta (UMS)','STIKES Muhammadiyah','Lainnya (PTMA)'] as $inst)
                                            <option value="{{ $inst }}" @selected(old('institution')===$inst)>{{ $inst }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="sur-label" for="campus_location">Detail Lokasi / Gedung <span class="req">*</span></label>
                                    <input class="sur-input" type="text" id="campus_location" name="campus_location" value="{{ old('campus_location') }}" required placeholder="Contoh: UMY Kampus Terpadu">
                                    @error('campus_location')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="sur-label" for="zone">Zona / Area Kampus <span class="req">*</span></label>
                                    <select class="sur-select" id="zone" name="zone" required>
                                        <option value="">-- Pilih Zona --</option>
                                        @foreach(['Area Kantin / Food Court','Taman / Ruang Terbuka Hijau','Parkiran','Gedung Perkuliahan','Asrama / Dormitory','Masjid / Mushola Kampus','Area Tempat Sampah','Koridor / Selasar','Laboratorium / RS Kampus','Lainnya'] as $z)
                                            <option value="{{ $z }}" @selected(old('zone')===$z)>{{ $z }}</option>
                                        @endforeach
                                    </select>
                                    @error('zone')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Koordinat Lokasi (GPS)</label>
                                    {{-- Auto-tag button --}}
                                    <button type="button" id="btnGetLoc" onclick="surGetLocation()"
                                        style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:9px;border:1.5px solid var(--sur-accent);background:#eaf4fd;color:var(--sur-accent);font-size:13px;font-weight:700;cursor:pointer;margin-bottom:10px;transition:all .2s;"
                                        onmouseover="this.style.background='#d0e9fb'" onmouseout="this.style.background='#eaf4fd'">
                                        <span id="locBtnIcon" style="font-size:16px;">📍</span>
                                        <span id="locBtnText">Dapatkan Lokasi Saat Ini</span>
                                    </button>

                                    {{-- Status badge --}}
                                    <div id="locStatus" style="display:none;align-items:center;gap:7px;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:600;margin-bottom:10px;"></div>

                                    {{-- Lat / Lng inputs side by side --}}
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                        <div>
                                            <label class="sur-label" for="latitude">Lintang (Latitude)</label>
                                            <input class="sur-input" type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="-7.xxxxx">
                                        </div>
                                        <div>
                                            <label class="sur-label" for="longitude">Bujur (Longitude)</label>
                                            <input class="sur-input" type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="110.xxxxx">
                                        </div>
                                    </div>

                                    {{-- Map preview link (shown after successful geolocation) --}}
                                    <div id="locMapLink" style="display:none;margin-top:8px;">
                                        <a id="locMapAnchor" href="#" target="_blank" rel="noopener"
                                            style="font-size:12px;color:var(--sur-accent);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                                            🗺️ Lihat di Google Maps
                                        </a>
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Kondisi Cuaca Saat Survei</label>
                                    <div class="sur-radio-group">
                                        @foreach(['cerah'=>'☀️ Cerah','berawan'=>'⛅ Berawan','hujan ringan'=>'🌦️ Hujan Ringan','hujan lebat'=>'🌧️ Hujan Lebat'] as $val=>$lbl)
                                            <label><input type="radio" name="weather" value="{{ $val }}" @checked(old('weather')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label" for="weather_notes">Catatan Khusus Lokasi</label>
                                    <textarea class="sur-textarea" id="weather_notes" name="weather_notes" placeholder="Kondisi khusus, aksesibilitas, dsb...">{{ old('weather_notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="sur-nav">
                            <span></span>
                            <button type="button" class="sur-btn sur-btn-next" onclick="surNextStep()">Lanjut → Sensus Visual</button>
                        </div>
                    </div>

                    {{-- ═══════════════ STEP 1: SENSUS VISUAL ═══════════════ --}}
                    <div class="sur-section" id="surStep1">
                        <div class="sur-info">📷 <strong>Metode Photographic Mark-Resight:</strong> Catat setiap kucing unik yang dijumpai. Identifikasi berdasarkan pola warna bulu, bentuk ekor, jenis kelamin, dan tanda khusus.</div>
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">📷</span> Data Sensus Visual Kucing</div>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label" for="observation_session">Sesi Pengamatan <span class="req">*</span></label>
                                    <select class="sur-select" id="observation_session" name="observation_session">
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Sesi 1 (Marking)','Sesi 2 (Resight)','Sesi 3 (Resight)','Sesi 4 (Resight)','Sesi 5 (Resight)'] as $s)
                                            <option value="{{ $s }}" @selected(old('observation_session')===$s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="sur-label" for="observation_time_range">Waktu Pengamatan</label>
                                    <select class="sur-select" id="observation_time_range" name="observation_time_range">
                                        @foreach(['Pagi (06.00–09.00)','Siang (11.00–13.00)','Sore (16.00–18.00)','Malam (18.00–21.00)'] as $t)
                                            <option value="{{ $t }}" @selected(old('observation_time_range')===$t)>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="sur-label" for="cats_observed">Jumlah Kucing Dijumpai (C) <span class="req">*</span></label>
                                    <input class="sur-input" type="number" id="cats_observed" name="cats_observed" min="0" value="{{ old('cats_observed', 0) }}" required>
                                    @error('cats_observed')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="sur-label" for="cats_resighted">Jumlah Kucing Resighted (R)</label>
                                    <input class="sur-input" type="number" id="cats_resighted" name="cats_resighted" min="0" value="{{ old('cats_resighted', 0) }}">
                                </div>
                                <div>
                                    <label class="sur-label" for="cats_with_ear_tip">Kucing Bertanda Ear-Tip</label>
                                    <input class="sur-input" type="number" id="cats_with_ear_tip" name="cats_with_ear_tip" min="0" value="{{ old('cats_with_ear_tip', 0) }}">
                                </div>
                                <div>
                                    <label class="sur-label" for="cats_needing_attention">Kucing Perlu Perhatian</label>
                                    <input class="sur-input" type="number" id="cats_needing_attention" name="cats_needing_attention" min="0" value="{{ old('cats_needing_attention', 0) }}">
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label" for="food_source">Sumber Pakan di Area</label>
                                    <input class="sur-input" type="text" id="food_source" name="food_source" value="{{ old('food_source') }}" placeholder="Contoh: sisa kantin / pemberian warga">
                                </div>
                            </div>

                            <hr class="sur-divider">
                            <p class="sur-sub">Data Individu Kucing yang Dijumpai</p>

                            <div id="surCatList">
                                {{-- first cat entry --}}
                                <div class="sur-cat-entry">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                        <strong style="font-size:13px;color:var(--sur-primary);">🐱 Kucing #1</strong>
                                        <span class="sur-cat-id">KUCING-001</span>
                                    </div>
                                    <div class="sur-grid">
                                        <div><label class="sur-label">Nama Kucing</label><input type="text" class="sur-input cat-name" placeholder="Nama panggilan / label"></div>
                                        <div><label class="sur-label">Jenis Kelamin</label>
                                            <select class="sur-select cat-gender">
                                                <option>Jantan</option><option>Betina</option><option>Tidak Diketahui</option>
                                            </select>
                                        </div>
                                        <div><label class="sur-label">Perkiraan Usia</label>
                                            <select class="sur-select cat-age">
                                                <option>Kitten (&lt;4 bulan)</option><option>Remaja (4–12 bulan)</option><option>Dewasa (&gt;1 tahun)</option>
                                            </select>
                                        </div>
                                        <div><label class="sur-label">Warna Bulu Dominan</label>
                                            <select class="sur-select cat-color">
                                                <option>Oranye / Tabby</option><option>Hitam</option><option>Putih</option><option>Abu-abu</option><option>Belang Tiga (Calico)</option><option>Hitam-Putih (Bicolor)</option><option>Cokelat / Cream</option><option>Lainnya</option>
                                            </select>
                                        </div>
                                        <div><label class="sur-label">Pola Bulu</label>
                                            <select class="sur-select cat-pattern">
                                                <option>Solid</option><option>Tabby (Belang)</option><option>Bicolor</option><option>Tricolor / Calico</option><option>Tortoiseshell</option><option>Lainnya</option>
                                            </select>
                                        </div>
                                        <div><label class="sur-label">Bentuk Ekor</label>
                                            <select class="sur-select cat-tail">
                                                <option>Normal / Panjang</option><option>Pendek / Bobtail</option><option>Bengkok</option><option>Tidak Ada</option>
                                            </select>
                                        </div>
                                        <div><label class="sur-label">Tanda Ear-Tip</label>
                                            <select class="sur-select cat-eartip">
                                                <option>Tidak Ada</option><option>Ear-tip Kiri</option><option>Ear-tip Kanan</option>
                                            </select>
                                        </div>
                                        <div class="sur-full"><label class="sur-label">Tanda Khusus / Ciri Pembeda</label><input type="text" class="sur-input cat-mark" placeholder="Bekas luka, bercak, dsb."></div>
                                        <div>
                                            <label class="sur-label">Status Resight?</label>
                                            <div class="sur-radio-group">
                                                <label><input type="radio" class="cat-resight" name="resight_1" value="Baru" checked> Baru</label>
                                                <label><input type="radio" class="cat-resight" name="resight_1" value="Resighted"> Resighted</label>
                                            </div>
                                        </div>
                                        <div><label class="sur-label">Foto Dokumentasi</label><input type="file" accept="image/*" capture="environment" class="cat-photo sur-input" style="padding:4px;font-size:11px;"></div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="surAddCat()"
                                style="background:#eaf4fd;color:var(--sur-accent);border:1.5px dashed var(--sur-accent);border-radius:8px;padding:9px;width:100%;font-weight:600;font-size:13px;cursor:pointer;margin-top:4px;">
                                ＋ Tambah Data Kucing
                            </button>
                        </div>
                        <div class="sur-nav">
                            <button type="button" class="sur-btn sur-btn-prev" onclick="surPrevStep()">← Kembali</button>
                            <button type="button" class="sur-btn sur-btn-next" onclick="surNextStep()">Lanjut → Pemeriksaan Fisik</button>
                        </div>
                    </div>

                    {{-- ═══════════════ STEP 2: PEMERIKSAAN FISIK ═══════════════ --}}
                    <div class="sur-section" id="surStep2">
                        <div class="sur-info">🔭 Pemeriksaan fisik dilakukan oleh/di bawah pengawasan <strong>Dokter Hewan Berizin</strong>. Gunakan APD standar: sarung tangan anti-gigitan, masker N95, jas lab.</div>

                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🏷️</span> Identitas Kucing yang Diperiksa</div>
                            <div class="sur-grid">
                                <div><label class="sur-label" for="physical_cat_id">ID Kucing</label><input class="sur-input" type="text" id="physical_cat_id" name="physical_cat_id" value="{{ old('physical_cat_id') }}" placeholder="KUCING-001"></div>
                                <div><label class="sur-label" for="physical_cat_name">Nama Kucing</label><input class="sur-input" type="text" id="physical_cat_name" name="physical_cat_name" value="{{ old('physical_cat_name') }}" placeholder="Nama panggilan"></div>
                                <div><label class="sur-label" for="examining_vet">Pemeriksa (Dokter Hewan)</label><input class="sur-input" type="text" id="examining_vet" name="examining_vet" value="{{ old('examining_vet') }}" placeholder="Nama drh."></div>
                                <div><label class="sur-label" for="physical_exam_date">Tanggal Pemeriksaan</label><input class="sur-input" type="date" id="physical_exam_date" name="physical_exam_date" value="{{ old('physical_exam_date') }}"></div>
                                <div class="sur-full">
                                    <label class="sur-label">Status Sterilisasi</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Sudah (Ear-tip)'=>'✂️ Sudah (Ear-tip)','Belum'=>'❌ Belum','Tidak Diketahui'=>'❓ Tidak Diketahui'] as $val=>$lbl)
                                            <label><input type="radio" name="sterilization_status" value="{{ $val }}" @checked(old('sterilization_status')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div><label class="sur-label" for="capture_method">Metode Penangkapan</label>
                                    <select class="sur-select" id="capture_method" name="capture_method">
                                        @foreach(['Humane Live-Trap Cage','Penangkapan Manual (Handgrip)','Drop Trap','Lainnya'] as $m)
                                            <option value="{{ $m }}" @selected(old('capture_method')===$m)>{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">💓</span> Tanda-Tanda Vital</div>
                            <div class="sur-grid">
                                <div><label class="sur-label" for="body_weight_kg">Berat Badan (kg)</label><input class="sur-input" type="number" id="body_weight_kg" name="body_weight_kg" step="0.1" min="0" value="{{ old('body_weight_kg') }}" placeholder="0.0"></div>
                                <div><label class="sur-label" for="rectal_temp_c">Suhu Rektal (°C)</label><input class="sur-input" type="number" id="rectal_temp_c" name="rectal_temp_c" step="0.1" min="35" max="42" value="{{ old('rectal_temp_c') }}" placeholder="38.0–39.2 normal"></div>
                                <div><label class="sur-label" for="heart_rate">Denyut Jantung (kali/mnt)</label><input class="sur-input" type="number" id="heart_rate" name="heart_rate" min="0" value="{{ old('heart_rate') }}" placeholder="120–140 normal"></div>
                                <div><label class="sur-label" for="resp_rate">Frekuensi Napas (kali/mnt)</label><input class="sur-input" type="number" id="resp_rate" name="resp_rate" min="0" value="{{ old('resp_rate') }}" placeholder="20–30 normal"></div>
                                <div><label class="sur-label" for="dehydration_status">Kondisi Dehidrasi</label>
                                    <select class="sur-select" id="dehydration_status" name="dehydration_status">
                                        @foreach(['Normal (<5%)','Ringan (5–7%)','Sedang (8–10%)','Berat (>10%)'] as $d)
                                            <option value="{{ $d }}" @selected(old('dehydration_status')===$d)>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label class="sur-label" for="consciousness_level">Tingkat Kesadaran</label>
                                    <select class="sur-select" id="consciousness_level" name="consciousness_level">
                                        @foreach(['Alert (Sadar Penuh)','Waspada / Agresif','Lemah / Letargi','Stupor / Tidak Responsif'] as $c)
                                            <option value="{{ $c }}" @selected(old('consciousness_level')===$c)>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">⚖️</span> Body Condition Score (BCS) - Skala Purina 1-9</div>
                            <p class="sur-sub">Pilih Nilai BCS</p>
                            <div class="bcs-grid" id="surBcsGrid"></div>
                            <div id="surBcsDesc" class="text-xs" style="color:var(--sur-muted);min-height:18px;"></div>
                            <input type="hidden" name="bcs_score" id="hidBcsScore" value="{{ old('bcs_score') }}">
                        </div>

                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🔍</span> Pemeriksaan Sistem Organ</div>

                            <p class="sur-sub">1. Kepala &amp; Mata</p>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Kondisi Mata</label>
                                    <div class="sur-check-group">
                                        @foreach(['Normal','Sekret / Discharge','Konjungtivitis','Kekeruhan Kornea','Anisokoria'] as $v)
                                            <label><input type="checkbox" name="eye_condition[]" value="{{ $v }}" @checked(is_array(old('eye_condition')) && in_array($v,old('eye_condition',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Warna Selaput Lendir (Mukosa)</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Pink (Normal)'=>'🟢 Pink (Normal)','Pucat'=>'⚪ Pucat','Ikterik (Kuning)'=>'🟡 Ikterik','Sianotik (Biru)'=>'🔵 Sianotik'] as $val=>$lbl)
                                            <label><input type="radio" name="mucosa_color" value="{{ $val }}" @checked(old('mucosa_color')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <p class="sur-sub" style="margin-top:14px;">2. Telinga</p>
                            <div class="sur-check-group">
                                @foreach(['Normal','Serumen Berlebih','Otitis','Ear Mites','Luka / Hematoma'] as $v)
                                    <label><input type="checkbox" name="ear_condition[]" value="{{ $v }}" @checked(is_array(old('ear_condition')) && in_array($v,old('ear_condition',[])))> {{ $v }}</label>
                                @endforeach
                            </div>

                            <p class="sur-sub" style="margin-top:14px;">3. Hidung &amp; Mulut</p>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Kondisi Hidung</label>
                                    <div class="sur-check-group">
                                        @foreach(['Normal','Discharge Serosa','Discharge Mukopurulen','Epistaksis'] as $v)
                                            <label><input type="checkbox" name="nose_condition[]" value="{{ $v }}" @checked(is_array(old('nose_condition')) && in_array($v,old('nose_condition',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Kondisi Rongga Mulut</label>
                                    <div class="sur-check-group">
                                        @foreach(['Normal','Karang Gigi (Tartar)','Gingivitis','Stomatitis','Gigi Patah/Hilang','Ulserasi'] as $v)
                                            <label><input type="checkbox" name="mouth_condition[]" value="{{ $v }}" @checked(is_array(old('mouth_condition')) && in_array($v,old('mouth_condition',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <p class="sur-sub" style="margin-top:14px;">4. Kulit &amp; Bulu</p>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Kondisi Bulu</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Baik (Berkilau)'=>'✅ Baik (Berkilau)','Kusam / Kotor'=>'⚠️ Kusam/Kotor','Kusut / Menggumpal'=>'❌ Kusut/Menggumpal'] as $val=>$lbl)
                                            <label><input type="radio" name="coat_condition" value="{{ $val }}" @checked(old('coat_condition')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Kelainan Kulit</label>
                                    <div class="sur-check-group">
                                        @foreach(['Normal','Alopecia','Dermatitis','Luka Terbuka','Abses','Scabies / Mange','Ringworm'] as $v)
                                            <label><input type="checkbox" name="skin_condition[]" value="{{ $v }}" @checked(is_array(old('skin_condition')) && in_array($v,old('skin_condition',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <p class="sur-sub" style="margin-top:14px;">5. Muskuloskeletal &amp; Postur</p>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Postur &amp; Gaya Berjalan</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Normal','Pincang','Ataksia','Paralisis'] as $v)
                                            <label><input type="radio" name="posture_gait" value="{{ $v }}" @checked(old('posture_gait')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Kelainan Muskuloskeletal</label>
                                    <div class="sur-check-group">
                                        @foreach(['Normal','Patah Tulang','Deformitas Anggota Gerak','Ekor Patah/Putus','Pembengkakan Sendi'] as $v)
                                            <label><input type="checkbox" name="musculoskeletal[]" value="{{ $v }}" @checked(is_array(old('musculoskeletal')) && in_array($v,old('musculoskeletal',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <p class="sur-sub" style="margin-top:14px;">6. Abdomen &amp; Organ Dalam</p>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Kondisi Abdomen (Palpasi)</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Normal (Tidak Nyeri)'=>'Normal','Distensi / Membuncit'=>'Distensi','Nyeri saat Palpasi'=>'Nyeri','Massa Teraba'=>'Massa Teraba'] as $val=>$lbl)
                                            <label><input type="radio" name="abdomen_condition" value="{{ $val }}" @checked(old('abdomen_condition')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Kelenjar Limfe</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Normal','Membesar (Limfadenopati)'] as $v)
                                            <label><input type="radio" name="lymph_nodes" value="{{ $v }}" @checked(old('lymph_nodes')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <p class="sur-sub" style="margin-top:14px;">7. Sistem Reproduksi</p>
                            <div class="sur-check-group">
                                @foreach(['Normal','Discharge Abnormal','Pyometra (Betina)','Dugaan Bunting','Mammae Membesar'] as $v)
                                    <label><input type="checkbox" name="reproductive_condition[]" value="{{ $v }}" @checked(is_array(old('reproductive_condition')) && in_array($v,old('reproductive_condition',[])))> {{ $v }}</label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Welfare Index --}}
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🛡️</span> Welfare Index - Penilaian Kesejahteraan Hewan</div>
                            <p style="font-size:12px;color:var(--sur-muted);margin-bottom:10px;">Pilih <strong>"YA"</strong> jika gejala/kondisi berikut <u>ditemukan</u>. Skor dihitung otomatis.</p>
                            <div id="surWelfareList"></div>
                            <hr class="sur-divider">
                            <div class="sur-grid">
                                <div><label class="sur-label">Skor Welfare Total (0–8)</label><input class="sur-input sur-readonly" type="number" id="surWelfareScore" name="welfare_score" readonly placeholder="Otomatis"></div>
                                <div><label class="sur-label">Status Welfare</label><input class="sur-input sur-readonly" type="text" id="surWelfareStatus" name="welfare_status" readonly></div>
                            </div>
                        </div>

                        {{-- Kesimpulan --}}
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">📝</span> Kesimpulan &amp; Rekomendasi Klinis</div>
                            <div class="sur-grid">
                                <div><label class="sur-label" for="diagnosis_presumptif">Diagnosa Presumptif</label><input class="sur-input" type="text" id="diagnosis_presumptif" name="diagnosis_presumptif" value="{{ old('diagnosis_presumptif') }}" placeholder="Diagnosa sementara (jika ada)"></div>
                                <div class="sur-full">
                                    <label class="sur-label">Tindak Lanjut yang Direkomendasikan</label>
                                    <div class="sur-check-group">
                                        @foreach(['Perawatan Luka','Pengobatan Antiparasit','Vaksinasi','Sterilisasi TNRM','Rujuk RSH','Isolasi / Karantina','Tidak Diperlukan'] as $v)
                                            <label><input type="checkbox" name="follow_up_actions[]" value="{{ $v }}" @checked(is_array(old('follow_up_actions')) && in_array($v,old('follow_up_actions',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label" for="clinical_notes">Catatan Klinis Tambahan</label>
                                    <textarea class="sur-textarea" id="clinical_notes" name="clinical_notes" placeholder="Deskripsi kondisi fisik lebih lanjut, temuan anomali, tindakan yang sudah dilakukan...">{{ old('clinical_notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="sur-nav">
                            <button type="button" class="sur-btn sur-btn-prev" onclick="surPrevStep()">← Kembali</button>
                            <button type="button" class="sur-btn sur-btn-next" onclick="surNextStep()">Lanjut → Parasitologi</button>
                        </div>
                    </div>

                    {{-- ═══════════════ STEP 3: PARASIT ═══════════════ --}}
                    <div class="sur-section" id="surStep3">
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🔬</span> Pemeriksaan Ektoparasit (Comb Test)</div>
                            <div style="margin-bottom:12px;">
                                <label class="sur-label" for="ectoparasite_cat_id">ID Kucing yang Diperiksa</label>
                                <input class="sur-input" type="text" id="ectoparasite_cat_id" name="ectoparasite_cat_id" value="{{ old('ectoparasite_cat_id') }}" placeholder="KUCING-001">
                            </div>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Hasil Comb Test</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Negatif'=>'✅ Negatif','Positif'=>'❌ Positif'] as $val=>$lbl)
                                            <label><input type="radio" name="comb_test_result" value="{{ $val }}" @checked(old('comb_test_result')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div><label class="sur-label" for="flea_count">Jumlah Pinjal Ditemukan</label><input class="sur-input" type="number" id="flea_count" name="flea_count" min="0" value="{{ old('flea_count') }}" placeholder="Jumlah (ekor)"></div>
                                <div>
                                    <label class="sur-label">Spesies Ektoparasit</label>
                                    <div class="sur-check-group">
                                        @foreach(['Ctenocephalides felis'=>'C. felis (pinjal)','Ixodes sp.'=>'Ixodes sp. (caplak)','Demodex sp.'=>'Demodex sp.','Lainnya'=>'Lainnya'] as $val=>$lbl)
                                            <label><input type="checkbox" name="ectoparasite_species[]" value="{{ $val }}" @checked(is_array(old('ectoparasite_species')) && in_array($val,old('ectoparasite_species',[])))> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div><label class="sur-label" for="ectoparasite_method">Metode Konfirmasi</label>
                                    <select class="sur-select" id="ectoparasite_method" name="ectoparasite_method">
                                        @foreach(['Identifikasi Visual Lapangan','Identifikasi Mikroskopis (Lab)','Keduanya'] as $m)
                                            <option value="{{ $m }}" @selected(old('ectoparasite_method')===$m)>{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sur-full"><label class="sur-label" for="ectoparasite_notes">Keterangan</label><textarea class="sur-textarea" id="ectoparasite_notes" name="ectoparasite_notes" placeholder="Area tubuh ditemukan, intensitas infestasi, dsb...">{{ old('ectoparasite_notes') }}</textarea></div>
                            </div>
                        </div>

                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🧫</span> Pemeriksaan Endoparasit Feses</div>
                            <div class="sur-grid">
                                <div>
                                    <label class="sur-label">Metode Sampling Feses</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Swab Rektal','Feses Segar'] as $v)
                                            <label><input type="radio" name="feces_collection_method" value="{{ $v }}" @checked(old('feces_collection_method')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div><label class="sur-label" for="feces_preservation">Media Preservasi</label>
                                    <select class="sur-select" id="feces_preservation" name="feces_preservation">
                                        @foreach(['Formalin 10%','SAF (Sodium Acetate Formalin)','PBS (Phosphate Buffered Saline)'] as $p)
                                            <option value="{{ $p }}" @selected(old('feces_preservation')===$p)>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="sur-label">Metode Pemeriksaan Lab</label>
                                    <div class="sur-check-group">
                                        @foreach(['Flotasi ZnSO4','Sedimentasi','Natif'] as $v)
                                            <label><input type="checkbox" name="lab_exam_method[]" value="{{ $v }}" @checked(is_array(old('lab_exam_method')) && in_array($v,old('lab_exam_method',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Hasil Endoparasit</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Negatif'=>'✅ Negatif','Positif'=>'❌ Positif','Pending Lab'=>'⏳ Pending Lab'] as $val=>$lbl)
                                            <label><input type="radio" name="endoparasite_result" value="{{ $val }}" @checked(old('endoparasite_result')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Agen Zoonosis Ditemukan</label>
                                    <div class="sur-check-group">
                                        @foreach(['Toxocara cati','Ancylostoma tubaeforme','Toxoplasma gondii','Dipylidium caninum','Giardia sp.','Cryptosporidium sp.','Lainnya'] as $v)
                                            <label><input type="checkbox" name="zoonotic_agents[]" value="{{ $v }}" @checked(is_array(old('zoonotic_agents')) && in_array($v,old('zoonotic_agents',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full"><label class="sur-label" for="endoparasite_notes">Catatan Laboratorium</label><textarea class="sur-textarea" id="endoparasite_notes" name="endoparasite_notes" placeholder="Jumlah telur/lapang pandang, intensitas infeksi, dsb...">{{ old('endoparasite_notes') }}</textarea></div>
                            </div>
                        </div>

                        <div class="sur-nav">
                            <button type="button" class="sur-btn sur-btn-prev" onclick="surPrevStep()">← Kembali</button>
                            <button type="button" class="sur-btn sur-btn-next" onclick="surNextStep()">Lanjut → Sampel Tanah</button>
                        </div>
                    </div>

                    {{-- ═══════════════ STEP 4: TANAH ═══════════════ --}}
                    <div class="sur-section" id="surStep4">
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🌱</span> Sampling Tanah / Pasir Lingkungan Kampus</div>
                            <div class="sur-info" style="margin-bottom:14px;">📍 Ambil 50 gram tanah dari kedalaman 0–5 cm pada area berisiko tinggi. Gunakan metode Flotasi Sentrifugasi ZnSO₄ (SG 1.20) di laboratorium.</div>
                            <div class="sur-grid">
                                <div><label class="sur-label" for="soil_sample_code">Kode Sampel Tanah</label><input class="sur-input" type="text" id="soil_sample_code" name="soil_sample_code" value="{{ old('soil_sample_code') }}" placeholder="UMY-T-001"></div>
                                <div><label class="sur-label" for="soil_sampling_area">Jenis Area Sampling</label>
                                    <select class="sur-select" id="soil_sampling_area" name="soil_sampling_area">
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Sekitar Tempat Sampah Kantin','Taman / Pasir Terbuka','Area Masjid / Mushola','Area Parkir','Sandbox / Kotoran Kucing Terlihat','Koridor Bangunan','Lainnya'] as $a)
                                            <option value="{{ $a }}" @selected(old('soil_sampling_area')===$a)>{{ $a }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Koordinat Titik Sampling (GPS)</label>
                                    {{-- Auto-tag button --}}
                                    <button type="button" id="soilBtnGetLoc" onclick="soilGetLocation()"
                                        style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:9px;border:1.5px solid var(--sur-accent);background:#eaf4fd;color:var(--sur-accent);font-size:13px;font-weight:700;cursor:pointer;margin-bottom:10px;transition:all .2s;"
                                        onmouseover="this.style.background='#d0e9fb'" onmouseout="this.style.background='#eaf4fd'">
                                        <span id="soilLocBtnIcon" style="font-size:16px;">📍</span>
                                        <span id="soilLocBtnText">Dapatkan Koordinat Sampling</span>
                                    </button>

                                    {{-- Status badge --}}
                                    <div id="soilLocStatus" style="display:none;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:600;margin-bottom:10px;"></div>

                                    {{-- Lat / Lng inputs side by side --}}
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                        <div>
                                            <label class="sur-label" for="soil_lat">Lintang (Latitude)</label>
                                            <input class="sur-input" type="number" step="any" id="soil_lat" name="soil_lat" value="{{ old('soil_lat') }}" placeholder="-7.xxxxx">
                                        </div>
                                        <div>
                                            <label class="sur-label" for="soil_lng">Bujur (Longitude)</label>
                                            <input class="sur-input" type="number" step="any" id="soil_lng" name="soil_lng" value="{{ old('soil_lng') }}" placeholder="110.xxxxx">
                                        </div>
                                    </div>

                                    {{-- Map preview link --}}
                                    <div id="soilLocMapLink" style="display:none;margin-top:8px;">
                                        <a id="soilLocMapAnchor" href="#" target="_blank" rel="noopener"
                                            style="font-size:12px;color:var(--sur-accent);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                                            🗺️ Lihat di Google Maps
                                        </a>
                                    </div>
                                </div>
                                <div><label class="sur-label" for="soil_weight_g">Berat Sampel (gram)</label><input class="sur-input" type="number" id="soil_weight_g" name="soil_weight_g" value="{{ old('soil_weight_g', 50) }}" min="10" max="200"></div>
                                <div><label class="sur-label" for="soil_depth_cm">Kedalaman Sampling (cm)</label><input class="sur-input" type="number" id="soil_depth_cm" name="soil_depth_cm" value="{{ old('soil_depth_cm', 5) }}" min="1" max="20"></div>
                                <div>
                                    <label class="sur-label">Kondisi Tanah</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Kering','Lembap','Basah'] as $v)
                                            <label><input type="radio" name="soil_condition" value="{{ $v }}" @checked(old('soil_condition')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="sur-label">Indikasi Visual Feses Kucing</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Ya','Tidak'] as $v)
                                            <label><input type="radio" name="feces_visual_indicator" value="{{ $v }}" @checked(old('feces_visual_indicator')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <hr class="sur-divider sur-full">
                                <div>
                                    <label class="sur-label">Hasil Pemeriksaan Lab Tanah</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Negatif'=>'✅ Negatif','Positif'=>'❌ Positif','Pending'=>'⏳ Pending'] as $val=>$lbl)
                                            <label><input type="radio" name="soil_lab_result" value="{{ $val }}" @checked(old('soil_lab_result')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Agen Parasitik pada Tanah</label>
                                    <div class="sur-check-group">
                                        @foreach(['Telur Toxocara cati','Telur Ancylostoma spp.','Ookista Toxoplasma gondii','Lainnya'] as $v)
                                            <label><input type="checkbox" name="soil_parasitic_agents[]" value="{{ $v }}" @checked(is_array(old('soil_parasitic_agents')) && in_array($v,old('soil_parasitic_agents',[])))> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div><label class="sur-label" for="eggs_per_gram">Jumlah Telur per Gram (EPG)</label><input class="sur-input" type="number" id="eggs_per_gram" name="eggs_per_gram" min="0" value="{{ old('eggs_per_gram') }}" placeholder="0"></div>
                                <div class="sur-full"><label class="sur-label" for="sanitation_notes">Catatan Sanitasi Lingkungan</label><textarea class="sur-textarea" id="sanitation_notes" name="sanitation_notes" placeholder="Kondisi kebersihan sekitar, sumber kontaminasi potensial, dsb...">{{ old('sanitation_notes') }}</textarea></div>
                            </div>
                        </div>
                        <div class="sur-nav">
                            <button type="button" class="sur-btn sur-btn-prev" onclick="surPrevStep()">← Kembali</button>
                            <button type="button" class="sur-btn sur-btn-next" onclick="surNextStep()">Lanjut → Survei KAP</button>
                        </div>
                    </div>

                    {{-- ═══════════════ STEP 5: KAP ═══════════════ --}}
                    <div class="sur-section" id="surStep5">
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">📊</span> Survei KAP: Pengetahuan, Sikap, dan Perilaku Civitas Akademika</div>

                            <p class="sur-sub">Data Responden</p>
                            <div class="sur-grid">
                                <div><label class="sur-label" for="kap_respondent_status">Status Responden</label>
                                    <select class="sur-select" id="kap_respondent_status" name="kap_respondent_status">
                                        @foreach(['Mahasiswa','Dosen','Tenaga Kependidikan','Lainnya'] as $s)
                                            <option value="{{ $s }}" @selected(old('kap_respondent_status')===$s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="sur-label">Jenis Kelamin Responden</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Laki-laki','Perempuan'] as $v)
                                            <label><input type="radio" name="kap_respondent_gender" value="{{ $v }}" @checked(old('kap_respondent_gender')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div><label class="sur-label" for="kap_faculty">Fakultas / Unit Kerja</label><input class="sur-input" type="text" id="kap_faculty" name="kap_faculty" value="{{ old('kap_faculty') }}" placeholder="Fakultas / Departemen"></div>
                                <div>
                                    <label class="sur-label">Riwayat Kontak Kucing Kampus</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Ya','Tidak'] as $v)
                                            <label><input type="radio" name="kap_cat_contact" value="{{ $v }}" @checked(old('kap_cat_contact')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <hr class="sur-divider">
                            <p class="sur-sub">A. Pengetahuan (Knowledge): Skala Benar/Salah</p>
                            <table class="sur-table" style="margin-bottom:14px;">
                                <thead><tr>
                                    <th style="width:60%">Pernyataan</th>
                                    <th>Benar</th><th>Salah</th><th>Tidak Tahu</th>
                                </tr></thead>
                                <tbody id="surKnowledgeTable"></tbody>
                            </table>

                            <p class="sur-sub">B. Sikap (Attitude): Skala Likert (1=Sangat Tidak Setuju, 5=Sangat Setuju)</p>
                            <table class="sur-table" style="margin-bottom:14px;">
                                <thead><tr>
                                    <th style="width:50%">Pernyataan</th>
                                    <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>
                                </tr></thead>
                                <tbody id="surAttitudeTable"></tbody>
                            </table>

                            <p class="sur-sub">C. Perilaku (Practice)</p>
                            <div class="sur-grid">
                                <div class="sur-full">
                                    <label class="sur-label">Apakah Anda pernah memberi makan kucing liar di kampus?</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Ya, sering'=>'Ya, sering','Ya, kadang'=>'Ya, kadang-kadang','Tidak Pernah'=>'Tidak pernah'] as $val=>$lbl)
                                            <label><input type="radio" name="kap_prac_feed" value="{{ $val }}" @checked(old('kap_prac_feed')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Apakah Anda mencuci tangan setelah kontak dengan kucing kampus?</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Selalu','Kadang'=>'Kadang-kadang','Tidak Pernah'=>'Tidak pernah'] as $val=>$lbl)
                                            <label><input type="radio" name="kap_prac_handwash" value="{{ $val }}" @checked(old('kap_prac_handwash')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Pernahkah Anda/teman dicakar/digigit kucing dan melapor ke poliklinik?</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Ya, lapor'=>'Ya, dan melapor','Ya, tidak lapor'=>'Ya, tetapi tidak melapor','Tidak Pernah'=>'Tidak pernah terjadi'] as $val=>$lbl)
                                            <label><input type="radio" name="kap_prac_bite_report" value="{{ $val }}" @checked(old('kap_prac_bite_report')===$val)> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Dukungan terhadap Program TNRM (Trap-Neuter-Return-Manage)</label>
                                    <div class="sur-radio-group">
                                        @foreach(['Sangat Mendukung','Mendukung','Netral','Tidak Mendukung'] as $v)
                                            <label><input type="radio" name="kap_prac_tnrm_support" value="{{ $v }}" @checked(old('kap_prac_tnrm_support')===$v)> {{ $v }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sur-nav">
                            <button type="button" class="sur-btn sur-btn-prev" onclick="surPrevStep()">← Kembali</button>
                            <button type="button" class="sur-btn sur-btn-next" onclick="surNextStep()">Lanjut → K3L &amp; SOP</button>
                        </div>
                    </div>

                    {{-- ═══════════════ STEP 6: K3L ═══════════════ --}}
                    <div class="sur-section" id="surStep6">
                        <div class="sur-card">
                            <div class="sur-card-title"><span class="si">🏢</span> Evaluasi Kebijakan K3L &amp; SOP Kampus</div>
                            <p style="font-size:12px;color:var(--sur-muted);margin-bottom:14px;">(Diisi oleh Pengelola K3L / Staf Manajemen Kampus)</p>
                            <div class="sur-grid">
                                <div><label class="sur-label" for="k3l_informant_name">Nama Informan Kunci</label><input class="sur-input" type="text" id="k3l_informant_name" name="k3l_informant_name" value="{{ old('k3l_informant_name') }}" placeholder="Nama informan"></div>
                                <div><label class="sur-label" for="k3l_informant_role">Jabatan / Peran</label>
                                    <select class="sur-select" id="k3l_informant_role" name="k3l_informant_role">
                                        @foreach(['Kepala Biro Umum / K3L','Pengelola Kantin','Dokter / Perawat Poliklinik / RSH','Kepala Rumah Tangga Kampus','Pimpinan Institusi','Lainnya'] as $r)
                                            <option value="{{ $r }}" @selected(old('k3l_informant_role')===$r)>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr class="sur-divider">
                            <p class="sur-sub">Ketersediaan Dokumen &amp; Prosedur</p>
                            <div id="surK3LChecklist"></div>

                            <hr class="sur-divider">
                            <div class="sur-grid">
                                <div class="sur-full">
                                    <label class="sur-label">Bagaimana penanganan kucing liar saat ini di kampus?</label>
                                    <div class="sur-check-group">
                                        @foreach(['Dibiarkan'=>'Dibiarkan','Diberi Makan'=>'Diberi makan sukarela','Ditangkap-Dibuang'=>'Ditangkap lalu dibuang/dipindahkan','TNRM'=>'Program TNRM (sudah berjalan)','Eliminasi'=>'Eliminasi/Culling'] as $val=>$lbl)
                                            <label><input type="checkbox" name="k3l_current_handling[]" value="{{ $val }}" @checked(is_array(old('k3l_current_handling')) && in_array($val,old('k3l_current_handling',[])))> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full">
                                    <label class="sur-label">Hambatan dalam Pengelolaan Stray Cat di Kampus</label>
                                    <div class="sur-check-group">
                                        @foreach(['Anggaran'=>'Keterbatasan anggaran','SDM'=>'Kurang SDM terlatih','Kebijakan'=>'Belum ada regulasi/SOP','Penolakan'=>'Penolakan dari civitas','Data'=>'Tidak ada data dasar populasi','Lainnya'=>'Lainnya'] as $val=>$lbl)
                                            <label><input type="checkbox" name="k3l_obstacles[]" value="{{ $val }}" @checked(is_array(old('k3l_obstacles')) && in_array($val,old('k3l_obstacles',[])))> {{ $lbl }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sur-full"><label class="sur-label" for="k3l_intervention_plan">Rencana Intervensi ke Depan</label><textarea class="sur-textarea" id="k3l_intervention_plan" name="k3l_intervention_plan" placeholder="Deskripsikan rencana atau strategi kampus dalam mengelola stray cat...">{{ old('k3l_intervention_plan') }}</textarea></div>
                                <div class="sur-full"><label class="sur-label" for="k3l_observation_notes">Catatan Wawancara / Observasi K3L</label><textarea class="sur-textarea" id="k3l_observation_notes" name="k3l_observation_notes" placeholder="Temuan dari wawancara mendalam, observasi fasilitas, dsb...">{{ old('k3l_observation_notes') }}</textarea></div>
                            </div>

                            {{-- Foto dokumentasi --}}
                            <hr class="sur-divider">
                            <div>
                                <label class="sur-label" for="photo">Foto Dokumentasi Area <span style="color:#9ca3af;">(opsional, maks 5 MB)</span></label>
                                <input type="file" id="photo" name="photo" accept="image/*" capture="environment"
                                    class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-teal-50 file:px-4 file:py-2 file:font-semibold file:text-teal-700 hover:file:bg-teal-100 mt-1">
                                @error('photo')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Global notes --}}
                            <div style="margin-top:14px;">
                                <label class="sur-label" for="notes">Catatan Umum Laporan</label>
                                <textarea class="sur-textarea" id="notes" name="notes" placeholder="Catatan tambahan untuk keseluruhan sesi surveilans...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="sur-nav" style="flex-wrap:wrap;">
                            <button type="button" class="sur-btn sur-btn-prev" onclick="surPrevStep()">← Kembali</button>
                            <button type="submit" class="sur-btn sur-btn-submit" onclick="surCollectJSON()">✅ Simpan &amp; Kirim Data</button>
                        </div>
                    </div>

                </form>
            </div>

            {{-- ──────────── RIWAYAT LAPORAN ──────────── --}}
            <section class="content-card">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h2 class="font-outfit text-xl font-bold text-slate-900">Riwayat Laporan Saya</h2>
                    <span class="text-xs font-bold text-slate-500">{{ $surveys->total() }} laporan</span>
                </div>
                @if($surveys->isEmpty())
                    <p class="text-sm text-slate-500 text-center py-6">Belum ada laporan eSurveillance.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="sur-hist-table">
                            <thead><tr>
                                <th>Waktu</th><th>Institusi</th><th>Zona</th>
                                <th>Terlihat</th><th>Ear-tip</th><th>Perlu Bantuan</th><th>Cuaca</th><th>Aksi</th>
                            </tr></thead>
                            <tbody>
                                @foreach($surveys as $survey)
                                    <tr>
                                        <td>{{ $survey->surveyed_at->format('d M Y, H:i') }}</td>
                                        <td>{{ $survey->institution ?: $survey->campus_location }}</td>
                                        <td>{{ $survey->zone }}</td>
                                        <td>{{ $survey->cats_observed }} ekor</td>
                                        <td>{{ $survey->cats_with_ear_tip }} ekor</td>
                                        <td>{{ $survey->cats_needing_attention }} ekor</td>
                                        <td>{{ ucfirst($survey->weather ?: '-') }}</td>
                                        <td>
                                            <a href="{{ route('volunteer.surveillance.pdf', $survey->id) }}" target="_blank"
                                               style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:#e63946;color:#fff;font-size:11px;font-weight:600;text-decoration:none;transition:background .2s;"
                                               onmouseover="this.style.background='#b02030'" onmouseout="this.style.background='#e63946'">
                                                🖨️ Cetak PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $surveys->links() }}</div>
                @endif
            </section>

        </div>
    </div>

<script>
// ────────── DATA ──────────
const SUR_BCS_DESCS = {
    1:'Sangat Kurus: tulang rusuk, tulang panggul sangat menonjol, tidak ada lemak.',
    2:'Kurus: tulang rusuk mudah diraba, sedikit otot.',
    3:'Kurus-Normal: tulang rusuk teraba, pinggang terlihat.',
    4:'Ideal: tulang rusuk teraba dengan sedikit lemak, pinggang terlihat dari atas.',
    5:'Ideal Optimal: proporsional, tulang rusuk teraba, perut sedikit tertarik.',
    6:'Gemuk-Normal: lapisan lemak tipis di rusuk, pinggang kurang terlihat.',
    7:'Gemuk: lemak berlapis, pinggang tidak terlihat.',
    8:'Obesitas: lapisan lemak tebal, perut membulat.',
    9:'Obesitas Berat: massa lemak sangat besar, susah bergerak.'
};
const SUR_WELFARE_ITEMS = [
    'Luka Fisik / Cedera (luka terbuka, patah tulang)',
    'Dermatitis / Lesi Kulit (alopecia, keropeng)',
    'Keluarnya Sekret Mata atau Hidung',
    'Batuk / Bersin (Upper Respiratory Infection / URI)',
    'Kotoran Mata Berlebihan',
    'Kekurusan Ekstrem (BCS ≤ 2)',
    'Kebersihan Bulu Buruk / Mite',
    'Ketertinggalan Motorik / Pincang',
];
const SUR_KNOWLEDGE_QS = [
    'Kucing liar dapat menjadi reservoir Toxoplasma gondii yang berbahaya bagi ibu hamil.',
    'Cat Scratch Disease (CSD) disebabkan oleh Bartonella henselae yang ditularkan melalui cakaran kucing.',
    'Telur cacing Toxocara cati dapat bertahan di tanah/pasir selama berbulan-bulan.',
    'Program Trap-Neuter-Return-Manage (TNRM) lebih efektif daripada culling dalam jangka panjang.',
    'Mencuci tangan setelah kontak dengan kucing dapat mencegah penularan zoonosis.',
];
const SUR_ATTITUDE_QS = [
    'Pengelolaan stray cat di kampus adalah tanggung jawab institusi, bukan hanya individu.',
    'Memberi makan kucing liar tanpa kontrol dapat memperburuk masalah populasi.',
    'Saya mendukung penerapan program sterilisasi (TNRM) untuk kucing kampus.',
    'Kampus perlu menyediakan SOP penanganan luka gigitan/cakaran kucing.',
    'Nilai Ihsan Islam mendukung perlakuan manusiawi terhadap kucing liar.',
];
const SUR_K3L_CHECKS = [
    'SOP Penanganan Insiden Gigitan/Cakaran Hewan (Post-Exposure Prophylaxis)',
    'Data Populasi Kucing Liar (Baseline Data)',
    'SOP Pemberian Makan Kucing yang Terstandar',
    'Kerjasama dengan Dinas Peternakan / Dokter Hewan',
    'Anggaran untuk Program TNRM',
    'Pelatihan APD untuk Petugas Kebersihan',
    'Laporan Insiden Zoonosis Terdokumentasi',
    'Kebijakan Tertulis Manajemen Hewan Liar Kampus',
];

// ────────── STATE ──────────
let surCurrentStep = 0;
const SUR_TOTAL = 7;
let surCatCount  = 1;
let surWelfare   = {};   // {idx: 0|1}
let surBcsVal    = 0;
let surK3LState  = {};   // {idx: 'Ada'|'Tidak Ada'|'Dalam Proses'}
let surKnowledge = {};   // {idx: 'Benar'|'Salah'|'Tidak Tahu'}
let surAttitude  = {};   // {idx: 1..5}

// ────────── INIT ──────────
document.addEventListener('DOMContentLoaded', () => {
    buildBCSGrid();
    buildWelfareList();
    buildKnowledgeTable();
    buildAttitudeTable();
    buildK3LChecklist();
    updateProgress();
});

// ────────── BCS ──────────
function buildBCSGrid() {
    const grid = document.getElementById('surBcsGrid');
    for (let i = 1; i <= 9; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'bcs-btn';
        btn.textContent = i;
        btn.title = SUR_BCS_DESCS[i];
        btn.id = 'surBcs' + i;
        btn.onclick = () => selectBCS(i);
        grid.appendChild(btn);
    }
}
function selectBCS(v) {
    surBcsVal = v;
    document.getElementById('hidBcsScore').value = v;
    for (let i = 1; i <= 9; i++) {
        const btn = document.getElementById('surBcs' + i);
        btn.classList.remove('sel-low','sel-ideal','sel-high');
        if (i === v) btn.classList.add(i<=3?'sel-low': i<=5?'sel-ideal':'sel-high');
    }
    document.getElementById('surBcsDesc').textContent = `BCS ${v}: ${SUR_BCS_DESCS[v]}`;
}

// ────────── WELFARE ──────────
function buildWelfareList() {
    const list = document.getElementById('surWelfareList');
    SUR_WELFARE_ITEMS.forEach((item, idx) => {
        surWelfare[idx] = 0;
        const div = document.createElement('div');
        div.className = 'sur-welfare-row';
        div.innerHTML = `<span>${item}</span>
            <div class="sur-tog-wrap">
                <button type="button" class="sur-tog yes" onclick="toggleWelfare(${idx},1,this)">YA</button>
                <button type="button" class="sur-tog no active" onclick="toggleWelfare(${idx},0,this)">TIDAK</button>
            </div>`;
        list.appendChild(div);
    });
}
function toggleWelfare(idx, val, btn) {
    surWelfare[idx] = val;
    btn.closest('.sur-tog-wrap').querySelectorAll('.sur-tog').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    calcWelfare();
}
function calcWelfare() {
    const score = Object.values(surWelfare).reduce((a,b) => a+b, 0);
    document.getElementById('surWelfareScore').value = score;
    document.getElementById('surWelfareStatus').value =
        score === 0 ? '✅ Baik (0)' : score <= 2 ? '⚠️ Cukup (1–2)' : '❌ Buruk (≥3)';
}

// ────────── KAP TABLES ──────────
function buildKnowledgeTable() {
    const tbody = document.getElementById('surKnowledgeTable');
    SUR_KNOWLEDGE_QS.forEach((q, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${q}</td>
            <td><input type="radio" name="kq_${i}" value="Benar" onchange="surKnowledge[${i}]='Benar'"></td>
            <td><input type="radio" name="kq_${i}" value="Salah" onchange="surKnowledge[${i}]='Salah'"></td>
            <td><input type="radio" name="kq_${i}" value="Tidak Tahu" onchange="surKnowledge[${i}]='Tidak Tahu'"></td>`;
        tbody.appendChild(tr);
    });
}
function buildAttitudeTable() {
    const tbody = document.getElementById('surAttitudeTable');
    SUR_ATTITUDE_QS.forEach((q, i) => {
        const tr = document.createElement('tr');
        let cells = `<td>${q}</td>`;
        for (let v = 1; v <= 5; v++) {
            cells += `<td><input type="radio" name="aq_${i}" value="${v}" onchange="surAttitude[${i}]=${v}"></td>`;
        }
        tr.innerHTML = cells;
        tbody.appendChild(tr);
    });
}

// ────────── K3L CHECKLIST ──────────
function buildK3LChecklist() {
    const cont = document.getElementById('surK3LChecklist');
    SUR_K3L_CHECKS.forEach((item, i) => {
        surK3LState[i] = '';
        const div = document.createElement('div');
        div.className = 'sur-welfare-row';
        div.innerHTML = `<span style="font-size:12px;">${item}</span>
            <div class="sur-tog-wrap">
                <button type="button" class="sur-tog k3l-tog ada"    onclick="toggleK3L(${i},'Ada',this)">Ada</button>
                <button type="button" class="sur-tog k3l-tog tidak"  onclick="toggleK3L(${i},'Tidak Ada',this)">Tidak Ada</button>
                <button type="button" class="sur-tog k3l-tog proses" onclick="toggleK3L(${i},'Dalam Proses',this)">Proses</button>
            </div>`;
        cont.appendChild(div);
    });
}
function toggleK3L(idx, val, btn) {
    surK3LState[idx] = val;
    btn.closest('.sur-tog-wrap').querySelectorAll('.sur-tog').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// ────────── CAT LIST ──────────
function surAddCat() {
    surCatCount++;
    const n   = surCatCount;
    const suf = String(n).padStart(3,'0');
    const div = document.createElement('div');
    div.className = 'sur-cat-entry';
    div.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <strong style="font-size:13px;color:var(--sur-primary);">🐱 Kucing #${n}</strong>
            <span class="sur-cat-id">KUCING-${suf}</span>
        </div>
        <div class="sur-grid">
            <div><label class="sur-label">Nama Kucing</label><input type="text" class="sur-input cat-name" placeholder="Nama panggilan / label"></div>
            <div><label class="sur-label">Jenis Kelamin</label>
                <select class="sur-select cat-gender"><option>Jantan</option><option>Betina</option><option>Tidak Diketahui</option></select>
            </div>
            <div><label class="sur-label">Perkiraan Usia</label>
                <select class="sur-select cat-age"><option>Kitten (&lt;4 bulan)</option><option>Remaja (4–12 bulan)</option><option>Dewasa (&gt;1 tahun)</option></select>
            </div>
            <div><label class="sur-label">Warna Bulu Dominan</label>
                <select class="sur-select cat-color"><option>Oranye / Tabby</option><option>Hitam</option><option>Putih</option><option>Abu-abu</option><option>Belang Tiga (Calico)</option><option>Hitam-Putih (Bicolor)</option><option>Cokelat / Cream</option><option>Lainnya</option></select>
            </div>
            <div><label class="sur-label">Pola Bulu</label>
                <select class="sur-select cat-pattern"><option>Solid</option><option>Tabby (Belang)</option><option>Bicolor</option><option>Tricolor / Calico</option><option>Tortoiseshell</option><option>Lainnya</option></select>
            </div>
            <div><label class="sur-label">Bentuk Ekor</label>
                <select class="sur-select cat-tail"><option>Normal / Panjang</option><option>Pendek / Bobtail</option><option>Bengkok</option><option>Tidak Ada</option></select>
            </div>
            <div><label class="sur-label">Tanda Ear-Tip</label>
                <select class="sur-select cat-eartip"><option>Tidak Ada</option><option>Ear-tip Kiri</option><option>Ear-tip Kanan</option></select>
            </div>
            <div class="sur-full"><label class="sur-label">Tanda Khusus</label><input type="text" class="sur-input cat-mark" placeholder="Bekas luka, bercak, dsb."></div>
            <div>
                <label class="sur-label">Status Resight?</label>
                <div class="sur-radio-group">
                    <label><input type="radio" name="resight_${n}" value="Baru" checked> Baru</label>
                    <label><input type="radio" name="resight_${n}" value="Resighted"> Resighted</label>
                </div>
            </div>
            <div><label class="sur-label">Foto</label><input type="file" accept="image/*" capture="environment" class="cat-photo sur-input" style="padding:4px;font-size:11px;"></div>
        </div>`;
    document.getElementById('surCatList').appendChild(div);
}

// ────────── COLLECT JSON ──────────
function surCollectJSON() {
    // Cat individuals
    const cats = [];
    document.querySelectorAll('.sur-cat-entry').forEach((entry, i) => {
        const n = i + 1;
        const resightEl = entry.querySelector(`.cat-resight[name="resight_${n}"]:checked`) ||
                          entry.querySelector('input[type=radio]:checked');
        cats.push({
            id: 'KUCING-' + String(n).padStart(3,'0'),
            name:    (entry.querySelector('.cat-name')   ||{}).value    || '',
            gender:  (entry.querySelector('.cat-gender') ||{}).value    || '',
            age:     (entry.querySelector('.cat-age')    ||{}).value    || '',
            color:   (entry.querySelector('.cat-color')  ||{}).value    || '',
            pattern: (entry.querySelector('.cat-pattern')||{}).value    || '',
            tail:    (entry.querySelector('.cat-tail')   ||{}).value    || '',
            eartip:  (entry.querySelector('.cat-eartip') ||{}).value    || '',
            mark:    (entry.querySelector('.cat-mark')   ||{}).value    || '',
            resight: resightEl ? resightEl.value : 'Baru',
        });
    });
    document.getElementById('hidCatIndividuals').value = JSON.stringify(cats);
    document.getElementById('hidWelfareFlags').value   = JSON.stringify(surWelfare);
    document.getElementById('hidKapKnowledge').value   = JSON.stringify(surKnowledge);
    document.getElementById('hidKapAttitude').value    = JSON.stringify(surAttitude);
    document.getElementById('hidK3LChecklist').value   = JSON.stringify(surK3LState);
}

// ────────── GEOLOCATION ──────────
function surGetLocation() {
    const btn      = document.getElementById('btnGetLoc');
    const btnIcon  = document.getElementById('locBtnIcon');
    const btnText  = document.getElementById('locBtnText');
    const status   = document.getElementById('locStatus');
    const mapLink  = document.getElementById('locMapLink');
    const mapAnchor= document.getElementById('locMapAnchor');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (!navigator.geolocation) {
        showLocStatus('error', '❌ Browser tidak mendukung Geolocation API.');
        return;
    }

    // Loading state
    btn.disabled  = true;
    btnIcon.textContent = '⏳';
    btnText.textContent = 'Mendeteksi lokasi…';
    btn.style.opacity   = '0.7';
    mapLink.style.display = 'none';
    showLocStatus('info', '⏳ Meminta izin akses lokasi…');

    navigator.geolocation.getCurrentPosition(
        // ── SUCCESS ──
        (pos) => {
            const lat = pos.coords.latitude.toFixed(7);
            const lng = pos.coords.longitude.toFixed(7);
            const acc = Math.round(pos.coords.accuracy);

            latInput.value = lat;
            lngInput.value = lng;

            // Highlight inputs briefly
            [latInput, lngInput].forEach(el => {
                el.style.borderColor = 'var(--sur-success)';
                el.style.background  = '#edfaf3';
                setTimeout(() => {
                    el.style.borderColor = '';
                    el.style.background  = '';
                }, 2000);
            });

            showLocStatus('success',
                `Lokasi berhasil didapatkan: akurasi ±${acc} meter`);

            // Maps link
            mapAnchor.href = `https://www.google.com/maps?q=${lat},${lng}`;
            mapLink.style.display = 'block';

            // Reset button
            btnIcon.textContent = '✅';
            btnText.textContent = 'Perbarui Lokasi';
            btn.disabled        = false;
            btn.style.opacity   = '1';
        },
        // ── ERROR ──
        (err) => {
            const msgs = {
                1: 'Izin akses lokasi ditolak. Aktifkan izin lokasi di pengaturan browser Anda.',
                2: 'Posisi tidak tersedia. Pastikan GPS/koneksi aktif.',
                3: 'Waktu permintaan habis. Coba lagi.',
            };
            showLocStatus('error', '❌ ' + (msgs[err.code] || 'Gagal mendapatkan lokasi.'));
            btnIcon.textContent = '📍';
            btnText.textContent = 'Dapatkan Lokasi Saat Ini';
            btn.disabled        = false;
            btn.style.opacity   = '1';
        },
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
    );
}

function showLocStatus(type, msg) {
    const el = document.getElementById('locStatus');
    const styles = {
        info:    'background:#e8f4fd;border:1px solid #90cdf4;color:#1b4f72;',
        success: 'background:#edfaf3;border:1px solid #9ae6b4;color:#1a4731;',
        error:   'background:#fff5f5;border:1px solid #fc8181;color:#742a2a;',
    };
    el.style.cssText = `display:flex;align-items:center;gap:7px;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:600;margin-bottom:10px;${styles[type]||styles.info}`;
    el.textContent = msg;
}

// ────────── GEOLOCATION – SOIL SAMPLING ──────────
function soilGetLocation() {
    const btn       = document.getElementById('soilBtnGetLoc');
    const btnIcon   = document.getElementById('soilLocBtnIcon');
    const btnText   = document.getElementById('soilLocBtnText');
    const mapLink   = document.getElementById('soilLocMapLink');
    const mapAnchor = document.getElementById('soilLocMapAnchor');
    const latInput  = document.getElementById('soil_lat');
    const lngInput  = document.getElementById('soil_lng');

    if (!navigator.geolocation) {
        showSoilLocStatus('error', '❌ Browser tidak mendukung Geolocation API.');
        return;
    }

    // Loading state
    btn.disabled            = true;
    btnIcon.textContent     = '⏳';
    btnText.textContent     = 'Mendeteksi koordinat…';
    btn.style.opacity       = '0.7';
    mapLink.style.display   = 'none';
    showSoilLocStatus('info', '⏳ Meminta izin akses lokasi…');

    navigator.geolocation.getCurrentPosition(
        // ── SUCCESS ──
        (pos) => {
            const lat = pos.coords.latitude.toFixed(7);
            const lng = pos.coords.longitude.toFixed(7);
            const acc = Math.round(pos.coords.accuracy);

            latInput.value = lat;
            lngInput.value = lng;

            // Highlight inputs briefly
            [latInput, lngInput].forEach(el => {
                el.style.borderColor = 'var(--sur-success)';
                el.style.background  = '#edfaf3';
                setTimeout(() => { el.style.borderColor = ''; el.style.background = ''; }, 2000);
            });

            showSoilLocStatus('success', `Koordinat sampling berhasil didapatkan: akurasi ±${acc} meter`);

            mapAnchor.href        = `https://www.google.com/maps?q=${lat},${lng}`;
            mapLink.style.display = 'block';

            btnIcon.textContent = '✅';
            btnText.textContent = 'Perbarui Koordinat';
            btn.disabled        = false;
            btn.style.opacity   = '1';
        },
        // ── ERROR ──
        (err) => {
            const msgs = {
                1: 'Izin akses lokasi ditolak. Aktifkan izin lokasi di pengaturan browser Anda.',
                2: 'Posisi tidak tersedia. Pastikan GPS/koneksi aktif.',
                3: 'Waktu permintaan habis. Coba lagi.',
            };
            showSoilLocStatus('error', '❌ ' + (msgs[err.code] || 'Gagal mendapatkan lokasi.'));
            btnIcon.textContent = '📍';
            btnText.textContent = 'Dapatkan Koordinat Sampling';
            btn.disabled        = false;
            btn.style.opacity   = '1';
        },
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
    );
}

function showSoilLocStatus(type, msg) {
    const el = document.getElementById('soilLocStatus');
    const styles = {
        info:    'background:#e8f4fd;border:1px solid #90cdf4;color:#1b4f72;',
        success: 'background:#edfaf3;border:1px solid #9ae6b4;color:#1a4731;',
        error:   'background:#fff5f5;border:1px solid #fc8181;color:#742a2a;',
    };
    el.style.cssText = `display:flex;align-items:center;gap:7px;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:600;margin-bottom:10px;${styles[type]||styles.info}`;
    el.textContent = msg;
}

// ────────── NAVIGATION ──────────
function surGoStep(n) {
    document.querySelectorAll('.sur-section').forEach(s => s.classList.remove('active'));
    document.getElementById('surStep' + n).classList.add('active');
    document.querySelectorAll('.sur-step-btn').forEach((b, i) => b.classList.toggle('active', i === n));
    surCurrentStep = n;
    updateProgress();
    window.scrollTo({ top: document.getElementById('surProgBar').getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' });
}
function surNextStep() { if (surCurrentStep < SUR_TOTAL - 1) surGoStep(surCurrentStep + 1); }
function surPrevStep() { if (surCurrentStep > 0) surGoStep(surCurrentStep - 1); }
function updateProgress() {
    const pct = Math.round(((surCurrentStep + 1) / SUR_TOTAL) * 100);
    document.getElementById('surProgBar').style.width = pct + '%';
    const labels = [
        'Langkah 1 dari 7: Identitas Surveyor & Lokasi',
        'Langkah 2 dari 7: Sensus Visual Kucing',
        'Langkah 3 dari 7: Pemeriksaan Fisik Lengkap',
        'Langkah 4 dari 7: Ekto & Endoparasit',
        'Langkah 5 dari 7: Sampling Tanah Lingkungan',
        'Langkah 6 dari 7: Survei KAP Civitas Akademika',
        'Langkah 7 dari 7: Evaluasi K3L & SOP',
    ];
    document.getElementById('surProgLbl').textContent = labels[surCurrentStep];
}
</script>
</x-app-layout>
