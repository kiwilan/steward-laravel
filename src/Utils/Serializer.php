<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\LaravelNotifier\Facades\Journal;
use Kiwilan\Steward\Services\DirectoryService;

class Serializer
{
    public function __construct(
    ) {}

    /**
     * Serialize the contents of a file.
     */
    public static function serialize(string $file_path, mixed $contents): bool
    {
        DirectoryService::ensureFileExists($file_path);

        try {
            return file_put_contents($file_path, serialize($contents));
        } catch (\Throwable $th) {
            Journal::error("Serializer: failed to serialize {$file_path}", [$th->getMessage()]);
        }

        return false;
    }

    /**
     * Unserialize the contents of a file.
     */
    public static function unserialize(string $file_path): mixed
    {
        try {
            return unserialize(file_get_contents($file_path));
        } catch (\Throwable $th) {
            Journal::error("Serializer: failed to unserialize {$file_path}", [$th->getMessage()]);
        }

        return null;
    }
}
