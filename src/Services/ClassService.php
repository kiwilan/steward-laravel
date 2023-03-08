<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\Class\ClassItem;
use SplFileInfo;

class ClassService
{
    /** @var Collection<int,ClassItem> */
    protected ?Collection $items = null;

    /**
     * @param  Collection<int,SplFileInfo|string>  $files
     * @return Collection<int,ClassItem>
     */
    public static function make(Collection $files): Collection
    {
        $self = new self();

        $self->items = $files->map(function ($file) {
            $path = null;

            if ($file instanceof SplFileInfo) {
                $path = $file->getPathname();
            } elseif (is_string($file)) {
                $path = $file;
            }

            return ClassItem::make($path);
        });

        return $self->items;
    }

    /**
     * @return Collection<int,SplFileInfo>
     */
    public static function files(string $path): Collection
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
