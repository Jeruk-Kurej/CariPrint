<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleLogin;
use App\Services\Auth\GoogleLoginRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class GoogleLoginController extends Controller
{
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request, GoogleLogin $login): RedirectResponse
    {
        try {
            /** @var GoogleUser $google */
            $google = Socialite::driver('google')->user();
            $user = $login->resolve($google);
        } catch (GoogleLoginRejected $exception) {
            return $this->failed($exception->getMessage());
        } catch (Throwable) {
            return $this->failed('Masuk dengan Google dibatalkan atau gagal. Coba lagi.');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    private function failed(string $message): RedirectResponse
    {
        return to_route('login')->withErrors(['google' => $message]);
    }
}
