<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Services\WishlistManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Shop — RHÁYỌ̀OGE')]
class Shop extends Component
{
    #[Url]
    public string $category = 'All';

    #[Url]
    public int $maxPrice = 90000;

    #[Url]
    public string $sort = 'featured';

    public bool $showFilters = false;

    public int $visible = 8;

    public array $categories = ['All', 'Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Sets', 'Accessories'];

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->visible = 8;
        $this->showFilters = false;
    }

    public function updatedCategory(): void
    {
        $this->visible = 8;
    }

    public function updatedMaxPrice(): void
    {
        $this->visible = 8;
    }

    public function updatedSort(): void
    {
        $this->visible = 8;
    }

    public function loadMore(): void
    {
        $this->visible += 8;
    }

    public function render()
    {
        $query = Product::query()->published()->where('price', '<=', $this->maxPrice);

        if ($this->category !== 'All') {
            $query->where('category', $this->category);
        }

        $query = match ($this->sort) {
            'price-asc' => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('newest')->orderByDesc('id'),
            default => $query->orderByDesc('featured')->orderBy('name'),
        };

        $all = $query->get();

        return view('livewire.store.shop', [
            'products' => $all->take($this->visible),
            'total' => $all->count(),
            'hasMore' => $all->count() > $this->visible,
            'savedIds' => app(WishlistManager::class)->ids(),
        ]);
    }
}
