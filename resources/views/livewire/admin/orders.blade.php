<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Orders</p>
            <h1>The book.</h1>
        </div>
    </header>

    <div class="admin-toolbar">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Name, email, phone, or order number" aria-label="Search orders">
        <select wire:model.live="status" aria-label="Filter by status">
            @foreach ($statuses as $item)
                <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
        </select>
    </div>

    @if ($orders->isEmpty())
        <p class="admin-empty">No orders in this view.</p>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Client</th>
                        <th>Pieces</th>
                        <th>City</th>
                        <th>Status</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" wire:navigate style="font-family:monospace;font-weight:600;color:var(--brown-deep);">
                                    #{{ $order->id }}
                                </a>
                                <span class="admin-mute">{{ $order->created_at->format('d M Y · H:i') }}</span>
                            </td>
                            <td>
                                <strong style="color:var(--brown-deep);">{{ $order->name }}</strong>
                                <span class="admin-mute">{{ $order->email }}</span>
                            </td>
                            <td>
                                <span>{{ $order->items_count }} {{ Str::plural('piece', $order->items_count) }}</span>
                            </td>
                            <td>
                                <span style="color:var(--charcoal);">{{ $order->city }}</span>
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

        @if ($orders->hasPages())
            <div class="admin-pager">
                <span>Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>
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
</div>
