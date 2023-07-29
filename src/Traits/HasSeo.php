<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasSeo
 *
 * - Default meta title is `title`, can be override by setting `$metaTitleFrom` property
 * - Default meta description is `description`, can be override by setting `$metaDescriptionFrom` property
 * - Database columns: `meta_title`, `meta_description`
 *
 * ```php
 * class Post extends Model
 * {
 *    use HasSeo;
 *
 *   protected $metaTitleFrom = 'from_title_column';
 *   protected $metaDescriptionFrom = 'from_description_column';
 * }
 * ```
 */
trait HasSeo
{
    protected $defaultMetaTitleFrom = 'name';

    protected $defaultMetaDescriptionFrom = 'description';

    public function initializeHasSeo()
    {
        $this->fillable[] = 'meta_title';
        $this->fillable[] = 'meta_description';
    }

    public static function bootHasSeo()
    {
        static::creating(function ($model) {
            if (empty($model->meta_title)) {
                $model->meta_title = $model->limitStringSize($model->{$model->getMetaTitle()});
            }

            if (empty($model->meta_description)) {
                $model->meta_description = $model->limitStringSize($model->{$model->getMetaDescription()});
            }
        });
    }

    public function getMetaTitle(): string
    {
        return $this->meta_title_from ?? $this->metaTitleFrom ?? $this->defaultMetaTitleFrom;
    }

    public function getMetaDescription(): string
    {
        return $this->meta_description_from ?? $this->metaDescriptionFrom ?? $this->defaultMetaDescriptionFrom;
    }

    /**
     * @return array<string, string>
     */
    public function getSeoAttribute(): array
    {
        return [
            'title' => $this->getMetaTitle(),
            'description' => $this->getMetaDescription(),
        ];
    }

    private function limitStringSize(string $string = null, int $limit = 250): ?string
    {
        if ($string && Str::length($string) > $limit) {
            return Str::limit($string, $limit, '...');
        }

        return $string;
    }
}
