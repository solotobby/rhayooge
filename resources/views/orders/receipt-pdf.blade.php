<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RHÁYỌ̀OGE — Receipt #{{ $order->id }}</title>
    <style>
        @page {
            margin: 28mm 20mm 25mm 20mm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1c1917;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #231d19;
            padding-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: #231d19;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .brand-subtitle {
            font-size: 9px;
            letter-spacing: 0.18em;
            color: #8c7362;
            text-transform: uppercase;
            margin: 0;
        }
        .receipt-badge {
            text-align: right;
        }
        .receipt-badge h1 {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: #231d19;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .receipt-badge p {
            font-size: 11px;
            color: #78695d;
            margin: 0;
        }
        .meta-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .meta-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-col {
            vertical-align: top;
            width: 50%;
        }
        .meta-col h3 {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #8c7362;
            margin: 0 0 8px 0;
            border-bottom: 1px solid #eee5dc;
            padding-bottom: 4px;
            display: inline-block;
            width: 90%;
        }
        .meta-col p {
            margin: 0 0 4px 0;
            font-size: 11px;
            color: #38312b;
            line-height: 1.4;
        }
        .status-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .status-paid {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-pending {
            background: #fff8e1;
            color: #b78103;
        }
        .status-unpaid {
            background: #fbe9e7;
            color: #c62828;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6d5d52;
            background: #faf7f4;
            border-top: 1px solid #e5dcce;
            border-bottom: 1px solid #e5dcce;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 10px 10px;
            border-bottom: 1px solid #f0eae1;
            font-size: 11px;
            color: #292420;
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 6px 10px;
            font-size: 11px;
        }
        .totals-table tr.grand-total td {
            font-size: 14px;
            font-weight: 700;
            color: #231d19;
            border-top: 2px solid #231d19;
            border-bottom: 1px solid #231d19;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e5dcce;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #8c7362;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td style="vertical-align: middle;">
                    <div class="brand-title">RHÁYỌ̀OGE</div>
                    <div class="brand-subtitle">Atelier · Modern Luxury · Lagos</div>
                </td>
                <td class="receipt-badge" style="vertical-align: middle;">
                    <h1>Official Receipt</h1>
                    <p>Order #{{ $order->id }} · {{ $order->created_at->format('d M Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="meta-section">
        <table>
            <tr>
                <td class="meta-col">
                    <h3>Client & Delivery Address</h3>
                    <p><strong>{{ $order->name }}</strong></p>
                    <p>{{ $order->phone }}</p>
                    <p>{{ $order->email }}</p>
                    <p style="margin-top: 6px;">{{ $order->address }}</p>
                    <p>{{ $order->city }}</p>
                    @if ($order->shipping_location_name || $order->shippingLocation)
                        <p style="color:#8c7362;margin-top:4px;">
                            <strong>Delivery Zone:</strong> {{ $order->shipping_location_name ?? $order->shippingLocation->name }}
                        </p>
                    @endif
                </td>
                <td class="meta-col">
                    <h3>Payment & Order Summary</h3>
                    <p><strong>Payment Status:</strong> 
                        <span class="status-pill {{ $order->payment_status === 'paid' ? 'status-paid' : ($order->payment_status === 'pending' ? 'status-pending' : 'status-unpaid') }}">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </p>
                    <p><strong>Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    @if ($order->payment_reference)
                        <p><strong>Reference:</strong> <span style="font-family:monospace;">{{ $order->payment_reference }}</span></p>
                    @endif
                    @if ($order->paid_at)
                        <p><strong>Settled On:</strong> {{ $order->paid_at->format('d M Y · H:i') }}</p>
                    @endif
                    @if ($order->be_code)
                        <p style="color:#b85d38;margin-top:4px;">
                            <strong>Executive Concierge:</strong> @<span>{{ $order->be_code }}</span>
                        </p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Item / Description</th>
                <th style="width: 15%;" class="text-center">Size</th>
                <th style="width: 15%;" class="text-center">Qty</th>
                <th style="width: 25%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        <strong style="color:#231d19;">{{ $item->product?->name ?? 'RHÁYỌ̀OGE Garment' }}</strong>
                        @if ($item->be_code)
                            <div style="font-size:9px;color:#8c7362;margin-top:2px;">Ref: @<span>{{ $item->be_code }}</span></div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->size ?: 'Standard' }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">₦{{ number_format($item->price * $item->qty) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                <div style="background: #faf7f4; border: 1px solid #eee5dc; border-radius: 4px; padding: 12px; font-size: 10.5px; color: #6d5d52; line-height: 1.5;">
                    <strong style="color: #231d19; display: block; margin-bottom: 4px;">Thank you for your patronage.</strong>
                    Every RHÁYỌ̀OGE piece is tailored with enduring craftsmanship and intention. For care instructions, alterations, or delivery inquiries, reach our client concierge at <strong>contact@rhayooge.com</strong>.
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td style="color:#6d5d52;">Subtotal</td>
                        <td class="text-right">₦{{ number_format($order->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td style="color:#6d5d52;">
                            Delivery Fee
                            @if ($order->shipping_location_name)
                                <span style="font-size:9px;display:block;color:#8c7362;">({{ $order->shipping_location_name }})</span>
                            @endif
                        </td>
                        <td class="text-right">
                            {{ $order->delivery > 0 ? '₦' . number_format($order->delivery) : 'Complimentary' }}
                        </td>
                    </tr>
                    <tr class="grand-total">
                        <td>Total Paid</td>
                        <td class="text-right">₦{{ number_format($order->total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>RHÁYỌ̀OGE ATELIER</strong> · Lagos, Nigeria · rhayooge.com</p>
        <p>This is a computer-generated client receipt. All rights reserved.</p>
    </div>

</body>
</html>
