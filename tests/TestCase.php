<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Kiwilan\Steward\StewardServiceProvider;
use Livewire\LivewireServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @internal
 */
class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Kiwilan\\Steward\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            StewardServiceProvider::class,
            LivewireServiceProvider::class,
            ExcelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_steward-laravel_table.php.stub';
        $migration->up();
        */
    }
}
