<?php

namespace Kiwilan\Steward\Utils;

use Closure;

/**
 * Improve Faker Laravel factory service.
 *
 * @deprecated Use `Kiwilan\Steward\Utils\Factory` instead.
 */
class FactoryService
{
    public static function beforeSeed(): bool
    {
        return Factory::beforeSeed();
    }

    public static function afterSeed(): void
    {
        Factory::afterSeed();
    }

    public static function make(string|\UnitEnum|null $mediaPath = null): Factory
    {
        return Factory::make($mediaPath);
    }

    public static function noSearch(string $model, Closure $closure): mixed
    {
        return Factory::noSearch($model, $closure);
    }
}
