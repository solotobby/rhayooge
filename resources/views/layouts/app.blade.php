<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RHÁYỌ̀OGE' }}</title>
    <meta name="description" content="RHÁYỌ̀OGE is a female apparel house of considered dresses, tailoring, and earth-toned essentials.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @livewireStyles
</head>
<body class="{{ ! empty($home) ? 'home' : '' }}{{ ! empty($authLayout) ? ' auth-screen' : '' }}" data-page="{{ ! empty($home) ? 'home' : 'page' }}">
    <livewire:store.chrome />

    {{ $slot }}

    @unless (! empty($authLayout))
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <img class="footer-logo" src="{{ asset('assets/logo-on-dark.png') }}" alt="RHÁYỌ̀OGE">
                <p>Quiet luxury for the modern woman. Pieces cut with warmth, ease, and presence.</p>
            </div>
            <div>
                <strong class="eyebrow">House</strong>
                <a href="{{ route('shop') }}" wire:navigate>The Collection</a>
                <a href="{{ route('about') }}" wire:navigate>About Us</a>
                <a href="{{ route('contact') }}" wire:navigate>Contact</a>
            </div>
            <div>
                <strong class="eyebrow">Client care</strong>
                <a href="{{ route('shop') }}" wire:navigate>Shipping</a>
                <a href="{{ route('contact') }}" wire:navigate>Returns</a>
                <a href="{{ auth()->check() ? route('profile') : route('account') }}" wire:navigate>{{ auth()->check() ? 'Your account' : 'Sign in' }}</a>
                <a href="{{ route('checkout') }}" wire:navigate>Checkout</a>
                <a href="{{ route('executive.login') }}" wire:navigate>Partner Portal</a>
            </div>
            <div>
                <strong class="eyebrow">Visit</strong>
                <p>Victoria Island, Lagos<br>Mon–Sat, 10am–7pm</p>
            </div>
        </div>
        <div class="container copyright">© {{ date('Y') }} RHÁYỌ̀OGE. All rights reserved.</div>
    </footer>
    @endunless

    @livewireScripts
    <script src="{{ asset('js/store.js') }}"></script>
</body>
</html>
