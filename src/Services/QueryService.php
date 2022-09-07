<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QueryService
{
    public static function boot(Builder $query, array $filters, array $config): Builder|Model
    {
        $service = new QueryService();

        return $query->where(
            function (Builder $query) use ($filters, $config, $service) {
                foreach ($filters as $field => $value) {
                    $mode = array_key_exists($field, $config) ? $config[$field] : 'exact';
                    if ($value) {
                        $query = match ($mode) {
                            'partial' => $service->wherePartial($query, $field, $value),
                            'exact' => $service->whereExact($query, $field, $value),
                            default => $service->whereScope($query, $field, $value, $mode),
                        };
                    }
                }
            }
        );
    }

    private function wherePartial(Builder $query, string $field, string $value)
    {
        return $query->where($field, 'like', "%{$value}%");
    }

    private function whereExact(Builder $query, string $field, string $value)
    {
        return $query->where($field, '=', $value);
    }

    private function whereScope(Builder $query, string $field, string $value, array $scope)
    {
        $scope = $scope[1];
        return $query->{$scope}($value);
    }
}
