<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\Steward\Utils\Picture\ColorThief;
use Spatie\Image\Image;

class Picture
{
    /**
     * Detecte dominant color of an image.
     */
    public static function color(mixed $image, string $default = 'fff', bool $addHashtag = true): string
    {
        $color = ColorThief::make($image, $default)->color();

        return $addHashtag ? "#{$color}" : $color;
    }

    public static function load(string $path): Image
    {
        return Image::load($path);
    }

    /**
     * Detect if an hexadecimal code is valid.
     */
    public static function isHex(string $hexadecimal): bool
    {
        $isHex = false;

        try {
            $isHex = @preg_match('/^[a-f0-9]{2,}$/i', $hexadecimal) && ! (strlen($hexadecimal) & 1);
        } catch (\Throwable $th) {
            // throw $th;
        }

        return $isHex;
    }
}
