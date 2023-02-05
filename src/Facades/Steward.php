<?php

namespace Kiwilan\Steward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Steward
 */
class Steward extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Steward\Steward::class;
    }
}
