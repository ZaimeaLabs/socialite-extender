<?php

declare(strict_types=1);

namespace Zaimea\SocialiteExtender\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zaimea\SocialiteExtender\SocialiteExtenderServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connection.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Laravel\Socialite\SocialiteServiceProvider::class,
            SocialiteExtenderServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Socialite' => \Laravel\Socialite\Facades\Socialite::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('services.github', [
            'client_id' => 'dummy-id',
            'client_secret' => 'dummy-secret',
            'redirect' => 'http://localhost/callback',
        ]);

        Facade::clearResolvedInstance('Socialite');

        // Programmatically create the minimal tables we need for tests.
        // This avoids invoking artisan migrations and makes the tests stable in CI.

        // users table (basic)
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // password_resets (if something expects it)
        if (! Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // social_accounts table (minimal)
        if (! Schema::hasTable('social_accounts')) {
            Schema::create('social_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('provider', 50);
                $table->string('provider_user_id', 191);
                $table->text('token')->nullable();
                $table->text('refresh_token')->nullable();
                $table->integer('expires_in')->nullable();
                $table->string('nickname')->nullable();
                $table->string('avatar')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    protected function tearDown(): void
    {
        // Drop tables to ensure clean state between tests (SQLite in-memory gets cleared,
        // but for safety, drop if exists)
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');

        parent::tearDown();
    }
}
