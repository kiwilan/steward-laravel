<?php

namespace Kiwilan\Steward\Traits;

/**
 * Add Model capacity to use Model configuration with `HttpQuery`.
 * - `$query_with`: `array` relationships
 * - `$query_with_count`: `array` relationships with count
 * - `$query_default_sort`: default sorting field to use
 * - `$query_default_sort_direction`: `asc` | `desc` default sorting direction to use
 * - `$query_allowed_filters`: `array` filters
 * - `setQueryAllowedFilters()`: `array` advanced filters, override `$query_allowed_filters`
 * - `$query_allowed_sorts`: `array` sorts
 * - `setQueryAllowedSorts()`: `array` advanced sorts, override `$query_allowed_sorts`
 * - `$query_full`: `bool` if pagination is disabled
 * - `$query_limit`: `int` limit of results
 * - `$query_export`: `string` Export class to use
 * - `$query_resource`: `string` Resource class to use
 *
 * ```php
 * protected $query_default_sort = 'slug';
 * protected $query_allowed_filters = ['name'];
 * protected $query_allowed_sorts = ['id', 'name', 'slug'];
 * protected $query_limit = 32;
 *
 * // Advanced, override $query_allowed_filters
 * protected function setQueryAllowedSorts(): array
 * {
 *    return [
 *       AllowedSort::custom('name-length', new StringLengthSort(), 'name'),
 *    ];
 * }
 *
 * // Advanced, override $query_allowed_filters
 * protected function setQueryAllowedFilters(): array
 * {
 *   return [
 *     AllowedFilter::custom('q', new GlobalSearchFilter(['name', 'serie'])),
 *     AllowedFilter::exact('id'),
 *     AllowedFilter::partial('name'),
 *     AllowedFilter::callback(
 *       'relation',
 *       fn (Builder $query, $value) => $query->whereHas(
 *         'relation',
 *         fn (Builder $query) => $query->where('name', 'like', "%{$value}%")
 *       )
 *     ),
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
        return $this->query_with ?? $this->with ?? [];
    }

    /**
     * Get Model relationships count, default is native `$withCount`.
     */
    public function getQueryWithCount(): array
    {
        return $this->query_with_count ?? $this->withCount ?? [];
    }

    /**
     * Get Model default sort field, default is `id` or configured `steward.query.default_sort`.
     */
    public function getQueryDefaultSort(): string
    {
        return $this->query_default_sort ?? config('steward.query.default_sort');
    }

    /**
     * Get Model default sort direction, default is `desc` or configured `steward.query.default_sort_direction`.
     */
    public function getQueryDefaultSortDirection(): string
    {
        return $this->query_default_sort_direction ?? config('steward.query.default_sort_direction');
    }

    /**
     * Get Model query allowed filters from `setQueryAllowedFilters()` or `$query_allowed_filters`.
     */
    public function getQueryAllowedFilters(): array
    {
        if (method_exists($this, 'setQueryAllowedFilters')) {
            return $this->setQueryAllowedFilters();
        }

        return $this->query_allowed_filters ?? [];
    }

    /**
     * Get Model query allowed sorts from `setQueryAllowedSorts()` or `$query_allowed_sorts`.
     */
    public function getQueryAllowedSorts(): array
    {
        if (method_exists($this, 'setQueryAllowedSorts')) {
            return $this->setQueryAllowedSorts();
        }

        return $this->query_allowed_sorts ?? [];
    }

    /**
     * Get Model no pagination directive or configured `steward.query.full`.
     */
    public function getQueryFull(): bool
    {
        return $this->query_full ?? config('steward.query.full');
    }

    /**
     * Get Model limit for pagination or configured `steward.query.limit`.
     */
    public function getQueryLimit(): int
    {
        return $this->query_limit ?? config('steward.query.limit');
    }

    /**
     * Get Model Export class.
     */
    public function getQueryExport(): ?string
    {
        return $this->query_export ?? null;
    }

    /**
     * Get Model Resource class.
     */
    public function getQueryResource(): ?string
    {
        return $this->query_resource ?? null;
    }
}
