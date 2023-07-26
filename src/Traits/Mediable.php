<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Facades\File;
use Kiwilan\Steward\StewardConfig;
use Kiwilan\Steward\Utils\MetaClass;
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
            return \Kiwilan\Steward\StewardConfig::mediableDefault();
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

    /**
     * Save mediable to `field` attribute, auto convert from `steard.mediable.format`.
     */
    public function mediableSave(
        string $media,
        string $field = 'picture',
        string $upload = null,
        bool $convert = true,
        bool $deleteOriginal = true
    ): void {
        $ext = pathinfo($media, PATHINFO_EXTENSION);
        $hasExt = ! empty($ext);
        $path = null;
        $content = null;
        $isPath = false;

        if ($hasExt) {
            $path = $media;

            if (! file_exists($path)) {
                // throw new \Exception("File {$path} not found");
                error_log("File {$path} not found");

                return;
            }

            $content = file_get_contents($path);
            $isPath = true;
        } else {
            $content = $media;
        }

        $meta = MetaClass::make(get_class($this));
        $directory = $meta->classSlugPlural();

        if ($upload) {
            $directory = $upload;
        }

        if ($isPath) {
            $basename = basename($path);
        } else {
            $basename = uniqid().'.'.$ext;
        }

        $basePath = public_path("storage/{$directory}");
        $fullPath = "{$basePath}/{$basename}";

        if (file_exists($fullPath)) {
            $name = uniqid().'-'.$basename;
            $fullPath = "{$basePath}/{$name}";
        }

        $dirname = dirname($fullPath);

        if (! file_exists($dirname)) {
            mkdir($dirname, 0775, true);
        }

        $extConvert = StewardConfig::mediableFormat();

        if ($convert && $ext !== StewardConfig::mediableFormat()) {
            $path = $this->convertTo(file_get_contents($path));
            $fullPath = str_replace($ext, $extConvert, $fullPath);
        }

        File::copy($path, $fullPath);
        $relativePath = explode('storage/', $fullPath);

        if ($deleteOriginal) {
            File::delete($path);
        }

        $this->{$field} = $relativePath[1] ?? null;
        $this->save();
    }

    private function convertTo(string $image): string|false
    {
        $image = imagecreatefromstring($image);

        if (! $image) {
            return false;
        }

        $path = storage_path('app/cache');

        if (! file_exists($path)) {
            mkdir($path, 0775, true);
        }

        $name = uniqid().'.'.StewardConfig::mediableFormat();
        $fullPath = "{$path}/{$name}";
        $result = imagewebp($image, $fullPath);

        imagedestroy($image);

        return $fullPath;
    }
}
