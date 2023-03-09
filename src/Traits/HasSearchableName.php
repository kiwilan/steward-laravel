<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Your model have to be connected to `laravel/scout` with `Searchable` trait.
 *
 * Create a `searchableAs` method in your model to return `APP_NAME` and Model's name slugify.
 *
 * ```php
 * class MyModel extends Model {
 *   use HasSearchableName, Searchable {
 *      HasSearchableName::searchableAs insteadof Searchable;
 *   }
 * }
 * ```
 */
trait HasSearchableName
{
    public function searchableNameAs(): string
    {
        $instance = new $this();
        $class = new ReflectionClass($instance);
        $name = Str::snake($class->getShortName());

        $appname = config('app.name');

        return Str::slug("{$appname} {$name}", '_');
    }

    public function isSearchable(): bool
    {
        return method_exists($this, 'searchableAs');
    }

    public function searchableAs()
    {
        return $this->searchableNameAs();
    }
}
