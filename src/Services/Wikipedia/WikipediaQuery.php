<?php

namespace Kiwilan\Steward\Services\Wikipedia;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Services\WikipediaService;

/**
 * Create WikipediaQuery from Model and ISBN
 */
class WikipediaQuery
{
    protected function __construct(
        protected int $identifier = 0,
        protected ?string $queryString = null,
        protected ?string $language = 'en',
        protected ?string $queryUrl = null,
        protected ?WikipediaItem $item = null,
    ) {
    }

    /**
     * Create WikipediaQuery from $queryString, $modelId, $language and WikipediaService.
     */
    public static function make(string $queryString, int $identifier, string $language = 'en'): self
    {
        $self = new WikipediaQuery(
            queryString: $queryString,
            identifier: $identifier,
            language: $language,
        );
        $self->queryUrl = $self->setQueryUrl();

        return $self;
    }

    public static function buildPageIdUrl(string $pageId, string $language = 'en'): string
    {
        // current search: http://fr.wikipedia.org/w/api.php?action=query&prop=info&pageids=1340228&inprop=url&format=json&prop=info|extracts|pageimages&pithumbsize=512
        $url = "http://{$language}.wikipedia.org/w/api.php?";
        $queries = [
            'action' => 'query',
            'pageids' => $pageId,
            'inprop' => 'url',
            'format' => 'json',
            'prop' => 'info|extracts|pageimages',
            'pithumbsize' => 512,
        ];

        return $url.http_build_query($queries);
    }

    public function identifier(): int
    {
        return $this->identifier;
    }

    public function queryString(): ?string
    {
        return $this->queryString;
    }

    public function language(): ?string
    {
        return $this->language;
    }

    public function queryUrl(): ?string
    {
        return $this->queryUrl;
    }

    public function item(): ?WikipediaItem
    {
        return $this->item;
    }

    /**
     * Build Wikipedia query URL from `queryString` and `language`.
     */
    private function setQueryUrl(): string
    {
        // generator search images: https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrsearch=Jul%20Maroh&gsrprop=snippet&prop=imageinfo&iiprop=url&rawcontinue&gsrnamespace=6&format=json
        // generator search: https://en.wikipedia.org/w/api.php?action=query&generator=search&gsrsearch=Baxter%20Stephen&prop=info|extracts|pageimages&format=json
        // current search: https://fr.wikipedia.org/w/api.php?action=query&list=search&srsearch=intitle:Les%20Annales%20du%20Disque-Monde&format=json
        $baseURL = "https://{$this->language}.wikipedia.org/w/api.php?";
        $queries = [
            'action' => 'query',
            'list' => 'search',
            'srsearch' => "intitle:{$this->queryString}",
            'format' => 'json',
        ];

        return $baseURL.http_build_query($queries);
    }
}
