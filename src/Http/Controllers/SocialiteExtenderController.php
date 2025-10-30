<?php

namespace Zaimea\SocialiteExtender\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Zaimea\SocialiteExtender\Models\SocialAccount;

class SocialiteExtenderController extends Controller
{
    /**
     * Redirect user to provider with a dynamic redirectUrl.
     */
    public function redirect(Request $request, string $provider)
    {
        // Build absolute callback URL from named route
        $callbackUrl = route('socialite-extender.callback', ['provider' => $provider], true);

        // Optionally attach scopes from config
        $scopes = config("services.{$provider}.scopes", []);
        $driver = Socialite::driver($provider)->redirectUrl($callbackUrl);

        if (!empty($scopes)) {
            $driver->scopes($scopes);
        }

        return $driver->redirect();
    }

    /**
     * Handle callback and persist tokens. Uses try/catch and dispatches event.
     */
    public function callback(Request $request, string $provider)
    {
        try {
            // stateless() is optional; use it for SPA or multi-domain setups
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = Auth::user();

            $account = SocialAccount::updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_user_id' => (string)$socialUser->getId(),
                    'user_id' => $user->id,
                ],
                [
                    'token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                    'expires_in' => $socialUser->expiresIn ?? null,
                    'nickname' => $socialUser->getNickname() ?? $socialUser->getName() ?? null,
                    'avatar' => $socialUser->getAvatar() ?? null,
                ]
            );

            Event::dispatch('socialite.connected', [$account, $user, $provider]);

            return redirect()
                ->route(config('socialite-extender.profile_route', 'profile.show'))
                ->with('success', ucfirst($provider) . ' connected.');
        } catch (\Throwable $e) {
            Log::error("Socialite callback error for {$provider}: {$e->getMessage()}", [
                'provider' => $provider,
                'exception' => $e,
            ]);

            return redirect()
                ->route(config('socialite-extender.profile_route', 'profile.show'))
                ->with('error', "Failed to connect {$provider} account.");
        }
    }
/**
     * Disconnect provider from current user.
     */
    public function disconnect(Request $request, string $provider)
    {
        $user = Auth::user();

        $request->validate([
            '_token' => 'required',
        ]);

        $account = $user->socialAccounts()->where('provider', $provider)->first();

        if ($account) {
            $account->delete();
            Event::dispatch('socialite.disconnected', [$account, $user, $provider]);
        }

        return redirect()
            ->route(config('socialite-extender.profile_route', 'profile.show'))
            ->with('success', ucfirst($provider) . ' disconnected.');
    }
}
