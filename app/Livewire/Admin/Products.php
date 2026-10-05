<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Collection — Dharmie')]
class Products extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $category = 'All';

    #[Url]
    public string $status = 'all';

    public ?int $inspectingProductId = null;

    public bool $showInspectModal = false;

    public array $categories = ['All', 'Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Sets', 'Accessories'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function inspectProduct(int $id): void
    {
        $this->inspectingProductId = $id;
        $this->showInspectModal = true;
    }

    public function closeInspectModal(): void
    {
        $this->showInspectModal = false;
        $this->inspectingProductId = null;
    }

    public function toggleStatus(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $newStatus = $product->status === 'draft' ? 'published' : 'draft';
        $product->update(['status' => $newStatus]);
        $this->dispatch('toast', message: "Piece status changed to {$newStatus}.");
    }

    public function toggleFeatured(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $product->update(['featured' => ! $product->featured]);
    }

    public function toggleNewest(int $id): void
    {
        $product = Product::query()->findOrFail($id);
        $product->update(['newest' => ! $product->newest]);
    }

    public function delete(int $id): void
    {
        $product = Product::query()->findOrFail($id);

        if ($product->orderItems()->exists()) {
            $this->dispatch('toast', message: 'This piece is on an order and cannot be removed.');

            return;
        }

        $product->delete();
        $this->dispatch('toast', message: 'Piece removed from the collection.');
    }

    public function render()
    {
        $query = Product::query()->latest();

        if ($this->category !== 'All') {
            $query->where('category', $this->category);
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (strlen(trim($this->search)) > 0) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)->orWhere('slug', 'like', $term);
            });
        }

        $inspectingProduct = $this->inspectingProductId
            ? Product::query()->with('orderItems')->find($this->inspectingProductId)
            : null;

        return view('livewire.admin.products', [
            'products' => $query->paginate(10),
            'inspectingProduct' => $inspectingProduct,
        ]);
    }
}
