<?php

namespace App\Http\Controllers;

use App\Models\BeCommission;
use App\Models\BusinessExecutive;
use App\Models\Order;
use App\Services\CartManager;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackController extends Controller
{
    public function __construct(
        protected PaystackService $paystack
    ) {}

    /**
     * Handle user return redirect from Paystack payment page.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (! $reference) {
            return redirect()->route('checkout')->with('error', 'No transaction reference found from payment.');
        }

        try {
            $result = $this->paystack->verifyTransaction($reference);
        } catch (\Exception $e) {
            Log::error('Paystack verification error: '.$e->getMessage(), ['reference' => $reference]);

            return redirect()->route('checkout')->with('error', 'Unable to verify payment with Paystack. Please contact support.');
        }

        $isSuccess = ($result['status'] ?? false) && (($result['data']['status'] ?? '') === 'success');

        $order = Order::query()->where('payment_reference', $reference)->first();

        if (! $order && isset($result['data']['metadata']['order_id'])) {
            $order = Order::query()->find($result['data']['metadata']['order_id']);
        }

        if (! $order) {
            Log::error('Paystack callback order not found', ['reference' => $reference]);

            return redirect()->route('checkout')->with('error', 'Order record could not be located.');
        }

        if (! $isSuccess) {
            $order->update([
                'payment_status' => 'failed',
            ]);

            return redirect()->route('checkout')->with('error', 'Your payment was not completed or was cancelled. Please try again.');
        }

        // Successfully paid!
        $this->markOrderAsPaid($order, $result['data'] ?? []);

        // Clear cart
        app(CartManager::class)->clear();

        return redirect()->route('orders.receipt', $order)->with('success', 'Payment successful! Your order has been placed.');
    }

    /**
     * Handle background asynchronous webhooks from Paystack.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $rawPayload = $request->getContent();

        if (! $this->paystack->verifyWebhookSignature($rawPayload, $signature)) {
            Log::warning('Paystack webhook received with invalid signature');

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data', []);

        if ($event === 'charge.success' && ($data['status'] ?? '') === 'success') {
            $reference = $data['reference'] ?? null;
            $order = Order::query()->where('payment_reference', $reference)->first();

            if (! $order && isset($data['metadata']['order_id'])) {
                $order = Order::query()->find($data['metadata']['order_id']);
            }

            if ($order && $order->payment_status !== 'paid') {
                $this->markOrderAsPaid($order, $data);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Idempotently mark order and commissions as paid.
     */
    protected function markOrderAsPaid(Order $order, array $paystackData = []): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'paid_at' => now(),
        ]);

        // Ensure commissions for this order are created or set to pending approval
        if ($order->commissions()->count() === 0 && $order->items()->count() > 0) {
            $this->generateCommissionsForOrder($order);
        }

        Log::info('Order successfully marked as paid via Paystack', [
            'order_id' => $order->id,
            'reference' => $order->payment_reference,
            'total' => $order->total,
        ]);
    }

    /**
     * Generate commissions if not already present.
     */
    protected function generateCommissionsForOrder(Order $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            $code = $item->be_code ? strtolower(trim($item->be_code)) : null;
            if (! $code) {
                continue;
            }

            $executive = BusinessExecutive::query()
                ->where('code', $code)
                ->where('status', 'active')
                ->first();

            if (! $executive || ! $item->product) {
                continue;
            }

            $itemPrice = (int) $item->price;
            $commPerUnit = $item->product->calculateCommissionAmount($itemPrice, $executive->default_commission_rate);
            $totalComm = $commPerUnit * $item->qty;
            $rateLabel = $item->product->commission_type === 'fixed'
                ? \App\Models\Product::naira($item->product->commission_rate).' fixed'
                : ($item->product->commission_rate ?? $executive->default_commission_rate).'%';

            BeCommission::query()->firstOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'business_executive_id' => $executive->id,
                ],
                [
                    'product_name' => $item->product->name,
                    'sale_amount' => $itemPrice * $item->qty,
                    'commission_amount' => $totalComm,
                    'commission_rate' => $rateLabel,
                    'status' => 'pending',
                ]
            );
        }
    }
}
