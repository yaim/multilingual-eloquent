<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Yaim\MultilingualEloquent\ServiceProvider::class,
        ];
    }
}
