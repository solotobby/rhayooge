<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Overview</p>
            <h1>The desk.</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.products.create') }}" wire:navigate>New piece</a>
    </header>

    <div class="admin-stats">
        <article>
            <p class="eyebrow">Revenue</p>
            <strong>{{ \App\Models\Product::naira($revenue) }}</strong>
            <span>Confirmed orders</span>
        </article>
        <article>
            <p class="eyebrow">Orders</p>
            <strong>{{ $orderCount }}</strong>
            <span>{{ $openOrders }} still in motion</span>
        </article>
        <article>
            <p class="eyebrow">Collection</p>
            <strong>{{ $productCount }}</strong>
            <span>Pieces on the floor</span>
        </article>
        <article>
            <p class="eyebrow">Clients</p>
            <strong>{{ $clientCount }}</strong>
            <span>{{ $unread }} unread note{{ $unread === 1 ? '' : 's' }}</span>
        </article>
    </div>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Recent orders</h2>
            <a href="{{ route('admin.orders') }}" wire:navigate>All orders</a>
        </div>

        @if ($recentOrders->isEmpty())
            <p class="admin-empty">No orders yet. The first one will appear here.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" wire:navigate style="font-family:monospace;font-weight:600;color:var(--brown-deep);">
                                        #{{ $order->id }}
                                    </a>
                                    <span class="admin-mute">{{ $order->created_at->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <strong style="color:var(--brown-deep);">{{ $order->name }}</strong>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($order->status) {
                                            'confirmed', 'delivered' => 'in-stock',
                                            'preparing' => 'low-stock',
                                            'cancelled' => 'out-of-stock',
                                            default => '',
                                        };
                                    @endphp
                                    <span class="admin-stock-badge {{ $badgeClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <strong style="font-family:monospace;color:var(--brown-deep);">{{ $order->formattedTotal() }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
