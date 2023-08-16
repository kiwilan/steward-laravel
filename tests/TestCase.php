<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Schema;
use Kiwilan\Steward\StewardServiceProvider;
use Livewire\LivewireServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @internal
 */
class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
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
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        Schema::dropAllTables();
        $migration = include __DIR__.'/Data/database/migrations/create_models_tables.php';
        $migration->up();
    }
}
