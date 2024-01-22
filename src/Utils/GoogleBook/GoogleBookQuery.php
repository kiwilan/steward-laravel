<?php

namespace Kiwilan\Steward\Utils\GoogleBook;

use Kiwilan\Steward\Utils\GoogleBook\Models\GoogleBookModel;
use Kiwilan\Steward\Utils\Wikipedia\WikipediaClient;

/**
 * Create GoogleBookQuery from Model and ISBN.
 */
class GoogleBookQuery
{
    protected function __construct(
        protected string $isbn,
        protected ?string $url = null,
        protected ?GoogleBookModel $model = null,
    ) {
    }

    /**
     * Create new GoogleBookQuery from GoogleBook.
     */
    public static function make(string $isbn): self
    {
        $self = new GoogleBookQuery($isbn);
        $self->setGoogleBookUrl();
        $self->search();

        return $self;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function getModel(): ?GoogleBookModel
    {
        return $this->model;
    }

    /**
     * Build GoogleBook API url from ISBN.
     */
    private function setGoogleBookUrl(): self
    {
        $url = 'https://www.googleapis.com/books/v1/volumes';
        $url .= "?q=isbn:{$this->isbn}";

        $this->url = $url;

        return $this;
    }

    private function search(): self
    {
        if (! $this->url) {
            return $this;
        }

        $client = WikipediaClient::make($this->url);
        $body = $client->getBody();

        $totalItems = $body['totalItems'] ?? 0;
        if ($totalItems < 1) {
            return $this;
        }

        $items = $body['items'] ?? [];
        $first = $items[0] ?? null;
        if (! $first) {
            return $this;
        }

        $this->model = GoogleBookModel::make($first, $this->url);

        return $this;
    }
}
