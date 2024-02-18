<?php

namespace Kiwilan\Steward\Services;

use Closure;
use Kiwilan\Steward\Utils\Faker;

/**
 * Improve Faker Laravel factory service.
 *
 * @deprecated Use `Kiwilan\Steward\Utils\Faker` instead.
 */
class FactoryService
{
    public static function beforeSeed(): bool
    {
        return Faker::beforeSeed();
    }

    public static function afterSeed(): void
    {
        Faker::afterSeed();
    }

    public static function make(string|\UnitEnum|null $mediaPath = null): Faker
    {
        return Faker::make($mediaPath);
    }

    public static function noSearch(string $model, Closure $closure): mixed
    {
        return Faker::noSearch($model, $closure);
    }
}
