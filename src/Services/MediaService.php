<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Enums\SpatieMediaMethodEnum;
use UnitEnum;

/**
 * MediaService to manage media files.
 *
 * @property Model $model
 * @property string $name
 * @property string|UnitEnum $disk
 * @property ?string $collection
 * @property ?string $extension
 * @property ?SpatieMediaMethodEnum $method
 */
class MediaService
{
    public function __construct(
        public Model $model,
        public string $name,
        public mixed $disk,
        public ?string $collection = null,
        public ?string $extension = null,
        public ?SpatieMediaMethodEnum $method = null,
    ) {
    }

    /**
     * Add a media file to the model.
     *
     * @param  Model  $model
     * @param  string  $name
     * @param  string|UnitEnum  $disk
     * @param  null|string  $collection
     * @param  null|string  $extension
     * @param  null|SpatieMediaMethodEnum  $method
     */
    public static function make(
        Model $model,
        string $name,
        mixed $disk = 'media',
        ?string $collection = null,
        ?string $extension = null,
        ?SpatieMediaMethodEnum $method = null
    ): self {
        if ($disk instanceof UnitEnum && property_exists($disk, 'value')) {
            $disk = $disk->value;
        }
        if (! $collection) {
            $collection = $disk;
        }
        if (! $extension) {
            $extension = config('bookshelves.cover_extension');
        }
        if (! $method) {
            $method = SpatieMediaMethodEnum::addMediaFromBase64;
        }

        return new MediaService($model, $name, $disk, $collection, $extension, $method);
    }

    public function setMedia(string|null $data): self
    {
        if ($data) {
            $this->model->{$this->method->value}($data)
                ->setName($this->name)
                ->setFileName($this->name.'.'.$this->extension)
                ->toMediaCollection($this->collection, $this->disk)
            ;
            $this->model->refresh();
        }

        return $this;
    }

    public function setColor(): self
    {
        // @phpstan-ignore-next-line
        $image = $this->model->getFirstMediaPath($this->collection);

        if ($image) {
            $color = ImageService::colorThief($image);
            // @phpstan-ignore-next-line
            $media = $this->model->getFirstMedia($this->collection);
            $media->setCustomProperty('color', $color);
            $media->save();
        }

        return $this;
    }

    public static function getFullUrl(Model $model, string $collection, ?string $conversion = ''): string
    {
        $cover = null;

        try {
            // @phpstan-ignore-next-line
            $cover = $model->getFirstMediaUrl($collection, $conversion);
        } catch (\Throwable $th) {
        }

        return $cover ? $cover : config('app.url').'/vendor/vendor/images/no-cover.webp';
    }
}
