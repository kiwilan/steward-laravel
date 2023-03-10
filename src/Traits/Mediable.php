<?php

namespace Kiwilan\Steward\Traits;

use stdClass;

trait Mediable
{
    protected array $default_mediables = ['picture'];

    public function initializeMediable()
    {
        $this->appends[] = 'mediable';
    }

    /**
     * @return string[]
     */
    public function getMediablesListAttribute(): array
    {
        return property_exists($this, 'mediables') ? $this->mediables : $this->default_mediables; // @phpstan-ignore-line
    }

    public function getMediableAttribute(): object
    {
        $mediable = new stdClass();

        foreach ($this->getMediablesListAttribute() as $field) {
            $mediable->{$field} = $this->mediable($field);
        }

        return $mediable;
    }

    /**
     * @return string|string[]|null
     */
    public function mediable(string $field = 'picture', bool $usePath = false): string|array|null
    {
        if (null === $this->{$field}) {
            return config('steward.media.default') ? config('steward.media.default') : null;
        }
        $path = $usePath ? $field : $this->{$field};

        if (is_array($this->{$field})) {
            $list = [];

            foreach ($this->{$field} as $media) {
                $list[] = config('app.url')."/storage/{$media}";
            }

            return $list;
        }

        return config('app.url')."/storage/{$path}";
    }
}
