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
use Kiwilan\Steward\Components\Button;
use Kiwilan\Steward\Components\FieldCheckbox;
use Kiwilan\Steward\Components\FieldEditor;
use Kiwilan\Steward\Components\FieldSelect;
use Kiwilan\Steward\Components\FieldText;
use Kiwilan\Steward\Components\FieldToggle;
use Kiwilan\Steward\Components\FieldUploadFile;
use Kiwilan\Steward\Livewire\Editor;
use Livewire\Livewire;
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
        Livewire::component('steward::editor', Editor::class);

        $package
            ->name('steward')
            ->hasConfigFile()
            ->hasViews()
            ->hasViewComponents(
                'steward',
                Button::class,
                FieldCheckbox::class,
                FieldEditor::class,
                FieldSelect::class,
                FieldText::class,
                FieldToggle::class,
                FieldUploadFile::class,
            )
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
