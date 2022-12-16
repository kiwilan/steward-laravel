<?php

namespace Kiwilan\Steward;

use Kiwilan\Steward\Commands\LaravelStewardCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelStewardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-steward')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-steward_table')
            ->hasCommand(LaravelStewardCommand::class);
    }
}
