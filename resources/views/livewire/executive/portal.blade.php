<div>
    <!-- Executive Top Navigation Bar -->
    <header class="executive-topbar" style="background:#ffffff;border-bottom:1px solid #eee7dc;position:sticky;top:0;z-index:40;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
        <div style="max-width:1280px;margin:0 auto;padding:0.9rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <a href="{{ route('home') }}" target="_blank" style="text-decoration:none;display:flex;align-items:center;gap:0.6rem;">
                    <span style="font-family:var(--font-serif);font-size:1.35rem;font-weight:600;letter-spacing:0.08em;color:#221f1e;text-transform:uppercase;">
                        RHÁYỌ̀OGE
                    </span>
                </a>
                <span style="width:1px;height:20px;background:#e5ded3;"></span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f4ede4] px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#715b4d]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#2e7d32]"></span> Partner Workspace
                </span>
            </div>

            <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
                <div style="text-align:right;">
                    <strong style="display:block;font-size:0.85rem;color:#221f1e;font-weight:600;">{{ $executive->name }}</strong>
                    <span style="display:block;font-size:0.75rem;color:#8c7362;font-family:monospace;">
                        @{{ $executive->code }} &bull; {{ $executive->default_commission_rate }}% Commission
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <a
                        href="{{ url('/shop?ref='.$executive->code) }}"
                        target="_blank"
                        class="btn btn-ghost"
                        style="font-size:0.75rem;padding:0.45rem 0.9rem;border:1px solid #dfd8cc;border-radius:6px;display:inline-flex;align-items:center;gap:0.35rem;text-decoration:none;"
                    >
                        <span>View Storefront</span>
                        <span style="font-size:0.85rem;">&nearr;</span>
                    </a>

                    <button
                        type="button"
                        wire:click="logout"
                        class="btn btn-ghost"
                        style="font-size:0.75rem;padding:0.45rem 0.85rem;border:1px solid #dfd8cc;border-radius:6px;cursor:pointer;color:#78695d;background:#ffffff;display:inline-flex;align-items:center;gap:0.35rem;"
                        title="Sign out of partner workspace"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div style="max-width:1280px;margin:0 auto;padding:2rem 1.5rem 5rem;">
        <!-- Executive Welcome Banner & General Link -->
        <div style="background:#ffffff;border:1px solid #ebe5dc;border-radius:12px;padding:2rem;margin-bottom:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:2rem;align-items:center;">
                <div>
                    <p class="eyebrow" style="color:var(--terracotta,#b85d38);margin-bottom:0.35rem;">Confidential Executive Portal</p>
                    <h1 style="font-size:clamp(1.8rem, 4vw, 2.6rem);margin:0 0 0.5rem;line-height:1.15;font-weight:500;color:#221f1e;">
                        Welcome, {{ $executive->name }}
                    </h1>
                    <p style="margin:0;font-size:0.92rem;color:#78695d;line-height:1.5;">
                        Your private commission headquarters. Track incoming client sales, generate dedicated referral links for any piece, and configure your payout account.
                    </p>
                    <div style="margin-top:1rem;display:flex;gap:1.5rem;flex-wrap:wrap;font-size:0.82rem;">
                        <div>
                            <span style="text-transform:uppercase;font-size:0.68rem;letter-spacing:0.12em;color:#9b8c7f;display:block;">Referral Code</span>
                            <code style="background:#f6f1ea;padding:0.15rem 0.45rem;border-radius:4px;color:#221f1e;font-weight:600;">{{ $executive->code }}</code>
                        </div>
                        <div>
                            <span style="text-transform:uppercase;font-size:0.68rem;letter-spacing:0.12em;color:#9b8c7f;display:block;">Commission Rate</span>
                            <strong style="color:#221f1e;">{{ $executive->default_commission_rate }}% Default</strong>
                        </div>
                        <div>
                            <span style="text-transform:uppercase;font-size:0.68rem;letter-spacing:0.12em;color:#9b8c7f;display:block;">Status</span>
                            <span class="inline-flex items-center gap-1 font-medium text-[#2e7d32]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#2e7d32]"></span> Active Partner
                            </span>
                        </div>
                    </div>
                </div>

                <!-- General Store Link Card -->
                <div style="background:#faf8f5;border:1px solid #e7dfd4;border-radius:10px;padding:1.4rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                        <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.14em;font-weight:600;color:#8c7362;">
                            General Storewide Referral Link
                        </span>
                        <span style="font-size:0.75rem;color:#a39284;">All items tracked</span>
                    </div>

                    <div style="display:flex;gap:0.5rem;margin-bottom:0.85rem;">
                        <input
                            type="text"
                            readonly
                            value="{{ $executive->storeReferralUrl() }}"
                            style="font-family:monospace;font-size:0.8rem;padding:0.55rem 0.75rem;background:#ffffff;border:1px solid #dcd3c5;border-radius:6px;width:100%;color:#221f1e;outline:none;"
                            id="general-ref-link"
                        >
                        <button
                            type="button"
                            class="btn btn-primary"
                            style="font-size:0.75rem;padding:0.55rem 1rem;white-space:nowrap;cursor:pointer;border-radius:6px;"
                            x-data
                            @click="navigator.clipboard.writeText('{{ $executive->storeReferralUrl() }}'); $wire.set('toast', 'Storewide referral link copied!')"
                        >
                            Copy Link
                        </button>
                    </div>

                    <!-- Direct Social Shortcuts -->
                    @php
                        $genShare = urlencode("Shop the exclusive luxury collection at RHÁYỌ̀OGE: ".$executive->storeReferralUrl());
                    @endphp
                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                        <a
                            href="https://api.whatsapp.com/send?text={{ $genShare }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="flex:1;min-width:90px;text-align:center;text-decoration:none;font-size:0.72rem;padding:0.4rem 0.6rem;background:#25D366;color:#ffffff;border-radius:6px;font-weight:500;"
                        >
                            WhatsApp
                        </a>
                        <a
                            href="https://twitter.com/intent/tweet?text={{ $genShare }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="flex:1;min-width:80px;text-align:center;text-decoration:none;font-size:0.72rem;padding:0.4rem 0.6rem;background:#111111;color:#ffffff;border-radius:6px;font-weight:500;"
                        >
                            X (Twitter)
                        </a>
                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($executive->storeReferralUrl()) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="flex:1;min-width:80px;text-align:center;text-decoration:none;font-size:0.72rem;padding:0.4rem 0.6rem;background:#1877F2;color:#ffffff;border-radius:6px;font-weight:500;"
                        >
                            Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Performance Metrics -->
        <section style="margin-bottom:2.5rem;">
            <div class="admin-stats" style="grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;">
                <article style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;padding:1.4rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <p class="eyebrow" style="margin-bottom:0.25rem;color:#8c7362;">Attributed Orders</p>
                    <strong style="font-size:2.2rem;color:#221f1e;font-weight:500;margin:0.2rem 0;">{{ $executive->totalSalesCount() }}</strong>
                    <span style="font-size:0.8rem;color:#9b8c7f;display:block;">Completed customer orders</span>
                </article>

                <article style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;padding:1.4rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <p class="eyebrow" style="margin-bottom:0.25rem;color:#8c7362;">Gross Commission Earned</p>
                    <strong style="font-size:2.2rem;color:#1e4422;font-weight:500;margin:0.2rem 0;">
                        {{ \App\Models\Product::naira($executive->totalCommissionEarned()) }}
                    </strong>
                    <span style="font-size:0.8rem;color:#9b8c7f;display:block;">All-time affiliate revenue</span>
                </article>

                <article style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;padding:1.4rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <p class="eyebrow" style="margin-bottom:0.25rem;color:#8c7362;">Pending Payout</p>
                    <strong style="font-size:2.2rem;color:var(--terracotta,#b85d38);font-weight:500;margin:0.2rem 0;">
                        {{ \App\Models\Product::naira($executive->pendingCommission()) }}
                    </strong>
                    <span style="font-size:0.8rem;color:#9b8c7f;display:block;">Awaiting settlement remittance</span>
                </article>

                <article style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;padding:1.4rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <p class="eyebrow" style="margin-bottom:0.25rem;color:#8c7362;">Paid to Bank</p>
                    <strong style="font-size:2.2rem;color:#221f1e;font-weight:500;margin:0.2rem 0;">
                        {{ \App\Models\Product::naira($executive->paidCommission()) }}
                    </strong>
                    <span style="font-size:0.8rem;color:#9b8c7f;display:block;">Remitted to your bank account</span>
                </article>
            </div>
        </section>

        <!-- Dashboard Navigation Tabs -->
        <div style="border-bottom:1px solid #e7dfd4;margin-bottom:2rem;display:flex;gap:1.5rem;overflow-x:auto;">
            <button
                type="button"
                wire:click="setTab('catalog')"
                style="background:none;border:none;padding:0.75rem 0.25rem 1rem;font-size:0.92rem;font-weight:{{ $tab === 'catalog' ? '600' : '400' }};color:{{ $tab === 'catalog' ? '#221f1e' : '#8c7362' }};border-bottom:2px solid {{ $tab === 'catalog' ? '#221f1e' : 'transparent' }};cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;transition:all 0.15s ease;"
            >
                <span>Pieces & Affiliate Links</span>
                <span style="font-size:0.72rem;padding:0.15rem 0.5rem;border-radius:12px;background:{{ $tab === 'catalog' ? '#221f1e' : '#efe9de' }};color:{{ $tab === 'catalog' ? '#ffffff' : '#715b4d' }};">
                    {{ $products->count() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('ledger')"
                style="background:none;border:none;padding:0.75rem 0.25rem 1rem;font-size:0.92rem;font-weight:{{ $tab === 'ledger' ? '600' : '400' }};color:{{ $tab === 'ledger' ? '#221f1e' : '#8c7362' }};border-bottom:2px solid {{ $tab === 'ledger' ? '#221f1e' : 'transparent' }};cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;transition:all 0.15s ease;"
            >
                <span>Commission Ledger</span>
                <span style="font-size:0.72rem;padding:0.15rem 0.5rem;border-radius:12px;background:{{ $tab === 'ledger' ? '#221f1e' : '#efe9de' }};color:{{ $tab === 'ledger' ? '#ffffff' : '#715b4d' }};">
                    {{ $commissions->count() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('payout')"
                style="background:none;border:none;padding:0.75rem 0.25rem 1rem;font-size:0.92rem;font-weight:{{ $tab === 'payout' ? '600' : '400' }};color:{{ $tab === 'payout' ? '#221f1e' : '#8c7362' }};border-bottom:2px solid {{ $tab === 'payout' ? '#221f1e' : 'transparent' }};cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;transition:all 0.15s ease;"
            >
                <span>Payout & Banking Form</span>
                @if ($executive->bank_account_number)
                    <span style="font-size:0.72rem;padding:0.15rem 0.5rem;border-radius:12px;background:#e8f4e9;color:#2e7d32;">
                        Active
                    </span>
                @else
                    <span style="font-size:0.72rem;padding:0.15rem 0.5rem;border-radius:12px;background:#fdf2e9;color:#d9534f;">
                        Setup Needed
                    </span>
                @endif
            </button>
        </div>

        <!-- TAB 1: PRODUCT CATALOG & CUSTOM LINKS -->
        @if ($tab === 'catalog')
            <section>
                <!-- Filters & Search Toolbar -->
                <div style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;padding:1.25rem;margin-bottom:1.75rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                    <div style="flex:1;min-width:240px;max-width:380px;">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search collection by piece name or category..."
                            class="admin-input"
                            style="padding:0.55rem 0.85rem;font-size:0.88rem;border-radius:6px;"
                        >
                    </div>

                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                class="chip {{ $category === $cat ? 'active' : '' }}"
                                wire:click="$set('category', '{{ $cat }}')"
                                style="cursor:pointer;font-size:0.75rem;padding:0.35rem 0.75rem;border-radius:20px;"
                            >
                                {{ ucfirst($cat) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if ($products->isEmpty())
                    <div style="text-align:center;padding:4rem 2rem;background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;">
                        <p class="eyebrow" style="margin-bottom:0.5rem;">No Pieces Matching Filter</p>
                        <p style="color:#8c7362;margin-bottom:1.5rem;">There are no pieces matching your search query in this category.</p>
                        <button type="button" wire:click="$set('search', ''); $set('category', 'all')" class="btn btn-ghost" style="font-size:0.8rem;">
                            Clear Filters
                        </button>
                    </div>
                @else
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(310px, 1fr));gap:1.75rem;">
                        @foreach ($products as $prod)
                            @php
                                $commAmt = $prod->calculateCommissionAmount($prod->price, $executive->default_commission_rate);
                                $uniqueLink = $executive->productReferralUrl($prod);
                                $shareText = urlencode("Discover the {$prod->name} from RHÁYỌ̀OGE: {$uniqueLink}");
                            @endphp
                            <article style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 2px 6px rgba(0,0,0,0.02);transition:box-shadow 0.2s ease;">
                                <!-- Product Image & Badges -->
                                <div style="position:relative;height:260px;background:#f8f5f0;overflow:hidden;">
                                    <img
                                        src="{{ $prod->image }}"
                                        alt="{{ $prod->name }}"
                                        style="width:100%;height:100%;object-fit:cover;"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                    >
                                    <span style="position:absolute;top:0.75rem;left:0.75rem;background:rgba(34,31,30,0.85);backdrop-filter:blur(4px);color:#faf7f2;font-size:0.65rem;letter-spacing:0.12em;text-transform:uppercase;padding:0.3rem 0.6rem;border-radius:4px;font-weight:500;">
                                        {{ $prod->category }}
                                    </span>
                                </div>

                                <!-- Card Content -->
                                <div style="padding:1.4rem;flex:1;display:flex;flex-direction:column;">
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;margin-bottom:0.4rem;">
                                        <h3 style="font-size:1.25rem;margin:0;font-weight:500;color:#221f1e;line-height:1.2;">
                                            {{ $prod->name }}
                                        </h3>
                                        <a
                                            href="{{ $uniqueLink }}"
                                            target="_blank"
                                            title="View in store"
                                            style="color:#8c7362;text-decoration:none;font-size:0.9rem;"
                                        >
                                            &nearr;
                                        </a>
                                    </div>

                                    <div style="display:flex;align-items:baseline;gap:0.5rem;margin-bottom:1rem;">
                                        @if ($prod->hasDiscount())
                                            <del style="opacity:0.5;font-size:0.85rem;color:#8c7362;text-decoration:line-through;">
                                                {{ $prod->formattedOriginalPrice() }}
                                            </del>
                                        @endif
                                        <strong style="color:var(--brown,#4a3228);font-size:1.15rem;font-weight:600;">
                                            {{ $prod->formattedPrice() }}
                                        </strong>
                                    </div>

                                    <!-- Commission Reward Box -->
                                    <div style="background:#f2f7f3;border:1px solid #d0e4d3;border-radius:8px;padding:0.75rem 0.9rem;margin-bottom:1.25rem;">
                                        <span style="display:block;font-size:0.68rem;text-transform:uppercase;letter-spacing:0.08em;color:#2e7d32;font-weight:600;margin-bottom:0.15rem;">
                                            Your Commission Return
                                        </span>
                                        <div style="display:flex;justify-content:space-between;align-items:baseline;">
                                            <strong style="font-size:1.15rem;color:#1b5e20;">
                                                Earn {{ \App\Models\Product::naira($commAmt) }}
                                            </strong>
                                            <span style="font-size:0.75rem;color:#2e7d32;">
                                                {{ $prod->commission_type === 'fixed' ? 'Fixed Fee' : ($prod->commission_rate ?? $executive->default_commission_rate).'% Rate' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Unique Referral Link -->
                                    <div style="margin-top:auto;">
                                        <label style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.1em;color:#8c7362;display:block;margin-bottom:0.35rem;font-weight:500;">
                                            Unique Referral Link
                                        </label>
                                        <div style="display:flex;gap:0.4rem;margin-bottom:0.85rem;">
                                            <input
                                                type="text"
                                                readonly
                                                value="{{ $uniqueLink }}"
                                                style="font-family:monospace;font-size:0.75rem;padding:0.45rem 0.65rem;border:1px solid #dfd8cc;background:#faf8f5;width:100%;border-radius:6px;outline:none;"
                                            >
                                            <button
                                                type="button"
                                                class="btn btn-ghost"
                                                style="font-size:0.75rem;padding:0.45rem 0.75rem;cursor:pointer;white-space:nowrap;border:1px solid #dfd8cc;border-radius:6px;"
                                                x-data
                                                @click="navigator.clipboard.writeText('{{ $uniqueLink }}'); $wire.set('toast', 'Link copied for {{ addslashes($prod->name) }}!')"
                                            >
                                                Copy
                                            </button>
                                        </div>

                                        <!-- Quick Social Channels -->
                                        <div style="display:flex;gap:0.4rem;">
                                            <a
                                                href="https://api.whatsapp.com/send?text={{ $shareText }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="flex:1;text-align:center;text-decoration:none;font-size:0.72rem;padding:0.45rem;background:#25D366;color:#ffffff;border-radius:6px;font-weight:500;"
                                            >
                                                WhatsApp
                                            </a>
                                            <a
                                                href="https://twitter.com/intent/tweet?text={{ $shareText }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="flex:1;text-align:center;text-decoration:none;font-size:0.72rem;padding:0.45rem;background:#111111;color:#ffffff;border-radius:6px;font-weight:500;"
                                            >
                                                X
                                            </a>
                                            <a
                                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($uniqueLink) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                style="flex:1;text-align:center;text-decoration:none;font-size:0.72rem;padding:0.45rem;background:#1877F2;color:#ffffff;border-radius:6px;font-weight:500;"
                                            >
                                                Facebook
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        <!-- TAB 2: COMMISSION LEDGER & ORDER HISTORY -->
        @if ($tab === 'ledger')
            <section>
                <div style="background:#ffffff;border:1px solid #ebe5dc;border-radius:10px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
                        <div>
                            <p class="eyebrow" style="margin-bottom:0.25rem;">Real-Time Transaction Log</p>
                            <h2 style="font-size:1.6rem;margin:0;font-weight:500;color:#221f1e;">Referral Orders & Commission Ledger</h2>
                        </div>
                        <span style="font-size:0.82rem;color:#8c7362;">
                            {{ $commissions->count() }} recorded event{{ $commissions->count() === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if ($commissions->isEmpty())
                        <div style="text-align:center;padding:4rem 1.5rem;background:#faf8f5;border:1px dashed #ded6c8;border-radius:8px;">
                            <p class="eyebrow" style="margin-bottom:0.4rem;">No Commissions Yet</p>
                            <p style="color:#8c7362;max-width:440px;margin:0 auto 1.5rem;font-size:0.92rem;">
                                You haven't made any referral sales yet. Share your general store link or individual piece links across WhatsApp and Instagram to begin earning.
                            </p>
                            <button
                                type="button"
                                wire:click="setTab('catalog')"
                                class="btn btn-primary"
                                style="font-size:0.8rem;padding:0.5rem 1.25rem;border-radius:6px;"
                            >
                                Browse Pieces & Copy Links
                            </button>
                        </div>
                    @else
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Piece / Item</th>
                                        <th>Order ID</th>
                                        <th>Customer Total</th>
                                        <th>Your Commission</th>
                                        <th>Rate</th>
                                        <th>Payout Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($commissions as $comm)
                                        <tr>
                                            <td style="color:#715b4d;font-size:0.85rem;white-space:nowrap;">
                                                {{ $comm->created_at->format('d M Y, H:i') }}
                                            </td>
                                            <td style="font-weight:500;color:#221f1e;">
                                                {{ $comm->product_name }}
                                            </td>
                                            <td style="font-family:monospace;font-size:0.85rem;color:#8c7362;">
                                                #{{ $comm->order_id }}
                                            </td>
                                            <td style="font-weight:500;color:#221f1e;">
                                                {{ $comm->formattedSale() }}
                                            </td>
                                            <td>
                                                <strong style="color:#1e4422;font-size:0.95rem;">
                                                    {{ $comm->formattedCommission() }}
                                                </strong>
                                            </td>
                                            <td style="font-size:0.82rem;color:#8c7362;">
                                                {{ $comm->commission_rate }}
                                            </td>
                                            <td>
                                                @if ($comm->status === 'paid')
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#e8f4e9] px-2.5 py-0.5 text-xs font-medium text-[#2e7d32] border border-[#cbe5cd]">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-[#2e7d32]"></span> Paid to Bank
                                                    </span>
                                                @elseif ($comm->status === 'approved')
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#eff6ff] px-2.5 py-0.5 text-xs font-medium text-[#1d4ed8] border border-[#bfdbfe]">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-[#2563eb]"></span> Approved
                                                    </span>
                                                @elseif ($comm->status === 'cancelled')
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#fdf2f2] px-2.5 py-0.5 text-xs font-medium text-[#991b1b] border border-[#fecaca]">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-[#ef4444]"></span> Cancelled
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#fef9ee] px-2.5 py-0.5 text-xs font-medium text-[#92400e] border border-[#fde68a]">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-[#f59e0b]"></span> Pending Payout
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <!-- TAB 3: PAYOUT & SETTLEMENT FORM (DASHBOARD-LIKE FORM) -->
        @if ($tab === 'payout')
            <section>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:2rem;align-items:start;">
                    <!-- Left: The Payout Form -->
                    <div style="background:#ffffff;border:1px solid #ebe5dc;border-radius:12px;padding:2rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="border-bottom:1px solid #eee7dc;padding-bottom:1.25rem;margin-bottom:1.75rem;">
                            <p class="eyebrow" style="color:var(--terracotta,#b85d38);margin-bottom:0.25rem;">Banking & Payout Preferences</p>
                            <h2 style="font-size:1.6rem;margin:0 0 0.35rem;font-weight:500;color:#221f1e;">
                                Settlement Account Form
                            </h2>
                            <p style="margin:0;font-size:0.88rem;color:#78695d;">
                                Provide your Nigerian commercial or fintech bank account details for direct commission remittances.
                            </p>
                        </div>

                        <form wire:submit="saveBankDetails" class="admin-form" style="gap:1.4rem;">
                            <!-- Executive Profile Info (Readonly) -->
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                                <div>
                                    <label class="admin-label">Partner Full Name</label>
                                    <input
                                        type="text"
                                        value="{{ $executive->name }}"
                                        readonly
                                        class="admin-input"
                                        style="background:#faf8f5;color:#6b5a4d;cursor:not-allowed;"
                                    >
                                </div>

                                <div>
                                    <label class="admin-label">Registered Email</label>
                                    <input
                                        type="email"
                                        value="{{ $executive->email }}"
                                        readonly
                                        class="admin-input"
                                        style="background:#faf8f5;color:#6b5a4d;cursor:not-allowed;"
                                    >
                                </div>
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label class="admin-label">Contact Phone / WhatsApp</label>
                                <input
                                    type="tel"
                                    wire:model="phone"
                                    placeholder="+234 800 000 0000"
                                    class="admin-input"
                                >
                                @error('phone') <p class="form-error" style="font-size:0.78rem;color:#b91c1c;margin-top:0.35rem;">{{ $message }}</p> @enderror
                            </div>

                            <!-- Bank Name -->
                            <div>
                                <label class="admin-label">Bank Institution</label>
                                <input
                                    type="text"
                                    list="nigerian-banks"
                                    wire:model="bank_name"
                                    placeholder="Type or select bank (e.g. GTBank, Zenith, Access)"
                                    class="admin-input"
                                >
                                <datalist id="nigerian-banks">
                                    <option value="Access Bank">
                                    <option value="Guaranty Trust Bank (GTBank)">
                                    <option value="Zenith Bank">
                                    <option value="First Bank of Nigeria">
                                    <option value="United Bank for Africa (UBA)">
                                    <option value="Stanbic IBTC Bank">
                                    <option value="Fidelity Bank">
                                    <option value="Sterling Bank">
                                    <option value="First City Monument Bank (FCMB)">
                                    <option value="Union Bank">
                                    <option value="Wema Bank / ALAT">
                                    <option value="Kuda Bank">
                                    <option value="Moniepoint Microfinance Bank">
                                    <option value="OPay">
                                    <option value="Palmpay">
                                    <option value="Polaris Bank">
                                    <option value="Ecobank Nigeria">
                                    <option value="Keystone Bank">
                                    <option value="Providus Bank">
                                    <option value="Taj Bank">
                                    <option value="Jaiz Bank">
                                </datalist>
                                @error('bank_name') <p class="form-error" style="font-size:0.78rem;color:#b91c1c;margin-top:0.35rem;">{{ $message }}</p> @enderror
                            </div>

                            <!-- Account Number & Account Name -->
                            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;">
                                <div>
                                    <label class="admin-label">10-Digit Account Number (NUBAN)</label>
                                    <input
                                        type="text"
                                        maxlength="10"
                                        wire:model="bank_account_number"
                                        placeholder="0123456789"
                                        class="admin-input"
                                        style="font-family:monospace;letter-spacing:0.06em;"
                                    >
                                    @error('bank_account_number') <p class="form-error" style="font-size:0.78rem;color:#b91c1c;margin-top:0.35rem;">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="admin-label">Account Holder Name</label>
                                    <input
                                        type="text"
                                        wire:model="bank_account_name"
                                        placeholder="Exact name registered on account"
                                        class="admin-input"
                                    >
                                    @error('bank_account_name') <p class="form-error" style="font-size:0.78rem;color:#b91c1c;margin-top:0.35rem;">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div style="padding-top:0.5rem;display:flex;gap:1rem;align-items:center;">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    style="padding:0.7rem 1.8rem;font-size:0.85rem;border-radius:6px;cursor:pointer;"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove>Save Payout Details</span>
                                    <span wire:loading>Saving Account...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right: Current Settlement Summary & Guidelines -->
                    <div style="display:flex;flex-direction:column;gap:1.5rem;">
                        <!-- Status Summary Card -->
                        <div style="background:#ffffff;border:1px solid #ebe5dc;border-radius:12px;padding:1.75rem;box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                            <p class="eyebrow" style="margin-bottom:0.35rem;">Current Settlement Status</p>
                            @if ($executive->bank_account_number)
                                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#e8f4e9] px-2.5 py-0.5 text-xs font-semibold text-[#2e7d32] border border-[#cbe5cd]">
                                        <span class="h-2 w-2 rounded-full bg-[#2e7d32]"></span> Payout Account Verified
                                    </span>
                                </div>

                                <div style="background:#faf8f5;border:1px solid #eee7dc;border-radius:8px;padding:1.2rem;display:flex;flex-direction:column;gap:0.75rem;">
                                    <div>
                                        <span style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.12em;color:#8c7362;display:block;">Bank</span>
                                        <strong style="color:#221f1e;font-size:0.95rem;">{{ $executive->bank_name }}</strong>
                                    </div>
                                    <div>
                                        <span style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.12em;color:#8c7362;display:block;">Account Number</span>
                                        <code style="color:#221f1e;font-size:1rem;font-weight:600;letter-spacing:0.06em;">{{ $executive->bank_account_number }}</code>
                                    </div>
                                    <div>
                                        <span style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.12em;color:#8c7362;display:block;">Account Name</span>
                                        <strong style="color:#221f1e;font-size:0.95rem;">{{ $executive->bank_account_name }}</strong>
                                    </div>
                                </div>
                            @else
                                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#fef2f2] px-2.5 py-0.5 text-xs font-semibold text-[#b91c1c] border border-[#fecaca]">
                                        <span class="h-2 w-2 rounded-full bg-[#ef4444]"></span> No Payout Account Linked
                                    </span>
                                </div>
                                <p style="font-size:0.88rem;color:#78695d;line-height:1.5;">
                                    You have not linked a settlement bank account yet. Please fill the form to the left so that accrued commissions can be remitted directly to you.
                                </p>
                            @endif
                        </div>

                        <!-- Settlement Guidelines Card -->
                        <div style="background:#faf8f5;border:1px solid #eee7dc;border-radius:12px;padding:1.75rem;">
                            <h4 style="font-family:var(--font-serif);font-size:1.25rem;margin:0 0 0.75rem;font-weight:500;color:#221f1e;">
                                Executive Settlement Policy
                            </h4>
                            <ul style="margin:0;padding-left:1.25rem;font-size:0.85rem;color:#78695d;line-height:1.7;">
                                <li>Commissions are accrued immediately upon completed customer checkout with your referral code.</li>
                                <li>Pending commissions transition to approved after the return and exchange window closes.</li>
                                <li>Approved commissions are remitted in Nigerian Naira (NGN) directly to your configured bank account.</li>
                                <li>Ensure your Account Name matches your registered bank records to prevent transfer delays.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>

    <!-- Floating Toast Notification -->
    @if ($toast)
        <div
            x-data
            x-init="setTimeout(() => $wire.clearToast(), 3000)"
            style="position:fixed;bottom:2rem;right:2rem;background:#221f1e;color:#ffffff;padding:0.85rem 1.4rem;border-radius:8px;font-size:0.88rem;box-shadow:0 8px 24px rgba(0,0,0,0.25);z-index:100;display:flex;align-items:center;gap:0.6rem;animation:fadeIn 0.2s ease-out;"
        >
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#4ade80;"></span>
            <span>{{ $toast }}</span>
        </div>
    @endif
</div>
