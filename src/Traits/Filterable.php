<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
use Kiwilan\Steward\Services\QueryService;
use ReflectionClass;

trait Filterable
{
    // protected $default_role_column = 'role';

    // public function initializeHasRole()
    // {
    //     $this->fillable[] = $this->getRoleColumn();
    //     $this->fillable[] = 'is_blocked';

    //     $this->casts[$this->getRoleColumn()] = UserRoleEnum::class;
    //     $this->casts['is_blocked'] = 'boolean';
    // }

    public function scopeFilter(Builder $query, array $filters)
    {
        // $queryAll = null;
        // foreach ($filters as $field => $value) {
        //     $queryAll[$field] = $query->where($field, $value);
        // }
        // // dd($queryAll);
        // // return $query->where('status', '=', $status);
        // return $queryAll;

        $queryConfig = [
            'name' => 'partial',
            'status' => 'exact',
        ];
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
        $instance = new $this();
        $class = new ReflectionClass($instance);

        $service = QueryService::boot($query, $class->getName(), $queryConfig, $filters);

        return $service;
    }
}
