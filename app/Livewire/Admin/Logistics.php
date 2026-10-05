<?php

namespace App\Livewire\Admin;

use App\Models\ShippingLocation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Logistics & Shipping — Dharmie')]
class Logistics extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public ?int $fee = null;
    public string $estimated_days = '';
    public bool $is_active = true;
    public int $sort_order = 0;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'fee' => 'required|integer|min:0',
            'estimated_days' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'fee', 'estimated_days', 'editingId']);
        $this->is_active = true;
        $this->sort_order = ShippingLocation::count() + 1;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $location = ShippingLocation::findOrFail($id);
        $this->editingId = $location->id;
        $this->name = $location->name;
        $this->fee = $location->fee;
        $this->estimated_days = (string) ($location->estimated_days ?? '');
        $this->is_active = (bool) $location->is_active;
        $this->sort_order = (int) $location->sort_order;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'fee', 'estimated_days', 'editingId']);
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            $location = ShippingLocation::findOrFail($this->editingId);
            $location->update([
                'name' => trim($this->name),
                'fee' => $this->fee,
                'estimated_days' => trim($this->estimated_days) ?: null,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ]);
            $this->dispatch('toast', message: 'Delivery location updated.');
        } else {
            ShippingLocation::create([
                'name' => trim($this->name),
                'fee' => $this->fee,
                'estimated_days' => trim($this->estimated_days) ?: null,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ]);
            $this->dispatch('toast', message: 'Delivery location added.');
        }

        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $location = ShippingLocation::findOrFail($id);
        $location->update(['is_active' => ! $location->is_active]);
        $status = $location->is_active ? 'active' : 'inactive';
        $this->dispatch('toast', message: "Location marked {$status}.");
    }

    public function delete(int $id): void
    {
        $location = ShippingLocation::findOrFail($id);
        $location->delete();
        $this->dispatch('toast', message: 'Location deleted successfully.');
    }

    public function render()
    {
        $query = ShippingLocation::query()->orderBy('sort_order')->orderBy('name');

        if (strlen(trim($this->search)) > 0) {
            $term = '%' . trim($this->search) . '%';
            $query->where('name', 'like', $term);
        }

        return view('livewire.admin.logistics', [
            'locations' => $query->paginate(15),
        ]);
    }
}
