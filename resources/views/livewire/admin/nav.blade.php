<div>
    <header class="admin-topbar">
        <button
            class="admin-menu-btn {{ $open ? 'is-open' : '' }}"
            type="button"
            wire:click="$toggle('open')"
            aria-label="{{ $open ? 'Close Dharmie menu' : 'Open Dharmie menu' }}"
            aria-expanded="{{ $open ? 'true' : 'false' }}"
        >
            <span></span><span></span><span></span>
        </button>
        <a class="admin-topbar-brand" href="{{ route('admin.dashboard') }}" wire:navigate>
            <img src="{{ asset('assets/logo-on-light.png') }}" alt="RHÁYỌ̀OGE">
            <span>Dharmie</span>
        </a>
        <span class="admin-topbar-mark">{{ $user->initials() }}</span>
    </header>

    <div class="admin-nav-overlay {{ $open ? 'open' : '' }}" wire:click="$set('open', false)"></div>

    <aside class="admin-nav {{ $open ? 'open' : '' }}" aria-label="Dharmie">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}" wire:navigate wire:click="$set('open', false)">
            <img src="{{ asset('assets/logo-on-dark.png') }}" alt="RHÁYỌ̀OGE">
            <span class="admin-brand-copy">
                <span class="admin-brand-kicker">The house</span>
                <strong>Dharmie</strong>
            </span>
        </a>

        <p class="admin-nav-label">Desk</p>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">Overview</a>
            <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">Collection</a>
            <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">
                Orders
                @if ($openOrders)
                    <em>{{ $openOrders }}</em>
                @endif
            </a>
            <a href="{{ route('admin.executives') }}" class="{{ request()->routeIs('admin.executives*') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">
                Executives
            </a>
            <a href="{{ route('admin.logistics') }}" class="{{ request()->routeIs('admin.logistics*') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">
                Logistics & Rates
            </a>
            <a href="{{ route('admin.messages') }}" class="{{ request()->routeIs('admin.messages*') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">
                Notes
                @if ($unread)
                    <em>{{ $unread }}</em>
                @endif
            </a>
            <a href="{{ route('admin.clients') }}" class="{{ request()->routeIs('admin.clients') ? 'active' : '' }}" wire:navigate wire:click="$set('open', false)">Clients</a>
        </nav>

        <p class="admin-nav-label">House</p>
        <nav class="admin-nav-secondary">
            <a href="{{ route('home') }}">View the house</a>
        </nav>

        <div class="admin-nav-foot">
            <div class="admin-user">
                <span class="admin-user-avatar">{{ $user->initials() }}</span>
                <div>
                    <strong>{{ $user->name }}</strong>
                    <span>House desk</span>
                </div>
            </div>
            <button type="button" wire:click="logout">Sign out</button>
        </div>
    </aside>

    <div
        class="toast {{ $toast ? 'show' : '' }}"
        @if ($toast) x-data x-init="setTimeout(() => $wire.clearToast(), 2200)" @endif
    >{{ $toast }}</div>
</div>
