@php
    // Inline Base64 Logo
    $logoPath = public_path('images/logo-muhammadiyah.svg');
    $logoData = null;
    if (file_exists($logoPath)) {
        $logoData = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath));
    }

    // Inline Base64 Photo
    $photoData = null;
    if ($cat->photo_path) {
        $storagePath = storage_path('app/public/' . $cat->photo_path);
        $publicPath = public_path('storage/' . $cat->photo_path);
        
        if (file_exists($storagePath)) {
            $photoData = 'data:' . mime_content_type($storagePath) . ';base64,' . base64_encode(file_get_contents($storagePath));
        } elseif (file_exists($publicPath)) {
            $photoData = 'data:' . mime_content_type($publicPath) . ';base64,' . base64_encode(file_get_contents($publicPath));
        }
    }
@endphp
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>KTAM KucingMu - {{ $cat->name }}</title>
    <style>
        @page {
            size: 86mm 54mm;
            margin: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #061d12;
            width: 86mm;
            height: 54mm;
            overflow: hidden;
            -webkit-print-color-adjust: exact;
        }

        .card {
            position: relative;
            width: 86mm;
            height: 54mm;
            background-color: #061d12;
            color: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Watermark Background */
        .watermark {
            position: absolute;
            right: -15mm;
            bottom: -15mm;
            width: 50mm;
            height: 50mm;
            opacity: 0.08;
            z-index: 0;
        }

        /* Gold bar border at the top and bottom edges */
        .gold-border-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1.5mm;
            background: #ffd700;
            z-index: 10;
        }

        /* Header section */
        .header-table {
            position: absolute;
            top: 1.5mm;
            left: 0;
            width: 100%;
            height: 12mm;
            padding: 2mm 3mm 0 3mm;
            z-index: 10;
            border-bottom: 0.5px solid rgba(255, 255, 255, 0.15);
            box-sizing: border-box;
        }

        .logo-container {
            width: 9mm;
            height: 9mm;
            vertical-align: middle;
        }

        .logo-img {
            width: 9mm;
            height: 9mm;
        }

        .header-text-container {
            padding-left: 2.5mm;
            vertical-align: middle;
            text-align: left;
        }

        .org-title {
            font-size: 5.5px;
            font-weight: bold;
            color: #ffd700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            line-height: 1;
        }

        .card-title {
            font-size: 8.5px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin: 1px 0 0 0;
            padding: 0;
            line-height: 1;
        }

        /* Content section */
        .content-table {
            position: absolute;
            top: 14.5mm;
            left: 0;
            width: 100%;
            height: 31mm;
            padding: 0 3mm;
            z-index: 10;
            box-sizing: border-box;
        }

        /* Column widths: Photo (21mm), Details (39mm), QR (20mm) */
        .col-photo {
            width: 21mm;
            vertical-align: top;
            padding-top: 1mm;
        }

        .col-details {
            width: 39mm;
            vertical-align: top;
            padding-left: 3mm;
            padding-top: 1mm;
        }

        .col-qr {
            width: 20mm;
            vertical-align: top;
            text-align: right;
            padding-top: 1mm;
        }

        /* Photo Styles */
        .photo-frame {
            width: 18mm;
            height: 23mm;
            border: 1.5px solid #ffd700;
            border-radius: 2px;
            background-color: rgba(255, 255, 255, 0.1);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            text-align: center;
            padding-top: 9mm;
            font-size: 6px;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
        }

        /* Detail Field Styles */
        .detail-field {
            margin-bottom: 1.8px;
        }

        .field-label {
            font-size: 4px;
            color: #a7f3d0;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.4px;
            margin: 0;
            line-height: 1;
        }

        .field-value {
            font-size: 7.5px;
            color: #ffffff;
            font-weight: bold;
            margin: 0.5px 0 0 0;
            line-height: 1.1;
            text-transform: uppercase;
        }

        /* QR Styles */
        .qr-frame {
            display: inline-block;
            width: 14mm;
            height: 14mm;
            background-color: #ffffff;
            padding: 1mm;
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .qr-img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .qr-label {
            font-size: 4px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.5px;
            text-align: center;
            margin-top: 1mm;
            text-transform: uppercase;
        }

        /* Footer section */
        .footer-table {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 7.5mm;
            padding: 0 3mm 2mm 3mm;
            z-index: 10;
            box-sizing: border-box;
            vertical-align: middle;
        }

        .ktam-badge {
            display: inline-block;
            background-color: #ffd700;
            color: #094a28;
            padding: 1px 4px;
            border-radius: 1.5px;
            font-size: 6.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .footer-slogan {
            font-size: 5px;
            font-style: italic;
            color: #a7f3d0;
            text-align: right;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="card">
        <!-- Gold Top Border -->
        <div class="gold-border-top"></div>

        <!-- Watermark Background -->
        @if($logoData)
            <img class="watermark" src="{{ $logoData }}" alt="watermark">
        @endif

        <!-- Header -->
        <table class="header-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="logo-container">
                    @if($logoData)
                        <img class="logo-img" src="{{ $logoData }}" alt="Logo">
                    @endif
                </td>
                <td class="header-text-container">
                    <div class="org-title">kucingmu.online</div>
                    <div class="card-title">Kartu Tanda Anggota Muhammadiyah KucingMu</div>
                </td>
            </tr>
        </table>

        <!-- Body Content -->
        <table class="content-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <!-- Column 1: Photo -->
                <td class="col-photo">
                    <div class="photo-frame">
                        @if($photoData)
                            <img class="photo-img" src="{{ $photoData }}" alt="{{ $cat->name }}">
                        @else
                            <div class="photo-placeholder">No Photo</div>
                        @endif
                    </div>
                </td>

                <!-- Column 2: Details -->
                <td class="col-details">
                    <div class="detail-field">
                        <div class="field-label">Nama Kucing</div>
                        <div class="field-value">{{ $cat->name }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="field-label">Ras / Breed</div>
                        <div class="field-value">{{ $cat->breed }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="field-label">Pemilik</div>
                        <div class="field-value">{{ $cat->owner->name }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="field-label">NBM Pemilik</div>
                        <div class="field-value">{{ $cat->owner->muhammadiyah_id ?? '-' }}</div>
                    </div>
                </td>

                <!-- Column 3: QR Code -->
                <td class="col-qr">
                    <div class="qr-frame">
                        <img class="qr-img" src="{{ $card->qr_code_payload }}" alt="QR Code">
                    </div>
                    <div class="qr-label">Scan Verifikasi</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <table class="footer-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" valign="middle">
                    <div class="ktam-badge">{{ $card->ktam_number }}</div>
                </td>
                <td align="right" valign="middle" class="footer-slogan">
                    "Sayangilah makhluk di bumi, niscaya yang di langit menyayangimu"
                </td>
            </tr>
        </table>
    </div>
</body>

</html>