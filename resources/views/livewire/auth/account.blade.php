<div class="auth-layout">
    <aside class="auth-visual" aria-hidden="true">
        <img src="{{ asset('assets/brand-guide.png') }}" alt="">
        <div class="auth-visual-copy">
            <p class="eyebrow">Client book</p>
            <p>{{ $tab === 'login' ? 'Your bag, saved pieces, and checkout — kept in one quiet profile.' : 'A profile for the woman who already knows her presence.' }}</p>
        </div>
    </aside>

    <section class="auth-panel">
        <p class="eyebrow">RHÁYỌ̀OGE</p>
        <h1>{{ $tab === 'login' ? 'Welcome back.' : 'Join the house.' }}</h1>
        <p class="auth-lead">{{ $tab === 'login' ? 'Sign in to continue where you left the collection.' : 'Create a profile with email and phone, or continue with Google or Facebook.' }}</p>

        <div class="auth-tabs" role="tablist">
            <button type="button" class="{{ $tab === 'login' ? 'active' : '' }}" wire:click="$set('tab', 'login')">Sign in</button>
            <button type="button" class="{{ $tab === 'signup' ? 'active' : '' }}" wire:click="$set('tab', 'signup')">Create account</button>
        </div>

        <div class="social-row">
            <a class="btn-social" href="{{ route('social.redirect', 'google') }}">
                <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                    <path fill="#4285F4" d="M17.6 9.2c0-.6-.1-1.2-.2-1.8H9v3.4h4.8c-.2 1.1-.8 2-1.8 2.6v2.2h2.9c1.7-1.6 2.7-3.9 2.7-6.4z"/>
                    <path fill="#34A853" d="M9 18c2.4 0 4.5-.8 6-2.2l-2.9-2.2c-.8.6-1.9.9-3.1.9-2.4 0-4.4-1.6-5.1-3.8H.9v2.3C2.4 15.8 5.5 18 9 18z"/>
                    <path fill="#FBBC05" d="M3.9 10.7c-.2-.6-.3-1.2-.3-1.7s.1-1.2.3-1.7V5H.9C.3 6.2 0 7.6 0 9s.3 2.8.9 4l3-2.3z"/>
                    <path fill="#EA4335" d="M9 3.6c1.3 0 2.5.5 3.4 1.3L15 2.3C13.5.9 11.4 0 9 0 5.5 0 2.4 2.2.9 5.2L3.9 7.5C4.6 5.3 6.6 3.6 9 3.6z"/>
                </svg>
                Continue with Google
            </a>
            <a class="btn-social" href="{{ route('social.redirect', 'facebook') }}">
                <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                    <path fill="#1877F2" d="M18 9a9 9 0 1 0-10.4 8.9V11.6H5.3V9h2.3V7c0-2.3 1.3-3.5 3.4-3.5.7 0 1.6.1 1.6.1v2.2h-1c-1.1 0-1.4.7-1.4 1.3V9h2.4l-.4 2.6h-2v6.3A9 9 0 0 0 18 9z"/>
                </svg>
                Continue with Facebook
            </a>
        </div>

        <p class="auth-split"><span>or with email &amp; phone</span></p>

        @if (session('error'))
            <p class="auth-alert">{{ session('error') }}</p>
        @endif

        @if ($tab === 'login')
            <form class="form auth-form" wire:submit="login">
                <div>
                    <label for="login-id">Email or phone</label>
                    <input id="login-id" type="text" wire:model="loginId" autocomplete="username" placeholder="you@email.com or +234">
                    @error('loginId') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="login-password">Password</label>
                    <input id="login-password" type="password" wire:model="loginPassword" autocomplete="current-password">
                    @error('loginPassword') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <button class="btn btn-primary btn-full" type="submit">Sign in</button>
            </form>
        @else
            <form class="form auth-form" wire:submit="register">
                <div>
                    <label for="signup-name">Full name</label>
                    <input id="signup-name" type="text" wire:model="name" autocomplete="name">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="signup-email">Email</label>
                    <input id="signup-email" type="email" wire:model="email" autocomplete="email">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="signup-phone">Phone</label>
                    <input id="signup-phone" type="tel" wire:model="phone" autocomplete="tel" placeholder="+234 800 000 0000">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="auth-form-split">
                    <div>
                        <label for="signup-password">Password</label>
                        <input id="signup-password" type="password" wire:model="password" autocomplete="new-password">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="signup-confirm">Confirm</label>
                        <input id="signup-confirm" type="password" wire:model="passwordConfirmation" autocomplete="new-password">
                    </div>
                </div>
                <button class="btn btn-accent btn-full" type="submit">Create account</button>
            </form>
        @endif

        <p class="auth-foot">
            <a href="{{ route('home') }}" wire:navigate>Return to the house</a>
        </p>
    </section>
</div>
