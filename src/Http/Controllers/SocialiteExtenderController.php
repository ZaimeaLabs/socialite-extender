<?php

namespace Zaimea\SocialiteExtender\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Zaimea\SocialiteExtender\Models\SocialAccount;

class SocialiteExtenderController extends \Illuminate\Routing\Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();
        $user = Auth::user();

        SocialAccount::updateOrCreate([
            'provider' => $provider,
            'provider_user_id' => $socialUser->id,
            'user_id' => $user->id,
        ], [
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'expires_in' => $socialUser->expiresIn ?? null,
            'nickname' => $socialUser->nickname,
            'avatar' => $socialUser->avatar,
        ]);

        return redirect()->route(config('socialite-extender.profile_route', 'profile.show'))
            ->with('success', ucfirst($provider) . ' a fost conectat cu succes!');
    }

    public function disconnect($provider)
    {
        $user = Auth::user();
        $account = $user->socialAccounts()->where('provider', $provider)->first();

        if ($account) {
            $account->delete();
        }

        return redirect()->route(config('socialite-extender.profile_route', 'profile.show'))
            ->with('success', ucfirst($provider) . ' a fost deconectat!');
    }
}
