<?php

namespace Kiwilan\Steward\Services;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class DirectoryService
{
    /**
     * Create a new DirectoryService instance.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Parse files in directory (recursive).
     */
    public function parse(string $directory): Generator
    {
        return $this->parseDirectory($directory);
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
    public function clearDirectory(string $path, array $ignore): void
    {
        $output = new ConsoleOutput();
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
            } elseif ('.' != $value && '..' != $value) {
                yield from $this->parseDirectory($path);

                yield $path;
            }
        }
    }
}
