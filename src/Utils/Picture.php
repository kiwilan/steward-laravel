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

    /**
     * Resize image to 800px height and create temp file.
     *
     * @return string Path to temp file.
     */
    public function resizeSpatie(string $content, int $newHeight = 800): string
    {
        $temp_path = storage_path('app/cache');
        $temp_name = uniqid().'.jpg';
        $temp_file = "{$temp_path}/{$temp_name}";

        $resize_name = explode('.', $temp_name);
        $resize_name = "{$resize_name[0]}_resize.{$resize_name[1]}";
        $resize_file = "{$temp_path}/{$resize_name}";

        file_put_contents($temp_file, $content);

        Image::load($temp_file)
            ->height($newHeight)
            ->save($resize_file)
        ;

        unlink($temp_file);

        return $resize_file;
    }

    public function resize(string $path, string $content, int $newHeight = 800): string
    {
        header('Content-Type: image/jpeg');

        if (base64_decode($content, true) !== false) {
            $content = base64_decode($content);
        }

        $stream = imagecreatefromstring($content);
        $width = imagesx($stream);
        $height = imagesy($stream);

        $newHeight = 800;
        $newWidth = intval($width / $height * $newHeight);

        $image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($image, $stream, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $temp_name = uniqid().'.jpg';
        $temp_file = "{$path}/{$temp_name}";

        imagejpeg($image, $temp_file, 80);

        return $temp_file;
    }
}
