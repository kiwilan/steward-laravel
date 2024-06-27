<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    protected function __construct(
        protected ?Model $model = null,
        protected string $slugWith = 'name',
        protected string $slugColumn = 'slug',
        protected bool $isEmpty = false,
        protected ?string $name = null,
        protected ?string $slug = null,
    ) {}

    public static function make(?string $origin = null): string
    {
        $service = new self();

        if (! $origin) {
            return $service->unique();
        }

        $service->name = $origin;

        return Str::slug($origin);
    }

    public static function makeFromModel(Model $model, string $slugWith = 'name', string $slugColumn = 'slug'): self
    {
        $service = new self($model, $slugWith, $slugColumn);

        if (! isset($model->{$slugWith})) {
            $service->slug = $service->unique();

            return $service;
        }

        $service->isEmpty = empty($model->{$slugColumn});
        $service->name = $service->setName();
        $service->slug = Str::slug($service->name);

        if ($service->isEmpty) {
            $service->slug = $service->unique($service->name, 0);
        } else {
            $slugExist = $service->model->where($service->slugColumn, $model->{$slugColumn})->exists();

            if ($slugExist) {
                $service->slug = $service->unique($service->name, 0);
            } else {
                $service->slug = $model->{$slugColumn};
            }
        }

        return $service;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
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
        if ($name === null) {
            $name = uniqid();
        }
        $updated_name = $counter == 0 ? $name : $name.'-'.$counter;

        if ($this->model->where($this->slugColumn, Str::slug($updated_name))->exists()) {
            return $this->unique($name, $counter + 1);
        }

        return Str::slug($updated_name);
    }
}
