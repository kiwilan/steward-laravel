<?php

namespace Kiwilan\Steward\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kiwilan\Steward\Support\ViteDeferSupport
 */
class ViteDefer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kiwilan\Steward\Support\ViteDeferSupport::class;
    }
}
