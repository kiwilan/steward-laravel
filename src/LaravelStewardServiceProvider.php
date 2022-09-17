<?php

namespace Kiwilan\Steward;

use Kiwilan\Steward\Commands\Filament\FilamentConfigCommand;
use Kiwilan\Steward\Commands\LaravelStewardCommand;
use Kiwilan\Steward\Commands\MediaCleanCommand;
use Kiwilan\Steward\Commands\PublishScheduledCommand;
use Kiwilan\Steward\Commands\ScoutFreshCommand;
use Kiwilan\Steward\Commands\SubmissionRgpdVerificationCommand;
use Kiwilan\Steward\Commands\SubmissionSendCommand;
use Kiwilan\Steward\Commands\TagCleanCommand;
use Kiwilan\Steward\Components\Text;
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
            ->hasViewComponent('steward', Text::class)
            // ->hasViewComponents('steward')
            ->hasMigration('create_laravel-steward_table')
            ->hasTranslations()
            ->hasCommands([
                FilamentConfigCommand::class,
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
