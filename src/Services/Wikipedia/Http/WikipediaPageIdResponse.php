<?php

namespace Kiwilan\Steward\Services\Wikipedia\Http;

use Illuminate\Support\Collection;
use Kiwilan\HttpPool\Response\HttpPoolResponse;

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
    public static function toCollection(HttpPoolResponse $response): Collection
    {
        /** @var Collection<int,WikipediaPageIdResponse> */
        $collection = collect([]);

        $body = $response->getBody()->toArray();

        if (! array_key_exists('query', $body) || ! array_key_exists('pages', $body['query'])) {
            return $collection;
        }

        $searchs = $body['query']['pages'];

        foreach ($searchs as $search) {
            $collection->push(
                self::make($search, $response->getMetadata()->getRequest()),
            );
        }

        return $collection;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getPageId(): ?string
    {
        return $this->pageid;
    }

    public function getNs(): ?string
    {
        return $this->ns;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContentModel(): ?string
    {
        return $this->contentmodel;
    }

    public function getPageLanguage(): ?string
    {
        return $this->pagelanguage;
    }

    public function getPageLanguageHtmlCode(): ?string
    {
        return $this->pagelanguagehtmlcode;
    }

    public function getPageLanguageDir(): ?string
    {
        return $this->pagelanguagedir;
    }

    public function getTouched(): ?string
    {
        return $this->touched;
    }

    public function getLastRevid(): ?string
    {
        return $this->lastrevid;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullurl;
    }

    public function getEditUrl(): ?string
    {
        return $this->editurl;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalurl;
    }

    public function getThumbnail(): ?WikipediaPageIdThumbnail
    {
        return $this->thumbnail;
    }

    public function getPageImage(): ?string
    {
        return $this->pageimage;
    }

    public function getExtract(): ?string
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

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }
}
