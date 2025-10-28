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
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = Auth::user();

            $account = SocialAccount::updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_user_id' => $socialUser->id,
                    'user_id' => $user->id,
                ],
                [
                    'token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                    'expires_in' => $socialUser->expiresIn ?? null,
                    'nickname' => $socialUser->nickname,
                    'avatar' => $socialUser->avatar,
                ]
            );

            Event::dispatch('socialite.connected', [$account]);

            return redirect()
                ->route(config('socialite-extender.profile_route', 'profile.show'))
                ->with('success', ucfirst($provider) . ' was successfully connected!');
        } catch (\Exception $e) {
            Log::error("Socialite callback error: {$e->getMessage()}", ['provider' => $provider]);
            return redirect()
                ->route(config('socialite-extender.profile_route', 'profile.show'))
                ->with('error', "Failed to connect {$provider} account.");
        }
    }

    public function disconnect(Request $request, string $provider)
    {
        $user = Auth::user();

        $request->validate([
            '_token' => 'required',
        ]);

        $account = $user->socialAccounts()->where('provider', $provider)->first();

        if ($account) {
            $account->delete();
            Event::dispatch('socialite.disconnected', [$account]);
        }

        return redirect()
            ->route(config('socialite-extender.profile_route', 'profile.show'))
            ->with('success', ucfirst($provider) . ' was successfully disconnected!');
    }
}
