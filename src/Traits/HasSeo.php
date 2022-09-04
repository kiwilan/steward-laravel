<?php

namespace Kiwilan\Steward\Traits;

trait HasSeo
{
    protected $default_meta_title_from = 'name';

    protected $default_meta_description_from = 'description';

    public function initializeHasSEO()
    {
        $this->fillable[] = 'meta_title';
        $this->fillable[] = 'meta_description';
    }

    public static function bootHasSEO()
    {
        static::creating(function ($model) {
            if (empty($model->meta_title)) {
                $model->meta_title = $this->limitStringSize($model->{$model->getMetaTitle()});
            }
            if (empty($model->meta_description)) {
                $model->meta_description = $this->limitStringSize($model->{$model->getMetaDescription()});
            }
        });
    }

    public function getMetaTitle(): string
    {
        return $this->meta_title_from ?? $this->default_meta_title_from;
    }

    public function getMetaDescription(): string
    {
        return $this->meta_description_from ?? $this->default_meta_description_from;
    }

    private function limitStringSize(string $string, int $limit = 250): string
    {
        if (strlen($string) > $limit) {
            return substr($string, 0, $limit) . '...';
        }

        return $string;
    }

    public function getSeoAttribute(): array
    {
        return [
            'title' => $this->meta_title,
            'description' => $this->meta_description,
        ];
    }
}
