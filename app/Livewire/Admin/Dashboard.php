<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Dharmie — RHÁYỌ̀OGE')]
class Dashboard extends Component
{
    public function render()
    {
        $revenue = (int) Order::query()->where('status', '!=', 'cancelled')->sum('total');

        return view('livewire.admin.dashboard', [
            'revenue' => $revenue,
            'orderCount' => Order::query()->count(),
            'openOrders' => Order::query()->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'productCount' => Product::query()->count(),
            'clientCount' => User::query()->where('is_admin', false)->count(),
            'unread' => ContactMessage::query()->whereNull('read_at')->count(),
            'recentOrders' => Order::query()->latest()->take(6)->get(),
        ]);
    }
}
