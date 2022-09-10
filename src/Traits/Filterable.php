<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
use Kiwilan\Steward\Services\QueryService;
use Kiwilan\Steward\Services\QueryService\SortModule;
use ReflectionClass;

trait Filterable
{
    public function scopeFilter(Builder $query, array $filters, ?array $configuration = []): Builder
    {
        if (empty($configuration)) {
            $configuration = $this->filterable();
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

        $sortable = array_filter($sortable, fn (SortModule $sort) => $sort->field === $field);
        if (empty($sortable)) {
            return $query;
        }
        $current = array_shift($sortable);

        $direction = $reverse ? 'desc' : 'asc';

        return $current->orderBy($query, $direction);
    }

    /**
     * @return \Kiwilan\Steward\Services\QueryService\SortModule[]
     */
    public static function sortable()
    {
        // @return (string|\Kiwilan\Steward\Services\QueryService\SortModule)[]
        return [];
    }

    /**
     * @return string[]
     */
    public static function getSortable()
    {
        $list = [];
        foreach (self::sortable() as $sort_module) {
            $list[$sort_module->field] = $sort_module->label;
        }

        return $list;
    }

    /**
     * @return \Kiwilan\Steward\Services\QueryService\FilterModule[]
     */
    public static function filterable()
    {
        return [];
    }
}
