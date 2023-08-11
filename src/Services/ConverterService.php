<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Facades\File;
use Kiwilan\HttpPool\Utils\PrintConsole;

class ConverterService
{
    public const CONFIG = [
        'APP_NAME' => 'app.name',
        'APP_URL' => 'app.url',
        'APP_FRONT_URL' => 'app.front_url',
    ];

    public static function prettyJson(mixed $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function saveAsJson(mixed $data, string $name, string $path = null, bool $print = true): void
    {
        $data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $defaultPath = storage_path('app/public/debug');
        $name = "{$name}.json";

        if (! $path) {
            $path = $defaultPath;
        }

        if (! File::exists($path)) {
            File::makeDirectory($path, recursive: true);
        }

        $path = "{$path}/{$name}";

        File::delete($path);
        File::put($path, $data);

        if ($print) {
            $console = PrintConsole::make();
            $console->print("Saved to `{$path}`.");
        }
    }

    public static function jsonToArray(string $path, bool $is_associative = true, bool $replace_dotenv = true): array
    {
        $file = File::get($path);

        if ($replace_dotenv) {
            $file = ConverterService::replaceWithDotenv($file);
        }

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

    public static function replaceWithDotenv(string $string): string
    {
        foreach (self::CONFIG as $dotenv_key => $config_key) {
            $string = str_replace($dotenv_key, config($config_key), $string);
        }

        return $string;
    }
}
