<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    protected function __construct(
        protected Model $model,
        protected string $slugWith = 'name',
        protected string $slugColumn = 'slug',
        protected bool $empty = false,
        public ?string $name = null,
        public ?string $slug = null,
    ) {
    }

    public static function make(Model $model, string $slugWith = 'name', string $slugColumn = 'slug'): string
    {
        $service = new self($model, $slugWith, $slugColumn);

        if (! property_exists($model, $slugColumn)) {
            throw new \Exception("Property {$slugWith} does not exist in model {$model->getTable()}, you can add `protected \$slug_with = 'name';` to your model.");
        }

        $service->empty = empty($model->{$slugColumn});
        $service->name = $service->setName();
        $service->slug = Str::slug($service->name);

        if ($service->empty) {
            $service->slug = $service->unique($service->name, 0);
        } else {
            $slugExist = $service->model->where($service->slugColumn, $model->{$slugColumn})->exists();

            if ($slugExist) {
                $service->slug = $service->unique($service->name, 0);
            } else {
                $service->slug = $model->{$slugColumn};
            }
        }

        return $service->slug;
    }

    private function setName(): string
    {
        $modelName = $this->model->{$this->slugWith};

        if (is_array($modelName)) {
            $modelName = reset($modelName);
        }

        return $modelName;
    }

    private function unique(?string $name = null, int $counter = 0): string
    {
        if (null === $name) {
            $name = uniqid();
        }
        $updated_name = 0 == $counter ? $name : $name.'-'.$counter;

        if ($this->model->where($this->slugColumn, Str::slug($updated_name))->exists()) {
            return $this->unique($name, $counter + 1);
        }

        return Str::slug($updated_name);
    }
}
