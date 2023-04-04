<?php

namespace Kiwilan\Steward\Traits;

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
}
