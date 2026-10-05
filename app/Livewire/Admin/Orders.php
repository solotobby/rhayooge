<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Orders — Dharmie')]
class Orders extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'All';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::query()->withCount('items')->latest();

        if ($this->status !== 'All') {
            $query->where('status', $this->status);
        }

        if (strlen(trim($this->search)) > 0) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('id', trim($this->search));
            });
        }

        return view('livewire.admin.orders', [
            'orders' => $query->paginate(12),
            'statuses' => ['All', ...Order::STATUSES],
        ]);
    }
}
