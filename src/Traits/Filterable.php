<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
use Kiwilan\Steward\Services\QueryService;
use ReflectionClass;

trait Filterable
{
    public function getFilterable(): array
    {
        return $this->filterable() ?? [];
    }

    public function scopeFilter(Builder $query, array $filters, ?array $configuration = []): Builder
    {
        if (empty($configuration)) {
            $configuration = $this->getFilterable();
        }

        // $queryAll = null;
        // foreach ($filters as $field => $value) {
        //     $queryAll[$field] = $query->where($field, $value);
        // }
        // // dd($queryAll);
        // // return $query->where('status', '=', $status);
        // return $queryAll;
        // dump($filters);

        // $manual = ModelsMiniature::where(
        //     function (Builder $query) use ($filters) {
        //         return $query
        //             ->where('name', 'like', "%{$filters['name']}%")
        //             ->where('status', '=', $filters['status'])
        //             // ->where('price', '>', $filters['min_price'])
        //         ;
        //     }
        // )
        //     ->get()
        // ;
        // dump($manual);
        // $instance = new $this();
        // $class = new ReflectionClass($instance);
        return QueryService::boot($query, $filters, $configuration);
    }

    public function scopeSort(Builder $query, string $field, bool $reverse = false): Builder
    {
        $class = new ReflectionClass($this);
        $instance = $class->getName();
        if (method_exists($instance, 'sortable')) {
            $sortable = $instance::sortable();
        }
        if (! array_key_exists($field, $sortable)) {
            return $query;
        }

        $direction = $reverse ? 'desc' : 'asc';
        $current = $sortable[$field];
        if (! is_string($current)) {
            return $query->{$current->scope}($direction);
        }

        return $query->orderBy($field, $direction);
    }
}
