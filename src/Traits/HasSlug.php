<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Services\SlugService;

/**
 * Trait HasSlug
 *
 * - Default slug column is `slug`, can be override by setting `$slugColumn` property
 * - Default slug with is `name`, can be override by setting `$slugWith` property
 *
 * ```php
 * class Post extends Model
 * {
 *    use HasSlug;
 *
 *   protected $slugColumn = 'slug_column';
 *   protected $slugWith = 'slug_with';
 * }
 * ```
 */
trait HasSlug
{
    protected $defaultSlugWith = 'name';

    protected $defaultSlugColumn = 'slug';

    public function initializeHasSlug()
    {
        $this->fillable[] = $this->getSlugColumn();
    }

    public function getSlugWith(): string
    {
        return $this->slug_with ?? $this->slugWith ?? $this->defaultSlugWith;
    }

    public function getSlugColumn(): string
    {
        return $this->slug_column ?? $this->slugColumn ?? $this->defaultSlugColumn;
    }

    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->{$model->getSlugColumn()} = SlugService::make($model, $model->getSlugWith(), $model->getSlugColumn());
        });
    }
}
