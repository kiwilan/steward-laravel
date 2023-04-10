<?php

namespace Kiwilan\Steward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Steward\Support\MatomoSupport
 */
class Matomo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Steward\Support\MatomoSupport::class;
    }
}
