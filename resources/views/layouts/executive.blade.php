<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Executive Partner Workspace — RHÁYỌ̀OGE' }}</title>
    <meta name="description" content="RHÁYỌ̀OGE Executive Partner Desk and Affiliate Portal.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="admin-body" style="background:#faf8f5;color:var(--charcoal,#221f1e);min-height:100vh;display:flex;flex-direction:column;">
    <main style="flex:1;">
        {{ $slot }}
    </main>

    <footer style="border-top:1px solid #eee7dc;background:#ffffff;padding:2rem 1.5rem;margin-top:auto;text-align:center;">
        <div style="max-width:1200px;margin:0 auto;display:flex;flex-direction:column;gap:0.5rem;align-items:center;justify-content:center;">
            <p style="margin:0;font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:#8c7362;font-weight:500;">
                RHÁYỌ̀OGE Atelier &bull; Executive Partner Network
            </p>
            <p style="margin:0;font-size:0.75rem;color:#a89989;">
                &copy; {{ date('Y') }} All rights reserved. Confidential partner desk for authorized executives.
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
