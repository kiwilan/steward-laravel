<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\Steward\Utils\Wikipedia\WikipediaItem;
use Kiwilan\Steward\Utils\Wikipedia\WikipediaQuery;

/**
 * Use Wikipedia to get some data about authors and series.
 * Documentation (in french) from https://korben.info/comment-utiliser-lapi-de-recherche-de-wikipedia.html.
 *
 * For each Wikipedia search, need to execute two API calls to search to get page id and to parse page id data.
 */
class Wikipedia
{
    /**
     * @param  array<string>  $precision
     */
    protected function __construct(
        protected string $subject,
        protected string $language = 'en',
        protected array $precision = [],
        protected bool $exact = false,
        protected bool $withImage = false,
        protected bool $isAvailable = false,
        protected ?WikipediaQuery $query = null,
        protected ?int $selected = null,
        protected ?WikipediaItem $item = null,
    ) {}

    /**
     * Create WikipediaService from Model and create WikipediaQuery for each entity only if hasn't WikipediaItem.
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
     * Set precision to use for Wikipedia API.
     *
     * Example: `['author', 'writer', 'novelist']` for author, this option will search into page title and page content
     *
     * @default `[]` for no precision
     */
    public function precision(array $precision): self
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Use exact match between subject and page title.
     *
     * @default `false`
     */
    public function exact(): self
    {
        $this->exact = true;

        return $this;
    }

    public function withImage(): self
    {
        $this->withImage = true;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * Get WikipediaItem.
     */
    public function getItem(): ?WikipediaItem
    {
        return $this->item;
    }

    /**
     * Execute Wikipedia API calls.
     */
    public function get(): self
    {
        $this->query = WikipediaQuery::make($this->subject)
            ->language($this->language);

        if ($this->exact) {
            $this->query->exact();
        }

        if (! empty($this->precision)) {
            $this->query->precision($this->precision);
        }

        $this->query->get();
        $this->isAvailable = $this->query->isAvailable();

        if ($this->isAvailable) {
            $this->selected = intval($this->query->getModelPage()->getPageId());
            $this->item = WikipediaItem::make(
                model: $this->query->getModelPage(),
                identifier: $this->query->getIdentifier(),
                withImage: $this->withImage,
            );
        }

        return $this;
    }
}
