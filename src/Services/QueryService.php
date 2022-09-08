<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Kiwilan\Steward\Services\QueryService\QueryModule;

class QueryService
{
    /**
     * @param  QueryModule[]  $config
     *
     * @throws InvalidArgumentException
     */
    public static function boot(Builder $query, array $filters, array $config): Builder|Model
    {
        /**
         * Format Model config with keys.
         */
        $list = [];
        foreach ($config as $module) {
            $list[$module->field] = $module;
        }

        return $query->where(
            function (Builder $query) use ($filters, $list) {
                /**
                 * Parse current filters to compare with config.
                 */
                foreach ($filters as $name => $filter) {
                    /**
                     * If filter is allowed, apply config method.
                     */
                    if (array_key_exists($name, $list)) {
                        /** @var QueryModule */
                        $module = $list[$name];
                        $module->value = $filter;
                        $query = match ($module->type) {
                            'partial' => $module->wherePartial($query),
                            'exact' => $module->whereExact($query),
                            'scope' => $module->whereScope($query),
                            default => null,
                        };
                    }
                }
            }
        );
    }
}
