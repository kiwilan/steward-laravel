<?php

namespace Kiwilan\Steward;

use Kiwilan\Steward\Commands\Filament\FilamentConfigCommand;
use Kiwilan\Steward\Commands\LaravelStewardCommand;
use Kiwilan\Steward\Commands\LogClearCommand;
use Kiwilan\Steward\Commands\MediaCleanCommand;
use Kiwilan\Steward\Commands\Publish\PublishCommand;
use Kiwilan\Steward\Commands\Publish\PublishScheduledCommand;
use Kiwilan\Steward\Commands\RoutePrintCommand;
use Kiwilan\Steward\Commands\ScoutFreshCommand;
use Kiwilan\Steward\Commands\StewardPhpCsFixerCommand;
use Kiwilan\Steward\Commands\SubmissionRgpdVerificationCommand;
use Kiwilan\Steward\Commands\SubmissionSendCommand;
use Kiwilan\Steward\Commands\TagCleanCommand;
use Kiwilan\Steward\Components\Button;
use Kiwilan\Steward\Components\Field\Checkbox;
use Kiwilan\Steward\Components\FieldEditor;
use Kiwilan\Steward\Components\FieldSelect;
use Kiwilan\Steward\Components\FieldText;
use Kiwilan\Steward\Components\FieldToggle;
use Kiwilan\Steward\Components\FieldUploadFile;
use Kiwilan\Steward\Http\Livewire\Editor;
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

        $package
            ->name('steward')
            ->hasConfigFile()
            ->hasViews()
            ->hasViewComponent('stw.field.', Checkbox::class)
            ->hasViewComponents(
                'stw',
                Button::class,
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
                LogClearCommand::class,
                MediaCleanCommand::class,
                PublishCommand::class,
                PublishScheduledCommand::class,
                RoutePrintCommand::class,
                ScoutFreshCommand::class,
                StewardPhpCsFixerCommand::class,
                SubmissionRgpdVerificationCommand::class,
                SubmissionSendCommand::class,
                TagCleanCommand::class,
            ]);
    }

    // public function bootingPackage()
    // {
    //     $this->registerLivewireComponents();
    // }

    // public function registerLivewireComponents()
    // {
    //     Livewire::component('stw-editor', Editor::class);
    // }
}
