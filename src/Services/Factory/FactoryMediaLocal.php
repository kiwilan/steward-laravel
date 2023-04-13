<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\ProcessService;
use Kiwilan\Steward\StewardConfig;
use Kiwilan\Steward\Utils\Console;

class FactoryMediaLocal
{
    public function __construct(
        public FactoryService $factory,
        public ?string $basePath = null,
        public ?string $path = null,
    ) {
    }

    /**
     * @param  Collection<int, Model>  $models
     */
    public function associate(Collection $models, string $field = 'picture', bool $multiple = false): void
    {
        $verbose = StewardConfig::factoryVerbose();
        ProcessService::executionTime(function () use ($models, $field, $multiple, $verbose) {
            $console = Console::make();
            $model = $models->first();

            $console->newLine();

            if ($verbose) {
                $console->print('  FactoryMediaLocal fetch medias', 'bright-blue');
            }
            $images = $this->fetchMedias($model->getTable());

            if ($verbose) {
                $console->print('  Assigning medias to models...');
            }

            foreach ($models as $key => $model) {
                $random = null;

                if ($multiple) {
                    $random = $this->factory->faker()->randomElements($images, $this->factory->faker()->numberBetween(1, 5));
                } else {
                    $random = $this->factory->faker()->randomElement($images);
                }

                if ($verbose) {
                    $console->print("    Assign to {$key}...");
                }
                $model->{$field} = $random;
                $model->save();
            }

            if ($verbose) {
                $console->print('  Done!');
            }
            $console->newLine();
        }, $verbose);
    }

    /**
     * @return Collection<int,string>
     */
    private function fetchMedias(?string $basePath = null)
    {
        $path = "{$this->basePath}/{$this->path}";

        if (! File::exists($path)) {
            throw new \Exception("Media path not found: {$path}");
        }
        $files = File::allFiles($path);

        $images = collect([]);

        foreach ($files as $file) {
            $images->push(
                FactoryService::mediaFromFile(
                    $file->getRealPath(),
                    $basePath
                )
            );
        }

        return $images;
    }
}
