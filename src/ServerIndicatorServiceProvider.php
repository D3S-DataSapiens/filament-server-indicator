<?php

declare(strict_types=1);

namespace D3SDataSapiens\ServerIndicator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ServerIndicatorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'server-indicator';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        //
    }
}
