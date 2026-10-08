<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Coba SMTP - {{ $appName }}</title>
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
        }

        .content {
            padding: 32px;
        }

        .badge-success {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 700;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 16px;
        }

        .tech-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            font-size: 12px;
        }

        .tech-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .tech-box td {
            padding: 4px 8px;
        }

        .tech-box td.label {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }

        .footer {
            background-color: #f1f5f9;
            padding: 20px 32px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $appName }}</h1>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: #ccfbf1;">Diagnostik & Uji Coba Server Email</p>
        </div>

        <div class="content">
            <span class="badge-success">✅ KONEKSI SMTP BERHASIL</span>

            <p style="font-size: 14px; color: #334155; margin: 0 0 16px 0;">
                Halo <strong>{{ $testerName }}</strong>,<br>
                Email ini dikirimkan untuk memverifikasi bahwa konfigurasi server email (SMTP) pada aplikasi
                <strong>{{ $appName }}</strong> telah berfungsi dengan benar dan siap mengirimkan notifikasi.
            </p>

            <div class="tech-box">
                <table>
                    <tr>
                        <td class="label">Transport Mailer:</td>
                        <td><strong>{{ $smtpDetails['mailer'] ?? 'smtp' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Host SMTP:</td>
                        <td><code>{{ $smtpDetails['host'] ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <td class="label">Port SMTP:</td>
                        <td><code>{{ $smtpDetails['port'] ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <td class="label">Username Pengirim:</td>
                        <td><code>{{ $smtpDetails['username'] ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <td class="label">Alamat From:</td>
                        <td><code>{{ $smtpDetails['from_address'] ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <td class="label">Waktu Pengujian:</td>
                        <td>{{ now()->format('d/m/Y H:i:s T') }}</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 12px; color: #94a3b8; margin-top: 20px;">
                Catatan: Jika Anda menerima email ini, sistem pengiriman email aplikasi Anda sudah siap digunakan untuk
                operasional.
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ $appName }}</strong> &bull; KucingMu</p>
            <p>Sistem Pengelolaan Kartu Tanda Anggota KucingMu (KTAKuMu)</p>
        </div>
    </div>
</body>

</html>