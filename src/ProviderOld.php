<?php

namespace Kiwilan\Steward;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
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
use Kiwilan\Steward\Components\Field\FieldCheckbox;
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
            // ->hasViewComponents(
            //     'stw',
            //     Button::class,
            //     FieldCheckbox::class,
            //     FieldEditor::class,
            //     FieldSelect::class,
            //     FieldText::class,
            //     FieldToggle::class,
            //     FieldUploadFile::class,
            // )
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

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/jetstream.php', 'jetstream');

        $this->app->afterResolving(BladeCompiler::class, function () {
            if (config('jetstream.stack') === 'livewire' && class_exists(Livewire::class)) {
                // Livewire::component('navigation-menu', NavigationMenu::class);
                // Livewire::component('profile.update-profile-information-form', UpdateProfileInformationForm::class);
                // Livewire::component('profile.update-password-form', UpdatePasswordForm::class);
                // Livewire::component('profile.two-factor-authentication-form', TwoFactorAuthenticationForm::class);
                // Livewire::component('profile.logout-other-browser-sessions-form', LogoutOtherBrowserSessionsForm::class);
                // Livewire::component('profile.delete-user-form', DeleteUserForm::class);
            }
        });
    }

    public function boot()
    {
        // Blade::component('stw-button', Button::class);
        // Blade::component('stw-field.checkbox', FieldCheckbox::class);

        Blade::componentNamespace('Steward\\Views\\Components', 'stw');
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
