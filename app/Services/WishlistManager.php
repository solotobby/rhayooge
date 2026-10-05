<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class WishlistManager
{
    public function ids(): array
    {
        return array_values(session('saved', []));
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function toggle(int $productId): bool
    {
        $ids = $this->ids();

        if (in_array($productId, $ids, true)) {
            $ids = array_values(array_filter($ids, fn ($id) => $id !== $productId));
            session(['saved' => $ids]);

            return false;
        }

        $ids[] = $productId;
        session(['saved' => $ids]);

        return true;
    }

    public function remove(int $productId): void
    {
        session(['saved' => array_values(array_filter($this->ids(), fn ($id) => $id !== $productId))]);
    }

    public function items(): Collection
    {
        return Product::query()->whereIn('id', $this->ids())->get();
    }

    public function count(): int
    {
        return count($this->ids());
    }
}
