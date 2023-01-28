<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\TypeableService\TypeableClass;
use Kiwilan\Steward\Services\TypeableService\Utils\TypeableTeam;

/**
 * @property string $path
 * @property TypeableClass[] $typeables
 */
class TypeableService
{
    protected function __construct(
        public string $path,
        /** @var TypeableClass[] */
        public array $typeables = [],
    ) {
    }

    public static function make(): self
    {
        $path = app_path('Models');

        $service = new TypeableService($path);
        $service->typeables = $service->setTypeables();
        $service->typeables['team'] = TypeableClass::fake('Team', TypeableTeam::setFakeTeam());

        $service->setModelTypes();

        return $service;
    }

    private function setModelTypes()
    {
        $content = [];

        $content[] = '// This file is auto generated by GenerateTypeCommand.';
        $content[] = 'declare namespace App.Models {';

        foreach ($this->typeables as $typeable) {
            $content[] = $typeable->typeableModel?->tsString;
        }
        $content[] = '}';

        $content = implode(PHP_EOL, $content);

        $path = config('steward.typescript.path') ?? resource_path('js');
        $filename = config('steward.typescript.file.models') ?? 'types-models.d.ts';

        $path = "{$path}/{$filename}";
        File::put($path, $content);
    }

    /**
     * @return TypeableClass[]
     */
    private function setTypeables(): array
    {
        $classes = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isDir()) {
                $classes[$file->getFilename()] = TypeableClass::make(
                    path: $file->getPathname(),
                    file: $file,
                );
            }
        }

        return $classes;
    }
}