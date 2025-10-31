<?php

namespace Zaimea\SocialiteExtender\Traits;

use Zaimea\SocialiteExtender\Models\SocialAccount;

trait HasSocialAccounts
{
    /**
     * Get all connected social accounts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the connected social account for a given provider (e.g. 'github').
     *
     * @param string $provider
     * @return \Zaimea\SocialiteExtender\Models\SocialAccount|null
     */
    public function socialAccount(string $provider): ?SocialAccount
    {
        return $this->socialAccounts()
            ->where('provider', $provider)
            ->first();
    }

    /**
     * Check if the user has connected a given provider.
     *
     * @param string $provider
     * @return bool
     */
    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()
            ->where('provider', $provider)
            ->exists();
    }

    /**
     * Get the avatar URL from the connected provider, if available.
     *
     * @param string $provider
     * @return string|null
     */
    public function socialAvatar(string $provider): ?string
    {
        return optional($this->socialAccount($provider))->avatar;
    }

    /**
     * Get the Nickname from the connected provider, if available.
     *
     * @param string $provider
     * @return string|null
     */
    public function socialNickname(string $provider): ?string
    {
        return optional($this->socialAccount($provider))->nickname;
    }

    /**
     * Get the token from the connected provider, if available.
     *
     * @param string $provider
     * @return string|null
     */
    public function socialToken(string $provider): ?string
    {
        return optional($this->socialAccount($provider))->token;
    }
}
