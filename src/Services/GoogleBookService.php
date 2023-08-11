<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kiwilan\HttpPool\HttpPool;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\Steward\Services\GoogleBook\GoogleBook;
use Kiwilan\Steward\Services\GoogleBook\GoogleBookQuery;

/**
 * Use GoogleBook API to improve data.
 */
class GoogleBookService
{
    /** @var ?Collection<int,object> */
    protected ?Collection $objects = null;

    /** @var ?Collection<int,GoogleBookQuery> */
    protected ?Collection $queries = null;

    /** @var array<string> */
    protected array $isbnFields = ['isbn'];

    /** @var ?Collection<int,GoogleBook> */
    protected ?Collection $items = null;

    protected function __construct(
        protected string $identifier = 'id',
        protected int $count = 0,
        protected bool $debug = false,
    ) {
        $this->objects = collect([]);
        $this->queries = collect([]);
        $this->items = collect([]);
    }

    /**
     * Get data from Google Books API with ISBN from meta
     * Example: https://www.googleapis.com/books/v1/volumes?q=isbn:9782700239904.
     *
     * Get all useful data to improve Book, Identifier, Publisher and Tag
     * If data exist, create GoogleBook associate with Book with useful data to purchase eBook
     *
     * @param  Collection<int,object>  $objects  List of scanned models
     */
    public static function make(Collection $objects): self
    {
        $self = new self();
        $self->objects = $objects;
        $self->count = $self->objects->count();

        return $self;
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
     * Set isbn fields to scan.
     *
     * @param  string[]  $isbnFields List of isbn fields into `$subject`, set more relevant first, default `['isbn']`
     */
    public function setIsbnFields(array $isbnFields = ['isbn']): self
    {
        $this->isbnFields = $isbnFields;

        return $this;
    }

    /**
     * Set unique identifier of the model.
     *
     * @param  string  $identifier Default is `id`
     */
    public function setIdentifier(string $identifier = 'id'): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get scannables count.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return Collection<int,GoogleBook>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Execute GoogleBookService.
     */
    public function execute(): self
    {
        $this->queries = $this->setQueries();
        $this->search();

        return $this;
    }

    /**
     * Make GET request from GoogleBook API and parse it.
     */
    private function search(): self
    {
        $http = HttpPool::make($this->queries)
            ->setIdentifierKey('identifier')
            ->allowPrintConsole()
            ->execute()
        ;

        $this->items = $this->setItems($http->getResponses());

        return $this;
    }

    /**
     * Create `GoogleBook` from `HttpPoolResponse`.
     *
     * @param  Collection<int,HttpPoolResponse>  $responses  Response from GoogleBook API
     * @return Collection<int,GoogleBook>
     */
    private function setItems(Collection $responses)
    {
        /** @var Collection<int,GoogleBook> */
        $items = collect([]);

        foreach ($responses as $id => $response) {
            if ($this->debug) {
                $this->print($response, 'googlebook', $id);
            }
            $item = GoogleBook::make($response);

            if ($item) {
                $items->put($id, $item);
            }
        }

        return $items;
    }

    /**
     * Create `GoogleBookQuery[]` from `models`.
     *
     * @return Collection<int,GoogleBookQuery>
     */
    private function setQueries()
    {
        /** @var Collection<int,GoogleBookQuery> */
        $queries = collect([]);

        foreach ($this->objects as $object) {
            $isbnItems = [];

            foreach ($this->isbnFields as $field) {
                if ($object->{$field}) {
                    $isbnItems[] = $object->{$field};

                    $query = GoogleBookQuery::make(
                        isbnItems: $isbnItems,
                        identifier: $object->{$this->identifier},
                    );
                    $queries->add($query);
                }
            }
        }

        return $queries;
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
