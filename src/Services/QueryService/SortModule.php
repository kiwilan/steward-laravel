<?php

namespace Kiwilan\Steward\Services\QueryService;

use Illuminate\Database\Eloquent\Builder;

class SortModule
{
    public function __construct(
        public string $type,
        public string $field,
        public ?string $scope = null,
        public mixed $value = null,
        public bool $filter = false,
    ) {
    }

    public static function scope(string $field, string $scope): SortModule
    {
        return new SortModule('scope', $field, $scope);
    }

    public function whereScope(Builder $query)
    {
        return $query->{$this->scope}($this->value);
    }
}
