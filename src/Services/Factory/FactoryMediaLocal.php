<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;

class FactoryMediaLocal
{
    public function __construct(
        public FactoryService $factory,
        public ?string $basePath = null,
        public ?string $path = null,
    ) {
    }

    /**
     * @param  Collection<int,Model>  $models
     * @return void
     */
    public function associate(Collection $models, string $field = 'picture', bool $multiple = false)
    {
        $images = $this->fetchMedias();

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
    }

    /**
     * @return Collection<int,string>
     */
    private function fetchMedias()
    {
        $path = "{$this->basePath}/{$this->path}";

        if (! File::exists($path)) {
            throw new \Exception("Media path not found: {$path}");
        }
        $files = File::allFiles($path);

        $images = collect([]);

        foreach ($files as $file) {
            $images->push(FactoryService::mediaFromFile($file->getRealPath()));
        }

        return $images;
    }
}
