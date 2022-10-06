<?php

namespace Kiwilan\Steward\Traits;

trait Queryable
{
    public function initializeQueryable()
    {
        // $this->fillable[] = $this->getSortColumn();

        // $this->casts[$this->getSortColumn()] = 'integer';
    }

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

    public function getQueryAllowedFilters(): array
    {
        return $this->query_allowed_filters ?? [];
    }

    public function getQueryAllowedSorts(): array
    {
        return $this->query_allowed_sorts ?? [];
    }

    public function getQueryExport(): string
    {
        return $this->query_export ?? null;
    }

    public function getQueryCollectionResource(): string
    {
        return $this->query_collection_resource ?? null;
    }

    public function getQueryResource(): string
    {
        return $this->query_resource ?? null;
    }

    public function getQueryPagination(): int
    {
        return $this->query_pagination ?? 15;
    }
}
