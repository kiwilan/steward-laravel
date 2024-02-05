<?php

namespace Kiwilan\Steward\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QueryResponse
{
    /**
     * @param  QueryResponseLink[]  $links
     */
    protected function __construct(
        public ?string $sort,
        public string|array|null $filter,
        public array $data,
        public ?int $current_page = null,
        public ?string $first_page_url = null,
        public ?int $from = null,
        public ?int $last_page = null,
        public ?string $last_page_url = null,
        public ?array $links = null,
        public ?string $next_page_url = null,
        public ?string $path = null,
        public ?int $per_page = null,
        public ?string $prev_page_url = null,
        public ?int $to = null,
        public ?int $total = null,
    ) {
    }

    public static function make(LengthAwarePaginator|Collection $original, string $defaultSort): self
    {
        if ($original instanceof Collection) {
            return new self(
                sort: request()->get('sort', $defaultSort),
                filter: request()->get('filter'),
                data: $original->toArray(),
            );
        }

        $array = $original->toArray();

        $url = request()->url();
        $items = $original->items();
        $perPage = count($items);

        $self = new self(
            sort: request()->get('sort', $defaultSort),
            filter: request()->get('filter'),
            data: $items,
            current_page: $original->currentPage(),
            first_page_url: $array['first_page_url'] ?? $url,
            from: $original->firstItem() ?? 1,
            last_page: $original->lastPage(),
            last_page_url: $original->url($original->lastPage()),
            links: QueryResponseLink::toArray($array['links'] ?? []),
            next_page_url: $original->nextPageUrl(),
            path: $original->path() ?? $url,
            per_page: $original->perPage(),
            prev_page_url: $original->previousPageUrl(),
            to: $original->lastItem() ?? $perPage,
            total: $original->total(),
        );

        if (! $self->current_page) {
            $self->current_page = 1;
        }

        if (! $self->last_page) {
            $self->last_page = 1;
        }

        if (! $self->last_page_url) {
            $self->last_page_url = $url;
        }

        if (! $self->per_page) {
            $self->per_page = $perPage;
        }

        if (! $self->total) {
            $self->total = $perPage;
        }

        return $self;
    }
}

class QueryResponseLink
{
    protected function __construct(
        public ?string $url,
        public ?string $label,
        public bool $active = false,
    ) {
    }

    public static function toArray(array $links): array
    {
        return array_map(fn ($link) => self::make($link), $links);
    }

    public static function make(array $original): self
    {
        return new self(
            url: $original['url'] ?? null,
            label: $original['label'] ?? null,
            active: $original['active'] ?? null,
        );
    }
}
