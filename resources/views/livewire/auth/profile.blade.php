<div class="profile-page">
    <header class="page-intro">
        <div class="container">
            <p class="eyebrow">Your account</p>
            <h1>Hello, {{ $firstName }}.</h1>
            <p class="shop-lead">
                {{ $user->provider === 'email' ? 'Signed in with email and phone.' : 'Connected with '.$user->provider.'.' }}
            </p>
        </div>
    </header>

    <section class="section profile-shell">
        <div class="container profile-grid">
            <form class="form profile-form" wire:submit="save">
                @if (session('notice'))
                    <p class="auth-alert">{{ session('notice') }}</p>
                @endif

                <p class="eyebrow">Details</p>
                <h2>Keep the book current.</h2>

                <div>
                    <label for="profile-name">Name</label>
                    <input id="profile-name" type="text" wire:model="name" autocomplete="name">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="profile-email">Email</label>
                    <input id="profile-email" type="email" value="{{ $email }}" readonly>
                </div>
                <div>
                    <label for="profile-phone">Phone</label>
                    <input id="profile-phone" type="tel" wire:model="phone" autocomplete="tel" placeholder="+234">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <button class="btn btn-primary" type="submit">Save changes</button>
            </form>

            <aside class="profile-aside">
                <span class="profile-avatar">{{ $user->initials() }}</span>
                <p class="eyebrow">Dharmie</p>
                <h2>Your pieces wait.</h2>
                <p>The bag and saved looks stay with this profile while you move through the house.</p>
                <div class="profile-aside-actions">
                    <a class="btn btn-light" href="{{ route('shop') }}" wire:navigate>Continue shopping</a>
                    <a class="btn btn-on-dark" href="{{ route('checkout') }}" wire:navigate>Go to checkout</a>
                    <button class="btn btn-on-dark" type="button" wire:click="logout">Sign out</button>
                </div>
            </aside>
        </div>
    </section>
</div>
