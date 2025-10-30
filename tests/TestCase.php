<?php

namespace Zaimea\SocialiteExtender\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Zaimea\SocialiteExtender\SocialiteExtenderServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SocialiteExtenderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Use sqlite in-memory for tests
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

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
