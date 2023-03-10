<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Class\ClassItem;

class ScoutService
{
    /** @var Collection<int,ClassItem> */
    protected ?Collection $models = null;

    /** @var array<string,string> */
    protected array $list = [];

    protected function __construct(
        protected ?string $path = null,
    ) {
    }

    public static function make(?string $path = null): self
    {
        if (is_null($path)) {
            $path = app_path('Models');
        }

        $self = new self($path);

        $self->models = $self->setScoutModels();
        $self->list = $self->setScoutList();

        return $self;
    }

    /**
     * @return Collection<int,ClassItem>
     */
    public function models(): Collection
    {
        return $this->models;
    }

    /**
     * @return array<string,string>
     */
    public function list(): array
    {
        return $this->list;
    }

    /**
     * Find all models with trait Searchable.
     *
     * @return Collection<int,ClassItem>
     */
    private function setScoutModels(): Collection
    {
        $models = collect([]);

        $files = ClassService::files($this->path);
        $items = ClassService::make($files);

        foreach ($items as $item) {
            if ($item->useTrait('Laravel\Scout\Searchable')) {
                $models->push($item);
            }
        }

        return $models;
    }

    /**
     * @return array<string,string>
     */
    private function setScoutList(): array
    {
        $list = [];

        foreach ($this->models as $item) {
            $name = $item->namespace();
            $name = str_replace('\\', '\\\\', $name);

            $list[$name] = ScoutService::getIndexName($item);
        }

        return $list;
    }

    public static function getIndexName(ClassItem $item): string
    {
        if (! $item->useTrait('Laravel\Scout\Searchable')) {
            throw new \Exception('Model '.$item->name().' have not `Searchable` trait.');
        }

        if (! $item->methodExists('searchableAs')) {
            return $item->model()->getTable();
        }

        return $item->model()->searchableAs(); // @phpstan-ignore-line
    }
}
