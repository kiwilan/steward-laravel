<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\BuilderEnum;
use stdClass;

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

    public function getBuilderDataAttribute(): ?stdClass
    {
        $builder_obj = new stdClass();
        $raw_data = $this->{$this->getBuilderColumn()};

        if (! is_array($raw_data)) {
            return $builder_obj;
        }

        $data_builder = [];

        foreach ($raw_data as $raw_builder) {
            $this->transformData($raw_builder, $data_builder);
        }

        return json_decode(json_encode($data_builder, true));
    }

    private function transformData(mixed $builder)
    {
        if (! is_array($builder) && ! array_key_exists('data', $builder)) {
            return [];
        }

        $data = [];

        foreach ($builder as $name => $value) {
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
            if (str_contains($value, $extension)) {
                $media_url = config('app.url')."/storage/{$value}";
                $value = $media_url;
            }
        }

        return $value;
    }
}
