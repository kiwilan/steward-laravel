<?php

namespace Kiwilan\Steward\Services\QueryService;

use Illuminate\Database\Eloquent\Builder;

class QueryModule
{
    public function __construct(
        public string $type,
        public string $field,
        public ?string $scope = null,
        public mixed $value = null,
        public bool $filter = false,
    ) {
    }

    public static function partial(string $field): QueryService
    {
        return new QueryService('partial', $field);
    }

    public static function exact(string $field): QueryService
    {
        return new QueryService('exact', $field);
    }

    public static function scope(string $field, string $scope): QueryService
    {
        return new QueryService('scope', $field, $scope);
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
