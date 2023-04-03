<?php

namespace Kiwilan\Steward\Services;

use Generator;

/**
 * Directory parser.
 *
 * Example
 *
 * ```php
 * $parser = DirectoryParserService::make($path);
 * $files = $parser->files();
 * ```
 */
class DirectoryParserService
{
    protected array $files = [];

    protected function __construct(
        protected string $directory,
    ) {
    }

    /**
     * Parser directory service.
     */
    public static function make(string $directory): self
    {
        $service = new DirectoryParserService($directory);

        /** @var array */
        $files = $service->parse($service->directory);
        $service->files = $files;

        return $service;
    }

    /**
     * Get files.
     *
     * @return string[]
     */
    public function files(): mixed
    {
        return $this->files;
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
