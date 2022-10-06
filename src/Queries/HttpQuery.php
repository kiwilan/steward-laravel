<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Kiwilan\Steward\Traits\Queryable;
use Kiwilan\Steward\Utils\ClassMetadata;
use ReflectionClass;
use Spatie\QueryBuilder\QueryBuilder;

class HttpQuery extends BaseQuery
{
    /**
     * Create the query with `HttpQuery`.
     *
     * Works with `spatie/laravel-query-builder`.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/introduction
     *
     * @param  EloquentBuilder|Relation|string  $class
     */
    public static function make($class, ?Request $request = null): self
    {
        $query = new HttpQuery();
        $query->metadata = ClassMetadata::create($class);
        $query->resource = $query->metadata->class_plural_snake;
        $query->request = $request;

        $query->defaultSort = 'id';
        $query->size = 15;

        $query->query = QueryBuilder::for($query->metadata->class);
        $query->setDefault();

        return $query;
    }

    /**
     * Set a resource like `PostResource::class`.
     */
    public function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set default sort colunm.
     *
     * @param  string  $defaultSort Any `fillable`, default is `id`
     * @param  string  $direction   `asc` | `desc`
     */
    public function defaultSort(string $defaultSort = 'id', string $direction = 'asc'): self
    {
        $direction = 'asc' === $direction ? '' : '-';
        $this->defaultSort = "{$direction}{$defaultSort}";
        $this->query = $this->query->defaultSort($this->defaultSort);

        return $this;
    }

    /**
     * Set allowed filters
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/filtering.
     */
    public function filters(array $filters): self
    {
        $this->allowFilters = $filters;
        $this->query = $this->query->allowedFilters($filters);

        return $this;
    }

    /**
     * Set allowed sorts
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/sorting.
     */
    public function sorts(array $sorts): self
    {
        $this->allowSorts = $sorts;
        $this->query = $this->query->allowedSorts($sorts);

        return $this;
    }

    /**
     * Set relationships
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function with(array $with = []): self
    {
        $this->with = $with;
        $this->query = $this->query->with($this->with);

        return $this;
    }

    /**
     * Set relationships count
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function withCount(array $withCount = []): self
    {
        $this->withCount = $withCount;
        $this->query = $this->query->withCount($this->withCount);

        return $this;
    }

    /**
     * Set default pagination size.
     */
    public function size(int $size = 32): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set Export class like `PostExport::class`.
     * If class is not set, it will be guessed from `App\Export\{ClassName}Export`.
     */
    public function exportable(string $export): self
    {
        $this->export = $export;

        return $this;
    }

    /**
     * Set default query from `Queryable` trait.
     */
    private function setDefault(): void
    {
        if ($this->isQueryable()) {
            $instance = $this->getInstance();

            $this->with($instance->getQueryWith());
            $this->withCount($instance->getQueryWithCount());
            $this->filters($instance->getQueryAllowedFilters());
            $this->sorts($instance->getQueryAllowedSorts());
            $this->defaultSort($instance->getQueryDefaultSort(), $instance->getQueryDefaultSortDirection());
            $this->size($instance->getQuerySize());

            $this->exportable($instance->getQueryExport());

            $this->resource($instance->getQueryResource());
        }
    }

    /**
     * Get instance of current class.
     */
    private function getInstance(): object
    {
        return new $this->metadata->class_namespaced();
    }

    /**
     * Check if current class uses `Queryable` trait.
     */
    private function isQueryable(): bool
    {
        $instance = new $this->metadata->class_namespaced();
        $class = new ReflectionClass($instance);

        return in_array(
            Queryable::class,
            array_keys($class->getTraits())
        );
    }
}
