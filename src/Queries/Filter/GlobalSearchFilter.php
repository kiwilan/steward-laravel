<?php

namespace Kiwilan\Steward\Queries\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class GlobalSearchFilter implements Filter
{
    public function __construct(
        public array $fields,
    ) {
    }

    public function __invoke(Builder $query, $value, string $property)
    {
        $fields = collect($this->fields);
        $relations = $fields->filter(fn ($field) => str_contains($field, '.'))
            ->map(fn ($field) => explode('.', $field)[0])
            ->unique()
            ->toArray();

        $query->with($relations)
            ->where(function (Builder $query) use ($fields, $value) {
                foreach ($fields as $field) {
                    if (str_contains($field, '.')) {
                        $field = explode('.', $field);
                        $query->orWhereHas(
                            $field[0],
                            fn (Builder $query) => $query->where($field[1], 'like', "%{$value}%")
                        );
                    } else {
                        $query->orWhere($field, 'like', "%{$value}%");
                    }
                }
            });
    }
}
