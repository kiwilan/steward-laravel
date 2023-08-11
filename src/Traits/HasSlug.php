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
 *   protected $slugColumn = 'slug_column'; // default is `slug`
 *   protected $slugWith = 'slug_with'; // default is `name`
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
            if (! isset($model->{$model->getSlugWith()})) {
                throw new \Exception("Property {$model->getSlugWith()} does not exist in table `{$model->getTable()}`, you can add `protected \$slugWith = 'name';` to your model.");
            }

            $model->{$model->getSlugColumn()} = SlugService::makeFromModel($model, $model->getSlugWith(), $model->getSlugColumn())->getSlug();
        });
    }
}
