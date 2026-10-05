<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Phone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        if (! config("services.{$provider}.client_id")) {
            return redirect()
                ->route('account')
                ->with('error', 'Add your '.$provider.' credentials in .env to enable this sign-in.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('account')
                ->with('error', 'We could not connect to '.$provider.'. Please try again.');
        }

        $email = $socialUser->getEmail();

        if (! $email) {
            return redirect()
                ->route('account')
                ->with('error', 'That '.$provider.' account did not share an email address.');
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $socialUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'phone' => null,
                'password' => Str::password(20),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        } else {
            $user->forceFill([
                'provider' => $user->provider === 'email' ? 'email' : $provider,
                'provider_id' => $user->provider_id ?: $socialUser->getId(),
            ])->save();
        }

        Auth::login($user, true);

        if (! Phone::isValid($user->phone)) {
            return redirect()->route('profile')->with('notice', 'Add your phone number to complete your profile.');
        }

        return redirect()->intended(route('profile'));
    }
}
