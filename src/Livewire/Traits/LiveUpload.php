<?php

namespace Kiwilan\Steward\Livewire\Traits;

use Illuminate\Support\Facades\File;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * `Livewire\Component` trait to handle uploads.
 *
 * ```
 * $images = [
 *   'profile_background' => $this->profile_background,
 *   'gallery' => $this->gallery,
 * ];
 *
 * $this->uploadConfig($images);
 * $this->validator();
 * $this->uploading($images);
 * ```
 */
trait LiveUpload
{
    public bool $uploading = false;

    public bool $saved = false;

    public ?array $toValidate = [];

    public ?array $multipleCurrent = [];

    public function initializeLiveUpload()
    {
        $this->listeners[] = 'deleteAllUpload';
        $this->listeners[] = 'cancelUpload';
    }

    /**
     * Handle upload before validation.
     */
    protected function uploadConfig(array $fields)
    {
        foreach ($fields as $field => $value) {
            $this->toValidate[$field] = null;

            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $type = gettype($v);

                    if ($type === 'string') {
                        $this->toValidate[$field][$k] = $v;
                        $this->{$field}[$k] = null;
                    }
                }
                $this->{$field} = array_filter($this->{$field});
            } else {
                $type = gettype($value);

                if ($type === 'string') {
                    $this->toValidate[$field] = $value;
                    $this->{$field} = null;
                }
            }
        }
    }

    /**
     * Handle upload.
     *
     * @param  array<string, mixed>  $fields
     */
    protected function uploading(array $fields)
    {
        foreach ($fields as $field => $value) {
            $this->saveToModel($field);
        }
    }

    /**
     * Save upload to model.
     */
    private function saveToModel(string $field, int $size = 2048, string $disk = 'upload'): void
    {
        if (! $this->{$field}) {
            return;
        }

        if (! is_array($this->{$field})) {
            $value = $this->setFile($this->{$field}, $size, $disk);

            $this->model->update([
                $field => $value,
            ]);

            return;
        }

        $files = [];

        foreach ($this->{$field} as $file) {
            $value = $this->setFile($file, $size, $disk);

            if ($value) {
                $files[] = $value;
            }
        }

        if (! empty($this->multipleCurrent)) {
            $files = [...$this->multipleCurrent, ...$files];
        }

        foreach ($files as $key => $value) {
            if (is_array($value)) {
                unset($files[$key]);
            }
        }

        if (! $this->isMultidimensional($files)) {
            $files = array_filter($files); // Remove empty values.
            $files = array_unique($files); // Remove duplicate values.
            // $files = array_values($files); // Re-index array.
        }

        $this->model->update([
            $field => $files,
        ]);
    }

    protected function isMultidimensional(mixed $array): bool
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    public function cancelUpload(string $name): void
    {
        // TODO
    }

    /**
     * Delete existing upload.
     *
     * @param  string  $field  The field name, like `avatar`.
     * @param  string  $name  The file name, like `my-avatar.jpg`.
     */
    public function deleteUpload(string $field, string $name): void
    {
        // Check if model field is an array.
        if (! is_array($this->{$field})) {
            $this->deleteToModel($field);

            return;
        }

        if (! $this->{$field}) {
            return;
        }

        $index = null;
        $current = null;

        // Search the file name into array.
        foreach ($this->{$field} as $filename) {
            if (is_string($filename) && str_contains($filename, $name)) {
                $current = $filename;
            }
        }

        if ($current) {
            // Get the index of file name into array.
            $index = array_search($current, $this->{$field});
        }

        $this->deleteToModel($field, $index);
    }

    public function deleteAllUpload(string $field)
    {
        $this->deleteToModel($field, all: true);
    }

    /**
     * Delete existing upload in model.
     */
    protected function deleteToModel(string $field, string|int|null $index = null, bool $all = false): void
    {
        $isArray = is_array($this->{$field});
        $current = $this->{$field} ?? null;

        if ($isArray) {
            if (array_key_exists($index, $current)) {
                $fileToDelete = $current[$index];
                unset($current[$index]);

                $pathFileToDelete = storage_path("app/public/{$fileToDelete}");

                if (File::exists($pathFileToDelete)) {
                    File::delete($pathFileToDelete);
                }
            }
            $current = array_values($current);

            if ($all) {
                $current = null;
            }
        } else {
            $current = null;
        }

        $this->model->update([
            $field => $current,
        ]);

        $this->{$field} = $current;
        $this->multipleCurrent = $this->model->{$field} ?? [];
    }

    private function setFile(TemporaryUploadedFile|string|null $file, int $size, string $disk): ?string
    {
        if (! $file) {
            return null;
        }

        if (gettype($file) === 'string') {
            return $file;
        }

        $sizeMax = $size * 1024;

        if ($file->getSize() > $sizeMax) {
            return null;
        }

        $ext = $file->extension();
        $name = uniqid().".{$ext}";
        $file->storePubliclyAs($disk, $name, 'public');

        return "{$disk}/{$name}";
    }
}
