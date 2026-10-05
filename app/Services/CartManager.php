<?php

namespace App\Services;

use App\Models\BusinessExecutive;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartManager
{
    public static function resolveReferralCode(?string $explicitCode = null): ?string
    {
        $candidate = $explicitCode ?? request()->query('ref') ?? request()->query('be') ?? session('be_ref');

        if (! $candidate) {
            return null;
        }

        $code = strtolower(trim((string) $candidate));

        $validCode = BusinessExecutive::query()
            ->whereRaw('LOWER(code) = ?', [$code])
            ->where('status', 'active')
            ->value('code');

        return $validCode ?: null;
    }

    public function raw(): array
    {
        return session('cart', []);
    }

    public function lines(): Collection
    {
        $products = Product::query()
            ->whereIn('id', collect($this->raw())->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return collect($this->raw())->map(function (array $item, string $key) use ($products) {
            $product = $products->get($item['product_id']);

            if (! $product) {
                return null;
            }

            return (object) [
                'key' => $key,
                'product' => $product,
                'size' => $item['size'],
                'qty' => $item['qty'],
                'line_total' => $product->price * $item['qty'],
                'referral_code' => $item['referral_code'] ?? null,
            ];
        })->filter()->values();
    }

    public function add(int $productId, string $size, int $qty = 1, ?string $referralCode = null): void
    {
        $cart = $this->raw();

        $refCode = $referralCode !== null
            ? ($referralCode !== '' ? strtolower(trim($referralCode)) : null)
            : self::resolveReferralCode();

        // Key uniquely identifies product + size + referral code (so referred vs organic items are distinct)
        $key = $productId.'::'.$size.($refCode ? '::'.$refCode : '');

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
            if ($refCode && empty($cart[$key]['referral_code'])) {
                $cart[$key]['referral_code'] = $refCode;
            }
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'size' => $size,
                'qty' => $qty,
                'referral_code' => $refCode,
            ];
        }

        session(['cart' => $cart]);
    }

    public function changeQty(string $key, int $delta): void
    {
        $cart = $this->raw();

        if (! isset($cart[$key])) {
            return;
        }

        $cart[$key]['qty'] += $delta;

        if ($cart[$key]['qty'] < 1) {
            unset($cart[$key]);
        }

        session(['cart' => $cart]);
    }

    public function remove(string $key): void
    {
        $cart = $this->raw();
        unset($cart[$key]);
        session(['cart' => $cart]);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }

    public function count(): int
    {
        return collect($this->raw())->sum('qty');
    }

    public function subtotal(): int
    {
        return $this->lines()->sum('line_total');
    }

    public function delivery(?int $customFee = null): int
    {
        $subtotal = $this->subtotal();

        if ($subtotal === 0) {
            return 0;
        }

        if ($customFee !== null) {
            return $customFee;
        }

        return 5000;
    }

    public function total(?int $customDeliveryFee = null): int
    {
        return $this->subtotal() + $this->delivery($customDeliveryFee);
    }
}
