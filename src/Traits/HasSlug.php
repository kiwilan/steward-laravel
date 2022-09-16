<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;

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
        if ($default === null) {
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
            if (empty($model->{$model->getSlugColumn()})) {
                $model->{$model->getSlugColumn()} = $model->generateUniqueSlug($model->{$model->getSlugWith()}, 0, $model->getSlugColumn());
            }
        });
    }

    protected function generateUniqueSlug(?string $name = null, int $counter = 0, string $slug_field = 'slug'): string
    {
        if ($name === null) {
            $name = uniqid();
        }
        $updated_name = 0 == $counter ? $name : $name.'-'.$counter;
        if (static::where($slug_field, Str::slug($updated_name))->exists()) {
            return $this->generateUniqueSlug($name, $counter + 1, $slug_field);
        }

        return Str::slug($updated_name);
    }
}
