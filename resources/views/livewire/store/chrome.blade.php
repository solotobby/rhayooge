<div>
    <header class="site-header" id="header">
        <div class="header-inner">
            <div class="header-start">
                <button class="header-btn hamburger {{ $showNav ? 'is-open' : '' }}" type="button" wire:click="toggleNav" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>
                <nav class="nav-links" aria-label="Primary">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" wire:navigate>Home</a>
                    <a href="{{ route('shop') }}" class="{{ request()->routeIs('shop') ? 'active' : '' }}" wire:navigate>Shop</a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}" wire:navigate>About</a>
                    <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}" wire:navigate>Contact</a>
                </nav>
            </div>
            <a class="logo" href="{{ route('home') }}" wire:navigate aria-label="RHÁYỌ̀OGE home">
                <img class="logo-light" src="{{ asset('assets/logo-on-light.png') }}" alt="RHÁYỌ̀OGE">
                <span class="wordmark logo-type">RHÁYỌ̀OGE</span>
            </a>
            <div class="header-actions">
                <button class="header-btn" type="button" wire:click="openSearch" aria-label="Search">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="5.5"/><path d="M12.5 12.5L16 16"/></svg>
                </button>
                @auth
                    <a class="header-btn avatar-link" href="{{ route('profile') }}" wire:navigate aria-label="Your account">
                        <span class="avatar">{{ auth()->user()->initials() }}</span>
                    </a>
                @else
                    <a class="header-btn" href="{{ route('account') }}" wire:navigate aria-label="Sign in">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="6.5" r="2.7"/><path d="M4 14.5c1.2-2.4 2.8-3.5 5-3.5s3.8 1.1 5 3.5"/></svg>
                    </a>
                @endauth
                <button class="header-btn" type="button" wire:click="openSaved" aria-label="Saved for later">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 15s-6-3.8-6-8a3.5 3.5 0 0 1 6-2.4A3.5 3.5 0 0 1 15 7c0 4.2-6 8-6 8z"/></svg>
                    @if ($savedCount)
                        <span class="badge">{{ $savedCount }}</span>
                    @endif
                </button>
                <button class="header-btn" type="button" wire:click="openCart" aria-label="Bag">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 6h10l-1 10H5L4 6z"/><path d="M7 6a2 2 0 0 1 4 0"/></svg>
                    @if ($cartCount)
                        <span class="badge">{{ $cartCount }}</span>
                    @endif
                </button>
            </div>
        </div>
    </header>

    <div class="nav-overlay {{ $showNav ? 'open' : '' }}">
        <img class="overlay-logo" src="{{ asset('assets/logo-on-dark.png') }}" alt="">
        <a href="{{ route('home') }}" wire:click="closeAll" wire:navigate>Home</a>
        <a href="{{ route('shop') }}" wire:click="closeAll" wire:navigate>Shop</a>
        <a href="{{ route('about') }}" wire:click="closeAll" wire:navigate>About us</a>
        <a href="{{ route('contact') }}" wire:click="closeAll" wire:navigate>Contact</a>
        <a href="{{ auth()->check() ? route('profile') : route('account') }}" wire:click="closeAll" wire:navigate>{{ auth()->check() ? 'Account' : 'Sign in' }}</a>
        <a href="{{ route('checkout') }}" wire:click="closeAll" wire:navigate>Checkout</a>
    </div>

    <div class="overlay-bg {{ $showCart || $showSaved ? 'open' : '' }}" wire:click="closeAll"></div>

    <aside class="drawer {{ $showCart ? 'open' : '' }}" aria-label="Shopping bag">
        <div class="drawer-head">
            <h2>Your bag</h2>
            <button class="close-x" type="button" wire:click="closeAll">×</button>
        </div>
        <div class="drawer-body">
            @forelse ($cartItems as $item)
                <article class="cart-item">
                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div>
                        <h3>{{ $item->product->name }}</h3>
                        <p class="note">{{ $item->size }}</p>
                        @if (! empty($item->referral_code))
                            <p style="margin:0.2rem 0 0;font-size:0.68rem;letter-spacing:0.04em;color:#8c7362;">
                                Partner ref: <strong style="font-family:monospace;color:#221f1e;">@{{ $item->referral_code }}</strong>
                            </p>
                        @endif
                        <div class="qty">
                            <button type="button" wire:click="changeQty('{{ $item->key }}', -1)" aria-label="Decrease">−</button>
                            <span>{{ $item->qty }}</span>
                            <button type="button" wire:click="changeQty('{{ $item->key }}', 1)" aria-label="Increase">+</button>
                        </div>
                    </div>
                    <div class="item-actions">
                        <strong>{{ \App\Models\Product::naira($item->line_total) }}</strong>
                        <button type="button" wire:click="moveToSaved('{{ $item->key }}')">Save for later</button>
                        <button type="button" wire:click="removeFromCart('{{ $item->key }}')">Remove</button>
                    </div>
                </article>
            @empty
                <p class="empty-state">Your bag is waiting to be filled.</p>
            @endforelse
        </div>
        <div class="drawer-foot">
            @if ($cartItems->isNotEmpty())
                <div class="totals"><span>Subtotal</span><strong>{{ \App\Models\Product::naira($cartTotal) }}</strong></div>
                <a class="btn btn-accent btn-full" href="{{ route('checkout') }}" wire:click="closeAll" wire:navigate>Checkout</a>
            @else
                <a class="btn btn-primary btn-full" href="{{ route('shop') }}" wire:click="closeAll" wire:navigate>Explore the collection</a>
            @endif
        </div>
    </aside>

    <aside class="drawer {{ $showSaved ? 'open' : '' }}" aria-label="Saved for later">
        <div class="drawer-head">
            <h2>Saved</h2>
            <button class="close-x" type="button" wire:click="closeAll">×</button>
        </div>
        <div class="drawer-body">
            @forelse ($savedItems as $item)
                <article class="saved-item">
                    <img src="{{ $item->image }}" alt="{{ $item->name }}" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div>
                        <h3>{{ $item->name }}</h3>
                        <p>{{ $item->formattedPrice() }}</p>
                    </div>
                    <div class="item-actions">
                        <button type="button" wire:click="addToCart({{ $item->id }})">Add to bag</button>
                        <button type="button" wire:click="toggleSaved({{ $item->id }})">Remove</button>
                    </div>
                </article>
            @empty
                <p class="empty-state">Pieces you love will live here.</p>
            @endforelse
        </div>
    </aside>

    <section class="product-panel {{ $showProduct ? 'open' : '' }}">
        @if ($activeProduct)
            <div class="product-panel-inner">
                <img src="{{ $activeProduct->image }}" alt="{{ $activeProduct->name }}" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                <div class="product-info">
                    <button class="close-x" type="button" wire:click="closeAll">×</button>
                    <p class="eyebrow">{{ $activeProduct->category }}</p>
                    <h2 class="display" style="font-size:clamp(2rem,5vw,3.4rem);margin:.3rem 0 0.6rem">{{ $activeProduct->name }}</h2>
                    <div style="display:flex;align-items:baseline;gap:0.6rem;margin-bottom:1rem;flex-wrap:wrap;">
                        @if ($activeProduct->hasDiscount())
                            <del style="font-size:1rem;text-decoration:line-through;opacity:0.6;color:var(--muted);">{{ $activeProduct->formattedOriginalPrice() }}</del>
                            <span style="font-size:0.68rem;background:var(--terracotta,#b85d38);color:#fff;padding:0.15rem 0.4rem;font-weight:600;">-{{ $activeProduct->discountPercent() }}%</span>
                        @endif
                        <p class="price" style="font-size:1.1rem;margin:0;">{{ $activeProduct->formattedPrice() }}</p>
                    </div>
                    <p>{{ $activeProduct->description }}</p>
                    <p class="eyebrow" style="margin-top:1.4rem">Size</p>
                    <div class="size-row">
                        @foreach ($activeProduct->sizes as $size)
                            <button class="size-btn {{ $selectedSize === $size ? 'active' : '' }}" type="button" wire:click="chooseSize('{{ $size }}')">{{ $size }}</button>
                        @endforeach
                    </div>
                    @if ($activeProduct->quantity <= 0)
                        <p style="color:#93291e;font-size:0.85rem;font-weight:500;margin-top:1rem;">Currently out of stock</p>
                        <div class="mobile-cta">
                            <button class="btn btn-ghost" type="button" wire:click="toggleSaved({{ $activeProduct->id }})">
                                {{ in_array($activeProduct->id, $savedIds, true) ? 'Saved' : 'Save for later' }}
                            </button>
                            <button class="btn btn-accent btn-full" type="button" disabled style="opacity:0.5;cursor:not-allowed;">Out of stock</button>
                        </div>
                    @else
                        <div class="mobile-cta">
                            <button class="btn btn-ghost" type="button" wire:click="toggleSaved({{ $activeProduct->id }})">
                                {{ in_array($activeProduct->id, $savedIds, true) ? 'Saved' : 'Save for later' }}
                            </button>
                            <button class="btn btn-accent btn-full" type="button" wire:click="addToCart">Add to bag</button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <div class="search-overlay {{ $showSearch ? 'open' : '' }}">
        <button class="close-x" type="button" wire:click="closeAll" style="position:absolute;top:1rem;right:1rem">×</button>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search the collection" aria-label="Search">
        <div class="search-results">
            @foreach ($searchResults as $hit)
                <a class="search-hit" href="{{ route('product', $hit) }}" wire:click="closeAll" wire:navigate>
                    <img src="{{ $hit->image }}" alt="" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <span>{{ $hit->name }}<br><small class="note">{{ $hit->category }}</small></span>
                    <span style="text-align:right;">
                        @if ($hit->hasDiscount())
                            <del style="display:block;font-size:0.75rem;opacity:0.6;text-decoration:line-through;">{{ $hit->formattedOriginalPrice() }}</del>
                        @endif
                        <strong>{{ $hit->formattedPrice() }}</strong>
                    </span>
                </a>
            @endforeach
            @if ($showSearch && strlen(trim($search)) > 1 && $searchResults->isEmpty())
                <p class="note">No pieces match that search.</p>
            @endif
        </div>
    </div>

    <div
        class="toast {{ $toast ? 'show' : '' }}"
        @if ($toast) x-data x-init="setTimeout(() => $wire.clearToast(), 2200)" @endif
    >{{ $toast }}</div>
</div>
