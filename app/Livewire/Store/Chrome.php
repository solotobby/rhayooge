<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Services\CartManager;
use App\Services\WishlistManager;
use Livewire\Attributes\On;
use Livewire\Component;

class Chrome extends Component
{
    public bool $showNav = false;

    public bool $showCart = false;

    public bool $showSaved = false;

    public bool $showSearch = false;

    public bool $showProduct = false;

    public string $search = '';

    public ?int $activeProductId = null;

    public string $selectedSize = '';

    public string $toast = '';

    public function mount(): void
    {
        $this->showCart = (bool) session('open_cart', false);
        session()->forget('open_cart');
    }

    #[On('open-cart')]
    public function openCart(): void
    {
        $this->closeAll();
        $this->showCart = true;
    }

    #[On('open-saved')]
    public function openSaved(): void
    {
        $this->closeAll();
        $this->showSaved = true;
    }

    #[On('open-product')]
    public function openProduct(int $id): void
    {
        $product = Product::query()->find($id);

        if (! $product) {
            return;
        }

        $this->closeAll();
        $this->activeProductId = $id;
        $this->selectedSize = $product->sizes[0] ?? 'One size';
        $this->showProduct = true;
    }

    #[On('toast')]
    public function showToast(string $message): void
    {
        $this->toast = $message;
    }

    #[On('add-to-cart')]
    public function onAddToCart(int $productId): void
    {
        $this->addToCart($productId);
    }

    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Re-render counts and drawers.
    }

    public function clearToast(): void
    {
        $this->toast = '';
    }

    public function closeAll(): void
    {
        $this->showNav = false;
        $this->showCart = false;
        $this->showSaved = false;
        $this->showSearch = false;
        $this->showProduct = false;
    }

    public function toggleNav(): void
    {
        $open = ! $this->showNav;
        $this->closeAll();
        $this->showNav = $open;
    }

    public function openSearch(): void
    {
        $this->closeAll();
        $this->showSearch = true;
    }

    public function chooseSize(string $size): void
    {
        $this->selectedSize = $size;
    }

    public function addToCart(?int $productId = null, ?string $size = null): void
    {
        $id = $productId ?: $this->activeProductId;
        $product = Product::query()->find($id);

        if (! $product) {
            return;
        }

        $referralCode = CartManager::resolveReferralCode();

        app(CartManager::class)->add(
            $product->id,
            $size ?: $this->selectedSize ?: ($product->sizes[0] ?? 'One size'),
            1,
            $referralCode
        );
        $this->toast = 'Added to bag · '.$product->name;
        $this->dispatch('cart-updated');
    }

    public function changeQty(string $key, int $delta): void
    {
        app(CartManager::class)->changeQty($key, $delta);
        $this->dispatch('cart-updated');
    }

    public function removeFromCart(string $key): void
    {
        app(CartManager::class)->remove($key);
        $this->dispatch('cart-updated');
    }

    public function moveToSaved(string $key): void
    {
        $cart = app(CartManager::class);
        $item = $cart->raw()[$key] ?? null;

        if (! $item) {
            return;
        }

        if (! app(WishlistManager::class)->has($item['product_id'])) {
            app(WishlistManager::class)->toggle($item['product_id']);
        }

        $cart->remove($key);
        $this->toast = 'Moved to saved for later';
        $this->dispatch('cart-updated');
    }

    public function toggleSaved(int $productId): void
    {
        $saved = app(WishlistManager::class)->toggle($productId);
        $this->toast = $saved ? 'Saved for later' : 'Removed from saved';
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $cart = app(CartManager::class);
        $wishlist = app(WishlistManager::class);
        $product = $this->activeProductId ? Product::query()->find($this->activeProductId) : null;

        $results = collect();
        if ($this->showSearch && strlen(trim($this->search)) > 1) {
            $q = '%'.trim($this->search).'%';
            $results = Product::query()
                ->where(fn ($query) => $query
                    ->where('name', 'like', $q)
                    ->orWhere('category', 'like', $q)
                    ->orWhere('description', 'like', $q))
                ->limit(8)
                ->get();
        }

        return view('livewire.store.chrome', [
            'cartItems' => $cart->lines(),
            'cartCount' => $cart->count(),
            'cartTotal' => $cart->subtotal(),
            'savedItems' => $wishlist->items(),
            'savedCount' => $wishlist->count(),
            'savedIds' => $wishlist->ids(),
            'activeProduct' => $product,
            'searchResults' => $results,
        ]);
    }
}
