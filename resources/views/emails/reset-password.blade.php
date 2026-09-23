<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color:#176C33;padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;">{{ config('app.name', 'Jejak Tumbuh') }}</h1>
                            <p style="color:#d4f0d8;margin:8px 0 0;font-size:14px;">Reset Password</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p style="color:#3D3D3D;font-size:16px;margin:0 0 16px;">Halo <strong>{{ $name }}</strong>,</p>
                            <p style="color:#3D3D3D;font-size:16px;margin:0 0 16px;">Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>
                            <p style="text-align:center;margin:30px 0;">
                                <a href="{{ $resetUrl }}" style="display:inline-block;background-color:#176C33;color:#ffffff;padding:14px 40px;border-radius:12px;text-decoration:none;font-weight:bold;font-size:16px;">Reset Password</a>
                            </p>
                            <p style="color:#666666;font-size:14px;margin:0 0 8px;">Link ini akan kedaluwarsa dalam 60 menit.</p>
                            <p style="color:#666666;font-size:14px;margin:0;">Jika Anda tidak meminta reset password, abaikan email ini.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f8f8;padding:20px;text-align:center;">
                            <p style="color:#999999;font-size:12px;margin:0;">&copy; {{ date('Y') }} {{ config('app.name', 'Jejak Tumbuh') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
