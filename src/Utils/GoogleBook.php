<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\Steward\Utils\GoogleBook\GoogleBookItem;
use Kiwilan\Steward\Utils\GoogleBook\GoogleBookQuery;

/**
 * Use GoogleBook API to improve data.
 *
 * Example: https://www.googleapis.com/books/v1/volumes?q=isbn:9782700239904.
 */
class GoogleBook
{
    protected function __construct(
        protected string $isbn,
        protected mixed $identifier = null,
        protected bool $isAvailable = false,
        protected ?GoogleBookQuery $query = null,
        protected ?GoogleBookItem $item = null,
    ) {}

    /**
     * Create GoogleBook from ISBN, can be ISBN-10 or ISBN-13.
     */
    public static function make(?string $isbn): ?self
    {
        if (! $isbn) {
            return null;
        }

        $self = new self($isbn);

        return $self;
    }

    /**
     * Set GoogleBook identifier.
     */
    public function identifier(mixed $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get GoogleBook ISBN.
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * Get GoogleBook identifier.
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * Get GoogleBook isAvailable.
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * Get GoogleBook query.
     */
    public function getQuery(): ?GoogleBookQuery
    {
        return $this->query;
    }

    /**
     * Get GoogleBookItem.
     */
    public function getItem(): ?GoogleBookItem
    {
        return $this->item;
    }

    public function get(): self
    {
        $this->query = GoogleBookQuery::make($this->isbn);

        if ($this->query->getModel()) {
            $this->item = GoogleBookItem::make($this->query->getModel(), $this->isbn);
            $this->isAvailable = true;
        }

        return $this;
    }
}
