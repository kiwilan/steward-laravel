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
    protected array $isbn = [];

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
     * @param  string[]  $isbn
     */
    public static function make(array $isbn, int|string $identifier): self
    {
        $self = new GoogleBookQuery();

        $self->isbn = array_filter($isbn);
        $self->identifier = $identifier;
        $self->setGoogleBookUrl();

        return $self;
    }

    /**
     * Build GoogleBook API url from ISBN.
     */
    public function setGoogleBookUrl(): self
    {
        $isbn = reset($this->isbn);

        if ($isbn) {
            $url = 'https://www.googleapis.com/books/v1/volumes';
            $url .= "?q=isbn:{$isbn}";

            $this->url = $url;
            $this->originalIsbn = $isbn;
        }

        return $this;
    }

    public function identifier(): int|string
    {
        return $this->identifier;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function originalIsbn(): ?string
    {
        return $this->originalIsbn;
    }

    public function book(): ?GoogleBook
    {
        return $this->book;
    }

    /**
     * @return string[]
     */
    public function isbn(): array
    {
        return $this->isbn;
    }
}
