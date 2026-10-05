<div class="admin-auth">
    <aside class="admin-auth-visual" aria-hidden="true">
        <img src="{{ asset('assets/brand-guide.png') }}" alt="">
        <div class="admin-auth-copy">
            <p class="eyebrow">The house</p>
            <p>A quiet desk for the collection, the orders, and Dharmie.</p>
        </div>
    </aside>

    <section class="admin-auth-panel">
        <p class="eyebrow">RHÁYỌ̀OGE</p>
        <h1>Dharmie</h1>
        <p class="admin-auth-lead">Sign in to the house desk. This is not the client book.</p>

        <form class="form admin-auth-form" wire:submit="login">
            <div>
                <label for="admin-email">Email</label>
                <input id="admin-email" type="email" wire:model="email" autocomplete="username">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="admin-password">Password</label>
                <input id="admin-password" type="password" wire:model="password" autocomplete="current-password">
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-primary btn-full" type="submit">Enter the desk</button>
        </form>

        <p class="admin-auth-foot">
            <a href="{{ route('home') }}">Return to the house</a>
        </p>
    </section>
</div>
