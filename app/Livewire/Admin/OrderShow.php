<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Order — Dharmie')]
class OrderShow extends Component
{
    public Order $order;

    public string $status = '';

    public function mount(Order $order): void
    {
        $this->order = $order->load('items.product');
        $this->status = $order->status;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'status' => 'required|in:'.implode(',', Order::STATUSES),
        ]);

        $this->order->update(['status' => $this->status]);
        $this->dispatch('toast', message: 'Order marked '.$this->status.'.');
    }

    public function render()
    {
        return view('livewire.admin.order-show');
    }
}
