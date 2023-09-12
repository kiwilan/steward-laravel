<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\BuilderEnum;

/**
 * Trait HasBuilder
 *
 * Add this trait to your model to use the builder.
 *
 * - Default column is `content`, you can change it by adding `$builderColumn` property to your model.
 * - You can use `builder_data` attribute to get the builder data.
 *
 * ```php
 * class Post extends Model
 * {
 *    use HasBuilder;
 *
 *   protected $builderColumn = 'content_column';
 * }
 * ```
 *
 * Into `Filament` resource, you can use this snippet to get the builder data:
 *
 * ```php
 * FilamentBuilder::make(WordpressBuilder::class)->get(),
 * ```
 */
trait HasBuilder
{
    protected $defaultBuilderColumn = 'content';

    public function initializeHasBuilder()
    {
        $this->fillable[] = $this->getBuilderColumn();

        // $this->casts['builder'] = BuilderEnum::class;
        $this->casts[$this->getBuilderColumn()] = 'array';
    }

    public function getBuilderColumn(): string
    {
        return $this->builder_column ?? $this->builderColumn ?? $this->defaultBuilderColumn;
    }

    /**
     * Transform `template` into response.
     */
    public function getBuilderDataAttribute(): array
    {
        $raw_data = $this->{$this->getBuilderColumn()};

        if (! is_array($raw_data)) {
            return [];
        }

        $data = [];
        $raw_data = $this->checkArrayNested($raw_data);

        foreach ($raw_data as $name => $raw_template) {
            $raw_template = $this->checkArrayNested($raw_template);
            $template = $this->transformData($raw_template);

            if (is_array($template)) {
                $data[$name] = array_reverse($template);
            } else {
                // only one value
                $data[$name] = $raw_data;
            }
        }

        return $data;
    }

    /**
     * If `array` has just one key, transform in `array`.
     */
    private function checkArrayNested(mixed $array): mixed
    {
        if (is_array($array) && count($array) === 1 && array_key_exists(0, $array)) {
            return $array[0];
        }

        return $array;
    }

    /**
     * Transform template parts into array.
     */
    private function transformData(mixed $template)
    {
        if (! is_array($template)) {
            return $template;
        }

        $data = [];

        foreach ($template as $name => $value) {
            $is_subarray = false;

            if (is_array($value)) {
                $is_subarray = true;
            }

            if ($is_subarray) {
                $data[$name] = $this->transformData($value);
            } else {
                $data[$name] = $this->setMedia($value);
            }
        }

        return $data;
    }

    private function setMedia(mixed $value = null)
    {
        $extensions = \Kiwilan\Steward\StewardConfig::mediableExtensions();

        if (! is_array($extensions)) {
            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'];
        }

        foreach ($extensions as $extension) {
            if (str_contains($value, '/') && str_contains($value, $extension) && strlen($value) < 255) {
                $media_url = config('app.url')."/storage/{$value}";
                $value = $media_url;
            }
        }

        return $value;
    }

    protected static function bootHasBuilder()
    {
        static::saving(function ($model) {
            if ($model->isDirty($model->getBuilderColumn())) {
                $model->{$model->getBuilderColumn()} = $model->replaceAddLazyToImgTag($model->{$model->getBuilderColumn()});
            }
        });
    }

    private function replaceAddLazyToImgTag(string|array|null $content): mixed
    {
        if (! $content) {
            return '';
        }

        if (is_array($content)) {
            $data = [];

            foreach ($content as $name => $value) {
                $data[$name] = $this->replaceAddLazyToImgTag($value);
            }

            return $data;
        }

        return str_replace('<img', '<img loading="lazy"', $content);
    }
}
