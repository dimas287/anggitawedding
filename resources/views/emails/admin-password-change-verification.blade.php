<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 620px; margin: 0 auto; background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(15,23,42,0.08); }
        .header { background: linear-gradient(135deg, #0b0a2f, #3b1d60); padding: 28px; color: white; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 28px; color: #1f2937; }
        .cta { display: inline-block; margin-top: 18px; padding: 12px 18px; border-radius: 999px; background: #111827; color: #fff; text-decoration: none; font-size: 13px; }
        .note { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 12px 14px; font-size: 12px; color: #92400e; margin-top: 16px; }
        .footer { background: #0f172a; color: rgba(255,255,255,0.7); text-align: center; font-size: 12px; padding: 16px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Verifikasi Perubahan Password Admin</h1>
        <p style="margin-top:6px;font-size:13px;color:rgba(255,255,255,0.8)">Klik tombol di bawah untuk konfirmasi.</p>
    </div>
    <div class="body">
        <p>Halo <strong>{{ $user->name }}</strong>,</p>
        <p style="font-size:13px;line-height:1.6;color:#4b5563">
            Kami menerima permintaan perubahan password admin. Jika ini Anda, silakan konfirmasi dengan tombol berikut.
        </p>
        <a class="cta" href="{{ $verificationUrl }}">Konfirmasi Perubahan Password</a>

        <div class="note">
            Link ini berlaku selama 30 menit. Jika Anda tidak meminta perubahan password, abaikan email ini.
        </div>
    </div>
    <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer</div>
</div>
</body>
</html>
