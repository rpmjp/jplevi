<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * Sign in with Google, GitHub or LinkedIn.
 *
 * Readers do not get a password to forget or for us to store. Everyone who
 * arrives this way is a subscriber: they can comment, and nothing more.
 */
class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'github', 'linkedin-openid'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider)
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        try {
            $account = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect('/blog')->withErrors(['auth' => 'That sign in did not complete.']);
        }

        if (blank($account->getEmail())) {
            return redirect('/blog')->withErrors([
                'auth' => 'That account did not share an email address, so we cannot use it here.',
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => strtolower($account->getEmail())],
            [
                'name' => $account->getName() ?: Str::before($account->getEmail(), '@'),
                // Never used: these accounts sign in through the provider only.
                'password' => bcrypt(Str::random(48)),
                'email_verified_at' => now(),
            ],
        );

        // An existing admin signing in with Google keeps their role; a new
        // account only ever gets subscriber.
        if ($user->roles->isEmpty()) {
            $user->syncRoles(['subscriber']);
        }

        Auth::login($user, remember: true);

        return redirect($request->session()->pull('url.intended', '/blog'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back();
    }
}
