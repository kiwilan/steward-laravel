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

    /**
     * @param  string  $model Model name like `User::class`
     * @return array<int, mixed>
     */
    public static function searchAsSearchable(string $search, bool $asObject = true): array
    {
        if (! method_exists(static::class, 'search') && ! method_exists(static::class, 'toSearchableArray')) {
            throw new \Exception('Model {static::class} does not have method `search` or `toSearchableArray`.');
        }

        $searched = static::class::search($search)->get();

        $results = [];

        foreach ($searched as $model) {
            $data = $model
                ->load([
                    'creator',
                    'owner',
                    'armies',
                    'universe',
                    'gameplays',
                ])
                ->toSearchableArray()
            ;

            if ($asObject) {
                $data = (object) $data;
            }
            $results[] = $data;
        }

        return $results;
    }
}
