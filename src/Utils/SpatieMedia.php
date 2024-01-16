<?php

namespace Kiwilan\Steward\Utils;

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
class SpatieMedia
{
    protected function __construct(
        protected Model $model,
        protected string $name,
        protected mixed $disk,
        protected ?string $collection = null,
        protected ?string $extension = null,
        protected ?SpatieMediaMethodEnum $method = null,
    ) {
    }

    /**
     * Add a media file to the model.
     *
     * @param  ?string  $disk - Default is `disks.public.root`.
     * @param  ?string  $collection - Default is `library`, which is a subfolder of `disk`.
     * @param  ?string  $extension - Default is `webp`, can be overridden by `config('bookshelves.cover_extension')`.
     * @param  ?SpatieMediaMethodEnum  $method - Default is `addMediaFromBase64`.
     */
    public static function make(
        Model $model,
        string $name,
        ?string $disk = null,
        ?string $collection = null,
        ?string $extension = null,
        ?SpatieMediaMethodEnum $method = null
    ): self {
        if (! $disk) {
            $disk = config('filesystems.disks.public.root');

            if (! file_exists($disk)) {
                mkdir($disk, 0775, true);
            }
        }

        if (! $collection) {
            $collection = 'library';
        }

        if (! $extension) {
            $extension = config('bookshelves.cover_extension');
        }

        if (! $method) {
            $method = SpatieMediaMethodEnum::addMediaFromBase64;
        }

        $path = $disk.'/'.$collection;

        if (! file_exists($path)) {
            mkdir($path, 0775, true);
        }

        return new self($model, $name, $disk, $collection, $extension, $method);
    }

    public function addMedia(...$data): self
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
            $color = Picture::color($image);
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
