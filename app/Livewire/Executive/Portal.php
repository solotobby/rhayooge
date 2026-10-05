<?php

namespace App\Livewire\Executive;

use App\Models\BusinessExecutive;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.executive')]
#[Title('Executive Partner Workspace — RHÁYỌ̀OGE')]
class Portal extends Component
{
    public string $token = '';

    public ?BusinessExecutive $executive = null;

    #[Url]
    public string $tab = 'catalog'; // 'catalog', 'ledger', 'payout'

    #[Url]
    public string $search = '';

    #[Url]
    public string $category = 'all';

    public string $phone = '';

    public string $bank_name = '';

    public string $bank_account_number = '';

    public string $bank_account_name = '';

    public bool $editingBank = false;

    public string $toast = '';

    public function mount(?string $token = null): void
    {
        if ($token) {
            $this->token = $token;
            $this->executive = BusinessExecutive::query()
                ->with(['commissions.order'])
                ->where(function ($q) use ($token) {
                    $q->where('invite_token', $token)
                        ->orWhere(function ($mq) use ($token) {
                            $mq->where('magic_token', $token)
                                ->where('magic_token_expires_at', '>=', now());
                        });
                })
                ->where('status', 'active')
                ->first();

            if (! $this->executive) {
                session()->forget(['executive_id', 'executive_token']);
                session()->flash('executive_login_error', 'Your access link is invalid or has expired. Please request a new magic link.');
                $this->redirectRoute('executive.login', navigate: true);

                return;
            }

            // Authenticate executive session
            session([
                'executive_id' => $this->executive->id,
                'executive_token' => $this->executive->invite_token,
            ]);
        } else {
            // Check existing active executive session
            $executiveId = session('executive_id');

            if (! $executiveId) {
                $this->redirectRoute('executive.login', navigate: true);

                return;
            }

            $this->executive = BusinessExecutive::query()
                ->with(['commissions.order'])
                ->where('id', $executiveId)
                ->where('status', 'active')
                ->first();

            if (! $this->executive) {
                session()->forget(['executive_id', 'executive_token']);
                session()->flash('executive_login_error', 'Your partner session has expired. Please sign in again.');
                $this->redirectRoute('executive.login', navigate: true);

                return;
            }

            $this->token = $this->executive->invite_token;
        }

        $this->phone = $this->executive->phone ?? '';
        $this->bank_name = $this->executive->bank_name ?? '';
        $this->bank_account_number = $this->executive->bank_account_number ?? '';
        $this->bank_account_name = $this->executive->bank_account_name ?? '';

        // Record activity
        $this->executive->update(['last_active_at' => now()]);
    }

    public function logout(): void
    {
        session()->forget(['executive_id', 'executive_token']);
        session()->flash('executive_login_notice', 'You have been safely signed out of your partner workspace.');

        $this->redirectRoute('executive.login', navigate: true);
    }

    public function saveBankDetails(): void
    {
        $this->validate([
            'phone' => 'nullable|string|max:30',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|min:10|max:20',
            'bank_account_name' => 'required|string|max:120',
        ]);

        $this->executive->update([
            'phone' => $this->phone,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_name' => $this->bank_account_name,
        ]);

        $this->editingBank = false;
        $this->toast = 'Settlement and banking details updated successfully.';
    }

    public function clearToast(): void
    {
        $this->toast = '';
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function render()
    {
        $query = Product::query()->published()->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('category', 'like', "%{$this->search}%");
            });
        }

        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }

        $products = $query->get();

        $categories = ['all', 'Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Sets', 'Accessories'];

        return view('livewire.executive.portal', [
            'products' => $products,
            'categories' => $categories,
            'commissions' => $this->executive->commissions()->with('order')->latest()->get(),
        ]);
    }
}
