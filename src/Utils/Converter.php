<?php

namespace Kiwilan\Steward\Utils;

class Converter
{
    public static function prettyJson(mixed $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function saveAsJson(mixed $data, string $path, bool $pretty = false): void
    {
        if ($pretty) {
            $data = Converter::prettyJson($data);
        } else {
            $data = json_encode($data);
        }
        $dir = dirname($path);

        if (file_exists($path)) {
            unlink($path);
        } elseif (! file_exists($dir)) {
            mkdir($dir, recursive: true);
        }

        file_put_contents($path, $data);
    }

    public static function jsonToArray(string $path, bool $is_associative = true): array
    {
        $file = file_get_contents($path);

        return json_decode($file, $is_associative);
    }

    public static function arrayToObject(array $data): object
    {
        return json_decode(json_encode($data, JSON_FORCE_OBJECT));
    }

    public static function objectToArray(object $data): array
    {
        return json_decode(json_encode($data), true);
    }
}
