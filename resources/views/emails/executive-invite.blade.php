<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RHÁYỌ̀OGE Business Executive Portal</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #fbf7f2; color: #32221a; margin: 0; padding: 2rem 1rem; }
        .wrapper { max-width: 580px; margin: 0 auto; background: #ffffff; border: 1px solid #ebd8cb; padding: 2.5rem 2rem; border-radius: 4px; }
        .logo { font-size: 1.25rem; letter-spacing: 0.18em; font-weight: 600; text-transform: uppercase; color: #201511; margin-bottom: 1.5rem; text-align: center; }
        h1 { font-size: 1.6rem; margin-top: 0; color: #201511; }
        p { line-height: 1.6; font-size: 0.95rem; color: #4a382f; }
        .cta-box { text-align: center; margin: 2rem 0; }
        .btn { display: inline-block; background-color: #32221a; color: #ffffff !important; text-decoration: none; padding: 0.9rem 2rem; font-size: 0.85rem; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 600; border-radius: 2px; }
        .details-box { background: #fdf9f5; border: 1px dashed #d8c3b4; padding: 1.25rem; margin: 1.5rem 0; border-radius: 3px; font-size: 0.9rem; }
        .footer { text-align: center; margin-top: 2rem; font-size: 0.78rem; color: #8e7a6f; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="logo">RHÁYỌ̀OGE</div>
        <h1>Welcome, {{ $executive->name }}</h1>
        <p>You have been invited to join <strong>RHÁYỌ̀OGE</strong> as a <strong>Business Executive Partner</strong>.</p>
        <p>Through your private portal, you have direct access to our full apparel collection, dedicated marketing links for each piece, live commission tracking, and instant social sharing tools.</p>
        
        <div class="details-box">
            <strong>Your Partner Details:</strong><br>
            • Referral Handle / Code: <code>{{ $executive->code }}</code><br>
            • Default Commission Rate: <strong>{{ $executive->default_commission_rate }}%</strong> (or specific product rates)
        </div>

        <div class="cta-box">
            <a href="{{ $executive->inviteUrl() }}" class="btn">Access Your Partner Portal</a>
        </div>

        <p style="font-size: 0.82rem; color: #7a665b; word-break: break-all;">
            If the button doesn't work, copy and paste this link into your browser:<br>
            <a href="{{ $executive->inviteUrl() }}">{{ $executive->inviteUrl() }}</a>
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} RHÁYỌ̀OGE Atelier. 12 Kofo Abayomi Street, Victoria Island, Lagos.
        </div>
    </div>
</body>
</html>
