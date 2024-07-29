<?php

namespace Kiwilan\Steward\Engines;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Search Engine with laravel/scout
 * - https://laravel.com/docs/11.x/scout.
 */
class SearchEngine
{
    /**
     * @param  string|null  $query  The search query. Example: "lord of".
     * @param  string[]  $scoutable  The list of scoutable models. Example: [\App\Models\Book::class, \App\Models\Serie::class, \App\Models\Author::class].
     * @param  int  $limit  The limit of results.
     * @param  int  $count  The count of results.
     * @param  Collection<string, Collection<Model>>|null  $results  The results.
     * @param  string|null  $resource  The `Illuminate\Http\Resources\Json\JsonResource` resource.
     */
    protected function __construct(
        protected ?string $query = null,
        protected array $scoutable = [],
        protected ?int $limit = null,
        protected int $count = 0,
        protected ?Collection $results = null,
        protected ?string $resource = null,
    ) {}

    /**
     * Search for models.
     *
     * @param  string  $query  The search query. Example: `lord of`.
     * @param  string[]  $scoutableModels  The list of scoutable models, each model must use `Laravel\Scout\Searchable` trait.
     *                                     The order of the models will be the order of the results.
     *                                     Example: [`\App\Models\Book::class`, `\App\Models\Serie::class`, `\App\Models\Author::class`].
     */
    public static function make(?string $query, array $scoutableModels): self
    {
        $self = new self($query, $scoutableModels);

        return $self;
    }

    /**
     * Set the limit of results by model
     */
    public function limit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Execute the search.
     */
    public function get(): self
    {
        $this->results = $this->search();

        return $this;
    }

    /**
     * Get the results.
     *
     * @return Collection<string, Collection<Model>>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * Get the count of results.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get the limit of results.
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the query.
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function toArray(bool $flatten = false): array
    {
        if ($flatten) {
            return $this->results->flatten()->toArray();
        }

        return $this->results->toArray();
    }

    public function toJson(bool $flatten = false): string
    {
        return json_encode($this->toArray($flatten));
    }

    /**
     * Transform the results to a `Illuminate\Http\Resources\Json\JsonResource`.
     */
    public function toResource(string $resource, bool $flatten = false): array
    {
        $instance = new $resource(collect());
        if (! $instance instanceof JsonResource) {
            throw new \InvalidArgumentException('The resource must be an instance of '.JsonResource::class);
        }

        $this->resource = $resource;

        /** @var Collection<string, JsonResource> */
        $resources = collect();

        // Transform the results to resources.
        foreach ($this->results as $class => $collect) {
            $resources->put($class, $this->resource::collection($collect));
        }

        if ($flatten) {
            /** @var Collection<JsonResource> */
            $items = collect();

            // Merge the resources.
            foreach ($resources as $resource) {
                $items->push(...$resource->toArray(request()));
            }

            return $items->toArray();
        }

        return $resources->toArray();

    }

    /**
     * Search for models.
     */
    private function search(): Collection
    {
        if (! $this->query) {
            // No query, no search.
            return collect();
        }

        if (empty($this->scoutable)) {
            // No scoutable models, no search.
            return collect();
        }

        /** @var Collection<string, Collection<Model>> */
        $results = collect();

        // Search for each scoutable model.
        foreach ($this->scoutable as $class) {
            if (method_exists($class, 'search')) {
                $builder = $class::search($this->query);
                $results->put($class, $builder->get());
            } else {
                throw new \InvalidArgumentException('The model '.$class.' must use Laravel\Scout\Searchable trait');
            }
        }

        // Rename the keys of the results.
        foreach ($results as $class => $collect) {
            $modelName = $this->modelName($class);
            $results->put($modelName, $collect);
            $results->forget($class);
        }

        // Limit the results.
        if ($this->limit) {
            $results = $results->map(function ($collect) {
                return $collect->take($this->limit);
            });
        }

        // Count the results.
        foreach ($results as $collect) {
            $collect->each(function () {
                $this->count++;
            });
        }

        return $results;
    }

    /**
     * Get the model name.
     */
    private function modelName(string $class): string
    {
        $instance = new $class;
        $class = new ReflectionClass($instance);
        $name = $class->getShortName();

        return Str::plural($name);
    }
}
