<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\ClassParser\ClassParserItem;
use SplFileInfo;

class ClassParserService
{
    /**
     * @param  string  $classString  Class string, like `Book::class`
     */
    public static function make(string $classString): ClassParserItem
    {
        return ClassParserItem::make($classString);
    }

    /**
     * @param  string|Collection<int,SplFileInfo|string>  $pathOrFiles  Path to directory or collection of SplFileInfo
     * @return Collection<int,ClassParserItem>
     */
    public static function toCollection(string|Collection $pathOrFiles): Collection
    {
        $self = new self;

        $files = collect();

        if (is_string($pathOrFiles)) {
            $files = $self->getFiles($pathOrFiles);
        }

        return $files->map(function ($file) {
            $path = null;

            if ($file instanceof SplFileInfo) {
                $path = $file->getPathname();
            } elseif (is_string($file)) {
                $path = $file;
            }

            return ClassParserItem::make($path);
        });
    }

    /**
     * @return Collection<int,SplFileInfo>
     */
    private function getFiles(string $path): Collection
    {
        /** @var Collection<int,SplFileInfo> */
        $files = collect(File::allFiles($path));

        /** @var Collection<int,SplFileInfo> */
        $classFiles = collect();

        $files->map(function ($file) use ($classFiles) {
            if ($file->getExtension() === 'php') {
                $classFiles->push($file);
            }
        });

        return $classFiles;
    }
}
