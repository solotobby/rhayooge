<main class="container section" style="padding-top:2rem">
    <p class="eyebrow">Checkout</p>
    <h1 class="page-hero" style="padding:0.4rem 0 1.6rem">Almost yours.</h1>

    @if (session('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.9rem 1.2rem;border-radius:8px;margin-bottom:1.5rem;font-size:0.9rem;display:flex;align-items:center;gap:0.75rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="checkout-grid">
        <form class="form" wire:submit="placeOrder">
            <h2 style="font-size:1.8rem">Delivery</h2>
            <div id="checkout-account">
                @auth
                    <p class="note">Signed in as <strong>{{ auth()->user()->email }}</strong></p>
                @else
                    <p class="note">Have an account? <a href="{{ route('account', ['next' => url()->current()]) }}">Sign in</a> for a faster checkout, or continue as a guest.</p>
                @endauth
            </div>
            <div>
                <label for="name">Full name</label>
                <input id="name" type="text" wire:model="name" autocomplete="name">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" wire:model="email" autocomplete="email">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone">Phone</label>
                <input id="phone" type="tel" wire:model="phone" autocomplete="tel" placeholder="+234">
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="address">Address</label>
                <input id="address" type="text" wire:model="address" autocomplete="street-address">
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="city">City</label>
                <input id="city" type="text" wire:model="city" autocomplete="address-level2">
                @error('city') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <h2 style="font-size:1.8rem; margin-top:1.5rem">Payment</h2>
            <div class="pay-options">
                <!-- Paystack Gateway Option -->
                <label class="pay-option {{ $payment === 'paystack' ? 'active' : '' }}" wire:click="$set('payment', 'paystack')">
                    <input type="radio" wire:model="payment" value="paystack">
                    <div style="flex:1;">
                        <span style="font-weight:600;display:flex;align-items:center;gap:0.5rem;">
                            <span>Paystack</span>
                            <span style="font-size:0.65rem;background:#00c3f7;color:#ffffff;padding:0.15rem 0.45rem;border-radius:4px;font-weight:700;letter-spacing:0.04em;">SECURE</span>
                        </span>
                        <span class="note" style="display:block;font-size:0.78rem;color:#78695d;margin-top:0.25rem;">
                            Cards (Visa, Mastercard, Verve), Bank Transfer, Apple Pay, USSD
                        </span>
                    </div>
                </label>

                {{-- Commented out per request: Pay on Delivery and Direct Bank Transfer --}}
                {{--
                <!-- Pay on Delivery Option -->
                <label class="pay-option {{ $payment === 'delivery' ? 'active' : '' }}" wire:click="$set('payment', 'delivery')">
                    <input type="radio" wire:model="payment" value="delivery">
                    <div style="flex:1;">
                        <span style="font-weight:500;">Pay on delivery</span>
                        <span class="note" style="display:block;font-size:0.78rem;color:#78695d;margin-top:0.25rem;">
                            Available in Lagos only
                        </span>
                    </div>
                </label>

                <!-- Bank Transfer Option -->
                <label class="pay-option {{ $payment === 'transfer' ? 'active' : '' }}" wire:click="$set('payment', 'transfer')">
                    <input type="radio" wire:model="payment" value="transfer">
                    <div style="flex:1;">
                        <span style="font-weight:500;">Direct bank transfer</span>
                        <span class="note" style="display:block;font-size:0.78rem;color:#78695d;margin-top:0.25rem;">
                            Manual settlement to our atelier bank account
                        </span>
                    </div>
                </label>
                --}}
            </div>
            @error('payment') <p class="form-error" style="margin-top:0.5rem;">{{ $message }}</p> @enderror

            <button class="btn btn-accent" type="submit" style="margin-top:1.2rem;width:100%;min-height:48px;display:flex;align-items:center;justify-content:center;gap:0.5rem;" @disabled($cartItems->isEmpty()) wire:loading.attr="disabled">
                <span wire:loading.remove>
                    @if ($payment === 'paystack')
                        Pay {{ \App\Models\Product::naira($total) }} with Paystack &rarr;
                    @else
                        Place order &rarr;
                    @endif
                </span>
                <span wire:loading style="display:inline-flex;align-items:center;gap:0.5rem;">
                    <svg style="animation:spin 1s linear infinite;width:16px;height:16px;" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25;"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" style="opacity:0.75;"></path>
                    </svg>
                    <span>Processing order...</span>
                </span>
            </button>
        </form>

        <aside class="summary-card">
            <h2 style="font-size:1.8rem; margin-bottom:1rem">Order</h2>
            @forelse ($cartItems as $item)
                <div class="cart-item">
                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div>
                        <h3>{{ $item->product->name }}</h3>
                        <p class="note">{{ $item->size }} · Qty {{ $item->qty }}</p>
                        @if (! empty($item->referral_code))
                            <p style="margin:0.2rem 0 0;font-size:0.68rem;letter-spacing:0.04em;color:#8c7362;">
                                Partner ref: <strong style="font-family:monospace;color:#2c1e16;">@{{ $item->referral_code }}</strong>
                            </p>
                        @endif
                    </div>
                    <strong>{{ \App\Models\Product::naira($item->line_total) }}</strong>
                </div>
            @empty
                <p class="empty-state">Your bag is empty. <a href="{{ route('shop') }}" wire:navigate>Return to the collection.</a></p>
            @endforelse
            @if ($cartItems->isNotEmpty())
                <div class="totals"><span>Subtotal</span><span>{{ \App\Models\Product::naira($subtotal) }}</span></div>
                <div class="totals"><span>Delivery</span><span>{{ $delivery ? \App\Models\Product::naira($delivery) : 'Complimentary' }}</span></div>
                <div class="totals"><span>Total</span><strong>{{ \App\Models\Product::naira($total) }}</strong></div>
            @endif
            <p class="note" style="margin-top:0.8rem">Complimentary delivery on orders above ₦50,000.</p>
        </aside>
    </div>

    <!-- Order Confirmation Screen -->
    <div class="success {{ $placed ? 'open' : '' }}">
        <div>
            <p class="eyebrow">RHÁYỌ̀OGE</p>
            <h2>Thank you</h2>
            @if ($confirmedOrder)
                <p style="margin-bottom:0.75rem;">Your order <strong>#{{ $confirmedOrder->id }}</strong> is confirmed.</p>
                <div style="background:#faf8f5;border:1px solid #ebe5dc;border-radius:8px;padding:1rem 1.25rem;text-align:left;margin:1rem auto 1.5rem;max-width:360px;font-size:0.85rem;color:#78695d;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem;">
                        <span>Payment Method:</span>
                        <strong style="color:#221f1e;">{{ ucfirst($confirmedOrder->payment_method) }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem;">
                        <span>Payment Status:</span>
                        <strong style="color:{{ $confirmedOrder->payment_status === 'paid' ? '#2e7d32' : '#b85d38' }};">
                            {{ ucfirst($confirmedOrder->payment_status) }}
                        </strong>
                    </div>
                    @if ($confirmedOrder->payment_reference)
                        <div style="display:flex;justify-content:space-between;margin-bottom:0.35rem;">
                            <span>Reference:</span>
                            <code style="font-family:monospace;color:#221f1e;">{{ $confirmedOrder->payment_reference }}</code>
                        </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;padding-top:0.35rem;border-top:1px solid #eee7dc;">
                        <span>Amount Paid:</span>
                        <strong style="color:#221f1e;">{{ $confirmedOrder->formattedTotal() }}</strong>
                    </div>
                </div>
            @else
                <p>Your order is confirmed. A note is on its way to your inbox.</p>
            @endif
            <br>
            <a class="btn btn-light" href="{{ route('shop') }}" wire:navigate>Continue browsing</a>
        </div>
    </div>
</main>
