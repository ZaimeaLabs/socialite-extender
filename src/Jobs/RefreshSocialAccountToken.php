<?php

namespace Zaimea\SocialiteExtender\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zaimea\SocialiteExtender\Models\SocialAccount;

class RefreshSocialAccountToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public SocialAccount $account) {}

    public function handle(): void
    {
        $provider = $this->account->provider;

        // Token endpoint can be specified in config/services.php as token_url
        $tokenUrl = config("services.{$provider}.token_url");
        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        if (!$tokenUrl || !$this->account->refresh_token || !$clientId || !$clientSecret) {
            Log::info("Refresh not available for provider {$provider} (missing config or refresh_token).");
            return;
        }

        try {
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->account->refresh_token,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            if ($response->failed()) {
                Log::warning("Failed refresh token for account {$this->account->id}: " . $response->body());
                return;
            }

            $data = $response->json();

            // Expected keys: access_token, refresh_token (optional), expires_in
            $this->account->update([
                'token' => $data['access_token'] ?? $this->account->token,
                'refresh_token' => $data['refresh_token'] ?? $this->account->refresh_token,
                'expires_in' => $data['expires_in'] ?? $this->account->expires_in,
            ]);

            Log::info("Refreshed token for social account {$this->account->id} ({$provider}).");
        } catch (\Throwable $e) {
            Log::error("Exception when refreshing token for account {$this->account->id}: {$e->getMessage()}");
        }
    }
}
