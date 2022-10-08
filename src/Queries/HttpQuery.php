<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Kiwilan\Steward\Traits\Queryable;
use Kiwilan\Steward\Class\MetaClass;
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
        $query->metadata = MetaClass::make($class);
        $query->request = $request;

        $query->defaultSort = $query->getSortDirection(config('steward.query.default_sort'), config('steward.query.default_sort_direction'));
        $query->full = config('steward.query.full');
        $query->limit = config('steward.query.limit');
        $query->resourceGuess();

        $query->query = QueryBuilder::for($query->metadata->class);
        $query->setDefault();

        return $query;
    }

    /**
     * Set a resource like `PostResource::class`, default is `$query_resource` into model.
     */
    public function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set default sort colunm, default is `$query_default_sort`
     * and `$query_default_sort_direction` for direction into model.
     *
     * @param  string  $defaultSort Any `fillable`, default is `id`
     * @param  string  $direction   `asc` | `desc`
     */
    public function defaultSort(string $defaultSort = 'id', string $direction = 'asc'): self
    {
        $this->defaultSort = $this->getSortDirection($defaultSort, $direction);
        $this->query = $this->query->defaultSort($this->defaultSort);

        return $this;
    }

    /**
     * Set allowed filters, default is `$query_allowed_filters` into model,
     * for advanced filters the method `setQueryAllowedFilters(): array` is available.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/filtering.
     *
     * Model simple usage
     * ```php
     * protected $query_allowed_filters = ['title', 'slug'];
     * ```
     *
     * Model advanced usage (override `$query_allowed_filters`)
     * ```php
     * protected function setQueryAllowedFilters(): array
     * {
     *   return [
     *      AllowedFilter::partial('title'),
     *      AllowedFilter::scope('language', 'whereLanguagesIs'),
     *   ];
     * }
     * ```
     */
    public function filters(array $filters = []): self
    {
        $this->allowFilters = $filters;
        $this->query = $this->query->allowedFilters($filters);

        return $this;
    }

    /**
     * Set allowed sorts, default is `$query_allowed_sorts` into model,
     * for advanced sorts the method `setQueryAllowedSorts(): array` is available.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/sorting.
     *
     * Model simple usage
     * ```php
     * protected $query_allowed_sorts = ['name'];
     * ```
     *
     * Model advanced usage (override `$query_allowed_sorts`)
     * ```php
     * protected function setQueryAllowedSorts(): array
     * {
     *   return [
     *      AllowedSort::custom('name-length', new StringLengthSort(), 'name'),
     *   ];
     * }
     * ```
     */
    public function sorts(array $sorts = []): self
    {
        $this->allowSorts = $sorts;
        $this->query = $this->query->allowedSorts($sorts);

        return $this;
    }

    /**
     * Set relationships, default is `$query_with` into model.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function with(array $with = []): self
    {
        $this->with = $with;
        $this->query = $this->query->with($this->with);

        return $this;
    }

    /**
     * Set relationships count, default is `$query_with_count` into model.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function withCount(array $withCount = []): self
    {
        $this->withCount = $withCount;
        $this->query = $this->query->withCount($this->withCount);

        return $this;
    }

    /**
     * Set full query (no pagination), default is `$query_full` into model.
     */
    public function full(): self
    {
        $this->full = true;

        return $this;
    }

    /**
     * Set default pagination limit, default is `$query_limit` into model.
     */
    public function limit(int $limit = 15): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set Export class like `PostExport::class`, default is `$query_export` into model.
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
            $this->defaultSort(
                $instance->getQueryDefaultSort(),
                $instance->getQueryDefaultSortDirection()
            );
            $this->full = $instance->getQueryFull();
            $this->limit($instance->getQueryLimit());

            if ($instance->getQueryExport()) {
                $this->exportable($instance->getQueryExport());
            }

            if ($instance->getQueryResource()) {
                $this->resource($instance->getQueryResource());
            }
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

    private function getSortDirection(string $sort, string $direction): string
    {
        $direction = 'asc' === $direction ? '' : '-';

        return "{$direction}{$sort}";
    }
}
