<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kiwilan\HttpPool\HttpPool;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\HttpPool\Utils\PrintConsole;
use Kiwilan\Steward\Services\Wikipedia\WikipediaItem;
use Kiwilan\Steward\Services\Wikipedia\WikipediaQuery;

/**
 * Use Wikipedia to get some data about authors and series.
 * Documentation (in french) from https://korben.info/comment-utiliser-lapi-de-recherche-de-wikipedia.html.
 *
 * For each Wikipedia search, need to execute two API calls to search to get page id and to parse page id data.
 */
class WikipediaService
{
    /** @var array<string> */
    protected array $queryAttributes = [];

    /** @var ?Collection<int,object> */
    protected ?Collection $original = null;

    /** @var ?Collection<int,WikipediaQuery> */
    protected ?Collection $queries = null;

    /** @var ?Collection<int,WikipediaItem> */
    protected ?Collection $items = null;

    /** @var ?array<string> */
    protected ?array $precisionQuery = null;

    protected function __construct(
        protected string $language = 'en',
        protected ?string $languageAttribute = null,
        protected string $identifier = 'id',
        protected int $count = 0,
        protected bool $debug = false,
    ) {
        $this->original = collect([]);
        $this->items = collect([]);
    }

    /**
     * Create WikipediaService from Model and create WikipediaQuery for each entity only if hasn't WikipediaItem.
     */
    public static function make(Collection $data): self
    {
        $self = new self();
        $self->original = $data;
        $self->count = $self->original->count();

        return $self;
    }

    /**
     * Set attributes to search on Wikipedia, can be unique or multiple for concat search.
     *
     * For example, if you have `lastname` and `firstname` attributes, you can set `setQueryAttributes(['lastname', 'firstname'])` to search with `lastname firstname`.
     *
     * @param  string|string[]  $attributes
     */
    public function setQueryAttributes(mixed $attributes = []): self
    {
        $list = [];

        if (is_string($attributes)) {
            $list[] = $attributes;
        } else {
            $list = $attributes;
        }

        $this->queryAttributes = $list;

        return $this;
    }

    /**
     * In Wikipedia research, you can set precision to search on specific category.
     *
     * For example, if you want to search only on authors, you can set `setPrecisionQuery('author')`.
     * If some page title contains `author`, this result will be selected over other results.
     *
     * You can use array to set multiple options, like `setPrecisionQuery(['author', 'actor'])`.
     * If `author` result if found, this result will be selected, even if `actor` result is found because `author` is first in array.
     *
     * Otherwise, the first result will be selected.
     */
    public function setPrecisionQuery(string|array $precisionQuery): self
    {
        if (is_string($precisionQuery)) {
            $precisionQuery = [$precisionQuery];
        }

        $this->precisionQuery = $precisionQuery;

        return $this;
    }

    /**
     * Set language attribute, present in each object or not, to use on Wikipedia.
     *
     * Fallback is English, you can use `setLanguage` to set a different language (available on Wikipedia).
     */
    public function setLanguageAttribute(string $attribute): self
    {
        $this->languageAttribute = $attribute;

        return $this;
    }

    /**
     * Set global language to use on Wikipedia, prefix in URL like `en` in `en.wikipedia.org`
     *
     * Fallback is `en`, you can use `setLanguageAttribute` to set language attribute for each object.
     * Check if exists on https://en.wikipedia.org/wiki/List_of_Wikipedias.
     *
     * `setLanguage` and `setLanguageAttribute` are independent, you can use both, `setLanguage` will be fallback if `setLanguageAttribute` is not set.
     */
    public function setLanguage(string $language = 'en'): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set debug mode.
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Set unique identifier of the model.
     *
     * @param  string  $identifier  Default is `id`
     */
    public function setIdentifier(string $identifier = 'id'): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return Collection<int,WikipediaItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Execute WikipediaService.
     */
    public function execute(): self
    {
        $this->queries = $this->setQueries();

        $console = PrintConsole::make();

        $console->print('List of query URL available, requests from query URL to get page id.');

        $http = HttpPool::make($this->queries)
            ->setIdentifierKey('identifier')
            ->setUrlKey('queryUrl')
            ->allowPrintConsole()
            ->execute()
        ;

        $queryItems = $this->setQueryItems($http->getResponses());

        $console->print('List of page id URL available, requests from page id URL to get extra content.');

        $http = HttpPool::make($queryItems)
            ->setIdentifierKey('identifier')
            ->setUrlKey('pageUrl')
            ->allowPrintConsole()
            ->execute()
        ;

        $queryItems = $queryItems->filter(fn ($item) => $item);
        $pageIdItems = $this->setPageIdItems($http->getResponses(), $queryItems);

        $console->print('Convert into WikipediaItem...');

        $this->items = $this->setItems($pageIdItems);

        return $this;
    }

