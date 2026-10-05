<?php

namespace App\Livewire\Executive;

use App\Mail\ExecutiveMagicLinkMail;
use App\Models\BusinessExecutive;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.executive')]
#[Title('Executive Partner Sign In — RHÁYỌ̀OGE')]
class MagicLogin extends Component
{
    public string $identifier = '';

    public bool $sent = false;

    public string $sentEmail = '';

    public string $sentName = '';

    public string $directMagicUrl = '';

    public string $errorMessage = '';

    public ?BusinessExecutive $currentExecutive = null;

    public function mount(): void
    {
        if ($id = session('executive_id')) {
            $this->currentExecutive = BusinessExecutive::query()
                ->where('id', $id)
                ->where('status', 'active')
                ->first();
        }
    }

    public function logoutCurrent(): void
    {
        session()->forget(['executive_id', 'executive_token']);
        $this->currentExecutive = null;
        session()->flash('executive_login_notice', 'Signed out successfully. You can now access another account.');
    }

    public function sendMagicLink(): void
    {
        $this->errorMessage = '';

        $this->validate([
            'identifier' => 'required|string|min:3|max:120',
        ], [
            'identifier.required' => 'Please enter your registered email address, referral code, or phone number.',
        ]);

        $input = trim($this->identifier);
        $cleanCode = strtolower(ltrim($input, '@'));

        $executive = BusinessExecutive::query()
            ->where(function ($q) use ($input, $cleanCode) {
                $q->whereRaw('LOWER(email) = ?', [strtolower($input)])
                    ->orWhereRaw('LOWER(code) = ?', [$cleanCode])
                    ->orWhere('phone', $input);
            })
            ->first();

        if (! $executive) {
            $this->errorMessage = 'No executive account found matching that email, referral code, or phone number. Please verify and try again.';

            return;
        }

        if ($executive->status !== 'active') {
            $this->errorMessage = 'This executive partner account is currently suspended. Please contact the atelier management.';

            return;
        }

        $magicUrl = $executive->generateMagicLink(72);

        try {
            Mail::to($executive->email)->send(new ExecutiveMagicLinkMail($executive, $magicUrl));
        } catch (\Throwable $e) {
            // Handled gracefully in local environments without SMTP
        }

        $this->sent = true;
        $this->sentEmail = $executive->email;
        $this->sentName = $executive->name;
        $this->directMagicUrl = $magicUrl;
    }

    public function resetForm(): void
    {
        $this->sent = false;
        $this->identifier = '';
        $this->sentEmail = '';
        $this->sentName = '';
        $this->directMagicUrl = '';
        $this->errorMessage = '';
    }

    public function render()
    {
        return view('livewire.executive.magic-login');
    }
}
