<?php

namespace Kiwilan\Steward\Utils\Wikipedia\Models;

class WikipediaModelPage
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
        protected ?WikipediaModelPageThumbnail $thumbnail = null,
        protected ?string $pageimage = null,
        protected ?string $extract = null,
    ) {}

    /**
     * Convert WikipediaModelPage.
     */
    public static function fromRequest(array $body, string $request): ?WikipediaModelPage
    {
        if (! array_key_exists('query', $body) || ! array_key_exists('pages', $body['query'])) {
            return null;
        }

        $pages = $body['query']['pages'];
        $pages = array_values($pages);

        return WikipediaModelPage::make($pages[0], $request);
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

    public function getThumbnail(): ?WikipediaModelPageThumbnail
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

    private static function make(array $body, string $origin): self
    {
        $thumbnail = null;

        if (array_key_exists('thumbnail', $body)) {
            $thumbnail = WikipediaModelPageThumbnail::make($body['thumbnail']);
        }

        return new self(
            requestUrl: $origin,
            pageid: $body['pageid'] ?? null,
            ns: $body['ns'] ?? null,
            title: $body['title'] ?? null,
            contentmodel: $body['contentmodel'] ?? null,
            pagelanguage: $body['pagelanguage'] ?? null,
            pagelanguagehtmlcode: $body['pagelanguagehtmlcode'] ?? null,
            pagelanguagedir: $body['pagelanguagedir'] ?? null,
            touched: $body['touched'] ?? null,
            lastrevid: $body['lastrevid'] ?? null,
            length: $body['length'] ?? null,
            fullurl: $body['fullurl'] ?? null,
            editurl: $body['editurl'] ?? null,
            canonicalurl: $body['canonicalurl'] ?? null,
            thumbnail: $thumbnail,
            pageimage: $body['pageimage'] ?? null,
            extract: $body['extract'] ?? null,
        );
    }
}
