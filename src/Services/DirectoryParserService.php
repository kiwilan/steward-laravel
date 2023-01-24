<?php

namespace Kiwilan\Steward\Services;

use Generator;

/**
 * Directory parser.
 *
 * @property string   $directory
 * @property string[] $files
 *
 * Example
 *
 * ```php
 * $service = DirectoryParserService::make($path);
 * $files = $service->files;
 * ```
 */
class DirectoryParserService
{
    public function __construct(
        public ?string $directory = null,
        public mixed $files = null,
    ) {
    }

    /**
     * Parser directory service.
     */
    public static function make(string $directory): self
    {
        $service = new DirectoryParserService();
        $service->directory = $directory;

        $service->files = $service->parse($service->directory);

        return $service;
    }

    /**
     * Parse directory.
     *
     * @return Generator<mixed, mixed, mixed, void>
     */
    private function parse(string $directory)
    {
        $files = scandir($directory);

        foreach ($files as $key => $value) {
            $path = realpath($directory.DIRECTORY_SEPARATOR.$value);

            if (! is_dir($path)) {
                yield $path;
            } elseif ('.' != $value && '..' != $value) {
                yield from $this->parse($path);

                yield $path;
            }
        }
    }
}
