<?php

namespace Kiwilan\Steward;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
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
            ->hasTranslations()
            ->hasCommands([
                \Kiwilan\Steward\Commands\Filament\FilamentConfigCommand::class,
                \Kiwilan\Steward\Commands\LighthouseCommand::class,
                \Kiwilan\Steward\Commands\Log\LogClearCommand::class,
                \Kiwilan\Steward\Commands\MediaCleanCommand::class,
                \Kiwilan\Steward\Commands\NotifyCommand::class,
                \Kiwilan\Steward\Commands\Publish\PublishCommand::class,
                \Kiwilan\Steward\Commands\Publish\PublishScheduledCommand::class,
                \Kiwilan\Steward\Commands\RoutePrintCommand::class,
                \Kiwilan\Steward\Commands\Scout\ScoutFreshCommand::class,
                \Kiwilan\Steward\Commands\Scout\ScoutListCommand::class,
                \Kiwilan\Steward\Commands\Submission\SubmissionGdprCommand::class,
                \Kiwilan\Steward\Commands\Submission\SubmissionSendCommand::class,
                \Kiwilan\Steward\Commands\TagCleanCommand::class,
                \Kiwilan\Steward\Commands\Optimize\OptimizeFeshCommand::class,
                \Kiwilan\Steward\Commands\ClearFreshCommand::class,
                \Kiwilan\Steward\Commands\Jobs\JobListCommand::class,
                \Kiwilan\Steward\Commands\Jobs\JobClearCommand::class,
            ])
        ;
    }

    public function bootingPackage()
    {
        $this->registerDirective();

        $this->loadViewsFrom(__DIR__.'/../resources/views/components', 'steward');

        $this->configureComponents();

        Str::macro('readDuration', function (...$text) {
            $words = strip_tags(implode(' ', $text));
            $totalWords = str_word_count($words);
            $minutesToRead = round($totalWords / 200);

            return (int) max(1, $minutesToRead);
        });
    }

    public function registeringPackage()
    {
        $this->registerLivewireComponents();
    }

    private function configureComponents()
    {
        $components = [
            'stw-app' => \Kiwilan\Steward\Components\BladeApp::class,
            'stw-button' => \Kiwilan\Steward\Components\Button::class,
            'stw-dropdown' => \Kiwilan\Steward\Components\Dropdown::class,
            'stw-color-mode' => \Kiwilan\Steward\Components\ColorMode::class,
            'stw-field.checkbox' => \Kiwilan\Steward\Components\Field\Checkbox::class,
            'stw-field.rich-editor' => \Kiwilan\Steward\Components\Field\RichEditor::class,
            'stw-field.select' => \Kiwilan\Steward\Components\Field\Select::class,
            'stw-field.text' => \Kiwilan\Steward\Components\Field\Text::class,
            'stw-field.toggle' => \Kiwilan\Steward\Components\Field\Toggle::class,
            'stw-field.upload-file' => \Kiwilan\Steward\Components\Field\UploadFile::class,
            'stw-listing' => \Kiwilan\Steward\Components\Listing\Index::class,
            'stw-listing.filters' => \Kiwilan\Steward\Components\Listing\Filters::class,
            'stw-listing.filters-mobile' => \Kiwilan\Steward\Components\Listing\FiltersMobile::class,
            'stw-listing.pagination' => \Kiwilan\Steward\Components\Listing\Pagination::class,
            'stw-listing.pagination-size' => \Kiwilan\Steward\Components\Listing\PaginationSize::class,
            'stw-listing.search' => \Kiwilan\Steward\Components\Listing\Search::class,
            'stw-listing.sorters' => \Kiwilan\Steward\Components\Listing\Sorters::class,
            'stw-head-meta' => \Kiwilan\Steward\Components\HeadMeta::class,
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
                $items = [
                    // <livewire:stw-field.tiptap wire:model="content" />
                    'stw-field.tiptap' => \Kiwilan\Steward\Livewire\Field\Tiptap::class,
                    // <livewire:stw-listing.option.clear />
                    'stw-listing.option.clear' => \Kiwilan\Steward\Livewire\Listing\Option\Clear::class,
                    // <livewire:stw-listing.option.sorter />
                    'stw-listing.option.sorter' => \Kiwilan\Steward\Livewire\Listing\Option\Sorter::class,
                    // <livewire:stw-listing.option.filter />
                    'stw-listing.option.filter' => \Kiwilan\Steward\Livewire\Listing\Option\Filter::class,
                ];

                foreach ($items as $name => $class) {
                    Livewire::component($name, $class);
                }
            }
        });
    }

    protected function registerDirective()
    {
        Blade::directive('darkMode',
            fn (string $expression) => '{!! Kiwilan\Steward\Facades\DarkMode::embed('.$expression.') !!}'
        );

        Blade::directive('gdpr',
            fn (string $expression) => '{!! Kiwilan\Steward\Facades\Gdpr::embed('.$expression.') !!}'
        );

        Blade::directive('viteDefer',
            fn (string $expression) => '{!! Kiwilan\Steward\Facades\ViteDefer::embed('.$expression.') !!}'
        );

        Blade::directive('steward',
            fn (string $expression) => '{!! Kiwilan\Steward\Facades\Steward::embed('.$expression.') !!}'
        );

        Blade::directive('loop',
            fn (string $expression) => "<?php foreach ({$expression}): ?>"
        );

        Blade::directive('endloop',
            fn (string $expression) => '<?php endforeach; ?>'
        );
    }
}
