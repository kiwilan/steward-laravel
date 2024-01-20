<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Enums\SpatieMediaMethodEnum;

/**
 * SpatieMedia to manage media files with `spatie/laravel-medialibrary`.
 */
class SpatieMedia
{
    protected function __construct(
        protected Model $model,
        protected mixed $data = null,
        protected ?string $name = null,
        protected string $disk = 'public',
        protected bool $color = false,
        protected string $collection = 'default',
        protected ?string $extension = null,
        protected ?SpatieMediaMethodEnum $method = null,
        protected array $allowedMimeTypes = []
    ) {
    }

    /**
     * Add a media file to the model.
     */
    public static function make(Model $model): self
    {
        if (! \Composer\InstalledVersions::isInstalled('spatie/laravel-medialibrary')) {
            throw new \Exception('Package spatie/laravel-medialibrary not installed, see https://spatie.be/docs/laravel-medialibrary');
        }

        return new self($model);
    }

    public function addMedia(string|\Symfony\Component\HttpFoundation\File\UploadedFile $file): self
    {
        $this->method = SpatieMediaMethodEnum::addMedia;
        $this->data = $file;

        return $this;
    }

    public function addMediaFromBase64(string $base64): self
    {
        $this->method = SpatieMediaMethodEnum::addMediaFromBase64;
        $this->data = $base64;

        return $this;
    }

    public function addMediaFromDisk(string $path): self
    {
        $this->method = SpatieMediaMethodEnum::addMediaFromDisk;
        $this->data = $path;

        return $this;
    }

    public function addMediaFromRequest(string $key): self
    {
        $this->method = SpatieMediaMethodEnum::addMediaFromRequest;
        $this->data = $key;

        return $this;
    }

    /**
     * @param  resource  $resource
     */
    public function addMediaFromStream($resource): self
    {
        $this->method = SpatieMediaMethodEnum::addMediaFromStream;
        $this->data = $resource;

        return $this;
    }

    public function addMediaFromString(string $data): self
    {
        $this->method = SpatieMediaMethodEnum::addMediaFromString;
        $this->data = $data;

        return $this;
    }

    public function addMediaFromUrl(string $url): self
    {
        $this->method = SpatieMediaMethodEnum::addMediaFromUrl;
        $this->data = $url;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function extension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * For `addMediaFromBase64` and `addMediaFromUrl`.
     */
    public function allowedMimeTypes(array $allowedMimeTypes): self
    {
        $this->allowedMimeTypes = $allowedMimeTypes;

        return $this;
    }

    public function collection(string $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    public function color(): self
    {
        $this->color = true;

        return $this;
    }

    public function save(): void
    {
        $spatie = $this->model->{$this->method->value}($this->data);

        if ($this->method === SpatieMediaMethodEnum::addMediaFromBase64 || $this->method === SpatieMediaMethodEnum::addMediaFromUrl) {
            $spatie = $this->model->{$this->method->value}($this->data, $this->allowedMimeTypes);
        }

        if (! $this->name) {
            $this->name = uniqid();
        }

        if (! $this->extension) {
            $this->extension = 'jpg';
        }

        if (! $this->collection) {
            $this->collection = '';
        }

        if (! $this->disk) {
            $this->disk = '';
        }

        /** @var \Spatie\MediaLibrary\MediaCollections\FileAdder $spatie */
        $media = $spatie->usingName($this->name)
            ->usingFileName("{$this->name}.{$this->extension}")
            ->toMediaCollection($this->collection, $this->disk);

        if ($this->color) {
            $this->model->refresh();
            $color = Picture::color($media->getPath());
            $media->setCustomProperty('color', $color);
            $media->save();
        }
    }

    public static function getFullUrl(Model $model, string $collection, ?string $conversion = ''): string
    {
        $defaultUrl = config('app.url').'/vendor/vendor/images/no-cover.webp';

        if (! method_exists($model, 'getFirstMediaUrl')) {
            return $defaultUrl;
        }

        try {
            $cover = $model->getFirstMediaUrl($collection, $conversion);

            if ($cover) {
                return $cover;
            }
        } catch (\Throwable $th) {
        }

        return $defaultUrl;
    }
}
