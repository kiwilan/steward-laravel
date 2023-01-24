<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Livewire\TemporaryUploadedFile;

trait Uploadable
{
    use Notifiable;

    public bool $uploading = false;

    public bool $saved = false;

    public function initializeUploadable()
    {
    }

    public function uploadableClearMedias(string $params)
    {
        $this->clearUpload([
            'property' => $params,
            'key' => 'all',
        ]);
    }

    public function clearUpload(array ...$params)
    {
        $params = $params[0] ?? [];
        $property = $params['property'] ?? null;
        $index = $params['key'] ?? null;

        $is_array = is_array($this->{$property});
        $value = $this->{$property} ?? null;

        if ($is_array) {
            if ('all' === $index) {
                $value = [];
            } else {
                unset($value[$index]);
            }
        } else {
            $value = null;
        }

        if ($is_array) {
            $value = array_values($value);
        }
        $this->user->collector->update([
            $property => $value,
        ]);

        $this->{$property} = $value;
        $this->notify();
    }

    protected function getListeners()
    {
        return ['uploadableClearMedias' => 'uploadableClearMedias'];
    }

    private function upload(string $property, int $size = 2048, string $disk = 'collectors'): bool
    {
        $success = false;
        $value = null;

        if (
            $this->isInstanceOf($this->{$property}, TemporaryUploadedFile::class, false)
            || $this->isUploadArray($property, TemporaryUploadedFile::class)
        ) {
            $current = $this->{$property};

            if (is_array($current)) {
                $value = [];

                foreach ($current as $file) {
                    $value[] = $this->setFile($file, $size, $disk);
                }
            } else {
                $value = $this->setFile($this->{$property}, $size, $disk);
            }
        }

        if ($value) {
            if (is_array($this->{$property})) {
                $field = $this->user->collector->{$property} ?? [];
                $value = array_merge($field, $value);
            }

            $this->user->collector->update([
                $property => $value,
            ]);
        }

        return $success;
    }

    private function setFile(TemporaryUploadedFile $file, int $size, string $disk): ?string
    {
        $sizeMax = $size * 1024;

        if ($file->getSize() > $sizeMax) {
            return null;
        }

        $ext = $file->extension();
        $name = uniqid().".{$ext}";
        $file->storePubliclyAs($disk, $name, 'public');

        return "{$disk}/{$name}";
    }

    private function isInstanceOf(mixed $item, mixed $type, bool $primary): bool
    {
        if ($primary) {
            return gettype($item) === $type;
        }

        return $item instanceof $type;
    }

    private function isUploadArray(string $property, mixed $type, bool $primary = false): bool
    {
        if (! is_array($this->{$property})) {
            return false;
        }

        $instances = array_map(
            fn ($item) => $this->isInstanceOf($item, $type, $primary),
            $this->{$property}
        );

        $success = true;

        foreach ($instances as $instance) {
            if (! $instance) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param  array<string, string>  $fields
     */
    private function prepareMedias(array $fields)
    {
        foreach ($fields as $key => $value) {
            if ($this->isInstanceOf($this->{$key}, 'string', true)) {
                $this->{$key} = null;
            }

            if ($this->isUploadArray($key, 'string', true)) {
                $this->{$key} = [];
            }
        }
    }

    /**
     * @param  array<string, string>  $fields
     */
    private function saveMedias(array $fields)
    {
        foreach ($fields as $property => $value) {
            $this->upload($property);
        }
        $this->saved = ! $this->saved;
    }
}
