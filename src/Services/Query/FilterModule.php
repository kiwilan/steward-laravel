<?php

namespace Kiwilan\Steward\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use Kiwilan\Steward\Queries\Filter\GlobalSearchFilter;
use Spatie\QueryBuilder\Filters\Filter;

class FilterModule
{
    public function __construct(
        public string $type,
        public string $field,
        public ?string $scope = null,
        public mixed $value = null,
        public bool $filter = false,
        public ?GlobalSearchFilter $global = null,
        public ?Filter $spatieFilter = null,
    ) {}

    public static function partial(string $field): FilterModule
    {
        return new FilterModule('partial', $field);
    }

    /**
     * @param  string[]  $global
     */
    public static function search(string $field, array $global): FilterModule
    {
        $global = new GlobalSearchFilter($global);

        return new FilterModule('search', $field, global: $global);
    }

    public static function custom(string $field, Filter $spatieFilter): FilterModule
    {
        return new FilterModule('custom', $field, spatieFilter: $spatieFilter);
    }

    public static function exact(string $field): FilterModule
    {
        return new FilterModule('exact', $field);
    }

    public static function scope(string $field, string $scope): FilterModule
    {
        return new FilterModule('scope', $field, $scope);
    }

    public function whereSearch(Builder $query, GlobalSearchFilter $global)
    {
        return $global($query, $this->value, $this->field);
    }

    public function whereCustom(Builder $query, Filter $global)
    {
        return $global($query, $this->value, $this->field);
    }

    public function wherePartial(Builder $query)
    {
        return $query->where($this->field, 'like', "%{$this->value}%");
    }

    public function whereExact(Builder $query)
    {
        return $query->where($this->field, '=', $this->value);
    }

    public function whereScope(Builder $query)
    {
        return $query->{$this->scope}($this->value);
    }
}
