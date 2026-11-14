<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Website Sedang Dalam Pemeliharaan - KucingMu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #0f766e;
            --primary-dark: #115e59;
            --primary-light: #ccfbf1;
            --amber: #f59e0b;
            --amber-dark: #d97706;
            --orange: #ea580c;
            --bg-start: #f8fafc;
            --bg-end: #f1f5f9;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --card-border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: linear-gradient(145deg, var(--bg-start) 0%, #ecfdf5 40%, var(--bg-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: var(--text-dark);
        }

        .container {
            max-width: 680px;
            width: 100%;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 40px 32px;
            box-shadow: 0 20px 45px -15px rgba(15, 118, 110, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative top accent */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0f766e 0%, #14b8a6 50%, #f59e0b 100%);
        }

        /* Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #f59e0b;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            animation: pulse-ring 1.8s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 7px rgba(245, 158, 11, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        /* Illustration wrapper */
        .illustration-wrapper {
            margin: 0 auto 20px;
            max-width: 320px;
            width: 100%;
        }

        /* SVG Animations */
        .gear-spin-cw {
            transform-origin: center;
            animation: spinCW 10s linear infinite;
        }

        .gear-spin-ccw {
            transform-origin: center;
            animation: spinCCW 7s linear infinite;
        }

        .tail-wag {
            transform-origin: 190px 185px;
            animation: wagTail 2.4s ease-in-out infinite;
        }

        .eye-blink {
            animation: blinkEyes 4s ease-in-out infinite;
            transform-origin: center;
        }

        .steam-bubble {
            animation: floatUp 2.5s ease-in-out infinite;
        }

        @keyframes spinCW {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinCCW {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(-360deg);
            }
        }

        @keyframes wagTail {

            0%,
            100% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(-12deg);
            }
        }

        @keyframes blinkEyes {

            0%,
            94%,
            100% {
                transform: scaleY(1);
            }

            97% {
                transform: scaleY(0.1);
            }
        }

        @keyframes floatUp {
            0% {
                opacity: 0;
                transform: translateY(0);
            }

            50% {
                opacity: 0.8;
            }

            100% {
                opacity: 0;
                transform: translateY(-16px);
            }
        }

        /* Typography */
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.25;
            margin-bottom: 12px;
        }

        p.description {
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-muted);
            max-width: 520px;
            margin: 0 auto 24px;
        }

        /* Info Card */
        .info-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            padding: 14px 18px;
            margin-bottom: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 13px;
            color: #334155;
        }

        .info-box span.icon {
            font-size: 18px;
        }

        /* Action Buttons */
        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            min-height: 48px;
            padding: 12px 24px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(15, 118, 110, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #115e59 0%, #134e4a 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 118, 110, 0.4);
        }

        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* Auto-refresh timer text */
        .auto-refresh {
            margin-top: 22px;
            font-size: 12px;
            color: #94a3b8;
        }

        .auto-refresh strong {
            color: var(--primary);
            font-weight: 700;
        }

        /* Footer */
        .footer-note {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        @media (max-width: 640px) {
            .container {
                padding: 32px 20px;
                border-radius: 22px;
            }

            h1 {
                font-size: 22px;
            }

            p.description {
                font-size: 13px;
            }

            .btn {
                width: 100%;
            }

            .footer-note {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Status Pill -->
        <div class="status-badge">
            <span class="pulse-dot"></span>
            <span>Status 503 &bull; Pemeliharaan Rutin</span>
        </div>

        <!-- SVG Cute Cat Engineer Illustration -->
        <div class="illustration-wrapper">
            <svg viewBox="0 0 320 220" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg" role="img"
                aria-label="Ilustrasi Kucing Teknisi Sedang Maintenance">
                <defs>
                    <!-- Shadow Filter -->
                    <filter id="soft-shadow" x="-10%" y="-10%" width="130%" height="130%">
                        <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#0f766e" flood-opacity="0.12" />
                    </filter>

                    <!-- Orange Cat Fur Gradient -->
                    <linearGradient id="cat-fur" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#fb923c" />
                        <stop offset="100%" stop-color="#ea580c" />
                    </linearGradient>

                    <!-- Helmet Gradient -->
                    <linearGradient id="helmet-grad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#fde047" />
                        <stop offset="100%" stop-color="#eab308" />
                    </linearGradient>

                    <!-- Laptop Metallic -->
                    <linearGradient id="laptop-body" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#cbd5e1" />
                        <stop offset="100%" stop-color="#94a3b8" />
                    </linearGradient>
                </defs>

                <!-- Desk Base Surface -->
                <ellipse cx="160" cy="200" rx="140" ry="14" fill="#e2e8f0" opacity="0.7" />

                <!-- Background Gears -->
                <g opacity="0.35">
                    <!-- Gear 1 (Teal) -->
                    <g class="gear-spin-cw" transform="translate(65, 55)">
                        <circle cx="0" cy="0" r="22" fill="none" stroke="#0f766e" stroke-width="6" />
                        <circle cx="0" cy="0" r="10" fill="#0f766e" />
                        <rect x="-3" y="-28" width="6" height="56" rx="2" fill="#0f766e" />
                        <rect x="-28" y="-3" width="56" height="6" rx="2" fill="#0f766e" />
                        <rect x="-20" y="-20" width="40" height="6" rx="2" fill="#0f766e" transform="rotate(45)" />
                        <rect x="-20" y="-20" width="40" height="6" rx="2" fill="#0f766e" transform="rotate(-45)" />
                    </g>

                    <!-- Gear 2 (Amber) -->
                    <g class="gear-spin-ccw" transform="translate(255, 60)">
                        <circle cx="0" cy="0" r="16" fill="none" stroke="#f59e0b" stroke-width="5" />
                        <circle cx="0" cy="0" r="7" fill="#f59e0b" />
                        <rect x="-2.5" y="-21" width="5" height="42" rx="1.5" fill="#f59e0b" />
                        <rect x="-21" y="-2.5" width="42" height="5" rx="1.5" fill="#f59e0b" />
                        <rect x="-15" y="-15" width="30" height="5" rx="1.5" fill="#f59e0b" transform="rotate(45)" />
                        <rect x="-15" y="-15" width="30" height="5" rx="1.5" fill="#f59e0b" transform="rotate(-45)" />
                    </g>
                </g>

                <!-- Coffee Mug with Paw Print & Steam -->
                <g transform="translate(42, 160)">
                    <rect x="0" y="8" width="18" height="22" rx="4" fill="#ffffff" stroke="#cbd5e1"
                        stroke-width="1.5" />
                    <!-- Handle -->
                    <path d="M 18 12 Q 26 12 26 19 Q 26 26 18 26" fill="none" stroke="#cbd5e1" stroke-width="2" />
                    <!-- Little Paw on Mug -->
                    <circle cx="9" cy="20" r="2.5" fill="#f59e0b" />
                    <circle cx="6" cy="16" r="1.2" fill="#f59e0b" />
                    <circle cx="9" cy="15" r="1.2" fill="#f59e0b" />
                    <circle cx="12" cy="16" r="1.2" fill="#f59e0b" />
                    <!-- Steam Bubbles -->
                    <circle class="steam-bubble" cx="8" cy="2" r="2" fill="#94a3b8" style="animation-delay: 0s;" />
                    <circle class="steam-bubble" cx="12" cy="-4" r="1.5" fill="#94a3b8"
                        style="animation-delay: 0.8s;" />
                </g>

                <!-- Tool Wrench on Desk -->
                <g transform="translate(245, 175) rotate(-25)">
                    <rect x="0" y="3" width="32" height="6" rx="2" fill="#94a3b8" />
                    <path d="M 0 0 C 4 0 7 2 7 6 C 7 10 4 12 0 12 C -2 12 -4 10 -4 6 C -4 2 -2 0 0 0 Z"
                        fill="#64748b" />
                    <circle cx="0" cy="6" r="2.5" fill="#f8fafc" />
                    <path d="M 32 1 C 35 1 37 3 37 6 C 37 9 35 11 32 11 Z" fill="#64748b" />
                </g>

                <!-- CAT BODY & TAIL -->
                <!-- Tail with animation -->
                <path class="tail-wag" d="M 190 180 Q 235 170 230 140 Q 225 125 212 135" fill="none"
                    stroke="url(#cat-fur)" stroke-width="12" stroke-linecap="round" />

                <!-- Cat Body Back -->
                <ellipse cx="160" cy="160" rx="42" ry="32" fill="url(#cat-fur)" filter="url(#soft-shadow)" />
                <!-- White chest bib -->
                <ellipse cx="160" cy="165" rx="22" ry="18" fill="#fff7ed" />

                <!-- Left & Right Ears -->
                <polygon points="128,105 118,65 145,85" fill="url(#cat-fur)" />
                <polygon points="130,100 124,73 142,87" fill="#fed7aa" />
                <polygon points="192,105 202,65 175,85" fill="url(#cat-fur)" />
                <polygon points="190,100 196,73 178,87" fill="#fed7aa" />

                <!-- Cat Head -->
                <circle cx="160" cy="115" r="36" fill="url(#cat-fur)" filter="url(#soft-shadow)" />

                <!-- Tabby Forehead Stripes -->
                <path d="M 160 88 L 160 98" stroke="#c2410c" stroke-width="2.5" stroke-linecap="round" />
                <path d="M 152 90 L 154 99" stroke="#c2410c" stroke-width="2" stroke-linecap="round" />
                <path d="M 168 90 L 166 99" stroke="#c2410c" stroke-width="2" stroke-linecap="round" />

                <!-- Engineer Safety Helmet -->
                <g>
                    <!-- Helmet dome -->
                    <path d="M 128 102 C 126 72, 194 72, 192 102 Z" fill="url(#helmet-grad)" stroke="#ca8a04"
                        stroke-width="1.5" />
                    <!-- Helmet rim/brim -->
                    <ellipse cx="160" cy="102" rx="36" ry="5.5" fill="#facc15" stroke="#ca8a04" stroke-width="1.5" />
                    <!-- Helmet badge (Green Paw Print) -->
                    <circle cx="160" cy="90" r="5.5" fill="#0f766e" />
                    <circle cx="160" cy="91" r="2.2" fill="#ffffff" />
                    <circle cx="157.5" cy="87.5" r="1.1" fill="#ffffff" />
                    <circle cx="160" cy="86.5" r="1.1" fill="#ffffff" />
                    <circle cx="162.5" cy="87.5" r="1.1" fill="#ffffff" />
                </g>

                <!-- Face details -->
                <!-- Eyes (Blinking animation) -->
                <g class="eye-blink">
                    <ellipse cx="147" cy="116" rx="4.5" ry="5.5" fill="#0f172a" />
                    <circle cx="145.5" cy="114" r="1.8" fill="#ffffff" />
                    <ellipse cx="173" cy="116" rx="4.5" ry="5.5" fill="#0f172a" />
                    <circle cx="171.5" cy="114" r="1.8" fill="#ffffff" />
                </g>

                <!-- Rosy Cheeks -->
                <ellipse cx="138" cy="124" rx="4" ry="2.5" fill="#fca5a5" opacity="0.6" />
                <ellipse cx="182" cy="124" rx="4" ry="2.5" fill="#fca5a5" opacity="0.6" />

                <!-- Nose and Mouth -->
                <polygon points="160,123 156,120 164,120" fill="#f43f5e" />
                <path d="M 156 123 Q 160 127 160 125 Q 160 127 164 123" fill="none" stroke="#78350f" stroke-width="1.5"
                    stroke-linecap="round" />

                <!-- Whiskers -->
                <g stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" opacity="0.9">
                    <!-- Left whiskers -->
                    <line x1="140" y1="122" x2="118" y2="118" />
                    <line x1="139" y1="125" x2="116" y2="126" />
                    <!-- Right whiskers -->
                    <line x1="180" y1="122" x2="202" y2="118" />
                    <line x1="181" y1="125" x2="204" y2="126" />
                </g>

                <!-- Paws resting on Laptop Keyboard -->
                <!-- LAPTOP WORKSTATION -->
                <g transform="translate(110, 148)">
                    <!-- Laptop Screen Back / Open lid -->
                    <polygon points="10,38 90,38 84,6 16,6" fill="url(#laptop-body)" stroke="#64748b"
                        stroke-width="1.5" />
                    <!-- Laptop Screen Inner Display (Teal Terminal) -->
                    <polygon points="13,36 87,36 82,9 18,9" fill="#042f2e" />
                    <!-- Terminal code lines on screen -->
                    <line x1="24" y1="15" x2="48" y2="15" stroke="#2dd4bf" stroke-width="1.5" stroke-linecap="round" />
                    <line x1="24" y1="20" x2="68" y2="20" stroke="#fef08a" stroke-width="1.5" stroke-linecap="round" />
                    <line x1="24" y1="25" x2="56" y2="25" stroke="#67e8f9" stroke-width="1.5" stroke-linecap="round" />
                    <line x1="24" y1="30" x2="40" y2="30" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" />

                    <!-- Keyboard Base -->
                    <polygon points="0,48 100,48 90,38 10,38" fill="#e2e8f0" stroke="#94a3b8" stroke-width="1.5" />
                    <line x1="20" y1="43" x2="80" y2="43" stroke="#cbd5e1" stroke-width="2" />
                </g>

                <!-- Cat Paws typing on keys -->
                <ellipse cx="132" cy="184" rx="7.5" ry="5.5" fill="#fff7ed" stroke="url(#cat-fur)" stroke-width="2" />
                <ellipse cx="188" cy="184" rx="7.5" ry="5.5" fill="#fff7ed" stroke="url(#cat-fur)" stroke-width="2" />

                <!-- Cute Floating wrench & screwdriver spark bubbles -->
                <g opacity="0.75">
                    <circle cx="85" cy="100" r="3" fill="#2dd4bf" />
                    <circle cx="235" cy="115" r="2.5" fill="#f59e0b" />
                    <path d="M 85 92 L 87 96 L 91 96 L 88 98 L 89 102 L 85 100 L 81 102 L 82 98 L 79 96 L 83 96 Z"
                        fill="#facc15" transform="scale(0.6) translate(60, 40)" />
                    <path
                        d="M 230 85 L 232 89 L 236 89 L 233 91 L 234 95 L 230 93 L 226 95 L 227 91 L 224 89 L 228 89 Z"
                        fill="#38bdf8" transform="scale(0.5) translate(220, 70)" />
                </g>
            </svg>
        </div>

        <!-- Headline & Copy -->
        <h1>Anabul Engineer Sedang Merapikan Sistem!</h1>
        <p class="description">
            Website KucingMu sedang menjalani peningkatan performa, pembaruan basis data, dan optimalisasi server
            berkala agar layanan komunitas anabul semakin cepat dan nyaman digunakan.
        </p>

        <!-- Informative status card -->
        <div class="info-box">
            <span class="icon">🔧</span>
            <span>Semua data akun dan catatan kesehatan kucing Anda tetap aman tersimpan.</span>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="handleRefresh()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Muat Ulang Halaman</span>
            </button>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20KucingMu,%20saya%20ingin%20menanyakan%20status%20layanan"
                target="_blank" rel="noopener" class="btn btn-secondary">
                <span>💬 Hubungi Admin</span>
            </a>
        </div>

        <!-- Auto Countdown Timer -->
        <div class="auto-refresh">
            Halaman akan dicek ulang otomatis dalam <strong id="countdown">30</strong> detik.
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <span>&copy; <span id="year"></span> KucingMu &bull; </span>
            <span>Kode Status: <strong>503 Service Unavailable</strong></span>
        </div>
    </div>

    <script>
        // Set dynamic year
        document.getElementById('year').textContent = new Date().getFullYear();

        // Auto Refresh Countdown
        let secondsLeft = 30;
        const countdownEl = document.getElementById('countdown');

        const interval = setInterval(() => {
            secondsLeft--;
            if (countdownEl) {
                countdownEl.textContent = secondsLeft;
            }
            if (secondsLeft <= 0) {
                clearInterval(interval);
                handleRefresh();
            }
        }, 1000);

        function handleRefresh() {
            window.location.reload();
        }
    </script>
</body>

</html>