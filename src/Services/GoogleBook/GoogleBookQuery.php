<?php

namespace Kiwilan\Steward\Services\GoogleBook;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Services\GoogleBookService;

/**
 * Create GoogleBookQuery from Model and ISBN.
 */
class GoogleBookQuery
{
    /** @var string[] */
    protected array $isbnItems = [];

    protected function __construct(
        protected string|int|null $identifier = null,
        protected ?string $url = null,
        protected ?string $originalIsbn = null,
        protected ?GoogleBook $book = null,
    ) {
    }

    /**
     * Create new GoogleBookQuery from Model and GoogleBookService.
     *
     * @param  string[]  $isbnItems
     */
    public static function make(array $isbnItems, int|string $identifier): self
    {
        $self = new GoogleBookQuery();

        $self->isbnItems = array_filter($isbnItems);
        $self->identifier = $identifier;
        $self->setGoogleBookUrl();

        return $self;
    }

    /**
     * Build GoogleBook API url from ISBN.
     */
    public function setGoogleBookUrl(): self
    {
        $isbn = reset($this->isbnItems);

        if ($isbn) {
            $url = 'https://www.googleapis.com/books/v1/volumes';
            $url .= "?q=isbn:{$isbn}";

            $this->url = $url;
            $this->originalIsbn = $isbn;
        }

        return $this;
    }

    public function getIdentifier(): int|string
    {
        return $this->identifier;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getOriginalIsbn(): ?string
    {
        return $this->originalIsbn;
    }

    public function getBook(): ?GoogleBook
    {
        return $this->book;
    }

    /**
     * @return string[]
     */
    public function getIsbnItems(): array
    {
        return $this->isbnItems;
    }
}
