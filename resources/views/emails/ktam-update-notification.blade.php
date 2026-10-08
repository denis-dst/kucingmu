<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyesuaian KTAKuMu - {{ $appName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 620px;
            margin: 28px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .header {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #ffffff;
            padding: 32px 32px 28px 32px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #ccfbf1;
            font-weight: 500;
        }

        .content {
            padding: 32px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 14px;
        }

        .intro-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .alert-box {
            background-color: #f0fdfa;
            border: 1px solid #99f6e4;
            border-left: 4px solid #0f766e;
            padding: 16px 18px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 13px;
            color: #115e59;
            line-height: 1.6;
        }

        .cards-list {
            margin: 24px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .cat-item {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
        }

        .cat-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .cat-meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .ktam-badge {
            display: inline-block;
            background-color: #0f766e;
            color: #ffffff;
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 6px;
            margin-top: 8px;
            letter-spacing: 0.5px;
        }

        .cta-section {
            text-align: center;
            margin: 32px 0 24px 0;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
        }

        .cta-button {
            display: inline-block;
            background-color: #0f766e;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(15, 118, 110, 0.2);
            transition: background-color 0.2s;
        }

        .custom-note {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 13px;
            color: #92400e;
            margin: 20px 0;
            line-height: 1.6;
        }

        .footer {
            background-color: #f8fafc;
            padding: 24px 32px;
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
        <!-- Header -->
        <div class="header">
            <h1>🐱 {{ $appName }}</h1>
            <p>Pemberitahuan Resmi Penyesuaian & Verifikasi KTAKuMu Digital</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Assalamu’alaikum Wr. Wb. / Halo, {{ $user->name }}
            </div>

            <div class="intro-text">
                Semoga Anda dan anabul kesayangan senantiasa dalam keadaan sehat dan berkah. Kami dari Tim Pengelola
                <strong>{{ $appName }}</strong> menginformasikan bahwa telah dilakukan <strong>standarisasi dan
                    penyesuaian nomor registrasi resmi KTAKuMu (Kartu Tanda Anggota KucingMu)</strong> untuk seluruh
                anabul yang telah terverifikasi.
            </div>

            <div class="alert-box">
                ℹ️ <strong>Informasi Pembaruan:</strong><br>
                Nomor identitas digital (NIAKuMu) kucing Anda telah diselaraskan dengan basis data nasional KucingMu.
                Kartu fisik maupun digital versi terbaru kini dapat langsung Anda cek dan unduh melalui portal anggota.
            </div>

            <!-- Cat List -->
            <div style="margin-top: 20px; font-weight: 700; font-size: 14px; color: #1e293b;">
                📋 Daftar Kucing & Nomor KTAKuMu Terbaru Anda:
            </div>
            <div class="cards-list">
                @forelse($cats as $cat)
                    <div class="cat-item">
                        <div class="cat-name">🐾 {{ $cat->name }}</div>
                        <div class="cat-meta">
                            Ras: {{ $cat->breed ?? 'Domestik' }} &bull;
                            Jenis Kelamin: {{ $cat->gender == 'male' ? 'Jantan' : 'Betina' }}
                        </div>
                        <div class="ktam-badge">
                            {{ $cat->unique_code ?: ($cat->ktamCard->ktam_number ?? 'TERBIT') }}
                        </div>
                    </div>
                @empty
                    <div class="cat-item">
                        <div class="cat-name">🐾 Data Kucing Terdaftar</div>
                        <div class="cat-meta">Silakan periksa detailnya langsung pada portal akun Anda.</div>
                    </div>
                @endforelse
            </div>

            @if(!empty($customNote))
                <div class="custom-note">
                    <strong>📌 Catatan Tambahan dari Admin:</strong><br>
                    {!! nl2br(e($customNote)) !!}
                </div>
            @endif

            <!-- CTA Section -->
            <div class="cta-section">
                <p style="font-size: 13px; color: #475569; margin: 0 0 14px 0; font-weight: 500;">
                    Silakan klik tombol di bawah untuk melihat kartu identitas digital dan riwayat pemeriksaan
                    kesehatan:
                </p>
                <a href="{{ $portalUrl }}" class="cta-button" target="_blank">
                    📱 Buka Portal & Lihat KTAKuMu Saya
                </a>
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.6;">
                Jika terdapat ketidaksesuaian data atau ada pertanyaan lebih lanjut, silakan hubungi tim kami melalui
                menu <em>Hubungi Kami</em> di portal atau membalas email ini.<br><br>
                Terima kasih atas partisipasi dan kepedulian Anda dalam menjaga kesehatan dan kesejahteraan anabul
                bersama Muhammadiyah.
            </p>

            <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
                Wassalamu’alaikum Wr. Wb.<br>
                Salam hormat,<br>
                <strong>{{ $senderName }}</strong><br>
                <span style="font-size: 11px; color: #94a3b8;">Tim Pengelola {{ $appName }}</span>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $appName }}</strong> &bull; KucingMu</p>
            <p>Alamat: Jl. Gedongkuning No.130 B, Rejowinangun, Kec. Kotagede, Kota Yogyakarta, D.I. Yogyakarta 55171
            </p>
            <p>Email: bidkes.immdiy@gmail.com / no-reply@kucingmu.online</p>
        </div>
    </div>
</body>

</html>