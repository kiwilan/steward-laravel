<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\BuilderEnum;
use stdClass;

trait HasBuilder
{
    protected $default_builder_column = 'content';

    public function initializeHasBuilder()
    {
        $this->fillable[] = 'builder';

        $this->casts['builder'] = BuilderEnum::class;
    }

    public function getBuilderColumn(): string
    {
        return $this->builder_column ?? $this->default_builder_column;
    }

    public function getBuilderAttribute(): ?stdClass
    {
        $builder_obj = new stdClass();
        if (is_array($this->{$this->getBuilderColumn()})) {
            $data_builder = [];
            foreach ($this->{$this->getBuilderColumn()} as $builder) {
                $this->transformData($builder, $data_builder);
            }

            return json_decode(json_encode($data_builder, true));
        }

        return $builder_obj;
    }

    private function transformData(mixed $builder, array &$data_builder)
    {
        if (! is_array($builder) && ! array_key_exists('data', $builder)) {
            return [];
        }
        $data = $builder['data'];
        $type = $builder['type'];

        foreach ($data as $key => $value) {
            $is_list = false;
            if ('list' === $key) {
                $is_list = true;
            }

            if (! $is_list) {
                $data_builder[$type][$key] = $this->setMedia($value);
            }

            // $this->transformData($value, $data);
        }
    }

    private function setMedia(mixed $value = null)
    {
        $extensions = config('steward.media.extensions');
        // foreach ($data as $key => $value) {
        foreach ($extensions as $extension) {
            if (str_contains($value, $extension)) {
                $media_url = config('app.url')."/storage/{$value}";
                $value = $media_url;
            }
        }
        // }

        return $value;
    }
}
