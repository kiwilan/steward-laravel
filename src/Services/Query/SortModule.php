<?php

namespace Kiwilan\Steward\Services\Query;

use Illuminate\Database\Eloquent\Builder;

class SortModule
{
    public function __construct(
        public string $type,
        public string $field,
        public string $label,
        public ?string $scope = null,
        public mixed $value = null,
        public bool $filter = false,
    ) {}

    public static function make(string $field, ?string $label = null): SortModule
    {
        $label ??= $field;

        return new SortModule('make', $field, $label);
    }

    public static function scope(string $field, ?string $label, string $scope): SortModule
    {
        return new SortModule('scope', $field, $label, $scope);
    }

    public function whereMake(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->field, $direction);
    }

    public function whereScope(Builder $query, string $direction = 'asc')
    {
        return $query->{$this->scope}($direction);
    }

    public function orderBy(Builder $query, string $direction = 'asc')
    {
        $type = ucfirst($this->type);

        return $this->{"where{$type}"}($query, $direction);
    }
}
