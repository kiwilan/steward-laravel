<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;

trait HasSeo
{
    protected $default_meta_title_from = 'name';

    protected $default_meta_description_from = 'description';

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
        $default = $this->default_meta_title_from;

        if (null === $default) {
            $default = 'title';
        }

        return $this->meta_title_from ?? $default;
    }

    public function getMetaDescription(): string
    {
        return $this->meta_description_from ?? $this->default_meta_description_from;
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

    private function limitStringSize(?string $string = null, int $limit = 250): ?string
    {
        if ($string && Str::length($string) > $limit) {
            return Str::limit($string, $limit, '...');
        }

        return $string;
    }
}
