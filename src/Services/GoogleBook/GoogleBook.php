<?php

namespace Kiwilan\Steward\Services\GoogleBook;

use DateTime;
use Kiwilan\Steward\Services\GoogleBook\Http\GoogleBookIndustryIdentifier;
use Kiwilan\Steward\Services\GoogleBook\Http\GoogleBookResponse;
use Kiwilan\Steward\Services\Http\HttpResponse;

/**
 * GoogleBook item.
 */
class GoogleBook
{
    /** @var GoogleBookIndustryIdentifier[] */
    protected array $industryIdentifiers = [];

    /** @var string[] */
    protected array $categories = [];

    protected function __construct(
        protected string $requestUrl,
        protected string $originalIsbn,
        protected string|int|null $identifier,
        protected ?string $bookId = null,
        protected ?DateTime $publishedDate = null,
        protected ?string $description = null,
        protected ?int $pageCount = null,
        protected ?string $maturityRating = null,
        protected ?string $language = null,
        protected ?string $previewLink = null,
        protected ?string $publisher = null,
        protected ?int $retailPriceAmount = null,
        protected ?int $retailPriceCurrencyCode = null,
        protected ?string $buyLink = null,
        protected ?string $isbn10 = null,
        protected ?string $isbn13 = null,
    ) {
    }

    public static function make(HttpResponse $response): ?self
    {
        if (! $response->isSuccess()) {
            return null;
        }

        $options = GoogleBookResponse::toCollection($response);

        if ($options->isEmpty()) {
            return null;
        }

        $current = $options->first();
        $self = new GoogleBook(
            requestUrl: $current->requestUrl(),
            originalIsbn: $current->originalIsbn(),
            identifier: $response->id(),
        );

        return $self->create($current);
    }

    public function requestUrl(): string
    {
        return $this->requestUrl;
    }

    public function originalIsbn(): string
    {
        return $this->originalIsbn;
    }

    public function identifier(): string|int|null
    {
        return $this->identifier;
    }

    public function bookId(): ?string
    {
        return $this->bookId;
    }

    public function publishedDate(): ?DateTime
    {
        return $this->publishedDate;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function pageCount(): ?int
    {
        return $this->pageCount;
    }

    public function maturityRating(): ?string
    {
        return $this->maturityRating;
    }

    public function language(): ?string
    {
        return $this->language;
    }

    public function previewLink(): ?string
    {
        return $this->previewLink;
    }

    public function publisher(): ?string
    {
        return $this->publisher;
    }

    public function retailPriceAmount(): ?int
    {
        return $this->retailPriceAmount;
    }

    public function retailPriceCurrencyCode(): ?int
    {
        return $this->retailPriceCurrencyCode;
    }

    public function buyLink(): ?string
    {
        return $this->buyLink;
    }

    public function isbn10(): ?string
    {
        return $this->isbn10;
    }

    public function isbn13(): ?string
    {
        return $this->isbn13;
    }

    public function industryIdentifiers(): array
    {
        return $this->industryIdentifiers;
    }

    public function categories(): array
    {
        return $this->categories;
    }

    private function create(GoogleBookResponse $response): self
    {
        $this->bookId = $response->id();

        $volumeInfo = $response->volumeInfo();

        $this->publishedDate = $volumeInfo?->publishedDate()
            ? new DateTime($volumeInfo->publishedDate())
            : null;
        $this->publisher = $volumeInfo?->publisher();
        $this->description = $volumeInfo?->description();
        $this->pageCount = $volumeInfo?->pageCount();
        $this->categories = $volumeInfo?->categories() ?? [];
        $this->maturityRating = $volumeInfo?->maturityRating();
        $this->language = $volumeInfo?->language();
        $this->previewLink = $volumeInfo?->previewLink();

        $saleInfo = $response->saleInfo();

        $this->retailPriceAmount = intval($saleInfo?->retailPrice()?->amount());
        $this->retailPriceCurrencyCode = intval($saleInfo?->retailPrice()?->currencyCode());
        $this->buyLink = $saleInfo?->buyLink();

        foreach ($volumeInfo?->industryIdentifiers() as $key => $gBookIdentifier) {
            $this->isbn13 = $gBookIdentifier->type() === 'ISBN_13'
                ? $gBookIdentifier->identifier()
                : null;

            $this->isbn10 = $gBookIdentifier->type() === 'ISBN_10'
                ? $gBookIdentifier->identifier()
                : null;
        }

        $this->industryIdentifiers = $volumeInfo?->industryIdentifiers() ?? [];

        return $this;
    }
}
