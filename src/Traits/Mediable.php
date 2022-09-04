<?php

namespace Kiwilan\Steward\Traits;

use ReflectionClass;

trait Mediable
{
    protected array $mediable = [];

    // public function getMediableAttribute(): string
    // {
    //     return $this->slug_with ?? $this->default_slug_with;
    // }

    public function initializeMediable()
    {
        $instance = new $this();
        $class = new ReflectionClass($instance);
        $static = $class->getName();

        $static::macro('concatenate', function (...$strings) {
            return implode('-', $strings);
        });
    }

    public function getMediable(?string $field = 'media', bool $get_path = false): ?string
    {
        if ($field) {
            $path = $get_path ? $field : $this->{$field};

            return config('app.url')."/storage/{$path}";
        }

        return null;
    }
}
