<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Model;

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

    public function getBodyColumn(): string
    {
        return $this->body_column ?? $this->default_body_column;
    }

    public function initializeHasBody()
    {
        $this->fillable[] = 'body';
    }

    public static function bootHasBody()
    {
        static::creating(function (Model $model) {
            if ($model->isDirty($model->getBodyColumn())) {
                $model->{$model->getBodyColumn()} = self::parseHtml($model);
            }
        });
    }

    private static function parseHtml(Model $model): string
    {
        $content = $model->{$model->getBodyColumn()};
        $isHtml = $content != strip_tags($content) ? true : false;

        if (! $isHtml) {
            return $content;
        }

        return preg_replace('/<img(.*?)>/', '<img$1 loading="lazy">', $content);
    }
}
