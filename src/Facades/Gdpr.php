<?php

namespace Kiwilan\Steward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Steward\Support\GdprSupport
 */
class Gdpr extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Steward\Support\GdprSupport::class;
    }
}
