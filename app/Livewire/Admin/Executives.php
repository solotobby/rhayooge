<?php

namespace App\Livewire\Admin;

use App\Mail\ExecutiveInviteMail;
use App\Models\BeCommission;
use App\Models\BusinessExecutive;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Business Executives — Dharmie')]
class Executives extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';

    public bool $showCreateModal = false;

    public bool $showDetailModal = false;

    public ?int $selectedExecutiveId = null;

    // Form fields
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public int $default_commission_rate = 10;

    public bool $send_invite = true;

    public function openCreate(): void
    {
        $this->reset(['name', 'email', 'phone']);
        $this->send_invite = true;
        $this->showCreateModal = true;
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showDetailModal = false;
        $this->selectedExecutiveId = null;
    }

    public function saveExecutive(): void
    {
        $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160|unique:business_executives,email',
            'phone' => 'nullable|string|max:40',
        ]);

        $code = BusinessExecutive::generateUniqueCode($this->name);

        $executive = BusinessExecutive::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'code' => $code,
            'invite_token' => BusinessExecutive::generateInviteToken(),
            'default_commission_rate' => $this->default_commission_rate ?: 10,
            'status' => 'active',
            'invited_at' => now(),
        ]);

        if ($this->send_invite) {
            try {
                Mail::to($executive->email)->send(new ExecutiveInviteMail($executive));
            } catch (\Throwable $e) {
                // Ignore mail sending error in local dev
            }
        }

        $this->closeModals();
        session()->flash('admin_toast', "Executive {$executive->name} created and invited.");
    }

    public function resendInvite(int $id): void
    {
        $executive = BusinessExecutive::query()->findOrFail($id);
        $executive->update(['invited_at' => now()]);

        try {
            Mail::to($executive->email)->send(new ExecutiveInviteMail($executive));
        } catch (\Throwable $e) {
            // Ignore error
        }

        session()->flash('admin_toast', "Invitation resent to {$executive->email}.");
    }

    public function sendMagicLink(int $id): void
    {
        $executive = BusinessExecutive::query()->findOrFail($id);
        $magicUrl = $executive->generateMagicLink(72);

        try {
            Mail::to($executive->email)->send(new \App\Mail\ExecutiveMagicLinkMail($executive, $magicUrl));
        } catch (\Throwable $e) {
            // Handled gracefully in local environments without SMTP
        }

        session()->flash('admin_toast', "Magic login link dispatched to {$executive->email}.");
    }

    public function toggleStatus(int $id): void
    {
        $executive = BusinessExecutive::query()->findOrFail($id);
        $newStatus = $executive->status === 'active' ? 'suspended' : 'active';
        $executive->update(['status' => $newStatus]);

        session()->flash('admin_toast', "Executive {$executive->name} is now {$newStatus}.");
    }

    public function viewExecutive(int $id): void
    {
        $this->selectedExecutiveId = $id;
        $this->showDetailModal = true;
    }

    public function markCommissionPaid(int $commissionId): void
    {
        $comm = BeCommission::query()->findOrFail($commissionId);
        $comm->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        session()->flash('admin_toast', 'Commission marked as paid.');
    }

    public function markAllPendingPaid(int $executiveId): void
    {
        BeCommission::query()
            ->where('business_executive_id', $executiveId)
            ->whereIn('status', ['pending', 'approved'])
            ->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

        session()->flash('admin_toast', 'All pending commissions marked as paid.');
    }

    public function render()
    {
        $query = BusinessExecutive::query()
            ->withCount('orders')
            ->withSum('commissions as total_earned', 'commission_amount')
            ->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $executives = $query->paginate(12);

        $selectedExecutive = $this->selectedExecutiveId
            ? BusinessExecutive::query()
                ->with(['commissions.order', 'orders'])
                ->find($this->selectedExecutiveId)
            : null;

        $totalExecutivesCount = BusinessExecutive::query()->count();
        $totalSalesSum = (int) BeCommission::query()->sum('sale_amount');
        $totalEarnedSum = (int) BeCommission::query()->sum('commission_amount');
        $totalPendingSum = (int) BeCommission::query()->whereIn('status', ['pending', 'approved'])->sum('commission_amount');

        return view('livewire.admin.executives', [
            'executives' => $executives,
            'selectedExecutive' => $selectedExecutive,
            'totalExecutivesCount' => $totalExecutivesCount,
            'totalSalesSum' => $totalSalesSum,
            'totalEarnedSum' => $totalEarnedSum,
            'totalPendingSum' => $totalPendingSum,
        ]);
    }
}
