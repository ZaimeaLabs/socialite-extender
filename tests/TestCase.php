<?php

namespace Zaimea\SocialiteExtender\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Zaimea\SocialiteExtender\SocialiteExtenderServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [SocialiteExtenderServiceProvider::class];
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

    /**
     * Load package migrations if present.
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
