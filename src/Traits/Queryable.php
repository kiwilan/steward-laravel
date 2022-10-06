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
        return $this->query_default_sort ?? 'id';
    }

    public function getQueryDefaultSortDirection(): string
    {
        return $this->query_default_sort_direction ?? 'asc';
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
        return $this->query_size ?? 15;
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
