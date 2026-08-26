@php
    // Inline Base64 Templates for 100% reliable DomPDF and browser rendering
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
<html lang="id">
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

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #0b1120;
        }

        /* CARD CONTAINER */
        .card-page {
            position: relative;
            width: 86mm;
            height: 54mm;
            max-width: 100%;
            overflow: hidden;
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            page-break-after: always;
            box-sizing: border-box;
            margin: 0 auto;
        }

        .card-page:last-child {
            page-break-after: avoid;
        }

        /* FRONT SIDE */
        .card-front {
            background-image: url('{{ $frontBase64 }}');
            background-color: #0d2b78;
        }

        .front-unique-code {
            position: absolute;
            left: 5%;
            bottom: 6.5%;
            font-family: 'Finger Paint', cursive, sans-serif;
            font-size: 11pt;
            font-weight: 400;
            color: #ffffff;
            letter-spacing: 0.8px;
            text-transform: lowercase;
            line-height: 1;
            z-index: 20;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
        }

        /* BACK SIDE */
        .card-back {
            background-image: url('{{ $backBase64 }}');
            background-color: #0d2b78;
        }

        /* Value capsules overlaying the white boxes precisely with percentage coords */
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
            padding: 0 1.5mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Left column boxes */
        .box-namaku {
            top: 13.43%;
            left: 17.75%;
            width: 30.03%;
            height: 6.57%;
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
            top: 22.29%;
            left: 17.75%;
            width: 30.03%;
            height: 6.57%;
        }

        .box-breed {
            top: 31.14%;
            left: 17.75%;
            width: 30.03%;
            height: 9.14%;
        }

        .box-color {
            top: 40.00%;
            left: 17.75%;
            width: 30.03%;
            height: 10.29%;
        }

        .box-nikumu {
            top: 50.00%;
            left: 17.75%;
            width: 30.03%;
            height: 5.43%;
        }
        .box-nikumu .back-val-inner {
            font-family: 'Courier New', Courier, monospace;
            font-size: 6pt;
            font-weight: 800;
            color: #0f4c3a;
        }

        /* Right column boxes */
        .box-owner-name {
            top: 13.71%;
            left: 66.72%;
            width: 30.03%;
            height: 13.43%;
        }

        .box-owner-nbm {
            top: 29.14%;
            left: 66.72%;
            width: 30.03%;
            height: 13.43%;
        }

        .box-owner-phone {
            top: 44.86%;
            left: 66.72%;
            width: 30.03%;
            height: 13.43%;
        }

        /* Paw & Verification Box (bottom right) */
        .box-paw-container {
            position: absolute;
            top: 63.14%;
            left: 51.19%;
            width: 45.56%;
            height: 30.29%;
            z-index: 10;
            padding: 1mm;
        }

        .paw-content-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        .paw-slot-img {
            width: 13mm;
            height: 13mm;
            object-fit: cover;
            border-radius: 2px;
            border: 0.5px solid #cbd5e1;
        }

        .paw-qr-img {
            width: 13mm;
            height: 13mm;
            display: block;
            margin: 0 auto;
        }

        .paw-tag {
            font-size: 4pt;
            font-weight: 800;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
            margin-top: 0.5mm;
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

        /* Browser & Screen Preview Styling: Centered & Clean without extra text */
        @media screen {
            body {
                background: #090d16;
                padding: 16px 12px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                gap: 16px;
                min-height: 100vh;
                box-sizing: border-box;
            }
            .card-page {
                border-radius: 12px;
                box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.1);
                flex-shrink: 0;
            }
        }

        /* Print & DomPDF Styling: Edge-to-edge exact card size */
        @media print {
            body {
                background-color: transparent;
                width: 86mm;
                height: 54mm;
                padding: 0;
                margin: 0;
            }
            .card-page {
                border-radius: 0;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- HALAMAN 1: TAMPAK DEPAN (FRONT) -->
    <div class="card-page card-front">
        @if(isset($isDraft) && $isDraft)
            <div class="draft-watermark">DRAFT KUCINGMU</div>
        @endif

        <!-- Kode Unik Kucing (Pojok Kiri Bawah) dengan Font Finger Paint Putih -->
        <div class="front-unique-code">
            {{ $uniqueCode }}
        </div>
    </div>

    <!-- HALAMAN 2: TAMPAK BELAKANG (BACK) -->
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
                                <img class="paw-qr-img" src="{{ $card->qr_code_payload }}" alt="QR">
                            @endif
                            <div class="paw-tag">Scan Verifikasi Resmi</div>
                        </td>
                    @endif
                </tr>
            </table>
        </div>

    </div>

</body>
</html>