<?php

namespace Kiwilan\Steward\Services;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class DirectoryService
{
    /**
     * Create a new DirectoryService instance.
     */
    public static function make(): self
    {
        return new self;
    }

    /**
     * Parse files in directory (recursive).
     *
     * @param  string[]|false  $ignore  Parser will ignore files or directories in this array.
     * @return string[]
     */
    public function parse(string $directory, array|false $ignore = ['.gitignore', '.DS_Store', '$RECYCLE.BIN'], bool $skipHiddenFiles = true, bool $skipDirs = true): array
    {
        /** @var string[] */
        $files = [];
        $generator = $this->parseDirectory($directory);

        /** @var string $file */
        foreach ($generator as $file) {
            $isDir = is_dir($file);
            $filename = pathinfo($file, PATHINFO_FILENAME);

            if (empty($filename)) {
                $splitted = explode('/', $file);
                $filename = end($splitted);
            }
            $valid = [];

            if ($ignore) {
                foreach ($ignore as $entry) {
                    if (str_contains($file, $entry)) {
                        $valid[] = false;
                    }
                }
            }

            if ($valid === []) {
                if ($skipHiddenFiles && str_starts_with($filename, '.')) {
                    continue;
                }

                if ($isDir && $skipDirs) {
                    continue;
                }

                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Works like `parse()` but return `SplFileInfo` array.
     *
     * @param  string[]|false  $ignore  Parser will ignore files or directories in this array.
     * @return SplFileInfo[]
     */
    public function parseFileInfo(string $directory, array|false $ignore = ['.gitignore', '.DS_Store', '$RECYCLE.BIN'], bool $skipHiddenFiles = true): array
    {
        $files = [];
        $items = $this->parse($directory, $ignore);

        foreach ($items as $path) {
            $files[] = new SplFileInfo($path);
        }

        return $files;
    }

    /**
     * Remove all files into selected directory from but keep files into $ignore.
     */
    public function clear(array|string $path, array $ignore = ['.gitignore']): void
    {
        if (is_string($path)) {
            $path = [$path];
        }

        foreach ($path as $p) {
            $this->clearDirectory($p, $ignore);
        }
    }

    /**
     * Remove all files into selected directory from but keep files into $ignore.
     */
    public function clearDirectory(string $path, array $ignore = ['.gitignore']): void
    {
        $output = new ConsoleOutput;
        $outputStyle = new OutputFormatterStyle('red', '', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $outputStyle);

        foreach (glob("{$path}/*") as $file) {
            if (! in_array(basename($file), $ignore)) {
                if (is_dir($file)) {
                    $this->removeDirectory($file);
                } else {
                    unlink($file);
                }
            }
        }

        $output->writeln('Clear storage/'.basename($path));
    }

    /**
     * Remove directory recursively.
     */
    private function removeDirectory(string $directory): void
    {
        $it = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($it as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($directory);
    }

    /**
     * Parse directory.
     *
     * @return Generator<mixed, mixed, mixed, void>
     */
    private function parseDirectory(string $directory): Generator
    {
        $files = scandir($directory);

        foreach ($files as $key => $value) {
            $path = realpath($directory.DIRECTORY_SEPARATOR.$value);

            if (! is_dir($path)) {
                yield $path;
            } elseif ($value != '.' && $value != '..') {
                yield from $this->parseDirectory($path);

                yield $path;
            }
        }
    }
}
