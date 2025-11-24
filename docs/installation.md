---
title: How to install package
description: How to install package
github: https://github.com/zaimealabs/socialite-extender/docs/edit/main/docs
onThisArticle: true
sidebar: true
rightbar: true
---

# Socialite Extender

[[TOC]]

## Introduction

Extends ``Laravel Socialite`` with persistent token storage and advanced API integrations for Providers. Allows linking existing users to external accounts, performing post-login API actions.

## Instalation

You can install the package via composer:

```bash
composer require zaimea/socialite-extender
```

or via composer.json

```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/zaimea/socialite-extender"
        }
    ]
```

### 1. Publish files (views, config)

``` bash
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

``` blade
@include('socialite-extender::profile.github-connect')
```

### 4. User model should use the Zaimea\SocialiteExtender\Traits\HasSocialAccounts trait

``` php
use Zaimea\SocialiteExtender\Traits\HasSocialAccounts;

class User extends Authenticatable
{
    use HasSocialAccounts, HasApiTokens, HasFactory, Notifiable;
}
```
