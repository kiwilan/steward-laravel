<?php

namespace Kiwilan\Steward\Tests\Data\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kiwilan\Steward\Queries\Filter\GlobalSearchFilter;
use Kiwilan\Steward\Queries\Sort\StringLengthSort;
use Kiwilan\Steward\Traits\Queryable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class Author extends Model
{
    use Queryable;

    protected $queryNoPaginate = true;

    protected $fillable = [
        'id',
        'name',
    ];

    protected function setQueryAllowedSorts(): array
    {
        return [
            AllowedSort::field('name'),
            AllowedSort::custom('name-length', new StringLengthSort, 'name'),
        ];
    }

    protected function setQueryAllowedFilters(): array
    {
        return [
            // Global search
            AllowedFilter::custom('q', new GlobalSearchFilter(['name', 'id'])),
            // Exact filter
            AllowedFilter::exact('id'),
            // Partial filter
            AllowedFilter::partial('name'),
            // Advanced filter
            AllowedFilter::callback('books', fn (Builder $query, $value) => $query->whereHas('books', fn (Builder $query) => $query->where('title', 'like', "%{$value}%"))),
        ];
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
