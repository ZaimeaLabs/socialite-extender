<?php

namespace Zaimea\SocialiteExtender\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zaimea\SocialiteExtender\Jobs\RefreshSocialAccountToken;
use Zaimea\SocialiteExtender\Models\SocialAccount;

class SocialApiClient
{
    /**
     * Perform a GET request with automatic refresh on 401.
     * $method can be GET|POST|... and $options passed to Http:: withOptions()
     */
    public static function request(SocialAccount $account, string $method, string $url, array $options = [])
    {
        $token = $account->token;

        $response = Http::withToken($token)->{$method}($url, $options);

        if ($response->status() === 401) {
            // Try to refresh synchronously (dispatch a job synchronously)
            if ($account->refresh_token) {
                try {
                    RefreshSocialAccountToken::dispatchSync($account->fresh());
                    $account->refresh(); // reload
                    $token = $account->token;
                    $response = Http::withToken($token)->{$method}($url, $options);
                } catch (\Throwable $e) {
                    Log::error("Token refresh retry failed for account {$account->id}: {$e->getMessage()}");
                }
            }
        }

        return $response;
    }
}
