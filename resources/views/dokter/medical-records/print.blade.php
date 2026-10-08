<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekam Medis {{ $record->record_number }} - {{ $record->cat->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header-title h1 {
            font-size: 16pt;
            margin: 0;
            color: #0f766e;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-title p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            color: #64748b;
        }

        .header-meta {
            text-align: right;
            font-size: 9pt;
        }

        .header-meta strong {
            font-size: 11pt;
            color: #0f172a;
        }

        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f766e;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .grid-2 {
            display: flex;
            gap: 20px;
        }

        .col {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        th,
        td {
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
            font-weight: 600;
        }

        td {
            border-bottom: 1px solid #f1f5f9;
        }

        .soap-block {
            margin-bottom: 10px;
        }

        .soap-tag {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            background: #0f766e;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9pt;
            margin-right: 6px;
        }

        .signature-box {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-inner {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #0f172a;
        }

        .no-print {
            padding: 10px 15px;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10pt;
        }

        .btn-print {
            background: #0f766e;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <span>Lembar Rekam Medis Pasien: <strong>{{ $record->cat->name }}</strong> ({{ $record->record_number }})</span>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
    </div>

    <div style="padding: 20px 0;">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>KucingMu &bull; Rekam Medis Veteriner</h1>
                <p>KucingMu</p>
                <p>{{ $record->clinic_name ?: 'Klinik Hewan KucingMu Terpadu' }}</p>
            </div>
            <div class="header-meta">
                <div>No. Rekam Medis:</div>
                <strong>{{ $record->record_number }}</strong>
                <div style="margin-top: 4px;">{{ $record->created_at->format('d F Y, H:i') }} WIB</div>
                <div>Layanan: {{ $record->service_type_label }}</div>
            </div>
        </div>

        <!-- Patient & Owner Info -->
        <div class="section">
            <div class="section-title">Identitas Pasien & Pemilik</div>
            <div class="grid-2">
                <div class="col">
                    <table>
                        <tr>
                            <td width="35%" style="color:#64748b;">Nama Pasien</td>
                            <td>: <strong>{{ $record->cat->name }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color:#64748b;">Nomor KTAM</td>
                            <td>: {{ $record->cat->unique_code ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b;">Ras / Warna</td>
                            <td>: {{ $record->cat->breed }} / {{ $record->cat->color ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b;">Kelamin / Tgl Lahir</td>
                            <td>: {{ $record->cat->gender === 'male' ? 'Jantan' : 'Betina' }} /
                                {{ $record->cat->date_of_birth ? \Carbon\Carbon::parse($record->cat->date_of_birth)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col">
                    <table>
                        <tr>
                            <td width="35%" style="color:#64748b;">Nama Pemilik</td>
                            <td>: <strong>{{ $record->cat->owner->name ?? ($record->member->name ?? '-') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#64748b;">NBM</td>
                            <td>: {{ $record->cat->owner->formatted_nbm ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b;">No. Telepon</td>
                            <td>: {{ $record->cat->owner->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b;">Dokter Pemeriksa</td>
                            <td>: <strong>{{ $record->doctor->name ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Clinical SOAP Breakdown -->
        <div class="section">
            <div class="section-title">Catatan Klinis (SOAP)</div>

            <!-- Subjective -->
            <div class="soap-block">
                <div style="font-weight:600; margin-bottom: 4px;">
                    <span class="soap-tag">S</span> Subjective (Anamnesa)
                </div>
                <div style="padding-left: 26px; font-size: 10pt;">
                    <div><strong>Keluhan Utama:</strong>
                        {{ $record->subjective?->member_complaint ?? $record->chief_complaint }}</div>
                    <div><strong>Onset:</strong> {{ $record->subjective?->symptom_onset ?? '-' }} &bull; <strong>Nafsu
                            Makan:</strong> {{ $record->subjective?->appetite_history ?? 'Normal' }} &bull;
                        <strong>Minum:</strong> {{ $record->subjective?->drinking_history ?? 'Normal' }}</div>
                    @if($record->subjective?->doctor_clarification)
                        <div><strong>Klarifikasi Dokter:</strong> {{ $record->subjective->doctor_clarification }}</div>
                    @endif
                </div>
            </div>

            <!-- Objective -->
            <div class="soap-block" style="margin-top: 12px;">
                <div style="font-weight:600; margin-bottom: 4px;">
                    <span class="soap-tag">O</span> Objective (Pemeriksaan Fisik & Parameter)
                </div>
                <div style="padding-left: 26px; font-size: 10pt;">
                    <div>
                        <strong>Berat Badan:</strong> {{ $record->weight }} kg &bull;
                        <strong>Suhu Tubuh:</strong> {{ $record->temperature }} °C &bull;
                        <strong>Kondisi Umum:</strong> {{ $record->general_condition }}
                    </div>
                </div>
            </div>

            <!-- Assessment -->
            <div class="soap-block" style="margin-top: 12px;">
                <div style="font-weight:600; margin-bottom: 4px;">
                    <span class="soap-tag">A</span> Assessment (Diagnosis)
                </div>
                <div style="padding-left: 26px; font-size: 10pt;">
                    @if($record->primaryDiagnosis)
                        <div><strong>Diagnosis Utama:</strong> {{ $record->primaryDiagnosis->diagnosis_name }}
                            ({{ ucfirst($record->primaryDiagnosis->certainty) }} -
                            {{ ucfirst($record->primaryDiagnosis->severity) }})</div>
                        @if($record->primaryDiagnosis->clinical_reasoning)
                            <div style="color: #475569; font-style: italic;">
                                "{{ $record->primaryDiagnosis->clinical_reasoning }}"</div>
                        @endif
                    @else
                        <div>{{ $record->general_condition }}</div>
                    @endif
                </div>
            </div>

            <!-- Plan -->
            <div class="soap-block" style="margin-top: 12px;">
                <div style="font-weight:600; margin-bottom: 4px;">
                    <span class="soap-tag">P</span> Plan (Tindakan, Terapi & Rekomendasi)
                </div>
                <div style="padding-left: 26px; font-size: 10pt;">
                    @if($record->treatment_notes)
                        <div><strong>Tindakan Medis:</strong> {{ $record->treatment_notes }}</div>
                    @endif
                    @if($record->recommendation)
                        <div><strong>Instruksi Perawatan:</strong> {{ $record->recommendation }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prescriptions -->
        @php $rx = $record->prescriptions->first(); @endphp
        @if($rx && $rx->items->isNotEmpty())
            <div class="section">
                <div class="section-title">Resep Obat Elektronik (#{{ $rx->prescription_number }})</div>
                <table>
                    <thead>
                        <tr>
                            <th width="30%">Nama Obat</th>
                            <th width="15%">Sediaan / Rute</th>
                            <th width="20%">Dosis & Frekuensi</th>
                            <th width="15%">Durasi & Qty</th>
                            <th width="20%">Aturan Pakai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rx->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->medicine_name_snapshot }}</strong>
                                    @if($item->active_ingredient)
                                        <div style="font-size:8pt; color:#64748b;">{{ $item->active_ingredient }}</div>
                                    @endif
                                </td>
                                <td>{{ ucfirst($item->dosage_form) }} / {{ ucfirst($item->administration_route) }}</td>
                                <td>{{ $item->dose_value }} {{ $item->dose_unit }} ({{ $item->frequency_value }})</td>
                                <td>{{ $item->duration_value }} {{ $item->duration_unit }} ({{ $item->quantity_value }}
                                    {{ $item->quantity_unit }})</td>
                                <td>{{ $item->usage_instructions }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Follow-up -->
        @php $fu = $record->followups->first(); @endphp
        @if($fu && $fu->scheduled_at)
            <div class="section">
                <div class="section-title">Jadwal Kontrol Ulang Pasien</div>
                <div style="font-size: 10pt;">
                    Kucing dijadwalkan untuk kontrol pada: <strong>{{ $fu->scheduled_at->format('d F Y') }}</strong> &bull;
                    Alasan: {{ $fu->reason }}
                </div>
            </div>
        @endif

        <!-- Doctor's Signature -->
        <div class="signature-box">
            <div class="signature-inner">
                <div>Dokter Pemeriksa,</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; margin-top: 6px;">{{ $record->doctor->name ?? '-' }}</div>
                <div style="font-size: 8.5pt; color: #64748b;">NBM: {{ $record->doctor->formatted_nbm ?? '-' }}</div>
            </div>
        </div>

    </div>

</body>

</html>