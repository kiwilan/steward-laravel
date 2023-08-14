<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\StewardConfig;

/**
 * Add Model capacity to use Model configuration with `HttpQuery`.
 *
 * ```php
 * protected $queryWith = ['relation'];
 * protected $queryWithCount = ['relation'];
 * protected $queryDefaultSort = '-slug';
 * protected $queryAllowedFilters = ['name'];
 * protected $queryAllowedSorts = ['id', 'name', 'slug'];
 * protected $queryNoPaginate = false;
 * protected $queryPagination = 32;
 * protected $queryExport = Export::class;
 * protected $queryResource = Resource::class;
 *
 * // Advanced, override $queryAllowedFilters
 * protected function setQueryAllowedSorts(): array
 * {
 *    return [
 *       AllowedSort::custom('name-length', new StringLengthSort(), 'name'),
 *    ];
 * }
 *
 * // Advanced, override $queryAllowedFilters
 * protected function setQueryAllowedFilters(): array
 * {
 *   return [
 *     // Global search
 *     AllowedFilter::custom('q', new GlobalSearchFilter(['name', 'serie'])),
 *     // Exact filter
 *     AllowedFilter::exact('id'),
 *     // Partial filter
 *     AllowedFilter::partial('name'),
 *     // Advanced filter
 *     AllowedFilter::callback('relation', fn (Builder $query, $value) => $query->whereHas('relation', fn (Builder $query) => $query->where('name', 'like', "%{$value}%"))),
 *   ];
 * }
 * ```
 */
trait Queryable
{
    /**
     * Get Model relationships, default is native `$with`.
     */
    public function getQueryWith(): array
    {
        return $this->queryWith ?? $this->query_with ?? $this->with ?? [];
    }

    /**
     * Get Model relationships count, default is native `$withCount`.
     */
    public function getQueryWithCount(): array
    {
        return $this->queryWithCount ?? $this->query_with_count ?? $this->withCount ?? [];
    }

    /**
     * Get Model default sort field, default is `id` or configured `steward.query.default_sort`.
     */
    public function getQueryDefaultSort(): string
    {
        return $this->queryDefaultSort ?? $this->query_default_sort ?? StewardConfig::queryDefaultSort();
    }

    /**
     * Get Model query allowed filters from `setQueryAllowedFilters()` or `$query_allowed_filters`.
     */
    public function getQueryAllowedFilters(): array
    {
        if (method_exists($this, 'setQueryAllowedFilters')) {
            return $this->setQueryAllowedFilters() ? $this->setQueryAllowedFilters() : [];
        }

        return $this->queryAllowedFilters ?? $this->query_allowed_filters ?? [];
    }

    /**
     * Get Model query allowed sorts from `setQueryAllowedSorts()` or `$query_allowed_sorts`.
     */
    public function getQueryAllowedSorts(): array
    {
        if (method_exists($this, 'setQueryAllowedSorts')) {
            return $this->setQueryAllowedSorts() ? $this->setQueryAllowedSorts() : [];
        }

        return $this->queryAllowedSorts ?? $this->query_allowed_sorts ?? [];
    }

    /**
     * Get Model no pagination directive or configured `steward.query.full`.
     */
    public function getQueryNoPaginate(): bool
    {
        return $this->queryNoPaginate ?? $this->query_full ?? StewardConfig::queryNoPaginate();
    }

    /**
     * Get Model limit for pagination or configured `steward.query.limit`.
     */
    public function getQueryPagination(): int
    {
        return $this->queryPagination ?? $this->query_limit ?? StewardConfig::queryPagination();
    }

    /**
     * Get Model Export class.
     */
    public function getQueryExport(): ?string
    {
        return $this->queryExport ?? $this->query_export ?? null;
    }

    /**
     * Get Model Resource class.
     */
    public function getQueryResource(): ?string
    {
        return $this->queryResource ?? $this->query_resource ?? null;
    }
}
