---
title: How to use package
description: How to use package
github: https://github.com/zaimealabs/socialite-extender/edit/main/docs/
onThisArticle: true
sidebar: true
rightbar: true
---

# Socialite Extender Usage

[[TOC]]

## Socialite Configuration

Add this in `config/services.php`:

``` php
'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('GITHUB_REDIRECT') ?? env('APP_URL') . '/socialite-extender/callback/github',
    'token_url' => env('GITHUB_TOKEN_URL', 'https://github.com/login/oauth/access_token'),
    'scopes' => ['read:user'],
],
```

## Usage

-   The logged-in user can connect a GitHub account from their profile.
-   They will see their avatar, name, and a link to their GitHub profile.
-   They can disconnect their GitHub account, which deletes stored tokens.

------------------------------------------------------------------------

## Main Features

### The `SocialAccount` Model

-   Stores token, refresh_token, nickname, avatar, and provider_user_id for each connected provider.

### Controller

-   `redirect($provider)` - redirects to the OAuth provider for authentication.
-   `callback($provider)` - saves or updates tokens for the logged-in user.
-   `disconnect($provider)` - removes the provider connection.

### Blade Partial

-   Displays connected GitHub account data (avatar, name, link, ID).
-   Provides buttons to disconnect or connect a new account.

### Zaimea\SocialiteExtender\Services\SocialApiClient

```php 
use Zaimea\SocialiteExtender\Models\SocialAccount;
use Zaimea\SocialiteExtender\Services\SocialApiClient;

$account = SocialAccount::find($id);
$response = SocialApiClient::request($account, 'get', 'https://api.github.com/user');

if ($response->ok()) {
    $data = $response->json();
}
```

------------------------------------------------------------------------

## Extensibility

You can connect any provider supported by Socialite (Google, Facebook, etc.) --- just change the `provider` in the route and config.

## Security

Tokens are securely stored and linked to the authenticated user. Use the same model pattern for any provider.

## Support

For issues or suggestions: [GitHub Issues](https://github.com/zaimealabs/socialite-extender/issues)
