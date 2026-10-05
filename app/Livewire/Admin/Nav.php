<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Nav extends Component
{
    public bool $open = false;

    public string $toast = '';

    public function mount(): void
    {
        $this->toast = (string) session('admin_toast', '');
    }

    #[On('toast')]
    public function showToast(string $message): void
    {
        $this->toast = $message;
    }

    public function clearToast(): void
    {
        $this->toast = '';
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirectRoute('admin.login', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.nav', [
            'unread' => ContactMessage::query()->whereNull('read_at')->count(),
            'openOrders' => Order::query()->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'user' => Auth::user(),
        ]);
    }
}
