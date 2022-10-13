<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\BuilderEnum;

trait HasTemplate
{
    protected $default_template_column = 'content';

    public function initializeHasTemplate()
    {
        $this->fillable[] = 'template';

        // $this->casts['builder'] = BuilderEnum::class;
    }

    public function getTemplateColumn(): string
    {
        return $this->template_column ?? $this->default_template_column;
    }

    public function getTemplateAttribute(): ?array
    {
        $raw_data = $this->{$this->getTemplateColumn()};
        if (! is_array($raw_data)) {
            return [];
        }

        $data = [];
        $raw_data = $this->checkArrayNested($raw_data);

        foreach ($raw_data as $name => $raw_template) {
            $raw_template = $this->checkArrayNested($raw_template);
            $template = $this->transformData($raw_template);
            $data[$name] = $template;
        }

        return $data;
    }

    private function checkArrayNested(mixed $array): mixed
    {
        if (is_array($array) && 1 === count($array) && array_key_exists(0, $array)) {
            return $array[0];
        }

        return $array;
    }

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
        $extensions = config('steward.mediable.extensions');
        if (! is_array($extensions)) {
            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'];
        }

        foreach ($extensions as $extension) {
            if (is_string($value) && str_contains($value, $extension)) {
                $media_url = config('app.url')."/storage/{$value}";
                $value = $media_url;
            }
        }

        return $value;
    }
}
