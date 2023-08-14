<?php

namespace Kiwilan\Steward\Queries\Sort;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class StringLengthSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->orderByRaw("LENGTH(`{$property}`) {$direction}");
    }
}
