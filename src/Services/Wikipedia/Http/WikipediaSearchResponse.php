<?php

namespace Kiwilan\Steward\Services\Wikipedia\Http;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\HttpResponse;

class WikipediaSearchResponse
{
    protected function __construct(
        protected string $requestUrl,
        protected ?string $ns = null,
        protected ?string $title = null,
        protected ?string $pageid = null,
        protected ?string $size = null,
        protected ?string $wordcount = null,
        protected ?string $snippet = null,
        protected ?string $timestamp = null,
    ) {
    }

    /**
     * Convert WikipediaSearchResponse to Collection.
     *
     * @return Collection<int,WikipediaSearchResponse>
     */
    public static function toCollection(HttpResponse $response): Collection
    {
        /** @var Collection<int,WikipediaSearchResponse> */
        $collection = collect([]);

        $body = $response->toArray();

        if (! array_key_exists('query', $body) || ! array_key_exists('search', $body['query'])) {
            return $collection;
        }

        $searchs = $body['query']['search'];

        foreach ($searchs as $search) {
            $collection->push(
                self::make($search, $response->metadata()->origin()),
            );
        }

        return $collection;
    }

    public function requestUrl(): string
    {
        return $this->requestUrl;
    }

    public function ns(): ?string
    {
        return $this->ns;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function pageid(): ?string
    {
        return $this->pageid;
    }

    public function size(): ?string
    {
        return $this->size;
    }

    public function wordcount(): ?string
    {
        return $this->wordcount;
    }

    public function snippet(): ?string
    {
        return $this->snippet;
    }

    public function timestamp(): ?string
    {
        return $this->timestamp;
    }

    private static function make(array $search, string $origin): self
    {
        return new self(
            requestUrl: $origin,
            ns: $search['ns'] ?? null,
            title: $search['title'] ?? null,
            pageid: $search['pageid'] ?? null,
            size: $search['size'] ?? null,
            wordcount: $search['wordcount'] ?? null,
            snippet: $search['snippet'] ?? null,
            timestamp: $search['timestamp'] ?? null,
        );
    }
}
