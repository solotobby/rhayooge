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

    #[Url]
    public string $paymentStatus = 'All';

    #[Url(as: 'view')]
    public ?int $inspectingOrderId = null;

    public bool $showInspectModal = false;

    public function mount(): void
    {
        if ($this->inspectingOrderId) {
            $this->showInspectModal = true;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentStatus(): void
    {
        $this->resetPage();
    }

    public function inspectOrder(int $id): void
    {
        $this->inspectingOrderId = $id;
        $this->showInspectModal = true;
    }

    public function closeInspectModal(): void
    {
        $this->showInspectModal = false;
        $this->inspectingOrderId = null;
    }

    public function updateOrderStatus(int $id, string $newStatus): void
    {
        if (! in_array($newStatus, Order::STATUSES, true)) {
            return;
        }

        $order = Order::findOrFail($id);
        $order->update(['status' => $newStatus]);

        $this->dispatch('toast', message: "Order #{$order->id} marked as " . ucfirst($newStatus) . ".");
    }

    public function markOrderPaid(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->dispatch('toast', message: "Order #{$order->id} marked as Paid.");
    }

    public function render()
    {
        $query = Order::query()
            ->with(['items.product', 'shippingLocation', 'businessExecutive', 'commissions'])
            ->withCount('items')
            ->latest();

        if ($this->status !== 'All') {
            $query->where('status', $this->status);
        }

        if ($this->paymentStatus !== 'All') {
            $query->where('payment_status', $this->paymentStatus);
        }

        if (strlen(trim($this->search)) > 0) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('shipping_location_name', 'like', $term)
                    ->orWhere('payment_reference', 'like', $term)
                    ->orWhere('be_code', 'like', $term)
                    ->orWhere('id', trim($this->search));
            });
        }

        $inspectingOrder = null;
        if ($this->showInspectModal && $this->inspectingOrderId) {
            $inspectingOrder = Order::query()
                ->with(['items.product', 'shippingLocation', 'businessExecutive', 'commissions'])
                ->find($this->inspectingOrderId);
        }

        $stats = [
            'total_count' => Order::query()->count(),
            'paid_revenue' => (int) Order::query()->where('payment_status', 'paid')->sum('total'),
            'pending_fulfillment' => Order::query()->whereIn('status', ['confirmed', 'preparing'])->count(),
            'delivered_count' => Order::query()->where('status', 'delivered')->count(),
        ];

        return view('livewire.admin.orders', [
            'orders' => $query->paginate(15),
            'statuses' => ['All', ...Order::STATUSES],
            'paymentStatuses' => ['All', 'paid', 'pending'],
            'inspectingOrder' => $inspectingOrder,
            'stats' => $stats,
        ]);
    }
}
