<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
            background-color: #ffffff;
            width: 86mm;
            height: 54mm;
            overflow: hidden;
            -webkit-print-color-adjust: exact;
        }
        .card {
            position: relative;
            width: 86mm;
            height: 54mm;
            background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
            color: #ffffff;
            overflow: hidden;
        }
        /* Top brand bar */
        .brand-header {
            padding: 4px 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        .brand-title {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 6px;
            color: #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* Content area */
        .content {
            padding: 8px 10px;
            position: relative;
            height: calc(54mm - 32px);
        }
        /* Photo container */
        .photo-container {
            float: left;
            width: 20mm;
            height: 25mm;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background-color: rgba(255, 255, 255, 0.15);
        }
        .photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Details area */
        .details {
            float: left;
            margin-left: 8px;
            width: 32mm;
        }
        .field {
            margin-bottom: 3px;
        }
        .field-label {
            font-size: 5px;
            text-transform: uppercase;
            color: #cbd5e1;
            letter-spacing: 0.3px;
        }
        .field-value {
            font-size: 8px;
            font-weight: bold;
            color: #ffffff;
        }
        /* QR Code area */
        .qr-container {
            float: right;
            width: 16mm;
            height: 16mm;
            background-color: #ffffff;
            border-radius: 3px;
            padding: 2px;
            text-align: center;
            margin-top: 2px;
        }
        .qr-img {
            width: 100%;
            height: 100%;
        }
        .ktam-num-tag {
            clear: both;
            position: absolute;
            bottom: 4px;
            left: 10px;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.2px;
            color: #f1f5f9;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 2px 6px;
            border-radius: 2px;
        }
        .muhammadiyah-logo {
            position: absolute;
            bottom: 4px;
            right: 10px;
            font-size: 6px;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand-header">
            <span class="brand-title">KucingMu</span>
            <div class="brand-subtitle">Kartu Tanda Anggota Muhammadiyah Kucing</div>
        </div>
        <div class="content">
            <div class="photo-container">
                @if($cat->photo_path)
                    <!-- We use public_path to ensure DomPDF can access the file locally on Windows -->
                    <img class="photo" src="{{ public_path('storage/' . $cat->photo_path) }}" alt="{{ $cat->name }}">
                @else
                    <!-- Fallback placeholder image -->
                    <div style="width:100%; height:100%; text-align:center; padding-top:25px; font-size:8px; color:rgba(255,255,255,0.4);">No Photo</div>
                @endif
            </div>
            
            <div class="details">
                <div class="field">
                    <div class="field-label">Nama Kucing</div>
                    <div class="field-value">{{ $cat->name }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Ras / Breed</div>
                    <div class="field-value">{{ $cat->breed }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Pemilik</div>
                    <div class="field-value">{{ $cat->owner->name }}</div>
                </div>
                <div class="field">
                    <div class="field-label">NBM Pemilik</div>
                    <div class="field-value">{{ $cat->owner->muhammadiyah_id ?? '-' }}</div>
                </div>
            </div>
            
            <div class="qr-container">
                <img class="qr-img" src="{{ $card->qr_code_payload }}" alt="QR Verification">
            </div>
            
            <div class="ktam-num-tag">
                {{ $card->ktam_number }}
            </div>
            
            <div class="muhammadiyah-logo">
                Warga Muhammadiyah Peduli
            </div>
        </div>
    </div>
</body>
</html>
