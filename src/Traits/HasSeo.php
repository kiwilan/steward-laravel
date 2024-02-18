<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait HasSeo
 *
 * - Default meta title is `name`, can be override by setting `$metaTitleFrom` property
 * - Default meta description is `description`, can be override by setting `$metaDescriptionFrom` property
 * - Database columns: `meta_title`, `meta_description` are not overridable
 *
 * ```php
 * class Post extends Model
 * {
 *    use HasSeo;
 *
 *   protected $metaTitleFrom = 'name';
 *   protected $metaDescriptionFrom = 'description';
 * }
 * ```
 *
 * Add `meta_title_from` and `meta_description_from` to migration
 *
 * ```php
 * Schema::create('your_model_table', function (Blueprint $table) {
 *     $table->string('meta_title')->nullable();
 *     $table->string('meta_description')->nullable();
 * });
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

        $this->appends[] = 'seo';
    }

    public static function bootHasSeo()
    {
        static::creating(function (Model $model) {
            if (empty($model->meta_title)) {
                $model->meta_title = $model->seoFormat($model->{$model->getMetaTitleFrom()});
            }

            if (empty($model->meta_description)) {
                $model->meta_description = $model->seoFormat($model->{$model->getMetaDescriptionFrom()});
            }
        });

        static::saving(function (Model $model) {
            if ($model->isDirty('meta_title')) {
                $model->meta_title = $model->seoFormat($model->meta_title);
            }

            if ($model->isDirty('meta_description')) {
                $model->meta_description = $model->seoFormat($model->meta_description);
            }
        });
    }

    public function getMetaTitleFrom(): string
    {
        return $this->meta_title_from ?? $this->metaTitleFrom ?? $this->defaultMetaTitleFrom;
    }

    public function getMetaDescriptionFrom(): string
    {
        return $this->meta_description_from ?? $this->metaDescriptionFrom ?? $this->defaultMetaDescriptionFrom;
    }

    /**
     * @return array<string, string>
     */
    public function getSeoAttribute(): array
    {
        return [
            'title' => $this->meta_title,
            'description' => $this->meta_description,
        ];
    }

    private function seoFormat(?string $string = null, int $limit = 250): ?string
    {
        if ($string) {
            $string = strip_tags($string);
        }

        if ($string && Str::length($string) > $limit) {
            return Str::limit($string, $limit, '...');
        }

        return $string;
    }
}
