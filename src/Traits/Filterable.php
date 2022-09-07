<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
use Kiwilan\Steward\Services\QueryService;
use ReflectionClass;

trait Filterable
{
    protected array $default_filter_config = [
        'name' => 'partial',
    ];

    public function getFilterConfig(): array
    {
        return $this->filter_config ?? $this->default_filter_config;
    }

    public function scopeFilter(Builder $query, array $filters, ?array $configuration = []): Builder
    {
        if (empty($configuration)) {
            $configuration = $this->getFilterConfig();
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

    public function scopeOrder(Builder $query, string $field, bool $reverse = false): Builder
    {
        // $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
        $direction = $reverse ? 'desc' : 'asc';
        // $field = str_starts_with($field, '-') ? substr($field, 1) : $field;

        return $query->orderBy($field, $direction);
    }
}
