<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\ProcessService;
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
        ProcessService::executionTime(function () use ($models, $field, $multiple) {
            $console = Console::make();
            $model = $models->first();

            $console->newLine();
            $console->print('  FactoryMediaLocal fetch medias', 'bright-blue');
            $images = $this->fetchMedias($model->getTable());

            $console->print('  Assigning medias to models...');

            foreach ($models as $key => $model) {
                $random = null;

                if ($multiple) {
                    $random = $this->factory->faker()->randomElements($images, $this->factory->faker()->numberBetween(1, 5));
                } else {
                    $random = $this->factory->faker()->randomElement($images);
                }

                $model->{$field} = $random;
                $model->save();
            }

            $console->print('  Done!');
            $console->newLine();
        });
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
