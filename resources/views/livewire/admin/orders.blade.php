<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Commerce & Logistics</p>
            <h1>Orders & Sales Ledger</h1>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}" wire:navigate style="font-size:0.75rem;padding:0.45rem 0.95rem;">
                &larr; Dashboard
            </a>
            <a class="btn btn-ghost" href="{{ route('admin.logistics') }}" wire:navigate style="font-size:0.75rem;padding:0.45rem 0.95rem;">
                Delivery Locations &rarr;
            </a>
        </div>
    </header>

    <!-- Top KPI Performance Strip -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));gap:1rem;margin-bottom:1.75rem;">
        <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:10px;padding:1.15rem 1.35rem;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:var(--muted);font-weight:600;display:block;">Total Placed Orders</span>
            <strong style="font-size:1.85rem;font-family:var(--font-serif);color:#1c1917;font-weight:500;display:block;margin-top:0.25rem;">
                {{ number_format($stats['total_count']) }}
            </strong>
            <span style="font-size:0.75rem;color:var(--muted);">All-time order volume</span>
        </div>

        <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:10px;padding:1.15rem 1.35rem;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:var(--muted);font-weight:600;display:block;">Verified Sales Revenue</span>
            <strong style="font-size:1.85rem;font-family:var(--font-serif);color:#2e5932;font-weight:500;display:block;margin-top:0.25rem;">
                {{ \App\Models\Product::naira($stats['paid_revenue']) }}
            </strong>
            <span style="font-size:0.75rem;color:#2e5932;">From confirmed paid transactions</span>
        </div>

        <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:10px;padding:1.15rem 1.35rem;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:var(--muted);font-weight:600;display:block;">Pending Fulfillment</span>
            <strong style="font-size:1.85rem;font-family:var(--font-serif);color:#925816;font-weight:500;display:block;margin-top:0.25rem;">
                {{ $stats['pending_fulfillment'] }}
            </strong>
            <span style="font-size:0.75rem;color:#925816;">Confirmed / In atelier preparation</span>
        </div>

        <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:10px;padding:1.15rem 1.35rem;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
            <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:var(--muted);font-weight:600;display:block;">Completed Deliveries</span>
            <strong style="font-size:1.85rem;font-family:var(--font-serif);color:#1c1917;font-weight:500;display:block;margin-top:0.25rem;">
                {{ $stats['delivered_count'] }}
            </strong>
            <span style="font-size:0.75rem;color:var(--muted);">Successfully fulfilled to clients</span>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="admin-toolbar" style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <div style="flex:1;min-width:260px;">
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search order #, client name, email, phone, location, ref, or BE code..."
                aria-label="Search orders"
                style="width:100%;"
            >
        </div>

        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
            <select wire:model.live="status" aria-label="Filter by fulfillment status" style="max-width:180px;">
                <option value="All">All Fulfillment Statuses</option>
                @foreach (array_diff($statuses, ['All']) as $item)
                    <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                @endforeach
            </select>

            <select wire:model.live="paymentStatus" aria-label="Filter by payment status" style="max-width:160px;">
                <option value="All">All Payment States</option>
                <option value="paid">● Paid</option>
                <option value="pending">○ Pending</option>
            </select>

            @if ($search !== '' || $status !== 'All' || $paymentStatus !== 'All')
                <button
                    type="button"
                    class="btn btn-ghost"
                    wire:click="$set('search', ''); $set('status', 'All'); $set('paymentStatus', 'All');"
                    style="font-size:0.75rem;padding:0.45rem 0.85rem;"
                >
                    Reset Filters
                </button>
            @endif
        </div>
    </div>

    @if ($orders->isEmpty())
        <div style="background:#ffffff;border:1px solid #ebe7df;border-radius:12px;padding:3rem 2rem;text-align:center;margin-top:1.5rem;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="color:var(--muted);opacity:0.4;margin-bottom:0.75rem;">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <p class="admin-empty" style="margin:0;font-size:0.95rem;color:var(--muted);">No orders match the selected criteria.</p>
        </div>
    @else
        <div class="admin-table-wrap" style="background:#ffffff;border:1px solid #ebe7df;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.02);margin-top:1.25rem;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="min-width:130px;">Order #</th>
                        <th style="min-width:170px;">Client</th>
                        <th style="min-width:160px;">Delivery Destination</th>
                        <th style="min-width:130px;">Pieces</th>
                        <th style="min-width:120px;">Payment</th>
                        <th style="min-width:120px;">Fulfillment</th>
                        <th style="min-width:130px;text-align:right;">Total Amount</th>
                        <th style="min-width:120px;text-align:right;">Quick Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr style="cursor:pointer;" wire:key="order-row-{{ $order->id }}" wire:click="inspectOrder({{ $order->id }})">
                            <td>
                                <div style="display:flex;flex-direction:column;gap:0.2rem;">
                                    <button
                                        type="button"
                                        wire:click.stop="inspectOrder({{ $order->id }})"
                                        style="font-family:monospace;font-weight:600;color:var(--brown-deep);background:none;border:none;padding:0;cursor:pointer;text-align:left;font-size:0.9rem;"
                                    >
                                        #{{ $order->id }}
                                    </button>
                                    <span class="admin-mute" style="font-size:0.72rem;">{{ $order->created_at->format('d M Y · H:i') }}</span>
                                    @if (! empty($order->be_code))
                                        <span style="display:inline-block;padding:0.12rem 0.4rem;background:#faf3ee;color:#b85d38;border:1px solid #ebd8cc;border-radius:3px;font-size:0.65rem;font-weight:600;width:fit-content;letter-spacing:0.02em;">
                                            Ref: @{{ $order->be_code }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <strong style="color:var(--brown-deep);display:block;font-size:0.88rem;">{{ $order->name }}</strong>
                                <span class="admin-mute" style="display:block;font-size:0.75rem;">{{ $order->email }}</span>
                                <span class="admin-mute" style="display:block;font-size:0.72rem;color:#78716c;">{{ $order->phone }}</span>
                            </td>

                            <td>
                                @if ($order->shipping_location_name || $order->shippingLocation)
                                    <span style="display:inline-block;padding:0.15rem 0.5rem;background:#f5efe6;color:#8c4c28;border-radius:4px;font-size:0.72rem;font-weight:600;margin-bottom:0.25rem;">
                                        {{ $order->shipping_location_name ?? $order->shippingLocation->name }}
                                    </span>
                                @endif
                                <span style="display:block;font-size:0.75rem;color:var(--charcoal);line-height:1.3;">
                                    {{ Str::limit($order->address . ', ' . $order->city, 36) }}
                                </span>
                            </td>

                            <td>
                                <div style="display:flex;align-items:center;gap:0.4rem;">
                                    <div style="display:flex;margin-right:0.25rem;">
                                        @foreach ($order->items->take(2) as $previewItem)
                                            <img
                                                src="{{ $previewItem->product?->image ?? asset('assets/brand-guide.png') }}"
                                                alt=""
                                                style="width:24px;height:32px;object-fit:cover;border-radius:3px;border:1px solid #ebe7df;margin-left:-4px;first:margin-left-0;"
                                                onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                            >
                                        @endforeach
                                    </div>
                                    <span style="font-size:0.8rem;color:#1c1917;font-weight:500;">
                                        {{ $order->items_count }} {{ Str::plural('piece', $order->items_count) }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                @if ($order->isPaid())
                                    <span class="admin-stock-badge in-stock" style="font-size:0.68rem;padding:0.2rem 0.5rem;">
                                        ● Paid
                                    </span>
                                @else
                                    <span class="admin-stock-badge low-stock" style="font-size:0.68rem;padding:0.2rem 0.5rem;">
                                        ○ Pending
                                    </span>
                                @endif
                                <span class="admin-mute" style="display:block;font-size:0.72rem;margin-top:0.2rem;text-transform:capitalize;">
                                    {{ $order->payment_method ?: 'Card' }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $badgeClass = match($order->status) {
                                        'confirmed', 'delivered' => 'in-stock',
                                        'preparing', 'dispatched' => 'low-stock',
                                        'cancelled' => 'out-of-stock',
                                        default => '',
                                    };
                                @endphp
                                <span class="admin-stock-badge {{ $badgeClass }}" style="font-size:0.68rem;padding:0.2rem 0.5rem;">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td style="text-align:right;">
                                <strong style="font-family:monospace;color:var(--brown-deep);font-size:0.95rem;display:block;">
                                    {{ $order->formattedTotal() }}
                                </strong>
                                <span class="admin-mute" style="font-size:0.7rem;display:block;">
                                    Fee: {{ $order->delivery ? \App\Models\Product::naira($order->delivery) : 'Free' }}
                                </span>
                            </td>

                            <td style="text-align:right;" wire:click.stop>
                                <div style="display:inline-flex;gap:0.35rem;align-items:center;">
                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        wire:click="inspectOrder({{ $order->id }})"
                                        style="font-size:0.72rem;padding:0.35rem 0.65rem;display:inline-flex;align-items:center;gap:0.3rem;font-weight:600;color:var(--brown-deep);"
                                        title="View comprehensive details"
                                    >
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Details
                                    </button>

                                    <a
                                        href="{{ route('orders.receipt.pdf', $order) }}"
                                        target="_blank"
                                        class="btn btn-ghost"
                                        style="font-size:0.72rem;padding:0.35rem 0.55rem;color:var(--muted);"
                                        title="Download PDF Receipt"
                                    >
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="admin-pager" style="margin-top:1.5rem;">
                <span>Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }} ({{ $orders->total() }} total orders)</span>
                <div style="display:flex;gap:0.75rem;">
                    @if ($orders->onFirstPage())
                        <span style="opacity:0.4;cursor:not-allowed;">Previous</span>
                    @else
                        <button type="button" wire:click="previousPage">Previous</button>
                    @endif

                    @if ($orders->hasMorePages())
                        <button type="button" wire:click="nextPage">Next</button>
                    @else
                        <span style="opacity:0.4;cursor:not-allowed;">Next</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Comprehensive Order & Sales Inspection Modal -->
    @if ($showInspectModal && $inspectingOrder)
        <div class="admin-modal-overlay" wire:click.self="closeInspectModal">
            <div class="admin-modal" style="max-width:960px;width:95%;max-height:92vh;overflow-y:auto;background:#ffffff;border-radius:14px;border:1px solid #ebe7df;box-shadow:0 25px 60px -15px rgba(28, 17, 12, 0.25);padding:2.25rem;">
                
                <!-- Modal Header -->
                <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f0eee9;padding-bottom:1.35rem;margin-bottom:1.75rem;gap:1.5rem;flex-wrap:wrap;">
                    <div>
                        <div style="display:flex;align-items:center;gap:0.65rem;margin-bottom:0.35rem;flex-wrap:wrap;">
                            <span class="eyebrow" style="color:var(--terracotta,#b85d38);margin:0;">Order Dossier & Settlement</span>
                            @if ($inspectingOrder->isPaid())
                                <span class="admin-stock-badge in-stock" style="font-size:0.72rem;">● Paid ({{ ucfirst($inspectingOrder->payment_method) }})</span>
                            @else
                                <span class="admin-stock-badge low-stock" style="font-size:0.72rem;">○ Payment Pending</span>
                            @endif
                            <span class="admin-stock-badge" style="background:#f5efe6;border:1px solid #e7ded0;color:#2c1e16;font-size:0.72rem;">
                                Fulfillment: {{ ucfirst($inspectingOrder->status) }}
                            </span>
                        </div>
                        <h2 style="font-family:var(--font-serif);font-size:2rem;margin:0;color:#1c1917;font-weight:500;">
                            Order #{{ $inspectingOrder->id }}
                        </h2>
                        <span class="admin-mute" style="font-size:0.8rem;color:#78716c;margin-top:0.25rem;display:block;">
                            Placed on {{ $inspectingOrder->created_at->format('l, d F Y · h:i A') }} ({{ $inspectingOrder->created_at->diffForHumans() }})
                        </span>
                    </div>

                    <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                        <a
                            href="{{ route('orders.receipt', $inspectingOrder) }}"
                            target="_blank"
                            class="btn btn-ghost"
                            style="font-size:0.75rem;padding:0.45rem 0.85rem;"
                        >
                            View Receipt ↗
                        </a>
                        <a
                            href="{{ route('orders.receipt.pdf', $inspectingOrder) }}"
                            target="_blank"
                            class="btn btn-primary"
                            style="font-size:0.75rem;padding:0.45rem 0.95rem;display:inline-flex;align-items:center;gap:0.35rem;"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download PDF Receipt
                        </a>
                        <a
                            href="{{ route('admin.orders.show', $inspectingOrder) }}"
                            wire:navigate
                            class="btn btn-ghost"
                            style="font-size:0.75rem;padding:0.45rem 0.85rem;"
                            title="Open standalone order page"
                        >
                            Standalone Page ↗
                        </a>
                        <button
                            type="button"
                            wire:click="closeInspectModal"
                            style="background:none;border:none;font-size:1.8rem;cursor:pointer;color:#78716c;line-height:1;margin-left:0.5rem;padding:0.2rem;"
                            title="Close modal"
                        >
                            &times;
                        </button>
                    </div>
                </div>

                <!-- 4-Card Order Dossier Grid -->
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1.25rem;margin-bottom:2rem;">
                    
                    <!-- Card 1: Client & Contact Profile -->
                    <div style="background:#faf8f5;border:1px solid #ebe7df;border-radius:10px;padding:1.25rem;">
                        <span style="font-size:0.68rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:600;display:block;margin-bottom:0.5rem;">
                            Client Profile
                        </span>
                        <strong style="display:block;font-size:1rem;color:#1c1917;margin-bottom:0.25rem;">
                            {{ $inspectingOrder->name }}
                        </strong>
                        <div style="font-size:0.8rem;color:#44403c;display:flex;flex-direction:column;gap:0.3rem;">
                            <a href="mailto:{{ $inspectingOrder->email }}" style="color:var(--terracotta);text-decoration:none;">
                                {{ $inspectingOrder->email }}
                            </a>
                            <a href="tel:{{ $inspectingOrder->phone }}" style="color:#1c1917;text-decoration:none;">
                                {{ $inspectingOrder->phone }}
                            </a>
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inspectingOrder->phone) }}"
                                target="_blank"
                                rel="noopener"
                                style="color:#25D366;font-size:0.75rem;font-weight:600;display:inline-flex;align-items:center;gap:0.3rem;margin-top:0.2rem;text-decoration:none;"
                            >
                                WhatsApp Client &nearr;
                            </a>
                            <span style="font-size:0.72rem;color:var(--muted);margin-top:0.25rem;">
                                {{ $inspectingOrder->user_id ? '● Registered Client Account' : '○ Guest Store Checkout' }}
                            </span>
                        </div>
                    </div>

                    <!-- Card 2: Logistics & Delivery Destination -->
                    <div style="background:#faf8f5;border:1px solid #ebe7df;border-radius:10px;padding:1.25rem;">
                        <span style="font-size:0.68rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:600;display:block;margin-bottom:0.5rem;">
                            Delivery Destination
                        </span>
                        <div style="margin-bottom:0.5rem;">
                            @if ($inspectingOrder->shipping_location_name || $inspectingOrder->shippingLocation)
                                <span style="display:inline-block;padding:0.2rem 0.55rem;background:#ebe4da;color:#7c3d1d;border-radius:4px;font-size:0.75rem;font-weight:600;">
                                    Zone: {{ $inspectingOrder->shipping_location_name ?? $inspectingOrder->shippingLocation->name }}
                                </span>
                            @else
                                <span style="font-size:0.75rem;color:var(--muted);">Standard City Delivery</span>
                            @endif
                        </div>
                        <p style="font-size:0.82rem;color:#1c1917;margin:0 0 0.4rem;line-height:1.45;">
                            {{ $inspectingOrder->address }}<br>
                            <strong>{{ $inspectingOrder->city }}</strong>
                        </p>
                        <span style="font-size:0.75rem;color:var(--muted);">
                            Logistics Fee: <strong>{{ $inspectingOrder->delivery ? \App\Models\Product::naira($inspectingOrder->delivery) : 'Complimentary' }}</strong>
                        </span>
                    </div>

                    <!-- Card 3: Payment & Settlement Dossier -->
                    <div style="background:#faf8f5;border:1px solid #ebe7df;border-radius:10px;padding:1.25rem;">
                        <span style="font-size:0.68rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:600;display:block;margin-bottom:0.5rem;">
                            Financial Settlement
                        </span>
                        <strong style="display:block;font-size:1.15rem;color:var(--brown-deep);font-family:var(--font-serif);margin-bottom:0.35rem;">
                            {{ $inspectingOrder->formattedTotal() }}
                        </strong>
                        <div style="font-size:0.78rem;color:#44403c;display:flex;flex-direction:column;gap:0.25rem;">
                            <span>Method: <strong style="text-transform:capitalize;">{{ $inspectingOrder->payment_method ?: 'Card' }}</strong></span>
                            <span>
                                State:
                                <strong style="color:{{ $inspectingOrder->isPaid() ? '#2e5932' : '#925816' }};">
                                    {{ ucfirst($inspectingOrder->payment_status) }}
                                </strong>
                            </span>
                            @if ($inspectingOrder->payment_reference)
                                <span>Ref: <code style="font-family:monospace;background:#f0eee9;padding:0.1rem 0.35rem;border-radius:3px;font-size:0.72rem;">{{ $inspectingOrder->payment_reference }}</code></span>
                            @endif
                            @if ($inspectingOrder->paid_at)
                                <span style="font-size:0.72rem;color:var(--muted);">Paid: {{ $inspectingOrder->paid_at->format('d M Y, H:i') }}</span>
                            @elseif (! $inspectingOrder->isPaid())
                                <button
                                    type="button"
                                    class="btn btn-ghost"
                                    wire:click="markOrderPaid({{ $inspectingOrder->id }})"
                                    style="font-size:0.7rem;padding:0.25rem 0.55rem;margin-top:0.4rem;color:#1e4422;border-color:#c2d6c2;"
                                >
                                    Mark as Paid ✓
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Card 4: Affiliate & Executive Referral -->
                    <div style="background:#faf8f5;border:1px solid #ebe7df;border-radius:10px;padding:1.25rem;">
                        <span style="font-size:0.68rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);font-weight:600;display:block;margin-bottom:0.5rem;">
                            Affiliate Referral
                        </span>
                        @if (! empty($inspectingOrder->be_code) || $inspectingOrder->business_executive_id)
                            <div style="margin-bottom:0.35rem;">
                                <span style="display:inline-block;padding:0.18rem 0.5rem;background:#faf3ee;color:#b85d38;border:1px solid #ebd8cc;border-radius:4px;font-size:0.75rem;font-weight:600;">
                                    Executive: @{{ $inspectingOrder->be_code }}
                                </span>
                            </div>
                            <strong style="display:block;font-size:0.85rem;color:#1c1917;">
                                {{ $inspectingOrder->businessExecutive?->name ?? 'Partner Executive' }}
                            </strong>
                            @php
                                $commissionTotal = $inspectingOrder->commissions->sum('commission_amount');
                            @endphp
                            <div style="font-size:0.75rem;color:#44403c;margin-top:0.35rem;">
                                <span>Commission: <strong style="color:#b85d38;">{{ \App\Models\Product::naira($commissionTotal) }}</strong></span>
                                <span style="display:block;color:var(--muted);font-size:0.7rem;">
                                    Status: {{ ucfirst($inspectingOrder->commissions->first()?->status ?? 'pending') }}
                                </span>
                            </div>
                        @else
                            <p style="font-size:0.82rem;color:var(--muted);margin:0;line-height:1.4;">
                                Direct storefront purchase.<br>No executive referral code attributed.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Pieces / Items Table -->
                <div style="margin-bottom:2rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                        <h3 style="font-family:var(--font-serif);font-size:1.35rem;margin:0;font-weight:500;color:#1c1917;">
                            Purchased Pieces ({{ $inspectingOrder->items->count() }})
                        </h3>
                        <span style="font-size:0.75rem;color:var(--muted);">
                            Total units: {{ $inspectingOrder->items->sum('qty') }}
                        </span>
                    </div>

                    <div class="admin-table-wrap" style="border:1px solid #ebe7df;border-radius:8px;overflow:hidden;">
                        <table class="admin-table" style="width:100%;margin:0;">
                            <thead>
                                <tr>
                                    <th style="min-width:240px;">Piece & Silhouette</th>
                                    <th>Size</th>
                                    <th style="text-align:center;">Quantity</th>
                                    <th style="text-align:right;">Unit Price</th>
                                    <th style="text-align:right;">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inspectingOrder->items as $item)
                                    <tr>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                                <img
                                                    src="{{ $item->product?->image ?? asset('assets/brand-guide.png') }}"
                                                    alt=""
                                                    style="width:40px;height:52px;object-fit:cover;border-radius:4px;border:1px solid #ebe7df;"
                                                    onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                                                >
                                                <div>
                                                    <strong style="display:block;font-size:0.88rem;color:#1c1917;">
                                                        {{ $item->product?->name ?? 'Archived Piece' }}
                                                    </strong>
                                                    <span style="font-size:0.72rem;color:var(--muted);">
                                                        Category: {{ $item->product?->category ?? 'Atelier' }}
                                                    </span>
                                                    @if (! empty($item->be_code))
                                                        <span style="display:block;font-size:0.68rem;color:#b85d38;font-weight:500;">
                                                            Affiliate: @{{ $item->be_code }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="display:inline-block;padding:0.2rem 0.55rem;background:#2c1e16;color:#ffffff;border-radius:4px;font-size:0.75rem;font-weight:600;">
                                                {{ $item->size }}
                                            </span>
                                        </td>
                                        <td style="text-align:center;font-weight:600;color:#1c1917;">
                                            {{ $item->qty }}
                                        </td>
                                        <td style="text-align:right;font-family:monospace;color:#44403c;">
                                            {{ \App\Models\Product::naira($item->price) }}
                                        </td>
                                        <td style="text-align:right;font-family:monospace;font-weight:600;color:var(--brown-deep);">
                                            {{ \App\Models\Product::naira($item->price * $item->qty) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:#faf8f5;">
                                    <td colspan="4" style="text-align:right;font-size:0.82rem;color:var(--muted);padding-top:1rem;">
                                        Pieces Subtotal:
                                    </td>
                                    <td style="text-align:right;font-family:monospace;font-size:0.9rem;padding-top:1rem;color:#1c1917;">
                                        {{ \App\Models\Product::naira($inspectingOrder->subtotal) }}
                                    </td>
                                </tr>
                                <tr style="background:#faf8f5;">
                                    <td colspan="4" style="text-align:right;font-size:0.82rem;color:var(--muted);">
                                        Delivery / Logistics Fee:
                                    </td>
                                    <td style="text-align:right;font-family:monospace;font-size:0.9rem;color:#1c1917;">
                                        {{ $inspectingOrder->delivery ? \App\Models\Product::naira($inspectingOrder->delivery) : 'Complimentary' }}
                                    </td>
                                </tr>
                                <tr style="background:#faf8f5;border-top:1px solid #ebe7df;">
                                    <td colspan="4" style="text-align:right;font-size:0.95rem;font-weight:600;color:var(--brown-deep);padding-bottom:1rem;">
                                        Grand Total:
                                    </td>
                                    <td style="text-align:right;font-family:monospace;font-size:1.15rem;font-weight:700;color:var(--brown-deep);padding-bottom:1rem;">
                                        {{ $inspectingOrder->formattedTotal() }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Fulfillment Lifecycle Actions -->
                <div style="background:#faf8f5;border:1px solid #ebe7df;border-radius:10px;padding:1.35rem 1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.25rem;">
                    <div>
                        <span style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.12em;color:var(--muted);font-weight:600;display:block;margin-bottom:0.4rem;">
                            Update Fulfillment Status
                        </span>
                        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                            @foreach (\App\Models\Order::STATUSES as $st)
                                <button
                                    type="button"
                                    class="chip {{ $inspectingOrder->status === $st ? 'active' : '' }}"
                                    wire:click="updateOrderStatus({{ $inspectingOrder->id }}, '{{ $st }}')"
                                    style="font-size:0.75rem;padding:0.35rem 0.75rem;text-transform:capitalize;"
                                >
                                    {{ $inspectingOrder->status === $st ? '● ' : '' }}{{ ucfirst($st) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <button
                            type="button"
                            class="btn btn-primary"
                            wire:click="closeInspectModal"
                            style="padding:0.6rem 1.5rem;font-size:0.8rem;"
                        >
                            Close Details
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
