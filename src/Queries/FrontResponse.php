<?php

namespace Kiwilan\Steward\Queries;

class FrontResponse
{
    /**
     * @param  FrontResponseLink[]  $links
     */
    protected function __construct(
        public ?string $sort,
        public ?string $filter,
        public array $data,
        public int $current_page,
        public ?string $first_page_url,
        public int $from,
        public int $last_page,
        public ?string $last_page_url,
        public array $links,
        public ?string $next_page_url,
        public ?string $path,
        public int $per_page,
        public ?string $prev_page_url,
        public int $to,
        public int $total,
    ) {
    }

    public static function make(array $original, string $defaultSort): self
    {
        $data = $original['data'];
        unset($original['data']);

        return new self(
            sort: request()->get('sort', $defaultSort),
            filter: request()->get('filter'),

            data: $data ?? [],
            // rad stack
            // $this->metadata()->classSnakePlural() => fn () => $this->collection(),

            current_page: $original['current_page'] ?? null,
            first_page_url: $original['first_page_url'] ?? null,
            from: $original['from'] ?? null,
            last_page: $original['last_page'] ?? null,
            last_page_url: $original['last_page_url'] ?? null,
            links: FrontResponseLink::toArray($original['links'] ?? []),
            next_page_url: $original['next_page_url'] ?? null,
            path: $original['path'] ?? null,
            per_page: $original['per_page'] ?? null,
            prev_page_url: $original['prev_page_url'] ?? null,
            to: $original['to'] ?? null,
            total: $original['total'] ?? null,
        );
    }
}

class FrontResponseLink
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
