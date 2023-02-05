<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Services\SlugService;

trait HasSlug
{
    protected $default_slug_with = 'name';

    protected $default_slug_column = 'slug';

    public function initializeHasSlug()
    {
        $this->fillable[] = $this->getSlugColumn();
    }

    public function getSlugWith(): string
    {
        $default = $this->default_slug_with;

        if (null === $default) {
            $default = 'title';
        }

        return $this->slug_with ?? $default;
    }

    public function getSlugColumn(): string
    {
        return $this->slug_column ?? $this->default_slug_column;
    }

    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->{$model->getSlugColumn()} = SlugService::make($model, $model->getSlugWith(), $model->getSlugColumn());
        });
    }
}
