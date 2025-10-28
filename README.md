# Laravel Socialite Extender

Extends Socialite to connect a GitHub account (or any other Socialite provider) to an existing user and store API tokens.

## Installation

``` bash
composer require zaimea/socialite-extender
```

### 1. Publish files (migrations, views, config)

``` bash
php artisan vendor:publish --provider="Zaimea\SocialiteExtender\SocialiteExtenderServiceProvider" --tag=migrations
php artisan vendor:publish --provider="Zaimea\SocialiteExtender\SocialiteExtenderServiceProvider" --tag=views
php artisan vendor:publish --provider="Zaimea\SocialiteExtender\SocialiteExtenderServiceProvider" --tag=config
```

### 2. Run migration

``` bash
php artisan migrate
```

### 3. Include the Blade partial in the user's profile

``` blade
@include('vendor.socialite-extender.profile.github-connect')
```

### 4. User model should use the Zaimea\SocialiteExtender\Traits\HasSocialAccounts trait

``` php
use Zaimea\SocialiteExtender\Traits\HasSocialAccounts;

class User extends Authenticatable
{
    use HasSocialAccounts, HasApiTokens, HasFactory, Notifiable;
}
```

------------------------------------------------------------------------

## Socialite Configuration

Add this in `config/services.php`:

``` php
'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('APP_URL') . '/socialite/callback/github',
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

------------------------------------------------------------------------

## Extensibility

You can connect any provider supported by Socialite (Google, Facebook, etc.) --- just change the `provider` in the route and config.

## Security

Tokens are securely stored and linked to the authenticated user. Use the same model pattern for any provider.

## Support

For issues or suggestions: [GitHub Issues](https://github.com/zaimea/socialite-extender/issues)
