<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\BuilderEnum;
use stdClass;

trait HasBuilder
{
    protected $default_builder_column = 'content';

    public function initializeHasBuilder()
    {
        $this->fillable[] = $this->getBuilderColumn();

        // $this->casts['builder'] = BuilderEnum::class;
        $this->casts[$this->getBuilderColumn()] = 'array';
    }

    public function getBuilderColumn(): string
    {
        return $this->builder_column ?? $this->default_builder_column;
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
        $extensions = config('steward.mediable.extensions');

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
