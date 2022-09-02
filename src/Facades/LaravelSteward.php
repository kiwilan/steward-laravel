<?php

namespace Kiwilan\LaravelSteward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\LaravelSteward\LaravelSteward
 */
class LaravelSteward extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\LaravelSteward\LaravelSteward::class;
    }
}
