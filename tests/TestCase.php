<?php

declare(strict_types=1);

namespace D3SDataSapiens\ServerIndicator\Tests;

use D3SDataSapiens\ServerIndicator\ServerIndicatorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ServerIndicatorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
