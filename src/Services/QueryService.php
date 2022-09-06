<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Builder;

class QueryService
{
    public static function boot(Builder $query, string $model, array $config, array $filters)
    {
        $service = new QueryService();

        return $query->where(
            function (Builder $query) use ($filters, $config, $service) {
                foreach ($filters as $field => $value) {
                    $mode = array_key_exists($field, $config) ? $config[$field] : 'exact';
                    $query = match ($mode) {
                        'partial' => $service->wherePartial($query, $field, $value),
                        default => $service->whereExact($query, $field, $value),
                    };
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
}
