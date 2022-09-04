<?php

namespace Kiwilan\Steward\Traits;

use stdClass;

trait Mediable
{
    protected array $default_mediables = ['picture'];

    public function getMediablesListAttribute(): array
    {
        return $this->mediables ?? $this->default_mediables;
    }

    /**
     * @return object
     */
    public function getMediableAttribute()
    {
        $mediable = new stdClass();
        foreach ($this->getMediablesListAttribute() as $field) {
            $mediable->{$field} = $this->mediable($field);
        }

        return $mediable;
    }

    public function mediable(?string $field = 'picture', bool $get_path = false): ?string
    {
        if ($field) {
            if (null === $this->{$field}) {
                return null;
            }
            $path = $get_path ? $field : $this->{$field};

            return config('app.url')."/storage/{$path}";
        }

        return null;
    }
}
