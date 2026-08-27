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

    // Auto font sizing for NAMAKu (Left narrow capsule)
    $catName = trim($cat->name);
    $catNameLen = strlen($catName);
    $catNameWordCount = count(preg_split('/\s+/', $catName));

    if ($catNameLen > 14 || $catNameWordCount > 2) {
        $catNameFontSize = '5.0pt';
        $catNameLineHeight = '1.15';
    } elseif ($catNameLen > 7 || $catNameWordCount > 1) {
        $catNameFontSize = '6.0pt';
        $catNameLineHeight = '1.25';
    } else {
        $catNameFontSize = '7.8pt';
        $catNameLineHeight = '2';
    }

    // Auto font sizing for NAMA OWNER (Right capsule)
    $ownerName = trim($cat->owner->name ?? 'Pemilik Kucing');
    $ownerNameLen = strlen($ownerName);
    $ownerNameWordCount = count(preg_split('/\s+/', $ownerName));

    if ($ownerNameLen > 22 || $ownerNameWordCount > 3) {
        $ownerNameFontSize = '5.2pt';
        $ownerNameLineHeight = '1.25';
    } elseif ($ownerNameLen > 13 || $ownerNameWordCount > 1) {
        $ownerNameFontSize = '6.2pt';
        $ownerNameLineHeight = '1.35';
    } else {
        $ownerNameFontSize = '7.5pt';
        $ownerNameLineHeight = '2';
    }
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>KTAM KucingMu - {{ $cat->name }}</title>
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
            margin: 0mm;
            padding: 0mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 86mm;
            font-family: 'Plus Jakarta Sans', Helvetica, Arial, sans-serif;
            background-color: #0b1120;
        }

        /* CARD CONTAINER */
        .card-page {
            position: relative;
            width: 86mm;
            height: 54mm;
            overflow: hidden;
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0 auto;
            box-sizing: border-box;
        }

        /* FRONT SIDE (Page 1) */
        .card-front {
            background-image: url('{{ $frontBase64 }}');
            background-color: #0d2b78;
            page-break-after: always;
        }

        /* Foto Kucing di Halaman 1 (Pojok Kiri Bawah, Tepat di Atas Nomor Kode Kucing) */
        .front-photo-container {
            position: absolute;
            left: 4.5mm;
            bottom: 9.5mm;
            width: 17mm;
            height: 17mm;
            border-radius: 3px;
            border: 1.2px solid #ffffff;
            overflow: hidden;
            background-color: #0f172a;
            box-shadow: 0 1.5px 4px rgba(0, 0, 0, 0.45);
            z-index: 20;
        }

        .front-cat-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .front-unique-code {
            position: absolute;
            left: 4.5mm;
            bottom: 3.2mm;
            font-family: 'Finger Paint', cursive, sans-serif;
            font-size: 10.5pt;
            font-weight: 400;
            color: #ffffff;
            letter-spacing: 0.8px;
            text-transform: lowercase;
            line-height: 1;
            z-index: 20;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
        }

        /* BACK SIDE (Page 2) */
        .card-back {
            background-image: url('{{ $backBase64 }}');
            background-color: #0d2b78;
        }

        /* Value capsules overlaying the white boxes precisely with left alignment */
        .back-val {
            position: absolute;
            display: table;
            overflow: hidden;
            font-weight: 800;
            color: #0b224d;
            text-transform: uppercase;
            letter-spacing: 0.1px;
            z-index: 10;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .back-val-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            padding: 0 1.8mm;
            line-height: 1.15;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }

        /* Left column boxes (Positioned precisely inside white capsules) */
        .box-namaku {
            top: 13.5%;
            left: 17.75%;
            width: 23.5%;
            height: 6.6%;
        }

        .box-namaku .back-val-inner {
            color: #082440;
        }

        .box-dob {
            top: 22.3%;
            left: 17.75%;
            width: 30.03%;
            height: 6.6%;
        }

        .box-dob .back-val-inner {
            font-size: 7.8pt;
        }

        .box-breed {
            top: 31.2%;
            left: 17.75%;
            width: 30.03%;
            height: 9.1%;
        }

        .box-breed .back-val-inner {
            font-size: 7pt;
        }

        .box-color {
            top: 40.0%;
            left: 17.75%;
            width: 30.03%;
            height: 10.3%;
        }

        .box-color .back-val-inner {
            font-size: 7pt;
        }

        .box-nikumu {
            top: 50.0%;
            left: 17.75%;
            width: 30.03%;
            height: 5.5%;
        }

        .box-nikumu .back-val-inner {
            font-family: 'Courier New', Courier, monospace;
            font-size: 7pt;
            font-weight: 800;
            color: #0f4c3a;
        }

        /* Right column boxes (Positioned precisely inside white capsules with text wrapping) */
        .box-owner-name {
            top: 13.7%;
            left: 66.72%;
            width: 30.03%;
            height: 13.4%;
        }

        .box-owner-nbm {
            top: 29.1%;
            left: 66.72%;
            width: 30.03%;
            height: 13.4%;
        }

        .box-owner-nbm .back-val-inner {
            font-size: 7pt;
        }

        .box-owner-phone {
            top: 44.9%;
            left: 66.72%;
            width: 30.03%;
            height: 13.4%;
        }

        .box-owner-phone .back-val-inner {
            font-size: 7pt;
        }

        /* QR Code & Paw Box on Back Side (Bottom Right below Kontak Owner - Center) */
        .box-qr-bottom-right {
            position: absolute;
            top: 62.5%;
            left: 51.19%;
            width: 45.0%;
            height: 31.0%;
            z-index: 15;
            padding: 0;
            box-sizing: border-box;
        }

        .qr-bottom-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .back-qr-img {
            width: 14mm;
            height: 14mm;
            display: inline-block;
            background-color: #ffffff;
            border-radius: 2px;
            padding: 0.4mm;
        }

        .paw-slot-img {
            width: 13mm;
            height: 13mm;
            object-fit: cover;
            border-radius: 2px;
            border: 0.5px solid #cbd5e1;
            display: inline-block;
        }

        .slot-tag {
            font-size: 3.5pt;
            font-weight: 800;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            line-height: 1;
            margin-top: 0.3mm;
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
    <div class="card-page card-front">
        @if(isset($isDraft) && $isDraft)
            <div class="draft-watermark">DRAFT KUCINGMU</div>
        @endif

        <!-- Foto Kucing (Pojok Kiri Bawah, Tepat di Atas Nomor Kode Kucing) -->
        @if($photoData)
            <div class="front-photo-container">
                <img class="front-cat-img" src="{{ $photoData }}" alt="{{ $cat->name }}">
            </div>
        @elseif($pawPhotoData)
            <div class="front-photo-container">
                <img class="front-cat-img" src="{{ $pawPhotoData }}" alt="{{ $cat->name }}">
            </div>
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

        <!-- 1. NAMAKu (Auto-scaled for multiple words) -->
        <div class="back-val box-namaku">
            <div class="back-val-inner"
                style="font-size: {{ $catNameFontSize }}; line-height: {{ $catNameLineHeight }};">
                {{ $catName }}
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

        <!-- 6. NAMA OWNER (Auto-scaled for long / multiple words names) -->
        <div class="back-val box-owner-name">
            <div class="back-val-inner"
                style="font-size: {{ $ownerNameFontSize }}; line-height: {{ $ownerNameLineHeight }};">
                {{ $ownerName }}
            </div>
        </div>

        <!-- 7. NBM OWNER -->
        <div class="back-val box-owner-nbm">
            <div class="back-val-inner">
                {{ $cat->owner->formatted_nbm ?? ($cat->owner->muhammadiyah_id ?: '-') }}
            </div>
        </div>

        <!-- 8. KONTAK OWNER -->
        <div class="back-val box-owner-phone">
            <div class="back-val-inner">
                {{ $cat->owner->phone ?: '-' }}
            </div>
        </div>

        <!-- 9. QR CODE & TANDA PAW KUCING (Halaman 2 - Pojok Kanan Bawah Center) -->
        <div class="box-qr-bottom-right">
            <table class="qr-bottom-table">
                <tr>
                    @if($pawPhotoData && isset($card->qr_code_payload) && $card->qr_code_payload)
                        <td align="center" valign="middle" style="width: 50%;">
                            <img class="paw-slot-img" src="{{ $pawPhotoData }}" alt="Paw Biometrik">
                            <div class="slot-tag">Paw</div>
                        </td>
                        <td align="center" valign="middle" style="width: 50%;">
                            <img class="back-qr-img" src="{{ $card->qr_code_payload }}" alt="QR Verifikasi">
                            <div class="slot-tag">QR Verifikasi</div>
                        </td>
                    @elseif(isset($card->qr_code_payload) && $card->qr_code_payload)
                        <td align="center" valign="middle">
                            <img class="back-qr-img" src="{{ $card->qr_code_payload }}" alt="QR Verifikasi"
                                style="width: 14.5mm; height: 14.5mm;">
                            <div class="slot-tag" style="font-size: 3.8pt; margin-top: 0.4mm;">Scan Verifikasi KTAM</div>
                        </td>
                    @elseif($pawPhotoData)
                        <td align="center" valign="middle">
                            <img class="paw-slot-img" src="{{ $pawPhotoData }}" alt="Paw Biometrik"
                                style="width: 14mm; height: 14mm;">
                            <div class="slot-tag">Biometrik Paw</div>
                        </td>
                    @endif
                </tr>
            </table>
        </div>

    </div>

</body>

</html>