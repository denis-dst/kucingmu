<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respon Pesan Kontak {{ $appName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0f766e;
            color: #ffffff;
            padding: 24px 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .response-box {
            background-color: #f0fdfa;
            border-left: 4px solid #0f766e;
            padding: 16px 20px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
            color: #134e4a;
            white-space: pre-line;
        }
        .original-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: 8px;
            margin-top: 24px;
            font-size: 13px;
        }
        .original-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px 32px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #ccfbf1;">Respon Resmi Pesan Kontak</p>
        </div>
        
        <div class="content">
            <div class="greeting">Halo, {{ $contactMessage->name }}</div>
            <p style="font-size: 14px; color: #475569; margin: 0 0 16px 0;">
                Terima kasih telah menghubungi tim <strong>{{ $appName }}</strong>. Berikut adalah tanggapan resmi dari administrator kami mengenai pesan Anda dengan subjek <em>"{{ $contactMessage->subject }}"</em>:
            </p>

            <div class="response-box">
                {!! nl2br(e($responseContent)) !!}
            </div>

            <p style="font-size: 12px; color: #64748b; margin-top: 16px;">
                Dibalas oleh: <strong>{{ $responderName }}</strong> (Admin {{ $appName }})
            </p>

            <div class="original-box">
                <div class="original-title">Pesan Asli yang Anda Kirimkan:</div>
                <p style="margin: 0; color: #475569; font-style: italic;">"{!! nl2br(e($contactMessage->message)) !!}"</p>
            </div>
        </div>

        <div class="footer">
            <p><strong>{{ $appName }}</strong> &bull; Majelis Lingkungan Hidup Pimpinan Pusat Muhammadiyah</p>
            <p>Alamat: Jl. Gedongkuning No.130 B, Rejowinangun, Kec. Kotagede, Kota Yogyakarta, D.I. Yogyakarta 55171</p>
            <p>Email: bidkes.immdiy@gmail.com / kucingmuhammadiyah@gmail.com</p>
        </div>
    </div>
</body>
</html>
