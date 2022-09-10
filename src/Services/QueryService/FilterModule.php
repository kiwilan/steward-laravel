<?php

namespace Kiwilan\Steward\Services\QueryService;

use Illuminate\Database\Eloquent\Builder;

class FilterModule
{
    public function __construct(
        public string $type,
        public string $field,
        public ?string $scope = null,
        public mixed $value = null,
        public bool $filter = false,
    ) {
    }

    public static function partial(string $field): FilterModule
    {
        return new FilterModule('partial', $field);
    }

    public static function exact(string $field): FilterModule
    {
        return new FilterModule('exact', $field);
    }

    public static function scope(string $field, string $scope): FilterModule
    {
        return new FilterModule('scope', $field, $scope);
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
