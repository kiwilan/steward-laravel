<?php

namespace Kiwilan\Steward\Utils\Wikipedia;

use Kiwilan\Steward\Utils\Wikipedia\Models\WikipediaModelPage;
use Kiwilan\Steward\Utils\Wikipedia\Models\WikipediaModelSearch;

/**
 * Create WikipediaQuery from object
 */
class WikipediaQuery
{
    /**
     * @param  WikipediaModelSearch[]  $searchResults
     * @param  array<string>  $precision
     */
    protected function __construct(
        protected ?string $subject = null,
        protected ?int $identifier = null,
        protected string $language = 'en',
        protected ?string $querySearch = null,
        protected ?string $queryPage = null,
        protected ?string $pageId = null,
        protected bool $exact = false,
        protected array $precision = [],
        protected bool $preventSearch = false,
        protected bool $isAvailable = false,
        protected array $searchResults = [],
        protected ?WikipediaModelPage $modelPage = null,
    ) {}

    /**
     * Create WikipediaQuery.
     */
    public static function make(string $subject): self
    {
        return new self($subject);
    }

    /**
     * Set language to use for Wikipedia API.
     *
     * @default `en` for english
     */
    public function language(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set identifier to identify WikipediaQuery.
     */
    public function identifier(int $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Set precision to use for Wikipedia API.
     *
     * Example: `['author', 'writer', 'novelist']` for author, this option will search into page title and page content
     *
     * @default `[]` for no precision
     *
     * @param  array<string>  $precision
     */
    public function precision(array $precision): self
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Set exact to use for Wikipedia API.
     *
     * Example: `true` for accept near match, this option will accept pages with title near to subject.
     *
     * @default `false` for no exact
     */
    public function exact(): self
    {
        $this->exact = true;

        return $this;
    }

    public function preventSearch(): self
    {
        $this->preventSearch = true;

        return $this;
    }

    /**
     * Execute Wikipedia API calls.
     */
    public function get(): self
    {
        $this->querySearch = $this->setQuerySearch();
        $search = $this->search($this->querySearch);
        $this->searchResults = WikipediaModelSearch::fromRequest($search, $this->querySearch);

        if (empty($this->searchResults)) {
            return $this;
        }

        $result = $this->selectResult();

        if (! $result) {
            return $this;
        }

        $this->pageId = $result->getPageid();

        if (! $this->pageId) {
            return $this;
        }

        $this->queryPage = $this->setQueryPageId($this->pageId);
        $pageResult = $this->search($this->queryPage);
        if (! $pageResult) {
            return $this;
        }

        $this->modelPage = WikipediaModelPage::fromRequest($pageResult, $this->queryPage);
        $this->isAvailable = true;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getIdentifier(): ?int
    {
        return $this->identifier;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getQuerySearch(): ?string
    {
        return $this->querySearch;
    }

    public function getQueryPage(): ?string
    {
        return $this->queryPage;
    }

    public function getPageId(): ?string
    {
        return $this->pageId;
    }

    public function getUseExact(): bool
    {
        return $this->exact;
    }

    public function getPrecision(): array
    {
        return $this->precision;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function getSearchResults(): array
    {
        return $this->searchResults;
    }

    public function getModelPage(): ?WikipediaModelPage
    {
        return $this->modelPage;
    }

    /**
     * Build Wikipedia query URL from `queryString` and `language`.
     */
    private function setQuerySearch(): string
    {
        $this->subject = strtolower($this->subject);

        // generator search images: https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrsearch=Jul%20Maroh&gsrprop=snippet&prop=imageinfo&iiprop=url&rawcontinue&gsrnamespace=6&format=json
        // generator search: https://en.wikipedia.org/w/api.php?action=query&generator=search&gsrsearch=Baxter%20Stephen&prop=info|extracts|pageimages&format=json
        // current search: https://fr.wikipedia.org/w/api.php?action=query&list=search&srsearch=intitle:Les%20Annales%20du%20Disque-Monde&format=json
        $baseURL = "https://{$this->language}.wikipedia.org/w/api.php?";
        $queries = [
            'action' => 'query',
            'list' => 'search',
            'srsearch' => "intitle:{$this->subject}",
            'format' => 'json',
        ];

        return $baseURL.http_build_query($queries);
    }

    private function setQueryPageId(string $pageId): string
    {
        // current search: http://fr.wikipedia.org/w/api.php?action=query&prop=info&pageids=1340228&inprop=url&format=json&prop=info|extracts|pageimages&pithumbsize=512
        $baseURL = "http://{$this->language}.wikipedia.org/w/api.php?";
        $queries = [
            'action' => 'query',
            'pageids' => $pageId,
            'inprop' => 'url',
            'format' => 'json',
            'prop' => 'info|extracts|pageimages',
            'pithumbsize' => 512,
        ];

        return $baseURL.http_build_query($queries);
    }

    private function selectResult(): ?WikipediaModelSearch
    {
        if (empty($this->searchResults)) {
            return null;
        }

        $result = $this->matchResult();

        if (! $this->exact && ! $result) { // if accept near match and no result, return first result
            return $this->searchResults[0] ?? null;
        } elseif ($result) {
            return $result;
        }

        return null;
    }

    private function matchResult(): ?WikipediaModelSearch
    {
        if (empty($this->searchResults)) {
            return null;
        }

        if (count($this->precision) > 0) {
            foreach ($this->searchResults as $result) {
                $title = $this->removeSpecialCharacters($result->getTitle());
                $subject = $this->removeSpecialCharacters($this->subject);
                $extract = strip_tags($result->getSnippet());

                $titleParts = explode(' ', $title);

                foreach ($titleParts as $titlePart) {
                    if (in_array($titlePart, $this->precision)) {
                        return $result;
                    }
                }

                foreach ($this->precision as $precision) {
                    if (str_contains($title, $precision)) {
                        return $result;
                    }

                    if (str_contains($subject, $precision)) {
                        return $result;
                    }

                    if (str_contains($extract, $precision)) {
                        return $result;
                    }
                }
            }
        }

        foreach ($this->searchResults as $result) {
            $title = $this->removeSpecialCharacters($result->getTitle());
            $subject = $this->removeSpecialCharacters($this->subject);

            if ($title === $subject) {
                return $result;
            }

            // check if title is in subject
            if (str_contains($title, $subject)) {
                return $result;
            }

            // if title contains only two words, reverse title and check if title is in subject
            $titleParts = explode(' ', $title);

            if (count($titleParts) === 2) {
                $title = implode(' ', array_reverse($titleParts));

                if (str_contains($title, $subject)) {
                    return $result;
                }
            }

            if (in_array($title, explode(' ', $subject))) {
                return $result;
            }
        }

        return null;
    }

    private function removeSpecialCharacters(string $text): string
    {
        $regex = '/[^a-z0-9 ]/i';
        $text = preg_replace($regex, ' ', $text);
        $text = preg_replace('/\s\s+/', ' ', $text);

        return strtolower($text);
    }

    private function search(string $url): ?array
    {
        $client = WikipediaClient::make($url);

        return $client->getBody();
    }
}
