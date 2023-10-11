<?php

namespace Kiwilan\Steward\Services\Image;

use GdImage;
use Kiwilan\Steward\Services\PictureService;

/**
 * PHP Simple Color Thief
 * ======================
 * Detect the Dominant Color used in an Image
 * Copyright 2019 Igor Gaffling.
 *
 * @param  mixed  $image
 * @param  string  $default
 */
class ColorThief
{
    protected function __construct(
        protected mixed $image,
        protected string $default = 'fff',
        protected bool $useDefault = false,
        protected bool $isImage = false,
        protected bool $isTransparent = false,
        protected int $type = 0,
        protected GdImage|false|null $handle = null,
        protected string $color = 'fff',
    ) {
    }

    public static function make(mixed $image, string $default = 'fff'): self
    {
        $self = new self($image);

        $self->default = $default;
        $self->isImage = $self->checkIfIsImage();

        if (! $self->isImage) {
            $self->color = $self->default;

            return $self;
        }

        $self->type = $self->setType();

        $self->handle = match ($self->type) {
            IMAGETYPE_GIF => $self->handleGif(),
            IMAGETYPE_JPEG => $self->handleJpeg(),
            IMAGETYPE_PNG => $self->handlePng(),
            IMAGETYPE_WEBP => $self->handleWebp(),
            IMAGETYPE_AVIF => $self->handleAvif(),
            default => null,
        };

        $self->isTransparent = $self->isTransparentType();

        if ($self->useDefault) {
            $self->color = $self->default;
        } else {
            $self->color = $self->dominantColor();
        }

        return $self;
    }

    public function color(): string
    {
        return $this->color;
    }

    private function checkIfIsImage(): bool
    {
        return @exif_imagetype($this->image) !== false;
    }

    private function setType(): int
    {
        try {
            $type = getimagesize($this->image)[2];
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $type ?? 0;
    }

    private function isTransparentType(): bool
    {
        if (! $this->handle) {
            return false;
        }

        $isTransparent = (imagecolorat($this->handle, 0, 0) >> 24) & 0x7F === 127;
        $isTransparent = $isTransparent === 1;

        if ($isTransparent) {
            $this->useDefault = true;
        }

        return $isTransparent;
    }

    private function handleGif(): mixed
    {
        $handle = imagecreatefromgif($this->image);

        // IF IMAGE IS TRANSPARENT (alpha=127) RETURN fff FOR WHITE
        if (imagecolorsforindex($handle, imagecolorstotal($handle) - 1)['alpha'] == 127) {
            $this->useDefault = true;

            return null;
        }

        return $handle;
    }

    private function handleJpeg(): mixed
    {
        return imagecreatefromjpeg($this->image);
    }

    private function handlePng(): mixed
    {
        return imagecreatefrompng($this->image);
    }

    private function handleWebp(): mixed
    {
        return imagecreatefromwebp($this->image);
    }

    private function handleAvif(): mixed
    {
        return imagecreatefromavif($this->image);
    }

    private function dominantColor(): string
    {
        if (! $this->handle) {
            return $this->default;
        }

        $newImg = imagecreatetruecolor(1, 1); // FIND DOMINANT COLOR
        imagecopyresampled($newImg, $this->handle, 0, 0, 0, 0, 1, 1, imagesx($this->handle), imagesy($this->handle));
        $hexaColor = dechex(imagecolorat($newImg, 0, 0));

        if (! PictureService::isHex($hexaColor)) {
            return $this->default;
        }

        return $hexaColor;
    }
}
