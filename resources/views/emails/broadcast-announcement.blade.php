<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }} - {{ $appName }}</title>
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
            padding: 30px 32px 26px 32px;
            text-align: center;
        }

        .header-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ccfbf1;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 8px;
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
            margin-bottom: 16px;
        }

        .message-body {
            font-size: 14px;
            color: #334155;
            line-height: 1.75;
            margin-bottom: 24px;
        }

        .message-body p {
            margin: 0 0 14px 0;
        }

        /* Event Card */
        .event-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            margin: 24px 0;
        }

        .event-banner {
            width: 100%;
            height: auto;
            max-height: 220px;
            object-fit: cover;
            display: block;
        }

        .event-body {
            padding: 20px;
        }

        .event-tag {
            display: inline-block;
            background-color: #0f766e;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .event-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 10px 0;
            line-height: 1.35;
        }

        .event-detail-item {
            font-size: 13px;
            color: #475569;
            margin: 6px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* CTA Section */
        .cta-section {
            text-align: center;
            margin: 30px 0 20px 0;
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
            box-shadow: 0 4px 6px -1px rgba(15, 118, 110, 0.25);
            transition: background-color 0.2s;
        }

        .info-tip {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid #16a34a;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 12px;
            color: #166534;
            margin: 24px 0 10px 0;
            line-height: 1.6;
        }

        .footer {
            background-color: #f8fafc;
            padding: 24px 32px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            line-height: 1.6;
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
            @if($broadcastType === 'event_announcement')
                <div class="header-badge">📅 Agenda & Kegiatan KucingMu</div>
            @elseif($broadcastType === 'ktam_update')
                <div class="header-badge">🪪 Pembaruan KTAKuMu</div>
            @else
                <div class="header-badge">📢 Pengumuman Komunitas</div>
            @endif

            <h1>🐱 {{ $appName }}</h1>
            <p>Pemberitahuan Resmi untuk Seluruh Rekan Member KucingMu</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Assalamu’alaikum Wr. Wb. / Halo, {{ $user->name }}
            </div>

            <!-- Message Body -->
            <div class="message-body">
                {!! nl2br(e($messageBody)) !!}
            </div>

            <!-- Event Card if attached -->
            @if($event)
                <div class="event-card">
                    @if($event->banner_path)
                        <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="event-banner">
                    @endif
                    <div class="event-body">
                        <span class="event-tag">Agenda Terdekat</span>
                        <h2 class="event-title">{{ $event->title }}</h2>
                        <div class="event-detail-item">
                            <span>📅</span> <strong>Hari & Tanggal:</strong> {{ $event->date ? $event->date->translatedFormat('l, d F Y') : '-' }}
                        </div>
                        <div class="event-detail-item">
                            <span>📍</span> <strong>Lokasi:</strong> {{ $event->location }}
                        </div>
                        <div class="event-detail-item">
                            <span>📋</span> <strong>Pendaftaran:</strong> {{ $event->registration_link ? 'Pendaftaran Online Dibuka' : 'Pendaftaran Langsung di Lokasi' }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Call-to-Action Button -->
            <div class="cta-section">
                <a href="{{ $ctaUrl }}" class="cta-button" target="_blank" rel="noopener noreferrer">
                    {{ $ctaText }} &rarr;
                </a>
            </div>

            <!-- Helpful tip for members who haven't registered cats yet -->
            <div class="info-tip">
                💡 <strong>Tips Member KucingMu:</strong> Pastikan data kucing peliharaan atau komunitas Anda sudah terdaftar di portal untuk mendapatkan Kartu Tanda Anggota KucingMu (KTAKuMu) resmi secara gratis serta rekam medis terpadu.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $appName }}</strong> — E-Surveillance & Pelayanan Kesehatan Kucing Komunitas.</p>
            <p>Pesan ini dikirimkan resmi oleh {{ $senderName }} kepada <strong>{{ $user->email }}</strong> sebagai member terdaftar.</p>
            <p style="margin-top: 8px; font-size: 11px; color: #94a3b8;">
                Jika Anda memiliki pertanyaan seputar kegiatan atau kartu anggota, silakan balas email ini atau hubungi layanan pengurus kami.
            </p>
        </div>
    </div>
</body>

</html>
