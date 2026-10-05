<?php

namespace App\Livewire\Store;

use App\Models\BeCommission;
use App\Models\BusinessExecutive;
use App\Models\Order;
use App\Services\CartManager;
use App\Services\PaystackService;
use App\Support\Phone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Checkout — RHÁYỌ̀OGE')]
class Checkout extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $city = 'Lagos';

    public string $payment = 'paystack'; // 'paystack', 'delivery', 'transfer'

    public bool $placed = false;

    public ?Order $confirmedOrder = null;

    #[Url]
    public ?string $paid = null;

    #[Url]
    public ?string $order_id = null;

    public function mount(): void
    {
        if ($user = Auth::user()) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
        }

        // Check if returning from a successful Paystack transaction
        if ($this->paid === '1' && $this->order_id) {
            $order = Order::query()->find($this->order_id);
            if ($order && $order->payment_status === 'paid') {
                $this->confirmedOrder = $order;
                $this->placed = true;
            }
        }
    }

    public function placeOrder()
    {
        $cart = app(CartManager::class);

        $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:80',
            'payment' => 'required|in:paystack,card,delivery,transfer',
        ]);

        if (! Phone::isValid($this->phone)) {
            $this->addError('phone', 'Please use a valid phone number.');

            return;
        }

        $lines = $cart->lines();

        if ($lines->isEmpty()) {
            $this->addError('name', 'Your bag is empty.');

            return;
        }

        // Identify all active executives referenced by items in the cart
        $beCodes = $lines->pluck('referral_code')->filter()->unique()->values();
        $executivesByCode = collect();
        if ($beCodes->isNotEmpty()) {
            $executivesByCode = BusinessExecutive::query()
                ->whereIn('code', $beCodes)
                ->where('status', 'active')
                ->get()
                ->keyBy(fn ($be) => strtolower($be->code));
        }

        $primaryExecutive = $executivesByCode->first();

        // Standardize payment method
        $paymentMethod = in_array($this->payment, ['paystack', 'card']) ? 'paystack' : $this->payment;

        // If paying with Paystack
        if ($paymentMethod === 'paystack') {
            $paystack = app(PaystackService::class);

            if (! $paystack->isConfigured()) {
                $this->addError('payment', 'Paystack API keys are not configured in your .env file. Please add PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY.');

                return;
            }

            $reference = 'RHY_'.strtoupper(Str::random(12));

            $order = Order::query()->create([
                'user_id' => Auth::id(),
                'business_executive_id' => $primaryExecutive?->id,
                'be_code' => $primaryExecutive?->code,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => Phone::normalize($this->phone),
                'address' => $this->address,
                'city' => $this->city,
                'payment_method' => 'paystack',
                'payment_reference' => $reference,
                'payment_status' => 'pending',
                'subtotal' => $cart->subtotal(),
                'delivery' => $cart->delivery(),
                'total' => $cart->total(),
                'status' => 'pending',
            ]);

            foreach ($lines as $line) {
                $itemRefCode = ! empty($line->referral_code) ? strtolower(trim($line->referral_code)) : null;

                $order->items()->create([
                    'product_id' => $line->product->id,
                    'size' => $line->size,
                    'qty' => $line->qty,
                    'price' => $line->product->price,
                    'be_code' => $itemRefCode,
                ]);

                // Decrement stock tentatively
                if ($line->product->quantity > 0) {
                    $line->product->decrement('quantity', min($line->qty, $line->product->quantity));
                }
            }

            try {
                $initResponse = $paystack->initializeTransaction([
                    'email' => $this->email,
                    'amount' => (int) ($order->total * 100), // in kobo
                    'reference' => $reference,
                    'callback_url' => route('paystack.callback'),
                    'metadata' => [
                        'order_id' => $order->id,
                        'customer_name' => $this->name,
                        'customer_phone' => Phone::normalize($this->phone),
                    ],
                ]);

                if (! empty($initResponse['data']['authorization_url'])) {
                    return redirect()->away($initResponse['data']['authorization_url']);
                }

                $this->addError('payment', 'Unable to redirect to Paystack payment gateway.');

                return;
            } catch (\Exception $e) {
                $this->addError('payment', 'Paystack Error: '.$e->getMessage());

                return;
            }
        }

        // Offline payment paths (Delivery or Manual Transfer)
        $order = Order::query()->create([
            'user_id' => Auth::id(),
            'business_executive_id' => $primaryExecutive?->id,
            'be_code' => $primaryExecutive?->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => Phone::normalize($this->phone),
            'address' => $this->address,
            'city' => $this->city,
            'payment_method' => $this->payment,
            'payment_reference' => null,
            'payment_status' => $this->payment === 'delivery' ? 'unpaid' : 'pending',
            'subtotal' => $cart->subtotal(),
            'delivery' => $cart->delivery(),
            'total' => $cart->total(),
            'status' => 'confirmed',
        ]);

        foreach ($lines as $line) {
            $itemRefCode = ! empty($line->referral_code) ? strtolower(trim($line->referral_code)) : null;

            $order->items()->create([
                'product_id' => $line->product->id,
                'size' => $line->size,
                'qty' => $line->qty,
                'price' => $line->product->price,
                'be_code' => $itemRefCode,
            ]);

            // Decrement stock
            if ($line->product->quantity > 0) {
                $line->product->decrement('quantity', min($line->qty, $line->product->quantity));
            }

            // Award affiliate commission ONLY if this item was attached with a valid BE link
            if ($itemRefCode && isset($executivesByCode[$itemRefCode])) {
                $executive = $executivesByCode[$itemRefCode];
                $itemPrice = (int) $line->product->price;
                $commPerUnit = $line->product->calculateCommissionAmount($itemPrice, $executive->default_commission_rate);
                $totalCommission = $commPerUnit * $line->qty;
                $rateLabel = $line->product->commission_type === 'fixed'
                    ? \App\Models\Product::naira($line->product->commission_rate).' fixed'
                    : ($line->product->commission_rate ?? $executive->default_commission_rate).'%';

                BeCommission::query()->create([
                    'business_executive_id' => $executive->id,
                    'order_id' => $order->id,
                    'product_id' => $line->product->id,
                    'product_name' => $line->product->name,
                    'sale_amount' => $itemPrice * $line->qty,
                    'commission_amount' => $totalCommission,
                    'commission_rate' => $rateLabel,
                    'status' => 'pending',
                ]);
            }
        }

        $cart->clear();
        $this->confirmedOrder = $order;
        $this->placed = true;
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $cart = app(CartManager::class);

        return view('livewire.store.checkout', [
            'cartItems' => $cart->lines(),
            'subtotal' => $cart->subtotal(),
            'delivery' => $cart->delivery(),
            'total' => $cart->total(),
        ]);
    }
}
