<?php

namespace Kiwilan\Steward;

use Kiwilan\Console\Commands\MediaCleanCommand;
use Kiwilan\Console\Commands\PublishScheduledCommand;
use Kiwilan\Console\Commands\ScoutFreshCommand;
use Kiwilan\Console\Commands\SubmissionRgpdVerificationCommand;
use Kiwilan\Console\Commands\SubmissionSendCommand;
use Kiwilan\Console\Commands\TagCleanCommand;
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
            ->name('steward')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-steward_table')
            ->hasTranslations()
            ->hasCommands([
                LaravelStewardCommand::class,
                MediaCleanCommand::class,
                PublishScheduledCommand::class,
                ScoutFreshCommand::class,
                SubmissionRgpdVerificationCommand::class,
                SubmissionSendCommand::class,
                TagCleanCommand::class,
            ]);
    }
}
