<?php

namespace Kiwilan\Steward\Traits;

trait HasTemplate
{
    protected $default_template_column = 'template';

    protected $default_content_column = 'content';

    public function initializeHasTemplate()
    {
        $this->fillable[] = $this->getTemplateColumn();
        $this->fillable[] = $this->getContentColumn();

        if ($enum = \Kiwilan\Steward\StewardConfig::templateEnum()) {
            $this->casts[$this->getTemplateColumn()] = $enum;
        }
        $this->casts[$this->getContentColumn()] = 'array';

        $this->appends[] = 'template_data';
    }

    public function getTemplateColumn(): string
    {
        return $this->template_column ?? $this->default_template_column;
    }

    /**
     * Get `content` field.
     */
    public function getContentColumn(): string
    {
        return $this->content_column ?? $this->default_content_column;
    }

    /**
     * Transform `template` into response.
     */
    public function getTemplateDataAttribute(): array
    {
        $raw_data = $this->{$this->getContentColumn()};

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
        if (is_array($array) && 1 === count($array) && array_key_exists(0, $array)) {
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

    /**
     * Set media if current value is `media`.
     */
    private function setMedia(mixed $value = null): mixed
    {
        $extensions = \Kiwilan\Steward\StewardConfig::mediableExtensions();

        if (! is_array($extensions)) {
            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'];
        }

        if (is_string($value) && str_contains($value, '.')) {
            foreach ($extensions as $extension) {
                if (str_contains($value, ".{$extension}")) {
                    return config('app.url')."/storage/{$value}";
                }
            }
        }

        return $value;
    }
}
