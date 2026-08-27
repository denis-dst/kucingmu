<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan eSurveillance Stray Cat #{{ $survey->id }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #1a3a5c;
            color: #ffffff;
            padding: 14px 18px;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 3px 0;
        }
        .header p {
            font-size: 10px;
            margin: 0;
            opacity: 0.9;
        }
        .badge-oh {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 12px;
            padding: 4px 10px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1a3a5c;
            border-bottom: 2px solid #0077b6;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .grid-table td {
            padding: 4px 6px;
            vertical-align: top;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .grid-table td.label {
            background-color: #f8fafc;
            font-weight: bold;
            color: #334155;
            width: 30%;
        }
        .grid-table td.value {
            color: #0f172a;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .data-table th {
            background-color: #1a3a5c;
            color: #ffffff;
            padding: 5px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-good { background: #dcfce7; color: #166534; }
        .badge-warn { background: #fef3c7; color: #92400e; }
        .badge-bad  { background: #fee2e2; color: #991b1b; }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>E-Surveillance Stray Cat</h1>
                    <p>Sistem Surveilans Stray Cat Berbasis One Health: PP Muhammadiyah</p>
                </td>
                <td style="text-align: right; width: 140px;">
                    <div class="badge-oh">🌐 One Health K3L<br>ID: SURV-{{ str_pad($survey->id, 5, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Step 0: Identitas Surveyor & Lokasi -->
    <div class="section-title">📋 1. Identitas Surveyor & Lokasi</div>
    <table class="grid-table">
        <tr>
            <td class="label">Tanggal Survei</td>
            <td class="value">{{ optional($survey->surveyed_at)->format('d M Y') ?: '-' }} (Waktu: {{ $survey->start_time ?: '-' }})</td>
            <td class="label">Surveyor</td>
            <td class="value">{{ $survey->surveyor_name ?: (optional($survey->volunteer)->name ?: '-') }} ({{ $survey->surveyor_role ?: 'Relawan' }})</td>
        </tr>
        <tr>
            <td class="label">Institusi / Kampus</td>
            <td class="value">{{ $survey->institution ?: $survey->campus_location }}</td>
            <td class="label">Detail Lokasi & Zona</td>
            <td class="value">{{ $survey->campus_location }} - {{ $survey->zone }}</td>
        </tr>
        <tr>
            <td class="label">Koordinat GPS (Lokasi)</td>
            <td class="value">Lat: {{ $survey->latitude ?: '-' }}, Lng: {{ $survey->longitude ?: '-' }}</td>
            <td class="label">Cuaca Saat Survei</td>
            <td class="value">{{ ucfirst($survey->weather ?: '-') }}</td>
        </tr>
        @if($survey->weather_notes)
        <tr>
            <td class="label">Catatan Lokasi</td>
            <td class="value" colspan="3">{{ $survey->weather_notes }}</td>
        </tr>
        @endif
    </table>

    <!-- Step 1: Sensus Visual -->
    <div class="section-title">📷 2. Data Sensus Visual Kucing</div>
    <table class="grid-table">
        <tr>
            <td class="label">Sesi Pengamatan</td>
            <td class="value">{{ $survey->observation_session ?: '-' }}</td>
            <td class="label">Waktu Pengamatan</td>
            <td class="value">{{ $survey->observation_time_range ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Dijumpai (C)</td>
            <td class="value"><strong>{{ $survey->cats_observed }}</strong> ekor</td>
            <td class="label">Jumlah Resighted (R)</td>
            <td class="value">{{ $survey->cats_resighted }} ekor</td>
        </tr>
        <tr>
            <td class="label">Bertanda Ear-Tip</td>
            <td class="value">{{ $survey->cats_with_ear_tip }} ekor</td>
            <td class="label">Perlu Perhatian</td>
            <td class="value">{{ $survey->cats_needing_attention }} ekor</td>
        </tr>
        @if($survey->food_source)
        <tr>
            <td class="label">Sumber Pakan di Area</td>
            <td class="value" colspan="3">{{ $survey->food_source }}</td>
        </tr>
        @endif
    </table>

    @if(!empty($survey->cat_individuals) && is_array($survey->cat_individuals))
        <p style="font-weight: bold; margin: 4px 0;">Data Individu Kucing yang Terdaftar ({{ count($survey->cat_individuals) }} Ekor):</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama / Label</th>
                    <th>Gender</th>
                    <th>Ras</th>
                    <th>Usia</th>
                    <th>Warna & Pola</th>
                    <th>Ear-Tip</th>
                    <th>Tanda Khusus</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($survey->cat_individuals as $cat)
                    <tr>
                        <td><strong>{{ $cat['id'] ?? '-' }}</strong></td>
                        <td>{{ $cat['name'] ?? '-' }}</td>
                        <td>{{ $cat['gender'] ?? '-' }}</td>
                        <td>{{ $cat['breed'] ?? 'Domestik' }}</td>
                        <td>{{ $cat['age'] ?? '-' }}</td>
                        <td>{{ $cat['color'] ?? '-' }} ({{ $cat['pattern'] ?? '-' }})</td>
                        <td>{{ $cat['eartip'] ?? '-' }}</td>
                        <td>{{ $cat['mark'] ?? '-' }}</td>
                        <td>{{ $cat['resight'] ?? 'Baru' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Step 2: Pemeriksaan Fisik -->
    <div class="section-title">🐈 3. Pemeriksaan Fisik & Welfare Index</div>
    <table class="grid-table">
        <tr>
            <td class="label">ID & Nama Kucing</td>
            <td class="value">{{ $survey->physical_cat_id ?: '-' }} {{ $survey->physical_cat_name ? '('.$survey->physical_cat_name.')' : '' }}</td>
            <td class="label">Pemeriksa (Dokter Hewan)</td>
            <td class="value">{{ $survey->examining_vet ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Sterilisasi</td>
            <td class="value">{{ $survey->sterilization_status ?: '-' }}</td>
            <td class="label">Metode Penangkapan</td>
            <td class="value">{{ $survey->capture_method ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Vital Signs</td>
            <td class="value">
                BB: {{ $survey->body_weight_kg ? $survey->body_weight_kg.' kg' : '-' }} | 
                Suhu: {{ $survey->rectal_temp_c ? $survey->rectal_temp_c.'°C' : '-' }}<br>
                HR: {{ $survey->heart_rate ? $survey->heart_rate.' bpm' : '-' }} | 
                RR: {{ $survey->resp_rate ? $survey->resp_rate.' bpm' : '-' }}
            </td>
            <td class="label">Dehidrasi & Kesadaran</td>
            <td class="value">Dehidrasi: {{ $survey->dehydration_status ?: '-' }}<br>Kesadaran: {{ $survey->consciousness_level ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Body Condition Score (BCS)</td>
            <td class="value"><strong>Skala {{ $survey->bcs_score ?: '-' }} / 9</strong></td>
            <td class="label">Welfare Index Score</td>
            <td class="value">
                <strong>Skor: {{ $survey->welfare_score ?? '0' }} / 8</strong> - 
                <span class="badge-status {{ ($survey->welfare_score ?? 0) == 0 ? 'badge-good' : (($survey->welfare_score ?? 0) <= 2 ? 'badge-warn' : 'badge-bad') }}">
                    {{ $survey->welfare_status ?: 'Baik' }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Temuan Sistem Organ</td>
            <td class="value" colspan="3">
                Mata: {{ is_array($survey->eye_condition) ? implode(', ', $survey->eye_condition) : '-' }} | Mukosa: {{ $survey->mucosa_color ?: '-' }}<br>
                Telinga: {{ is_array($survey->ear_condition) ? implode(', ', $survey->ear_condition) : '-' }} | Bulu & Kulit: {{ $survey->coat_condition ?: '-' }}, {{ is_array($survey->skin_condition) ? implode(', ', $survey->skin_condition) : '' }}<br>
                Muskuloskeletal: {{ $survey->posture_gait ?: '-' }}, {{ is_array($survey->musculoskeletal) ? implode(', ', $survey->musculoskeletal) : '' }}
            </td>
        </tr>
        @if($survey->diagnosis_presumptif || $survey->clinical_notes)
        <tr>
            <td class="label">Diagnosa & Rekomendasi</td>
            <td class="value" colspan="3">
                <strong>Diagnosa:</strong> {{ $survey->diagnosis_presumptif ?: '-' }}<br>
                <strong>Tindak Lanjut:</strong> {{ is_array($survey->follow_up_actions) ? implode(', ', $survey->follow_up_actions) : '-' }}<br>
                <strong>Catatan Klinis:</strong> {{ $survey->clinical_notes ?: '-' }}
            </td>
        </tr>
        @endif
    </table>

    <div class="page-break"></div>

    <!-- Step 3: Parasitologi -->
    <div class="section-title">🔬 4. Pemeriksaan Ekto & Endoparasit</div>
    <table class="grid-table">
        <tr>
            <td class="label">Hasil Comb Test (Ektoparasit)</td>
            <td class="value"><strong>{{ $survey->comb_test_result ?: '-' }}</strong> (Pinjal: {{ $survey->flea_count ?? 0 }} ekor)</td>
            <td class="label">Spesies Ektoparasit</td>
            <td class="value">{{ is_array($survey->ectoparasite_species) ? implode(', ', $survey->ectoparasite_species) : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pemeriksaan Endoparasit Feses</td>
            <td class="value">Metode: {{ $survey->feces_collection_method ?: '-' }} ({{ $survey->feces_preservation ?: '-' }})</td>
            <td class="label">Hasil Endoparasit</td>
            <td class="value"><strong>{{ $survey->endoparasite_result ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Agen Zoonosis Ditemukan</td>
            <td class="value" colspan="3">{{ is_array($survey->zoonotic_agents) ? implode(', ', $survey->zoonotic_agents) : 'Tidak ditemukan / -' }}</td>
        </tr>
        @if($survey->endoparasite_notes || $survey->ectoparasite_notes)
        <tr>
            <td class="label">Catatan Laboratorium</td>
            <td class="value" colspan="3">{{ $survey->ectoparasite_notes }} {{ $survey->endoparasite_notes }}</td>
        </tr>
        @endif
    </table>

    <!-- Step 4: Sampling Tanah -->
    <div class="section-title">🌱 5. Sampling Tanah / Pasir Lingkungan</div>
    <table class="grid-table">
        <tr>
            <td class="label">Kode & Area Sampel</td>
            <td class="value">{{ $survey->soil_sample_code ?: '-' }} - {{ $survey->soil_sampling_area ?: '-' }}</td>
            <td class="label">Koordinat GPS Titik Sampling</td>
            <td class="value">Lat: {{ $survey->soil_lat ?: '-' }}, Lng: {{ $survey->soil_lng ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kondisi & Indikasi Visual</td>
            <td class="value">Berat: {{ $survey->soil_weight_g ?: 50 }}g, Kedalaman: {{ $survey->soil_depth_cm ?: 5 }}cm (Kondisi: {{ $survey->soil_condition ?: '-' }})</td>
            <td class="label">Indikasi Visual Feses</td>
            <td class="value">{{ $survey->feces_visual_indicator ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hasil Lab Tanah</td>
            <td class="value"><strong>{{ $survey->soil_lab_result ?: '-' }}</strong> (EPG: {{ $survey->eggs_per_gram ?? 0 }})</td>
            <td class="label">Agen Parasitik Ditemukan</td>
            <td class="value">{{ is_array($survey->soil_parasitic_agents) ? implode(', ', $survey->soil_parasitic_agents) : '-' }}</td>
        </tr>
        @if($survey->sanitation_notes)
        <tr>
            <td class="label">Catatan Sanitasi Lingkungan</td>
            <td class="value" colspan="3">{{ $survey->sanitation_notes }}</td>
        </tr>
        @endif
    </table>

    <!-- Step 5 & 6: KAP Civitas & Evaluasi K3L -->
    <div class="section-title">📊 6. Ringkasan Survei KAP & Evaluasi K3L Kampus</div>
    <table class="grid-table">
        <tr>
            <td class="label">Responden KAP Civitas</td>
            <td class="value">{{ $survey->kap_respondent_status ?: '-' }} ({{ $survey->kap_respondent_gender ?: '-' }}) - {{ $survey->kap_faculty ?: '-' }}</td>
            <td class="label">Dukungan Program TNRM</td>
            <td class="value"><strong>{{ $survey->kap_prac_tnrm_support ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Informan Kunci K3L Kampus</td>
            <td class="value">{{ $survey->k3l_informant_name ?: '-' }} ({{ $survey->k3l_informant_role ?: '-' }})</td>
            <td class="label">Penanganan Kucing Saat Ini</td>
            <td class="value">{{ is_array($survey->k3l_current_handling) ? implode(', ', $survey->k3l_current_handling) : '-' }}</td>
        </tr>
        @if($survey->k3l_obstacles || $survey->k3l_intervention_plan)
        <tr>
            <td class="label">Hambatan & Rencana Intervensi</td>
            <td class="value" colspan="3">
                <strong>Hambatan:</strong> {{ is_array($survey->k3l_obstacles) ? implode(', ', $survey->k3l_obstacles) : '-' }}<br>
                <strong>Rencana:</strong> {{ $survey->k3l_intervention_plan ?: '-' }}
            </td>
        </tr>
        @endif
        @if($survey->notes)
        <tr>
            <td class="label">Catatan Umum Laporan</td>
            <td class="value" colspan="3">{{ $survey->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="footer">
        Dokumen Resmi e-Surveillance Stray Cat: PP Muhammadiyah &bull; Dicetak pada {{ date('d M Y H:i') }} WIB
    </div>

</body>
</html>
