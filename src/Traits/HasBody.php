<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;

/**
 * Override the default body column with `$body_column` attribute.
 *
 * ```php
 * protected $body_column = 'content';
 * ```
 */
trait HasBody
{
    protected $default_body_column = 'body';

    public function getBody(): string
    {
        return $this->body_column ?? $this->default_body_column;
    }

    public function initializeHasBody()
    {
        $this->fillable[] = 'body';
    }

    public static function bootHasBody()
    {
        static::created(function ($model) {
            if ($model->isHTML($model->body)) {
                $content = $model->getBody();
                $content = preg_replace('/<img(.*?)>/', '<img$1 loading="lazy">', $content);
                $model->update(['body' => $content]);
            }
        });

        static::updated(function ($model) {
            if ($model->isHTML($model->body)) {
                $content = $model->getBody();
                $content = preg_replace('/<img(.*?)>/', '<img$1 loading="lazy">', $content);
                $model->update(['body' => $content]);
            }
        });
    }

    private function isHTML(string $string)
    {
        return $string != strip_tags($string) ? true : false;
    }

    // public function getMetaTitle(): string
    // {
    //     $default = $this->default_meta_title_from;

    //     if (null === $default) {
    //         $default = 'title';
    //     }

    //     return $this->meta_title_from ?? $default;
    // }

    // /**
    //  * @return array<string, string>
    //  */
    // public function getSeoAttribute(): array
    // {
    //     return [
    //         'title' => $this->getMetaTitle(),
    //         'description' => $this->getMetaDescription(),
    //     ];
    // }

    // private function limitStringSize(?string $string = null, int $limit = 250): ?string
    // {
    //     if ($string && Str::length($string) > $limit) {
    //         return Str::limit($string, $limit, '...');
    //     }

    //     return $string;
    // }
}
