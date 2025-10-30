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
        // you can configure DB sqlite in-memory for tests
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        // run migrations from package (or load your test migrations)
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
