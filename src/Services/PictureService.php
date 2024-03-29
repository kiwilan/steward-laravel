<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Utils\Picture\ColorThief;

/**
 * @deprecated
 */
class PictureService
{
    /**
     * Detecte dominant color of an image.
     */
    public static function colorThief(mixed $image, string $default = 'fff'): string
    {
        return ColorThief::make($image, $default)->color();
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
