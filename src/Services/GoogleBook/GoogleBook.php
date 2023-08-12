<?php

namespace Kiwilan\Steward\Services\GoogleBook;

use DateTime;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\Steward\Services\GoogleBook\Http\GoogleBookIndustryIdentifier;
use Kiwilan\Steward\Services\GoogleBook\Http\GoogleBookResponse;

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
        protected string|int|null $identifier,
        protected ?string $originalIsbn = null,
        protected ?string $bookId = null,
        protected ?DateTime $publishedDate = null,
        protected ?string $description = null,
        protected ?int $pageCount = null,
        protected bool $isMaturityRating = false,
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

    public static function make(HttpPoolResponse $response): ?self
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
            requestUrl: $current->getRequestUrl(),
            identifier: $response->getId(),
            originalIsbn: $current->getOriginalIsbn(),
        );

        return $self->create($current);
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getOriginalIsbn(): ?string
    {
        return $this->originalIsbn;
    }

    public function getIdentifier(): string|int|null
    {
        return $this->identifier;
    }

    public function getBookId(): ?string
    {
        return $this->bookId;
    }

    public function getPublishedDate(): ?DateTime
    {
        return $this->publishedDate;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function isMaturityRating(): bool
    {
        return $this->isMaturityRating;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getPreviewLink(): ?string
    {
        return $this->previewLink;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getRetailPriceAmount(): ?int
    {
        return $this->retailPriceAmount;
    }

    public function getRetailPriceCurrencyCode(): ?int
    {
        return $this->retailPriceCurrencyCode;
    }

    public function getBuyLink(): ?string
    {
        return $this->buyLink;
    }

    public function getIsbn10(): ?string
    {
        return $this->isbn10;
    }

    public function getIsbn13(): ?string
    {
        return $this->isbn13;
    }

    public function getIndustryIdentifiers(): array
    {
        return $this->industryIdentifiers;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    private function create(GoogleBookResponse $response): self
    {
        $this->bookId = $response->getId();

        $volumeInfo = $response->getVolumeInfo();

        if ($volumeInfo) {
            $this->publishedDate = $volumeInfo->getPublishedDate()
            ? new DateTime($volumeInfo->getPublishedDate())
            : null;
            $this->publisher = $volumeInfo->getPublisher();
            $this->description = $volumeInfo->getDescription();
            $this->pageCount = $volumeInfo->getPageCount();
            $this->categories = $volumeInfo->getCategories();
            $this->isMaturityRating = $volumeInfo->isMaturityRating();
            $this->language = $volumeInfo->getLanguage();
            $this->previewLink = $volumeInfo->getPreviewLink();
        }

        $saleInfo = $response->getSaleInfo();

        $this->retailPriceAmount = intval($saleInfo?->getRetailPrice()?->getAmount());
        $this->retailPriceCurrencyCode = intval($saleInfo?->getRetailPrice()?->getCurrencyCode());
        $this->buyLink = $saleInfo?->getBuyLink();

        foreach ($volumeInfo?->getIndustryIdentifiers() as $key => $gBookIdentifier) {
            $this->isbn13 = $gBookIdentifier->getType() === 'ISBN_13'
                ? $gBookIdentifier->getIdentifier()
                : null;

            $this->isbn10 = $gBookIdentifier->getType() === 'ISBN_10'
                ? $gBookIdentifier->getIdentifier()
                : null;
        }

        $this->industryIdentifiers = $volumeInfo?->getIndustryIdentifiers() ?? [];

        return $this;
    }
}
