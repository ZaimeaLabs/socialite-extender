<?php

namespace Zaimea\SocialiteExtender;

use Illuminate\Support\ServiceProvider;

class SocialiteExtenderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/socialite-extender.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'socialite-extender');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/socialite-extender'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../config/socialite-extender.php' => config_path('socialite-extender.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/socialite-extender.php',
            'socialite-extender'
        );
    }
}
