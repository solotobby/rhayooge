<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Services\CartManager;
use App\Services\WishlistManager;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductShow extends Component
{
    public Product $product;

    public string $selectedSize = '';

    #[Url]
    public ?string $ref = null;

    #[Url]
    public ?string $be = null;

    public function mount(Product $product): void
    {
        if ($product->isDraft() && ! auth()->user()?->is_admin) {
            abort(404);
        }

        $this->product = $product;
        $this->selectedSize = $product->sizes[0] ?? 'One size';
        $this->ref = request()->query('ref');
        $this->be = request()->query('be');
    }

    public function chooseSize(string $size): void
    {
        $this->selectedSize = $size;
    }

    public function addToCart(): void
    {
        $referralCode = CartManager::resolveReferralCode($this->ref ?? $this->be);

        app(CartManager::class)->add(
            $this->product->id,
            $this->selectedSize,
            1,
            $referralCode
        );

        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: 'Added to bag · '.$this->product->name);
        $this->dispatch('open-cart');
    }

    public function toggleSaved(): void
    {
        $saved = app(WishlistManager::class)->toggle($this->product->id);
        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: $saved ? 'Saved for later' : 'Removed from saved');
    }

    public function render()
    {
        $related = Product::query()
            ->published()
            ->where('category', $this->product->category)
            ->where('id', '!=', $this->product->id)
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = $related->concat(
                Product::query()
                    ->published()
                    ->where('id', '!=', $this->product->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->inRandomOrder()
                    ->limit(4 - $related->count())
                    ->get()
            );
        }

        return view('livewire.store.product-show', [
            'related' => $related,
            'saved' => app(WishlistManager::class)->has($this->product->id),
        ])->layout('layouts::app', [
            'title' => $this->product->name.' — RHÁYỌ̀OGE',
        ]);
    }
}
