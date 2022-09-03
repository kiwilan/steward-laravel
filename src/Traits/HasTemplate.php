<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\TemplateEnum;

trait HasTemplate
{
    protected $default_template_column = 'content';

    public function initializeHasTemplate()
    {
        $this->fillable[] = 'template';

        $this->casts['template'] = TemplateEnum::class;
    }

    public function getTemplateColumn(): string
    {
        return $this->template_column ?? $this->default_template_column;
    }

    public function getBuilderAttribute(): ?array
    {
        if (is_array($this->{$this->getTemplateColumn()})) {
            $data = [];
            foreach ($this->{$this->getTemplateColumn()} as $builder) {
                $this->transformData($builder, $data);
            }

            return $this->setMedia($data);
        }

        return [];
    }

    private function transformData(mixed $builder, array &$data)
    {
        if (is_array($builder) && array_key_exists('data', $builder)) {
            foreach ($builder['data'] as $key => $value) {
                $is_list = false;
                if ('list' === $key) {
                    $is_list = true;
                }

                if (! $is_list) {
                    $data[$key] = $value;
                }

                $this->transformData($value, $data);
            }
        }
    }

    private function setMedia(?array $data = [])
    {
        $extensions = config('steward.media_extensions');
        foreach ($data as $key => $value) {
            foreach ($extensions as $extension) {
                if (str_contains($value, $extension)) {
                    $media_url = config('app.url')."/storage/{$value}";
                    $data[$key] = $media_url;
                }
            }
        }

        return $data;
    }
}
