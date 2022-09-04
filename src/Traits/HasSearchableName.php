<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Your model have to be connected to `laravel/scout` with `Searchable` trait.
 *
 * Create a `searchableAs` method in your model to return `APP_NAME` and Model's name slugify.
 */
trait HasSearchableName
{
    public function searchableAs()
    {
        $instance = new $this();
        $class = new ReflectionClass($instance);
        $name = $class->getShortName();

        $appname = config('app.name');

        return Str::slug("{$appname} {$name}", '_');
    }
}
