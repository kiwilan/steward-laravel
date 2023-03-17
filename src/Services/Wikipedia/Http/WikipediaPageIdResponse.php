<?php

namespace Kiwilan\Steward\Services\Wikipedia\Http;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\HttpResponse;

class WikipediaPageIdResponse
{
    protected function __construct(
        protected string $requestUrl,
        protected ?string $pageid = null,
        protected ?string $ns = null,
        protected ?string $title = null,
        protected ?string $contentmodel = null,
        protected ?string $pagelanguage = null,
        protected ?string $pagelanguagehtmlcode = null,
        protected ?string $pagelanguagedir = null,
        protected ?string $touched = null,
        protected ?string $lastrevid = null,
        protected ?string $length = null,
        protected ?string $fullurl = null,
        protected ?string $editurl = null,
        protected ?string $canonicalurl = null,
        protected ?WikipediaPageIdThumbnail $thumbnail = null,
        protected ?string $pageimage = null,
        protected ?string $extract = null,
    ) {
    }

    /**
     * Convert WikipediaPageIdResponse to Collection.
     *
     * @return Collection<int,WikipediaPageIdResponse>
     */
    public static function toCollection(HttpResponse $response): Collection
    {
        /** @var Collection<int,WikipediaPageIdResponse> */
        $collection = collect([]);

        $body = $response->toArray();

        if (! array_key_exists('query', $body) || ! array_key_exists('pages', $body['query'])) {
            return $collection;
        }

        $searchs = $body['query']['pages'];

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

    public function pageid(): ?string
    {
        return $this->pageid;
    }

    public function ns(): ?string
    {
        return $this->ns;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function contentmodel(): ?string
    {
        return $this->contentmodel;
    }

    public function pagelanguage(): ?string
    {
        return $this->pagelanguage;
    }

    public function pagelanguagehtmlcode(): ?string
    {
        return $this->pagelanguagehtmlcode;
    }

    public function pagelanguagedir(): ?string
    {
        return $this->pagelanguagedir;
    }

    public function touched(): ?string
    {
        return $this->touched;
    }

    public function lastrevid(): ?string
    {
        return $this->lastrevid;
    }

    public function length(): ?string
    {
        return $this->length;
    }

    public function fullurl(): ?string
    {
        return $this->fullurl;
    }

    public function editurl(): ?string
    {
        return $this->editurl;
    }

    public function canonicalurl(): ?string
    {
        return $this->canonicalurl;
    }

    public function thumbnail(): ?WikipediaPageIdThumbnail
    {
        return $this->thumbnail;
    }

    public function pageimage(): ?string
    {
        return $this->pageimage;
    }

    public function extract(): ?string
    {
        return $this->extract;
    }

    private static function make(array $search, string $origin): self
    {
        $thumbnail = null;

        if (array_key_exists('thumbnail', $search)) {
            $thumbnail = WikipediaPageIdThumbnail::make($search['thumbnail']);
        }

        return new self(
            requestUrl: $origin,
            pageid: $search['pageid'] ?? null,
            ns: $search['ns'] ?? null,
            title: $search['title'] ?? null,
            contentmodel: $search['contentmodel'] ?? null,
            pagelanguage: $search['pagelanguage'] ?? null,
            pagelanguagehtmlcode: $search['pagelanguagehtmlcode'] ?? null,
            pagelanguagedir: $search['pagelanguagedir'] ?? null,
            touched: $search['touched'] ?? null,
            lastrevid: $search['lastrevid'] ?? null,
            length: $search['length'] ?? null,
            fullurl: $search['fullurl'] ?? null,
            editurl: $search['editurl'] ?? null,
            canonicalurl: $search['canonicalurl'] ?? null,
            thumbnail: $thumbnail,
            pageimage: $search['pageimage'] ?? null,
            extract: $search['extract'] ?? null,
        );
    }
}

class WikipediaPageIdThumbnail
{
    protected function __construct(
        protected ?string $source = null,
        protected ?string $width = null,
        protected ?string $height = null,
    ) {
    }

    public static function make(array $thumbnail): self
    {
        return new self(
            source: $thumbnail['source'] ?? null,
            width: $thumbnail['width'] ?? null,
            height: $thumbnail['height'] ?? null,
        );
    }

    public function source(): ?string
    {
        return $this->source;
    }

    public function width(): ?string
    {
        return $this->width;
    }

    public function height(): ?string
    {
        return $this->height;
    }
}
