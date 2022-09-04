<?php

namespace Kiwilan\Steward\Traits;

trait Mediable
{
    protected array $mediable = [];

    // public function getMediableAttribute(): string
    // {
    //     return $this->slug_with ?? $this->default_slug_with;
    // }

    public function getMediable(?string $field = 'media', bool $get_path = false): ?string
    {
        if ($field) {
            $path = $get_path ? $field : $this->{$field};

            return config('app.url')."/storage/{$path}";
        }

        return null;
    }
}
