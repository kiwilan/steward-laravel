<?php

namespace Kiwilan\Steward\Services;

/**
 * Directory parser.
 *
 * Example
 *
 * ```php
 * $files = DirectoryParserService::parse($path);
 * ```
 */
class DirectoryParserService
{
    /**
     * Parse directory (recursive).
     *
     * @return \Generator<mixed, mixed, mixed, void>
     */
    public static function parse(string $directory)
    {
        $files = scandir($directory);
        foreach ($files as $key => $value) {
            $path = realpath($directory.DIRECTORY_SEPARATOR.$value);
            if (! is_dir($path)) {
                yield $path;
            } elseif ('.' != $value && '..' != $value) {
                yield from self::parse($path);
                yield $path;
            }
        }
    }
}
