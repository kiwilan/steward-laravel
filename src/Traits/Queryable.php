<?php

namespace Kiwilan\Steward\Traits;

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
