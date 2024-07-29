<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Kiwilan\Steward\StewardConfig;
use Kiwilan\Steward\Traits\Queryable;

class HttpQuery extends BaseQuery
{
    /**
     * Create the query with `HttpQuery`.
     *
     * Works with `spatie/laravel-query-builder` for API and Laravel Builder for front.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/introduction
     *
     * @param  string|Builder  $model  Class string or Builder, like `Book::class` or `Book::query()` or `Book::where('id', 1)`
     * @param  Request  $request  from `Illuminate\Http\Request`
     */
    public static function for(string|Builder $model, Request $request): self
    {
        $self = new self;

        $self->setup($model, $request);

        $self->defaultSort = StewardConfig::queryDefaultSort();
        $self->noPaginate = StewardConfig::queryNoPaginate();
        $self->pagination = StewardConfig::queryPagination();

        $self->setDefault();

        return $self;
    }

    /**
     * Set default sort colunm, default is `$queryDefaultSort`
     * and `$queryDefaultSortDirection` for direction into model.
     *
     * @param  string  $defaultSort  Any `fillable`, default is `id`
     */
    public function defaultSort(string $defaultSort = 'id'): self
    {
        $this->defaultSort = $defaultSort;
        $this->loadRequest();

        return $this;
    }

    /**
     * Set allowed filters, default is `$queryAllowedFilters` into model,
     * for advanced filters the method `setQueryAllowedFilters(): array` is available.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/filtering.
     *
     * Model simple usage
     * ```php
     * protected $queryAllowedFilters = ['title', 'slug'];
     * ```
     *
     * Model advanced usage (override `$queryAllowedFilters`)
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
        $this->allowFilters = array_unique(array_merge($this->allowFilters, $filters));
        $this->loadRequest();

        return $this;
    }

    /**
     * Set allowed sorts, default is `$queryAllowedSorts` into model,
     * for advanced sorts the method `setQueryAllowedSorts(): array` is available.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/sorting.
     *
     * Model simple usage
     * ```php
     * protected $queryAllowedSorts = ['name'];
     * ```
     *
     * Model advanced usage (override `$queryAllowedSorts`)
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
        $this->allowSorts = array_unique(array_merge($this->allowSorts, $sorts));
        $this->loadRequest();

        return $this;
    }

    /**
     * Set relationships, default is `$queryWith` into model.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function with(string|array $with = []): self
    {
        if (is_string($with)) {
            $with = [$with];
        }

        $this->with = array_unique(array_merge($this->with, $with));
        $this->loadRequest();

        return $this;
    }

    /**
     * Set relationships count, default is `$queryWithCount` into model.
     * Docs: https://spatie.be/docs/laravel-query-builder/v5/features/including-relationships.
     */
    public function withCount(string|array $withCount = []): self
    {
        if (is_string($withCount)) {
            $withCount = [$withCount];
        }

        $this->withCount = array_unique(array_merge($this->withCount, $withCount));
        $this->loadRequest();

        return $this;
    }

    /**
     * Disable pagination, default is `$noPaginate` into model.
     */
    public function noPaginate(): self
    {
        $this->noPaginate = true;

        return $this;
    }

    /**
     * Set default pagination, default is `$pagination` into model.
     */
    public function pagination(int $pagination = 15): self
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * Set a resource like `PostResource::class`, default is `$queryResource` into model.
     */
    public function resource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set Export class like `PostExport::class`, default is `$queryExport` into model.
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
        if (! $this->getParser()->useTrait(Queryable::class)) {
            throw new \Exception('Class must use `Kiwilan\Steward\Traits\Queryable` trait');
        }

        $instance = $this->getInstance();

        if (method_exists($instance, 'getQueryWith')) {
            $this->with = $instance->getQueryWith() ? $instance->getQueryWith() : $this->with;
        }

        if (method_exists($instance, 'getQueryWithCount')) {
            $this->withCount = $instance->getQueryWithCount() ? $instance->getQueryWithCount() : $this->withCount;
        }

        if (method_exists($instance, 'getQueryAllowedFilters')) {
            $this->allowFilters = $instance->getQueryAllowedFilters() ? $instance->getQueryAllowedFilters() : $this->allowFilters;
        }

        if (method_exists($instance, 'getQueryAllowedSorts')) {
            $this->allowSorts = $instance->getQueryAllowedSorts() ? $instance->getQueryAllowedSorts() : $this->allowSorts;
        }

        if (method_exists($instance, 'getQueryDefaultSort')) {
            $this->defaultSort = $instance->getQueryDefaultSort() ? $instance->getQueryDefaultSort() : $this->defaultSort;
        }

        if (method_exists($instance, 'getQueryNoPaginate')) {
            $this->noPaginate = $instance->getQueryNoPaginate() ? $instance->getQueryNoPaginate() : $this->noPaginate;
        }

        if (method_exists($instance, 'getQueryPagination')) {
            $this->pagination = $instance->getQueryPagination() ? $instance->getQueryPagination() : $this->pagination;
        }

        if (method_exists($instance, 'getQueryExport')) {
            $this->export = $instance->getQueryExport() ? $instance->getQueryExport() : $this->export;
        }

        if (method_exists($instance, 'getQueryResource')) {
            $this->resource = $instance->getQueryResource() ? $instance->getQueryResource() : $this->resource;
        }
    }
}