    /**
     * Create `WikipediaItem` from `HttpPoolResponse`.
     *
     * @param  Collection<int,HttpPoolResponse>  $responses  Response from Wikipedia API
     * @return Collection<int,WikipediaItem>
     */
    private function setQueryItems(Collection $responses)
    {
        /** @var Collection<int,WikipediaItem> */
        $items = collect([]);

        foreach ($responses as $id => $response) {
            if ($this->debug) {
                $this->print($response, 'wikipedia-pageid', $id);
            }
            $item = WikipediaItem::make($response, $this->precisionQuery);
            $items->put($id, $item);
        }

        return $items;
    }

    /**
     * Attach `WikipediaItem` to page ID from `HttpPoolResponse`.
     *
     * @param  Collection<int,HttpPoolResponse>  $responses  Response from Wikipedia API
     * @param  Collection<int,WikipediaItem>  $queryItems  List of `WikipediaItem`
     * @return Collection<int,WikipediaItem>
     */
    private function setPageIdItems(Collection $responses, Collection $queryItems)
    {
        /** @var Collection<int,WikipediaItem> */
        $items = collect([]);

        foreach ($responses as $id => $response) {
            /** @var WikipediaItem */
            $item = $queryItems->first(fn (WikipediaItem $item) => $item->getIdentifier() == $id);

            if ($this->debug) {
                $this->print($response, 'wikipedia', $id);
            }
            $item = WikipediaItem::makePageId($response, $item);
            $items->put($id, $item);
        }

        return $items;
    }

    /**
     * Create `WikipediaItem[]`.
     *
     * @param  Collection<int,WikipediaItem>  $pageIdItems  List of `WikipediaItem`
     * @return Collection<int,WikipediaItem>
     */
    private function setItems(Collection $pageIdItems)
    {
        /** @var Collection<int,WikipediaItem> */
        $items = collect([]);

        foreach ($pageIdItems as $id => $item) {
            $items->put($id, $item);
        }

        return $items;
    }

    /**
     * Set WikipediaQuery for current `$model`.
     *
     * @return Collection<int,WikipediaQuery>
     */
    private function setQueries(): Collection
    {
        /** @var Collection<int,WikipediaQuery> */
        $queries = collect([]);

        foreach ($this->original as $key => $object) {
            // Test each attribute
            foreach ($this->queryAttributes as $attribute) {
                if (! $this->attributeExistInModel($attribute, $object)) {
                    continue;
                }
            }

            $lang = $this->language;

            // If language attribute is unknown, set it to english.
            if ($this->languageAttribute && $this->attributeExistInModel($this->languageAttribute, $object)) {
                $lang = $object->{$this->languageAttribute};

                if ($lang === 'unknown' || $lang === null) {
                    $lang = $this->language;
                }
            }

            if (! is_string($lang)) {
                $lang = json_encode($lang);
            }

            // set query string from `$queryAttributes`
            $queryString = null;

            foreach ($this->queryAttributes as $attr) {
                $attr = $object->{$attr};

                if (! is_string($attr)) {
                    $attr = json_encode($attr);
                }
                $queryString .= $attr.' ';
            }

            $queryString = trim($queryString);

            $id = null;

            if ($this->attributeExistInModel($this->identifier, $object)) {
                $id = $object->{$this->identifier};
            } else {
                $id = $key;
            }

            $queries->put(
                $id,
                WikipediaQuery::make(
                    queryString: $queryString,
                    identifier: $id,
                    language: $lang,
                ),
            );
        }

        return $queries;
    }

    /**
     * Check if attribute exist into Model.
     */
    private function attributeExistInModel(string $attribute, object $model): bool
    {
        if ($model instanceof Model) {
            return array_key_exists($attribute, $model->getAttributes());
        }

        return property_exists($model, $attribute);
    }

    /**
     * Print response into JSON format to debug, store it to `public/storage/debug/wikipedia/{$directory}/`.
     */
    private function print(HttpPoolResponse $response, string $directory, int $id)
    {
        $response_json = json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        Storage::disk('public')->put("debug/wikipedia/{$directory}/{$id}.json", $response_json);
    }
}
