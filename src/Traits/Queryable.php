<?php

namespace Kiwilan\Steward\Traits;

trait Queryable
{
    public function getQueryWith(): array
    {
        return $this->query_with ?? $this->with ?? [];
    }

    public function getQueryWithCount(): array
    {
        return $this->query_with_count ?? $this->with_count ?? [];
    }

    public function getQueryDefaultSort(): string
    {
        return $this->query_default_sort ?? config('steward.query.default_sort');
    }

    public function getQueryDefaultSortDirection(): string
    {
        return $this->query_default_sort_direction ?? config('steward.query.default_sort_direction');
    }

    public function getQueryAllowedFilters(): array
    {
        return $this->query_allowed_filters ?? [];
    }

    public function getQueryAllowedSorts(): array
    {
        return $this->query_allowed_sorts ?? [];
    }

    public function getQuerySize(): int
    {
        return $this->query_size ?? config('steward.query.size');
    }

    public function getQueryExport(): ?string
    {
        return $this->query_export ?? null;
    }

    public function getQueryResource(): ?string
    {
        return $this->query_resource ?? null;
    }
}
