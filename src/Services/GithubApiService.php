<?php

namespace Zaimea\SocialiteExtender\Services;

use Illuminate\Support\Facades\Http;
use Zaimea\SocialiteExtender\Models\SocialAccount;

class GithubApiService
{
    public function getUserRepos(SocialAccount $account)
    {
        $response = Http::withToken($account->token)
            ->get('https://api.github.com/user/repos');

        return $response->json();
    }
}
