<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Support\Phone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Account — RHÁYỌ̀OGE')]
class Account extends Component
{
    public string $tab = 'login';

    public string $loginId = '';

    public string $loginPassword = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('profile', navigate: true);
        }

        if (request()->query('next')) {
            session(['url.intended' => request()->query('next')]);
        }

        if (request()->query('tab') === 'create') {
            $this->tab = 'signup';
        }
    }

    public function login(): void
    {
        $this->validate([
            'loginId' => 'required',
            'loginPassword' => 'required',
        ]);

        $identifier = trim($this->loginId);
        $user = str_contains($identifier, '@')
            ? User::query()->where('email', strtolower($identifier))->first()
            : User::query()->where('phone', Phone::normalize($identifier))->first();

        if (! $user) {
            $this->addError('loginId', 'We could not find that account.');

            return;
        }

        if ($user->provider !== 'email') {
            $this->addError('loginId', 'This profile was created with '.$user->provider.'. Continue with '.$user->provider.'.');

            return;
        }

        if (! Hash::check($this->loginPassword, $user->password)) {
            $this->addError('loginPassword', 'That password does not match.');

            return;
        }

        Auth::login($user, true);
        $this->redirectIntended(route('profile'), navigate: true);
    }

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|min:6|same:passwordConfirmation',
        ]);

        if (! Phone::isValid($this->phone)) {
            $this->addError('phone', 'Please use a valid phone number.');

            return;
        }

        $phone = Phone::normalize($this->phone);

        if (User::query()->where('phone', $phone)->exists()) {
            $this->addError('phone', 'An account with this phone already exists. Sign in instead.');

            return;
        }

        $user = User::query()->create([
            'name' => $this->name,
            'email' => strtolower($this->email),
            'phone' => $phone,
            'password' => $this->password,
            'provider' => 'email',
        ]);

        Auth::login($user, true);
        $this->redirectIntended(route('profile'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.account')
            ->layout('layouts::app', ['authLayout' => true]);
    }
}
