<?php

namespace Kiwilan\Steward\Services;

use Doctrine\DBAL\Types\Types;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\ModelTypeService\TypeColumnConverter;
use Kiwilan\Steward\Services\ModelTypeService\TypeModelConverter;
use Kiwilan\Steward\Services\ModelTypeService\TypePropertyConverter;
use Kiwilan\Steward\Services\ModelTypeService\TypeRelationConverter;
use SplFileInfo;

/**
 * @property string $namespace_prefix
 * @property string $converter_path
 * @property string[] $models_namespaces
 * @property bool $isValid
 * @property string|null $error
 * @property TypeModelConverter[] $models_ts
 */
class ModelTypeService
{
    protected function __construct(
        public string $namespace_prefix,
        public string $converter_path,
        /** @var string[] */
        public array $models_namespaces = [],
        public bool $isValid = false,
        public ?string $error = null,
        /** @var TypeModelConverter[] */
        public array $models_ts = [],
    ) {
    }

    public static function make(): self
    {
        $namespace_prefix = 'App\\Types';
        $converter_path = app_path('Types');

        $converter = new ModelTypeService($namespace_prefix, $converter_path);
        $converter->models_namespaces = $converter->getModelsNamespaces();

        $converter->setup();

        return $converter;
    }

    private function setup()
    {
        File::deleteDirectory($this->converter_path);
        File::makeDirectory($this->converter_path);

        foreach ($this->models_namespaces as $model) {
            $this->models_ts[] = $this->parseClass($model);
        }

        $content = '';

        $content .= 'declare namespace App.Models {' . PHP_EOL;
        foreach ($this->models_ts as $key => $model_ts) {
            $content .= $model_ts->typescript;
        }
        $content .= '}' . PHP_EOL;

        $path = config('steward.typescript.path') ?? resource_path('js');
        $filename = config('steward.typescript.file') ?? 'models.d.ts';

        $path = "{$path}/{$filename}";
        File::put($path, $content);
    }

    private function parseClass(string $model_namespaced): ?TypeModelConverter
    {
        if (!class_exists($model_namespaced)) {
            $this->error = "{$model_namespaced} class not exist.";
            return null;
        }

        $model = new $model_namespaced();

        if (!$model instanceof Model) {
            $this->error = "{$model_namespaced} is not an instance of Model.";
            return null;
        }

        $reflector = new \ReflectionClass($model);
        $model_name = $reflector->getShortName();

        $columns = TypeColumnConverter::tableToColumns($model);
        $relations = TypeRelationConverter::make($model);

        $type_fields = TypePropertyConverter::make([
            ...array_map(fn (TypeColumnConverter $column) => $column->typescript, $columns),
            ...array_map(fn (TypeRelationConverter $relation) => $relation->typescript, $relations),
        ]);

        return TypeModelConverter::make($model, $type_fields);
    }

    private function getFileNamespace(SplFileInfo $file): string
    {
        $path = $file->getPathName();
        $name = $file->getBasename('.php');

        $ns = null;
        $handle = fopen($path, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }

        return "{$ns}\\{$name}";
    }

    /**
     * @return string[]
     */
    private function getModelsNamespaces(): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path('Models'), \FilesystemIterator::SKIP_DOTS)
        );

        $namespaces = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $ns = $this->getFileNamespace($file);
                $namespaces[] = $ns;
            }
        }

        return $namespaces;
    }
}
