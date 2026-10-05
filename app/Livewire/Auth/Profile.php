<?php

namespace App\Livewire\Auth;

use App\Support\Phone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Your profile — RHÁYỌ̀OGE')]
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required',
        ]);

        if (! Phone::isValid($this->phone)) {
            $this->addError('phone', 'Please use a valid phone number.');

            return;
        }

        $phone = Phone::normalize($this->phone);
        $user = Auth::user();

        if (
            $user->phone !== $phone
            && \App\Models\User::query()->where('phone', $phone)->where('id', '!=', $user->id)->exists()
        ) {
            $this->addError('phone', 'That phone number is already on another account.');

            return;
        }

        $user->update([
            'name' => $this->name,
            'phone' => $phone,
        ]);

        $this->dispatch('toast', message: 'Profile updated');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirectRoute('home', navigate: true);
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.auth.profile', [
            'user' => $user,
            'firstName' => Str::before($user->name, ' ') ?: $user->name,
        ]);
    }
}
