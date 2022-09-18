<?php

namespace Kiwilan\Steward\Tests;

use DOMDocument;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kiwilan\Steward\Http\Livewire\Editor;
use Kiwilan\Steward\LaravelStewardServiceProvider;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Livewire\Testing\TestableLivewire;
use Maatwebsite\Excel\ExcelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Kiwilan\\Steward\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        parent::setUp();

        View::addNamespace('test', __DIR__.'/resources/views');

        $this
            ->registerLivewireComponents()
            ->registerLivewireTestMacros();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelStewardServiceProvider::class,
            LivewireServiceProvider::class,
            ExcelServiceProvider::class,
        ];
    }

    private function registerLivewireComponents(): self
    {
        Livewire::component('editor', Editor::class);

        return $this;
    }

    public function registerLivewireTestMacros(): self
    {
        TestableLivewire::macro('jsonContent', function (string $elementId) {
            $document = new DOMDocument();

            $document->loadHTML($this->lastRenderedDom);

            $content = $document->getElementById($elementId)->textContent;

            return json_decode($content, true);
        });

        TestableLivewire::macro('htmlContent', function (string $elementId) {
            $document = new DOMDocument();

            $document->preserveWhiteSpace = false;

            $document->loadHTML($this->lastRenderedDom);

            $domNode = $document->getElementById($elementId);

            return Str::of($document->saveHTML($domNode))
                ->replace("\n", "\r\n")
                ->trim()
                ->toString();
        });

        return $this;
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-steward_table.php.stub';
        $migration->up();
        */
    }
}
