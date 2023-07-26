<?php

namespace Kiwilan\Steward\Tests;

use Kiwilan\Steward\StewardServiceProvider;
use Livewire\LivewireServiceProvider;
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
        ];
    }
}
