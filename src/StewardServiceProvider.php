<?php

namespace Kiwilan\Steward;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Kiwilan\Steward\Commands\Filament\FilamentConfigCommand;
use Kiwilan\Steward\Commands\GenerateTypeCommand;
use Kiwilan\Steward\Commands\LogClearCommand;
use Kiwilan\Steward\Commands\MediaCleanCommand;
use Kiwilan\Steward\Commands\Publish\PublishCommand;
use Kiwilan\Steward\Commands\Publish\PublishScheduledCommand;
use Kiwilan\Steward\Commands\RoutePrintCommand;
use Kiwilan\Steward\Commands\ScoutFreshCommand;
use Kiwilan\Steward\Commands\StewardCommand;
use Kiwilan\Steward\Commands\SubmissionRgpdVerificationCommand;
use Kiwilan\Steward\Commands\SubmissionSendCommand;
use Kiwilan\Steward\Commands\TagCleanCommand;
use Kiwilan\Steward\Components\BladeApp;
use Kiwilan\Steward\Components\Button;
use Kiwilan\Steward\Components\Field\FieldCheckbox;
use Kiwilan\Steward\Components\Field\FieldRichEditor;
use Kiwilan\Steward\Components\Field\FieldSelect;
use Kiwilan\Steward\Components\Field\FieldText;
use Kiwilan\Steward\Components\Field\FieldToggle;
use Kiwilan\Steward\Components\Field\FieldUploadFile;
use Kiwilan\Steward\Http\Livewire\Field\FieldEditor;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StewardServiceProvider extends PackageServiceProvider
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
            ->hasMigration('create_steward-laravel_table')
            ->hasTranslations()
            ->hasCommands([
                FilamentConfigCommand::class,
                StewardCommand::class,
                LogClearCommand::class,
                MediaCleanCommand::class,
                PublishCommand::class,
                PublishScheduledCommand::class,
                RoutePrintCommand::class,
                ScoutFreshCommand::class,
                SubmissionRgpdVerificationCommand::class,
                SubmissionSendCommand::class,
                TagCleanCommand::class,
                GenerateTypeCommand::class,
            ])
        ;
    }

    public function bootingPackage()
    {
        $this->registerDirective();

        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'steward');

        $this->configureComponents();
    }

    public function registeringPackage()
    {
        $this->registerLivewireComponents();
    }

    private function configureComponents()
    {
        $components = [
            'stw-app' => BladeApp::class,
            'stw-button' => Button::class,
            'stw-field.checkbox' => FieldCheckbox::class,
            'stw-field.rich-editor' => FieldRichEditor::class,
            'stw-field.select' => FieldSelect::class,
            'stw-field.text' => FieldText::class,
            'stw-field.toggle' => FieldToggle::class,
            'stw-field.upload-file' => FieldUploadFile::class,
        ];

        $this->callAfterResolving(BladeCompiler::class, function () use ($components) {
            foreach ($components as $name => $class) {
                Blade::component($name, $class);
            }
        });
    }

    public function registerLivewireComponents()
    {
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (class_exists(Livewire::class)) {
                // Livewire::component('stw-field-editor', FieldEditor::class); // <livewire:stw-field-editor wire:model="about" />
            }
        });
    }

    protected function registerDirective()
    {
        Blade::directive('darkMode', function ($expression) {
            return '{!! Kiwilan\Steward\Facades\DarkMode::embed('.$expression.') !!}';
        });
    }
}
