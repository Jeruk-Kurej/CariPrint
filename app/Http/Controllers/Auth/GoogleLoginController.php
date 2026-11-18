<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleLogin;
use App\Services\Auth\GoogleLoginRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
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
        if ($request->has('error')) {
            return $this->failed('Masuk dengan Google dibatalkan.');
        }

        try {
            /** @var GoogleUser $google */
            $google = Socialite::driver('google')->user();
            $user = $login->resolve($google);
        } catch (GoogleLoginRejected $exception) {
            return $this->failed($exception->getMessage());
        } catch (InvalidStateException) {
            return $this->failed('Sesi masuk kedaluwarsa. Tekan Masuk dengan Google sekali lagi.');
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed('Masuk dengan Google gagal. Coba lagi sebentar lagi.');
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
