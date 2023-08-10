<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kiwilan\HttpPool\HttpPool;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\Steward\Services\Wikipedia\WikipediaItem;
use Kiwilan\Steward\Services\Wikipedia\WikipediaQuery;
use Kiwilan\Steward\Utils\Console;

/**
 * Use Wikipedia to get some data about authors and series.
 * Documentation (in french) from https://korben.info/comment-utiliser-lapi-de-recherche-de-wikipedia.html.
 *
 * For each Wikipedia search, need to execute two API calls to search to get page id and to parse page id data.
 */
class WikipediaService
{
    /** @var array<string> */
    protected array $queryAttributes = ['name'];

    /** @var ?Collection<int,object> */
    protected ?Collection $objects = null;

    /** @var ?Collection<int,WikipediaItem> */
    protected ?Collection $items = null;

    protected function __construct(
        protected string $languageField,
        protected string $identifier = 'id',
        protected int $count = 0,
        protected bool $debug = false,
    ) {
        $this->objects = collect([]);
        $this->items = collect([]);
    }

    /**
     * Create WikipediaService from Model and create WikipediaQuery for each entity only if hasn't WikipediaItem.
     *
     * @param  Collection<int,object>  $objects  List of objects
     * @param  string  $languageField  Language field to use for Wikipedia instance
     */
    public static function make(Collection $objects, string $languageField): self
    {
        $self = new WikipediaService($languageField);
        $self->objects = $objects;
        $self->count = $self->objects->count();

        return $self;
    }

    /**
     * Set attributes to search on Wikipedia, can be unique or multiple for concat search.
     *
     * @param  string|string[]  $attributes
     */
    public function setQueryAttributes(mixed $attributes = ['name']): self
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
     * @param  string  $identifier Default is `id`
     */
    public function setidentifier(string $identifier = 'id'): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return Collection<int,WikipediaItem>
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * Execute WikipediaService.
     */
    public function execute(): self
    {
        $queries = $this->setQueries();

        $console = Console::make();

        $console->print('List of query URL available, requests from query URL to get page id.');
        $console->newLine();

        $http = HttpPool::make($queries)
            ->setIdentifierKey('identifier')
            ->setUrlKey('queryUrl')
            ->execute()
        ;

        $queryItems = $this->setQueryItems($http->getResponses());

        $console->print('List of page id URL available, requests from page id URL to get extra content.');
        $console->newLine();

        $http = HttpPool::make($queryItems)
            ->setIdentifierKey('identifier')
            ->setUrlKey('pageUrl')
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
            $item = WikipediaItem::make($response);
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
            $item = $queryItems->first(fn (WikipediaItem $item) => $item->identifier() == $id);

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

        foreach ($this->objects as $object) {
            // Test each attribute
            foreach ($this->queryAttributes as $attribute) {
                if (! $this->attributeExistInModel($attribute, $object)) {
                    continue;
                }
            }

            $lang = 'en';
            // If language attribute is unknown, set it to english.
            if ($this->attributeExistInModel($this->languageField, $object)) {
                $lang = $object->{$this->languageField};

                if ('unknown' === $lang || null === $lang) {
                    $lang = 'en';
                }
            }

            // set query string from `$queryAttributes`
            $queryString = null;

            foreach ($this->queryAttributes as $attr) {
                $queryString .= $object->{$attr}.' ';
            }

            $queryString = trim($queryString);

            $queries->put(
                $object->{$this->identifier},
                WikipediaQuery::make(
                    queryString: $queryString,
                    identifier: $object->{$this->identifier},
                    language: $lang,
                ),
            );
        }

        return $queries;
    }

    /**
     * Check if attribute exist into Model.
     */
    private function attributeExistInModel(string $attribute, Model $model): bool
    {
        return array_key_exists($attribute, $model->getAttributes());
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
