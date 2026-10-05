<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin-auth')]
#[Title('Dharmie — RHÁYỌ̀OGE')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public function mount(): void
    {
        if (Auth::check() && Auth::user()->is_admin) {
            $this->redirectRoute('admin.dashboard', navigate: true);
        }
    }

    public function login(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', strtolower(trim($this->email)))->first();

        if (! $user || ! Hash::check($this->password, $user->password) || ! $user->is_admin) {
            $this->addError('email', 'This desk is for the house only.');

            return;
        }

        Auth::login($user, true);

        $this->redirectRoute('admin.dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
