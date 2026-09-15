<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - {{ $appName }}</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b; line-height: 1.6;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05); border: 1px solid #e2e8f0;">

                    <!-- Header Section -->
                    <tr>
                        <td align="center"
                            style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); padding: 36px 24px 30px;">
                            <div style="margin-bottom: 12px;">
                                @if(!empty($logoPath) && file_exists($logoPath))
                                    <img src="{{ $message->embed($logoPath) }}" alt="{{ $appName }}"
                                        style="max-height: 56px; max-width: 180px; height: auto; display: block; margin: 0 auto; object-fit: contain;">
                                @else
                                    <div
                                        style="display: inline-block; background-color: rgba(255, 255, 255, 0.2); width: 56px; height: 56px; line-height: 56px; border-radius: 50%; font-size: 28px; text-align: center;">
                                        🐱
                                    </div>
                                @endif
                            </div>
                            <h1
                                style="margin: 8px 0 0; color: #ffffff; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                {{ $appName }}
                            </h1>
                            <p
                                style="margin: 4px 0 0; color: #ccfbf1; font-size: 13px; font-weight: 500; letter-spacing: 0.5px;">
                                Platform Komunitas Pecinta Kucing Muhammadiyah
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 32px 28px;">
                            <p style="margin: 0 0 12px; font-size: 15px; color: #64748b; font-weight: 500;">
                                Assalamu'alaikum Warahmatullahi Wabarakatuh,
                            </p>
                            <h2 style="margin: 0 0 16px; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Halo, {{ $user->name }}! 👋
                            </h2>
                            <p style="margin: 0 0 20px; font-size: 15px; color: #334155; line-height: 1.6;">
                                Selamat! Akun Anda pada platform <strong>{{ $appName }}</strong> telah berhasil
                                didaftarkan. Kini Anda telah resmi bergabung dalam inisiatif kepedulian kesehatan dan
                                pendataan kucing bersama komunitas kami. Silakan daftarkan Kucing Kesayanganmu pada
                                platform KucingMu dan dapatkan manfaat lainnya.
                            </p>

                            <!-- Account Details Card -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 18px 20px;">
                                        <div
                                            style="font-size: 12px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px;">
                                            Detail Akun Anda
                                        </div>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                            width="100%" style="font-size: 14px;">
                                            <tr>
                                                <td
                                                    style="padding: 6px 0; color: #64748b; width: 130px; vertical-align: top;">
                                                    Nama Lengkap:</td>
                                                <td style="padding: 6px 0; color: #0f172a; font-weight: 600;">
                                                    {{ $user->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #64748b; vertical-align: top;">Alamat
                                                    Email:</td>
                                                <td style="padding: 6px 0; color: #0f172a; font-weight: 600;">
                                                    {{ $user->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #64748b; vertical-align: top;">Waktu
                                                    Pendaftaran:</td>
                                                <td style="padding: 6px 0; color: #0f172a; font-weight: 600;">
                                                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }}
                                                    WIB
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Call to Action -->
                            <div style="text-align: center; margin: 30px 0 24px;">
                                <a href="{{ $loginUrl }}" target="_blank"
                                    style="display: inline-block; background-color: #0f766e; background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 10px rgba(15, 118, 110, 0.3);">
                                    Masuk ke Akun {{ $appName }} &rarr;
                                </a>
                            </div>

                            <!-- Security Notice -->
                            <div
                                style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 12px 16px; margin: 24px 0 16px;">
                                <p style="margin: 0; font-size: 13px; color: #92400e; line-height: 1.5;">
                                    <strong>Tips Keamanan:</strong> Jangan pernah membagikan kata sandi akun Anda kepada
                                    siapapun. Tim {{ $appName }} tidak akan pernah meminta kata sandi Anda.
                                </p>
                            </div>

                            <p style="margin: 20px 0 0; font-size: 14px; color: #475569; line-height: 1.6;">
                                Jika Anda tidak merasa melakukan pendaftaran ini, Anda dapat mengabaikan email ini
                                dengan aman.
                            </p>

                            <p style="margin: 20px 0 0; font-size: 14px; color: #334155;">
                                Wassalamu'alaikum Warahmatullahi Wabarakatuh,<br>
                                <strong style="color: #0f766e;">Tim {{ $appName }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td align="center"
                            style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 24px;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                &copy; {{ date('Y') }} {{ $appName }}. Warga Muhammadiyah Peduli Hewan.<br>
                                Email ini dikirim secara otomatis oleh sistem kami (<span
                                    style="color: #0f766e;">no-reply@kucingmu.online</span>).
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>