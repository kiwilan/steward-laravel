<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Kiwilan\Steward\Class\MetaClass;
use Kiwilan\Steward\Traits\Queryable;
use ReflectionClass;
use Spatie\QueryBuilder\QueryBuilder;

class HttpQuery extends BaseQuery
{
    /**
     * Create the query with `HttpQuery`.
     *
     * Works with `spatie/laravel-query-builder` for API and Laravel Builder for front.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/introduction
     *
     * @param  string  $class
     */
    public static function make(string $class, ?Request $request = null): self
    {
        $api = new HttpQuery();
        $api->class = $class;
        $api->setMetadata(MetaClass::make($class));
        $api->setRequest($request);

        $api->defaultSort = $api->getSortDirection(config('steward.query.default_sort'), config('steward.query.default_sort_direction'));
        $api->full = config('steward.query.full');
        $api->limit = config('steward.query.limit');
        $api->resourceGuess();

        $api->setQuery(QueryBuilder::for($api->metadata()->class()));
        $api->setDefault();

        /** @var Builder $builder */
        $builder = $api->class::query();
        $api->setBuilder($builder);

        return $api;
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
        $this->setQuery($this->query()->defaultSort($this->defaultSort));

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
        $this->setQuery($this->query()->allowedFilters($filters));

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
        $this->setQuery($this->query()->allowedSorts($sorts));

        return $this;
    }

    /**
     * Set relationships, default is `$query_with` into model.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function with(array $with = []): self
    {
        $this->with = $with;
        $this->setQuery($this->query()->with($this->with));

        return $this;
    }

    /**
     * Set relationships count, default is `$query_with_count` into model.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function withCount(array $withCount = []): self
    {
        $this->withCount = $withCount;
        $this->setQuery($this->query()->withCount($this->withCount));

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
        $namespaced = $this->metadata()->classNamespaced();

        return new $namespaced();
    }

    /**
     * Check if current class uses `Queryable` trait.
     */
    private function isQueryable(): bool
    {
        $instance = $this->getInstance();
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
