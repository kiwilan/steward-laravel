<?php

namespace Kiwilan\Steward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Steward\LaravelSteward
 */
class LaravelSteward extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Steward\LaravelSteward::class;
    }
}
