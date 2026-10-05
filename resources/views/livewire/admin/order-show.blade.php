<div class="admin-page">
    <header class="admin-head">
        <div>
            <p class="eyebrow">Order #{{ $order->id }}</p>
            <h1>{{ $order->name }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.orders') }}" wire:navigate>All orders</a>
    </header>

    <div class="admin-detail">
        <section class="admin-panel">
            <h2>Pieces</h2>
            @foreach ($order->items as $item)
                <article class="admin-line">
                    <img src="{{ $item->product?->image ?? asset('assets/brand-guide.png') }}" alt="" onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'">
                    <div>
                        <strong>{{ $item->product?->name ?? 'Removed piece' }}</strong>
                        <p class="admin-mute">
                            {{ $item->size }} · Qty {{ $item->qty }}
                            @if (! empty($item->be_code))
                                · <span style="color:#b85d38;font-weight:500;">Ref: @{{ $item->be_code }}</span>
                            @endif
                        </p>
                    </div>
                    <span>{{ \App\Models\Product::naira($item->price * $item->qty) }}</span>
                </article>
            @endforeach
            <div class="admin-totals">
                <p><span>Subtotal</span><span>{{ \App\Models\Product::naira($order->subtotal) }}</span></p>
                <p><span>Delivery</span><span>{{ $order->delivery ? \App\Models\Product::naira($order->delivery) : 'Complimentary' }}</span></p>
                <p><span>Total</span><strong>{{ $order->formattedTotal() }}</strong></p>
            </div>
        </section>

        <aside class="admin-panel">
            <h2>Client</h2>
            <p>{{ $order->name }}<br>{{ $order->email }}<br>{{ $order->phone }}</p>
            <p class="admin-mute" style="margin-top:1rem">
                Payment: <strong>{{ ucfirst($order->payment_method) }}</strong> ({{ ucfirst($order->payment_status) }})
                @if ($order->payment_reference)
                    <br>Reference: <code style="font-family:monospace;">{{ $order->payment_reference }}</code>
                @endif
                <br>Date: {{ $order->created_at->format('d M Y, H:i') }}
            </p>

            <form class="form" wire:submit="updateStatus" style="margin-top:1.6rem">
                <label for="o-status">Status</label>
                <select id="o-status" wire:model="status">
                    @foreach (\App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit" style="margin-top:1rem">Update status</button>
            </form>
        </aside>
    </div>
</div>
