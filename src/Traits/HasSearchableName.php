<?php

namespace Kiwilan\Steward\Traits;

use ReflectionClass;

trait HasSearchableName
{
    public function searchableAs()
    {
        $instance = new $this();
        $class = new ReflectionClass($instance);
        $name = $class->getShortName();
        $name = strtolower($name);

        $appname = config('app.name');

        return "{$appname}_{$name}";
    }
}
