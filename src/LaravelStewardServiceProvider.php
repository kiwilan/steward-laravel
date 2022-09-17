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
use Kiwilan\Steward\Components\AppName;
use Kiwilan\Steward\Components\Field\Checkbox;
use Kiwilan\Steward\Components\Field\Select;
use Kiwilan\Steward\Components\Field\Text;
use Kiwilan\Steward\Components\Field\Toggle;
use Kiwilan\Steward\Components\Field\UploadFile;
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
            ->hasViewComponents('steward', [
                Checkbox::class,
                Select::class,
                Text::class,
                Toggle::class,
                UploadFile::class,
                AppName::class,
            ])
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
