@php
    // Inline Base64 Templates for reliable DomPDF and browser rendering
    $frontTemplatePath = public_path('reference/ktamu-front.png');
    $backTemplatePath = public_path('reference/ktamu-back.png');
    $fontPath = public_path('fonts/FingerPaint-Regular.ttf');

    $frontBase64 = file_exists($frontTemplatePath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($frontTemplatePath))
        : '';

    $backBase64 = file_exists($backTemplatePath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($backTemplatePath))
        : '';

    $fontBase64 = file_exists($fontPath)
        ? base64_encode(file_get_contents($fontPath))
        : '';

    // Inline Base64 Primary Cat Photo
    $photoPathToUse = $cat->primary_photo_path ?? $cat->photo_path;
    $photoData = null;
    if ($photoPathToUse) {
        $storagePath = storage_path('app/public/' . $photoPathToUse);
        $publicPath = public_path('storage/' . $photoPathToUse);

        if (file_exists($storagePath)) {
            $photoData = 'data:' . mime_content_type($storagePath) . ';base64,' . base64_encode(file_get_contents($storagePath));
        } elseif (file_exists($publicPath)) {
            $photoData = 'data:' . mime_content_type($publicPath) . ';base64,' . base64_encode(file_get_contents($publicPath));
        }
    }

    // Inline Base64 Biometric Paw Photo if present
    $pawPhotoData = null;
    if ($cat->biometric_photo_path) {
        $storagePaw = storage_path('app/public/' . $cat->biometric_photo_path);
        if (file_exists($storagePaw)) {
            $pawPhotoData = 'data:' . mime_content_type($storagePaw) . ';base64,' . base64_encode(file_get_contents($storagePaw));
        }
    }

    // Unique Code formatting: "kode_wilayah.kcg.xxxx"
    $uniqueCode = $cat->formatted_unique_code ?? ($cat->wilayah_code ?? '34') . '.kcg.' . str_pad($cat->id ?? 1, 4, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>KTAM KucingMu - {{ $cat->name }}</title>
    <!-- Google Font fallback for browser view -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finger+Paint&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @font-face {
            font-family: 'Finger Paint';
            font-style: normal;
            font-weight: 400;
            @if($fontBase64)
            src: url('data:font/truetype;charset=utf-8;base64,{{ $fontBase64 }}') format('truetype');
            @endif
        }

        @page {
            size: 86mm 54mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 86mm;
            background-color: #f1f5f9;
        }

        .card-page {
            position: relative;
            width: 86mm;
            height: 54mm;
            overflow: hidden;
            background-size: 86mm 54mm;
            background-position: center;
            background-repeat: no-repeat;
            page-break-after: always;
            box-sizing: border-box;
        }

        .card-page:last-child {
            page-break-after: avoid;
        }

        @media screen {
            body {
                background: #090d16;
                padding: 24px 16px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 20px;
                min-height: 100vh;
                width: 100%;
                box-sizing: border-box;
            }
            .card-page {
                border-radius: 14px;
                box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.12);
                flex-shrink: 0;
            }
            .screen-label {
                display: block;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                margin-bottom: 8px;
                text-align: center;
            }
            .card-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
        }

        @media print {
            body {
                background-color: transparent;
                width: 86mm;
            }
            .screen-label {
                display: none;
            }
        }

        /* FRONT SIDE */
        .card-front {
            background-image: url('{{ $frontBase64 }}');
            background-color: #0d2b78;
        }

        .front-unique-code {
            position: absolute;
            left: 5.5mm;
            bottom: 4.5mm;
            font-family: 'Finger Paint', cursive, sans-serif;
            font-size: 11pt;
            font-weight: 400;
            color: #ffffff;
            letter-spacing: 0.8px;
            text-transform: lowercase;
            line-height: 1;
            z-index: 20;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.45);
        }

        /* BACK SIDE */
        .card-back {
            background-image: url('{{ $backBase64 }}');
            background-color: #0d2b78;
        }

        /* Value capsules overlaying the white boxes */
        .back-val {
            position: absolute;
            display: table;
            overflow: hidden;
            font-size: 6.5pt;
            font-weight: 700;
            color: #0b224d;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            z-index: 10;
        }

        .back-val-inner {
            display: table-cell;
            vertical-align: middle;
            padding: 0 2mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Exact White Box Placements (left column) */
        .box-namaku {
            top: 7.2mm;
            left: 15.2mm;
            width: 25.8mm;
            height: 3.8mm;
        }
        .box-namaku .back-val-inner {
            font-size: 7pt;
            color: #082440;
        }

        .gender-badge {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: 800;
            margin-left: 1mm;
        }
        .gender-male { color: #0284c7; }
        .gender-female { color: #db2777; }

        .box-dob {
            top: 12.0mm;
            left: 15.2mm;
            width: 25.8mm;
            height: 3.8mm;
        }

        .box-breed {
            top: 16.8mm;
            left: 15.2mm;
            width: 25.8mm;
            height: 4.8mm;
        }

        .box-color {
            top: 21.6mm;
            left: 15.2mm;
            width: 25.8mm;
            height: 5.2mm;
        }

        .box-nikumu {
            top: 26.8mm;
            left: 15.2mm;
            width: 25.8mm;
            height: 3.5mm;
        }
        .box-nikumu .back-val-inner {
            font-family: 'Courier New', Courier, monospace;
            font-size: 6pt;
            font-weight: 800;
            color: #0f4c3a;
        }

        /* Exact White Box Placements (right column) */
        .box-owner-name {
            top: 7.4mm;
            left: 57.3mm;
            width: 25.8mm;
            height: 7.2mm;
        }

        .box-owner-nbm {
            top: 15.7mm;
            left: 57.3mm;
            width: 25.8mm;
            height: 7.2mm;
        }

        .box-owner-phone {
            top: 24.2mm;
            left: 57.3mm;
            width: 25.8mm;
            height: 7.2mm;
        }

        /* Paw & Verification Box (bottom right) */
        .box-paw-container {
            position: absolute;
            top: 34.0mm;
            left: 44.0mm;
            width: 39.1mm;
            height: 16.3mm;
            z-index: 10;
            padding: 1mm;
        }

        .paw-content-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        .paw-slot-img {
            width: 13.5mm;
            height: 13.5mm;
            object-fit: cover;
            border-radius: 2px;
            border: 0.5px solid #cbd5e1;
        }

        .paw-qr-img {
            width: 13.5mm;
            height: 13.5mm;
            display: block;
        }

        .paw-tag {
            font-size: 4.2pt;
            font-weight: 800;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
        }

        .draft-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 16pt;
            font-weight: 900;
            color: rgba(220, 38, 38, 0.4);
            border: 1.5px solid rgba(220, 38, 38, 0.4);
            padding: 1.5mm 4mm;
            text-transform: uppercase;
            letter-spacing: 2px;
            z-index: 100;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <!-- HALAMAN 1: TAMPAK DEPAN (FRONT) -->
    <div class="card-wrapper">
        <div class="screen-label">Tampak Depan (Front)</div>
        <div class="card-page card-front">
            @if(isset($isDraft) && $isDraft)
                <div class="draft-watermark">DRAFT KUCINGMU</div>
            @endif

            <!-- Kode Unik Kucing (Pojok Kiri Bawah) dengan Font Finger Paint Putih -->
            <div class="front-unique-code">
                {{ $uniqueCode }}
            </div>
        </div>
    </div>

    <!-- HALAMAN 2: TAMPAK BELAKANG (BACK) -->
    <div class="card-wrapper">
        <div class="screen-label">Tampak Belakang (Back)</div>
        <div class="card-page card-back">
            @if(isset($isDraft) && $isDraft)
                <div class="draft-watermark">DRAFT KUCINGMU</div>
            @endif

            <!-- 1. NAMAKu -->
            <div class="back-val box-namaku">
                <div class="back-val-inner">
                    {{ $cat->name }}
                    @if($cat->gender === 'male')
                        <span class="gender-badge gender-male">♂</span>
                    @else
                        <span class="gender-badge gender-female">♀</span>
                    @endif
                </div>
            </div>

            <!-- 2. DOB -->
            <div class="back-val box-dob">
                <div class="back-val-inner">
                    {{ $cat->date_of_birth ? $cat->date_of_birth->format('d-m-Y') : '-' }}
                </div>
            </div>

            <!-- 3. BREED -->
            <div class="back-val box-breed">
                <div class="back-val-inner">
                    {{ $cat->breed ?: 'Domestik' }}
                </div>
            </div>

            <!-- 4. COLOR -->
            <div class="back-val box-color">
                <div class="back-val-inner">
                    {{ $cat->color ?: 'Campuran / Ras' }}
                </div>
            </div>

            <!-- 5. NIKuMu -->
            <div class="back-val box-nikumu">
                <div class="back-val-inner">
                    {{ $uniqueCode }}
                </div>
            </div>

            <!-- 6. NAMA OWNER -->
            <div class="back-val box-owner-name">
                <div class="back-val-inner">
                    {{ $cat->owner->name ?? 'Pemilik Kucing' }}
                </div>
            </div>

            <!-- 7. NBM OWNER -->
            <div class="back-val box-owner-nbm">
                <div class="back-val-inner">
                    {{ $cat->owner->muhammadiyah_id ?: '-' }}
                </div>
            </div>

            <!-- 8. KONTAK OWNER -->
            <div class="back-val box-owner-phone">
                <div class="back-val-inner">
                    {{ $cat->owner->phone ?: '-' }}
                </div>
            </div>

            <!-- 9. TANDA PAW KUCING & QR VERIFIKASI -->
            <div class="box-paw-container">
                <table class="paw-content-table">
                    <tr>
                        @if($pawPhotoData)
                            <td width="50%" align="center" valign="middle">
                                <img class="paw-slot-img" src="{{ $pawPhotoData }}" alt="Paw Biometrik">
                                <div class="paw-tag">Biometrik Paw</div>
                            </td>
                            <td width="50%" align="center" valign="middle">
                                @if(isset($card->qr_code_payload) && $card->qr_code_payload)
                                    <img class="paw-qr-img" src="{{ $card->qr_code_payload }}" alt="QR">
                                @endif
                                <div class="paw-tag">Scan QR</div>
                            </td>
                        @elseif($photoData)
                            <td width="50%" align="center" valign="middle">
                                <img class="paw-slot-img" src="{{ $photoData }}" alt="Foto Kucing">
                                <div class="paw-tag">Foto Resmi</div>
                            </td>
                            <td width="50%" align="center" valign="middle">
                                @if(isset($card->qr_code_payload) && $card->qr_code_payload)
                                    <img class="paw-qr-img" src="{{ $card->qr_code_payload }}" alt="QR">
                                @endif
                                <div class="paw-tag">Scan QR</div>
                            </td>
                        @else
                            <td align="center" valign="middle">
                                @if(isset($card->qr_code_payload) && $card->qr_code_payload)
                                    <img class="paw-qr-img" style="margin: 0 auto;" src="{{ $card->qr_code_payload }}" alt="QR">
                                @endif
                                <div class="paw-tag">Scan Verifikasi Resmi</div>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>

        </div>
    </div>

</body>
</html>