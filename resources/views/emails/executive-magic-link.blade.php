<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your Magic Access Link — RHÁYỌ̀OGE Partner Desk</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #faf8f5; color: #2c1e16; margin: 0; padding: 2.5rem 1rem; }
        .wrapper { max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #ebd8cb; padding: 2.5rem 2rem; border-radius: 6px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); }
        .logo { font-size: 1.3rem; letter-spacing: 0.18em; font-weight: 600; text-transform: uppercase; color: #201511; margin-bottom: 1.75rem; text-align: center; }
        h1 { font-size: 1.55rem; margin-top: 0; color: #201511; font-weight: 500; }
        p { line-height: 1.6; font-size: 0.95rem; color: #5a493f; }
        .cta-box { text-align: center; margin: 2rem 0; }
        .btn { display: inline-block; background-color: #2c1e16; color: #ffffff !important; text-decoration: none; padding: 0.95rem 2.2rem; font-size: 0.85rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 600; border-radius: 4px; }
        .info-box { background: #faf7f2; border: 1px solid #e7ded3; padding: 1.25rem; margin: 1.5rem 0; border-radius: 4px; font-size: 0.88rem; }
        .footer { text-align: center; margin-top: 2rem; font-size: 0.78rem; color: #8e7a6f; border-top: 1px solid #eee7dc; padding-top: 1.25rem; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="logo">RHÁYỌ̀OGE</div>
        <h1>Hello, {{ $executive->name }}</h1>
        <p>You requested a magic access link to sign in to your confidential <strong>Executive Partner Workspace</strong>.</p>
        <p>Click the button below to log in directly to your dashboard — no password required:</p>

        <div class="cta-box">
            <a href="{{ $magicUrl }}" class="btn">Sign In to Partner Dashboard</a>
        </div>

        <div class="info-box">
            <strong>Partner Details:</strong><br>
            • Referral Handle: <code>@{{ $executive->code }}</code><br>
            • Default Commission: <strong>{{ $executive->default_commission_rate }}%</strong><br>
            • Security: This authenticated access link is valid for 72 hours.
        </div>

        <p style="font-size: 0.82rem; color: #7a665b; word-break: break-all;">
            If the button doesn't open, copy and paste this URL into your browser:<br>
            <a href="{{ $magicUrl }}" style="color: #b85d38;">{{ $magicUrl }}</a>
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} RHÁYỌ̀OGE Atelier. 12 Kofo Abayomi Street, Victoria Island, Lagos.<br>
            If you did not request this link, you can safely ignore this email.
        </div>
    </div>
</body>
</html>
