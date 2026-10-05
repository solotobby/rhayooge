@extends('layouts.app')

@section('title', 'Order Receipt #' . $order->id . ' — RHÁYỌ̀OGE')

@section('content')
<main class="container section" style="padding-top:2.5rem;padding-bottom:5rem;max-width:880px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem;flex-wrap:wrap;gap:1.5rem;border-bottom:1px solid #ebe4da;padding-bottom:1.5rem;">
        <div>
            <p class="eyebrow" style="margin-bottom:0.4rem;">RHÁYỌ̀OGE Atelier</p>
            <h1 class="page-hero" style="font-size:2.4rem;line-height:1.15;margin:0;">Order Receipt #{{ $order->id }}</h1>
            <p class="note" style="margin-top:0.4rem;font-size:0.9rem;color:#78695d;">
                Placed on {{ $order->created_at->format('d F Y, H:i') }}
            </p>
        </div>

        <div style="display:flex;gap:0.75rem;align-items:center;">
            <a href="{{ route('orders.receipt.pdf', $order) }}" class="btn btn-accent" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.4rem;" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Download PDF Receipt</span>
            </a>
            <button type="button" class="btn btn-light" onclick="window.print()" style="display:inline-flex;align-items:center;gap:0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print</span>
            </button>
        </div>
    </div>

    {{-- Order Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:1.5rem;margin-bottom:2.5rem;">
        <div style="background:#faf8f5;border:1px solid #eee7dc;border-radius:8px;padding:1.4rem;">
            <p class="eyebrow" style="font-size:0.72rem;margin-bottom:0.6rem;color:#8c7362;">Client & Delivery</p>
            <strong style="display:block;font-size:1.05rem;color:#221f1e;margin-bottom:0.25rem;">{{ $order->name }}</strong>
            <p style="margin:0;font-size:0.85rem;color:#6b5d52;line-height:1.5;">
                {{ $order->phone }}<br>
                {{ $order->email }}
            </p>
            <div style="margin-top:0.9rem;padding-top:0.9rem;border-top:1px dashed #e2dad0;font-size:0.85rem;color:#443b35;">
                <strong>Address:</strong> {{ $order->address }}, {{ $order->city }}
                @if ($order->shipping_location_name || $order->shippingLocation)
                    <br><span style="color:#8c7362;font-size:0.8rem;">Zone: {{ $order->shipping_location_name ?? $order->shippingLocation->name }}</span>
                @endif
            </div>
        </div>

        <div style="background:#faf8f5;border:1px solid #eee7dc;border-radius:8px;padding:1.4rem;">
            <p class="eyebrow" style="font-size:0.72rem;margin-bottom:0.6rem;color:#8c7362;">Payment & Settlement</p>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
                <span style="font-size:0.85rem;color:#6b5d52;">Status</span>
                <span style="padding:0.25rem 0.65rem;border-radius:4px;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;background:{{ $order->payment_status === 'paid' ? '#e8f5e9' : '#fff8e1' }};color:{{ $order->payment_status === 'paid' ? '#2e7d32' : '#b78103' }};">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:0.85rem;">
                <span style="color:#6b5d52;">Method</span>
                <strong style="color:#221f1e;">{{ ucfirst($order->payment_method) }}</strong>
            </div>
            @if ($order->payment_reference)
                <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:0.85rem;">
                    <span style="color:#6b5d52;">Reference</span>
                    <code style="font-family:monospace;color:#221f1e;">{{ $order->payment_reference }}</code>
                </div>
            @endif
            @if ($order->paid_at)
                <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:0.85rem;">
                    <span style="color:#6b5d52;">Date Settled</span>
                    <span style="color:#221f1e;">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                </div>
            @endif
            @if ($order->be_code)
                <div style="display:flex;justify-content:space-between;margin-top:0.4rem;font-size:0.85rem;color:#b85d38;">
                    <span>Concierge / Ref</span>
                    <strong style="font-family:monospace;">@{{ $order->be_code }}</strong>
                </div>
            @endif
        </div>
    </div>

    {{-- Items List --}}
    <div style="background:#ffffff;border:1px solid #ebe4da;border-radius:8px;overflow:hidden;margin-bottom:2.5rem;">
        <div style="padding:1.25rem 1.5rem;background:#faf8f5;border-bottom:1px solid #ebe4da;">
            <h2 style="font-size:1.2rem;margin:0;font-family:var(--font-serif);color:#221f1e;">Purchased Pieces</h2>
        </div>

        <div style="padding:0.5rem 1.5rem;">
            @foreach ($order->items as $item)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 0;border-bottom:1px solid #f0eee9;gap:1.5rem;">
                    <div style="display:flex;align-items:center;gap:1.2rem;">
                        <img 
                            src="{{ $item->product?->image ?? asset('assets/brand-guide.png') }}" 
                            alt="{{ $item->product?->name }}" 
                            style="width:64px;height:80px;object-fit:cover;border-radius:4px;border:1px solid #e7dfd5;background:#f5efe6;"
                            onerror="this.onerror=null;this.src='{{ asset('assets/brand-guide.png') }}'"
                        >
                        <div>
                            <h3 style="font-size:1.05rem;margin:0 0 0.25rem;color:#221f1e;font-family:var(--font-serif);">
                                {{ $item->product?->name ?? 'Garment' }}
                            </h3>
                            <p style="margin:0;font-size:0.82rem;color:#78695d;">
                                Size: <strong>{{ $item->size ?: 'Standard' }}</strong> &bull; Qty: <strong>{{ $item->qty }}</strong>
                                @if (!empty($item->be_code))
                                    &bull; <span style="color:#b85d38;font-weight:600;">Ref: @{{ $item->be_code }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:0.82rem;color:#78695d;display:block;">
                            ₦{{ number_format($item->price) }} each
                        </span>
                        <strong style="font-family:monospace;font-size:1.15rem;color:#221f1e;">
                            ₦{{ number_format($item->price * $item->qty) }}
                        </strong>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Totals Breakdown --}}
        <div style="background:#faf8f5;padding:1.5rem;border-top:1px solid #ebe4da;">
            <div style="max-width:320px;margin-left:auto;display:flex;flex-direction:column;gap:0.6rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.92rem;color:#6b5d52;">
                    <span>Subtotal</span>
                    <span style="font-family:monospace;">₦{{ number_format($order->subtotal) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:0.92rem;color:#6b5d52;">
                    <span>
                        Delivery
                        @if ($order->shipping_location_name)
                            <small style="display:block;font-size:0.75rem;color:#8c7362;">({{ $order->shipping_location_name }})</small>
                        @endif
                    </span>
                    <span style="font-family:monospace;">
                        {{ $order->delivery > 0 ? '₦' . number_format($order->delivery) : 'Complimentary' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:1.25rem;font-weight:700;color:#221f1e;padding-top:0.75rem;border-top:1px solid #e0d7cb;">
                    <span>Total Paid</span>
                    <span style="font-family:monospace;">₦{{ number_format($order->total) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <a href="{{ route('shop') }}" class="btn btn-light" wire:navigate>&larr; Return to Collection</a>
        <a href="{{ route('orders.receipt.pdf', $order) }}" class="btn btn-accent" target="_blank">
            Download Official PDF &rarr;
        </a>
    </div>
</main>
@endsection
