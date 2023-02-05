<?php

namespace Kiwilan\Steward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Steward
 */
class DarkMode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Steward\Support\DarkModeSupport::class;
    }
}
