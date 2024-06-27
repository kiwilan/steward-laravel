<?php

namespace Kiwilan\Steward\Utils\Wikipedia\Models;

use Illuminate\Support\Collection;

class WikipediaModelSearch
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
    ) {}

    /**
     * Convert WikipediaModelSearch to Collection.
     *
     * @return WikipediaModelSearch[]
     */
    public static function fromRequest(array $body, string $request): array
    {
        $items = [];

        if (! array_key_exists('query', $body) || ! array_key_exists('search', $body['query'])) {
            return $items;
        }

        $searchs = $body['query']['search'];

        foreach ($searchs as $search) {
            $items[] = WikipediaModelSearch::make($search, $request);
        }

        return $items;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getNs(): ?string
    {
        return $this->ns;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getPageid(): ?string
    {
        return $this->pageid;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function getWordcount(): ?string
    {
        return $this->wordcount;
    }

    public function getSnippet(): ?string
    {
        return $this->snippet;
    }

    public function getTimestamp(): ?string
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
