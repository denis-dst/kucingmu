<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ringkasan Rekam Medis {{ $record->record_number }} - {{ $record->cat->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title h1 {
            font-size: 15pt;
            font-weight: bold;
            color: #0f766e;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-title p {
            font-size: 8.5pt;
            color: #64748b;
            margin: 0;
        }

        .header-meta {
            text-align: right;
            vertical-align: top;
            font-size: 8.5pt;
            color: #475569;
        }

        .header-meta .rm-number {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }

        .badge-readonly {
            display: inline-block;
            background-color: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #0f766e;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0f766e;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 4px 6px;
            font-size: 9pt;
            vertical-align: top;
            border: 1px solid #e2e8f0;
        }

        .info-table td.label {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            width: 20%;
        }

        .info-table td.value {
            color: #0f172a;
            width: 30%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 8px;
        }

        .data-table th {
            background-color: #0f766e;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid #0f766e;
        }

        .data-table td {
            padding: 5px 6px;
            font-size: 8.5pt;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .box {
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }

        .box-teal {
            background-color: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #134e4a;
        }

        .box-amber {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #78350f;
        }

        .box-rose {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #881337;
        }

        .box-blue {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
        }

        .box-title {
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.2px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        .signature-table td {
            vertical-align: top;
            font-size: 8.5pt;
        }

        .signature-box {
            width: 220px;
            text-align: center;
            float: right;
        }

        .signature-line {
            height: 45px;
            border-bottom: 1px solid #0f172a;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            text-align: center;
            font-size: 7.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Header / Kop Dokumen -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-title" style="width: 65%;">
                    <h1>Klinik Hewan KucingMu</h1>
                    <p>Layanan Kesehatan Kucing &bull; Lembaga Dakwah Komunitas PP Muhammadiyah</p>
                    <span class="badge-readonly">Salinan Resmi Pemilik Kucing (Read-Only)</span>
                </td>
                <td class="header-meta" style="width: 35%;">
                    <div class="rm-number">RM: {{ $record->record_number }}</div>
                    <div>Tanggal: <strong>{{ $record->completed_at ? $record->completed_at->format('d/m/Y H:i') : $record->created_at->format('d/m/Y') }} WIB</strong></div>
                    <div>Layanan: {{ $record->service_type_label }}</div>
                    <div>Klinik/Event: {{ $record->clinic_name ?: 'Klinik Hewan KucingMu' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Identitas Kucing & Pemilik -->
    <div class="section-title">Identitas Pasien & Pemilik</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Kucing</td>
            <td class="value"><strong>{{ $record->cat->name }}</strong></td>
            <td class="label">Nama Pemilik</td>
            <td class="value"><strong>{{ $record->cat->owner->name ?? ($record->member->name ?? '-') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Ras & Gender</td>
            <td class="value">{{ $record->cat->breed }} &bull; {{ $record->cat->gender == 'male' ? 'Jantan' : 'Betina' }}</td>
            <td class="label">No. Telepon/WA</td>
            <td class="value">{{ $record->cat->owner->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor KTAKuMu</td>
            <td class="value"><strong>{{ $record->cat->unique_code ?: 'Menunggu Terbit' }}</strong></td>
            <td class="label">NBM Pemilik</td>
            <td class="value">{{ $record->cat->owner->formatted_nbm ?? ($record->cat->owner->muhammadiyah_id ?? '-') }}</td>
        </tr>
        <tr>
            <td class="label">Lahir / Umur</td>
            <td class="value">{{ $record->cat->date_of_birth ? $record->cat->date_of_birth->format('d/m/Y') : '-' }} ({{ $record->cat->age_text }})</td>
            <td class="label">Domisili</td>
            <td class="value">{{ $record->cat->wilayah->nama ?? ($record->cat->wilayah_code ?? '-') }}</td>
        </tr>
    </table>

    <!-- Hasil Pemeriksaan & Diagnosis -->
    <div class="section-title">Hasil Pemeriksaan Fisik & Diagnosis</div>
    <table class="info-table">
        <tr>
            <td class="label">Keluhan Awal</td>
            <td class="value" colspan="3"><em>"{{ $record->chief_complaint }}"</em></td>
        </tr>
        <tr>
            <td class="label">Berat Badan</td>
            <td class="value"><strong>{{ $record->weight }} kg</strong></td>
            <td class="label">Suhu Tubuh</td>
            <td class="value"><strong>{{ $record->temperature }} &deg;C</strong></td>
        </tr>
        <tr>
            <td class="label">Kondisi Umum</td>
            <td class="value">{{ $record->general_condition }}</td>
            <td class="label">Status Pasien</td>
            <td class="value">Hidup & Rawat Jalan</td>
        </tr>
        <tr>
            <td class="label">Diagnosis Utama</td>
            <td class="value" colspan="3" style="color: #0f766e; font-weight: bold; font-size: 9.5pt;">
                {{ $record->primaryDiagnosis->diagnosis_name ?? ($record->general_condition ?: 'Pemeriksaan Rutin') }}
            </td>
        </tr>
        @if($record->primaryDiagnosis && $record->primaryDiagnosis->notes)
            <tr>
                <td class="label">Catatan Diagnosis</td>
                <td class="value" colspan="3">{{ $record->primaryDiagnosis->notes }}</td>
            </tr>
        @endif
    </table>

    <!-- Resep Obat & Aturan Pakai -->
    <div class="section-title">Resep Obat & Aturan Pakai</div>
    @if($issuedRx && $issuedRx->isManual())
        <div class="box box-amber">
            <div class="box-title">Lembar Resep Obat Fisik Manual</div>
            <div>Dokter pemeriksa telah menyerahkan resep obat fisik manual kepada Anda pada saat kunjungan klinik. Silakan tebus atau berikan obat sesuai instruksi tertulis dokter hewan.</div>
            @if($issuedRx->notes)
                <div style="margin-top: 4px;"><strong>Catatan Tambahan:</strong> {{ $issuedRx->notes }}</div>
            @endif
        </div>
    @elseif($issuedRx && $issuedRx->items->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Nama Obat</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 22%;">Dosis & Rute</th>
                    <th style="width: 40%;">Aturan Pakai & Peringatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($issuedRx->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->is_racikan ? ($item->racikan_name ?: $item->medicine_name_snapshot) : $item->medicine_name_snapshot }}</strong>
                            <div style="font-size: 7.5pt; color: #64748b;">
                                {{ $item->is_racikan ? '[Racikan] ' : '' }}{{ strtoupper($item->dosage_form) }}
                            </div>
                            @if($item->is_racikan && $item->composition)
                                <div style="font-size: 7.5pt; color: #4338ca; margin-top: 2px;">
                                    Komposisi: {{ $item->composition }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->quantity_value }} {{ $item->quantity_unit }}</td>
                        <td>
                            @if(!$item->is_racikan)
                                <strong>{{ $item->dose_value }} {{ $item->dose_unit }}</strong><br/>
                            @endif
                            {{ $item->frequency_value }} {{ $item->frequency_unit }} &bull; {{ $item->duration_value }} {{ $item->duration_unit }}<br/>
                            <span style="font-size: 7.5pt; color: #475569;">Rute: {{ $item->administration_route }}</span>
                        </td>
                        <td>
                            {{ $item->usage_instructions }}
                            @if($item->warnings)
                                <div style="font-size: 7.5pt; color: #be123c; margin-top: 2px;">
                                    <strong>Perhatian:</strong> {{ $item->warnings }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="box box-teal">
            Tidak ada resep obat khusus yang diterbitkan pada pemeriksaan ini.
        </div>
    @endif

    <!-- Instruksi Perawatan Rumah & Tanda Bahaya -->
    <div class="section-title">Instruksi Dokter Untuk Pemilik Kucing</div>
    <div class="box box-teal">
        <div class="box-title">Instruksi Perawatan di Rumah:</div>
        <div>{{ $advices['home_care']->instruction ?? ($record->recommendation ?: 'Jaga kucing tetap hangat dan nyaman, berikan makanan bernutrisi tinggi dan air minum segar secara berkala.') }}</div>
    </div>

    <div class="box box-rose">
        <div class="box-title">Tanda Bahaya (Kapan Harus Segera Kembali):</div>
        <div>{{ $advices['warning_sign']->instruction ?? 'Jika kucing lemas hebat, tidak mau makan/minum lebih dari 24 jam, kesulitan bernapas, kejang, atau muntah terus-menerus, segera bawa ke dokter hewan terdekat.' }}</div>
    </div>

    <!-- Jadwal Kontrol Lanjutan (jika ada) -->
    @if($followup && $followup->scheduled_at)
        <div class="box box-blue">
            <div class="box-title">Jadwal Kontrol Lanjutan Disarankan:</div>
            <div>
                Tanggal: <strong>{{ $followup->scheduled_at->format('d F Y') }}</strong> &bull; Catatan: {{ $followup->reason }}
            </div>
        </div>
    @endif

    <!-- Tanda Tangan Dokter Pemeriksa -->
    <table class="signature-table">
        <tr>
            <td style="width: 55%; color: #64748b; font-size: 8pt; vertical-align: bottom;">
                <em>Dokumen rekam medis ini sah dan diterbitkan secara elektronik oleh Klinik Hewan KucingMu.</em>
            </td>
            <td style="width: 45%;">
                <div class="signature-box">
                    <div>Dokter Hewan Pemeriksa,</div>
                    <div class="signature-line"></div>
                    <div style="font-weight: bold; margin-top: 4px;">{{ $record->doctor->name ?? '-' }}</div>
                    <div style="font-size: 8pt; color: #64748b;">NBM: {{ $record->doctor->formatted_nbm ?? ($record->doctor->muhammadiyah_id ?? '-') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Salinan Rekam Medis Pasien KucingMu &bull; Dokumen resmi untuk pemilik kucing &bull; Dicetak secara mandiri melalui Aplikasi KucingMu
    </div>

</body>
</html>
