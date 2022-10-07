<?php

namespace Kiwilan\Steward\Utils;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class PaginatorHelper
{
    /**
     * Paginate a existing collection.
     */
    public static function paginate(iterable $items, int $size = 15, int $page = 1, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $size), $items->count(), $size, $page, $options);
    }
}
