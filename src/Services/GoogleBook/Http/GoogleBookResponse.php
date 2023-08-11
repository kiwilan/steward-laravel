<?php

namespace Kiwilan\Steward\Services\GoogleBook\Http;

use Illuminate\Support\Collection;
use Kiwilan\HttpPool\Response\HttpPoolResponse;

class GoogleBookResponse
{
    protected function __construct(
        protected string $requestUrl,
        protected ?string $originalIsbn = null,
        protected ?string $kind = null,
        protected ?string $id = null,
        protected ?string $etag = null,
        protected ?GoogleBookVolumeInfo $volumeInfo = null,
        protected ?GoogleBookVolumeSaleInfo $saleInfo = null,
        protected ?GoogleBookAccessInfo $accessInfo = null,
        protected ?GoogleBookSearchInfo $searchInfo = null,
    ) {
    }

    /**
     * Convert GoogleBookResponse to Collection.
     *
     * @return Collection<int,GoogleBookResponse>
     */
    public static function toCollection(HttpPoolResponse $response): Collection
    {
        /** @var Collection<int,GoogleBookResponse> */
        $collection = collect([]);

        $body = $response->getBody()->toArray();

        if (! array_key_exists('items', $body)) {
            return $collection;
        }

        $searchs = $body['items'];

        foreach ($searchs as $search) {
            $collection->push(
                self::make($search, $response->getMetadata()->getRequest()),
            );
        }

        return $collection;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getOriginalIsbn(): ?string
    {
        return $this->originalIsbn;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEtag(): ?string
    {
        return $this->etag;
    }

    public function getVolumeInfo(): ?GoogleBookVolumeInfo
    {
        return $this->volumeInfo;
    }

    public function getSaleInfo(): ?GoogleBookVolumeSaleInfo
    {
        return $this->saleInfo;
    }

    public function getAccessInfo(): ?GoogleBookAccessInfo
    {
        return $this->accessInfo;
    }

    public function getSearchInfo(): ?GoogleBookSearchInfo
    {
        return $this->searchInfo;
    }

    private static function make(array $search, string $origin): self
    {
        $originalIsbn = explode('isbn:', $origin)[1] ?? null;

        return new self(
            requestUrl: $origin,
            originalIsbn: $originalIsbn,
            kind: $search['kind'] ?? null,
            id: $search['id'] ?? null,
            etag: $search['etag'] ?? null,
            volumeInfo: GoogleBookVolumeInfo::make($search['volumeInfo'] ?? []),
            saleInfo: GoogleBookVolumeSaleInfo::make($search['saleInfo'] ?? []),
            accessInfo: GoogleBookAccessInfo::make($search['accessInfo'] ?? []),
            searchInfo: GoogleBookSearchInfo::make($search['searchInfo'] ?? []),
        );
    }
}

class GoogleBookVolumeInfo extends GoogleBookResponse
{
    /** @var array<string> */
    protected array $authors = [];

    /** @var GoogleBookIndustryIdentifier[] */
    protected array $industryIdentifiers = [];

    /** @var array<string> */
    protected array $categories = [];

    protected function __construct(
        protected ?string $title = null,
        protected ?string $publishedDate = null,
        protected ?string $publisher = null,
        protected ?string $description = null,
        protected ?GoogleBookReadingModes $readingModes = null,
        protected ?int $pageCount = null,
        protected ?string $printType = null,
        protected ?string $maturityRating = null,
        protected ?bool $allowAnonLogging = null,
        protected ?string $contentVersion = null,
        protected ?string $language = null,
        protected ?string $previewLink = null,
        protected ?string $infoLink = null,
        protected ?string $canonicalVolumeLink = null,
    ) {
    }

    public static function make(array $search): self
    {
        $self = new self(
            title: $search['title'] ?? null,
            publishedDate: $search['publishedDate'] ?? null,
            publisher: $search['publisher'] ?? null,
            description: $search['description'] ?? null,
            readingModes: GoogleBookReadingModes::make($search['readingModes'] ?? []),
            pageCount: $search['pageCount'] ?? null,
            printType: $search['printType'] ?? null,
            maturityRating: $search['maturityRating'] ?? null,
            allowAnonLogging: $search['allowAnonLogging'] ?? null,
            contentVersion: $search['contentVersion'] ?? null,
            language: $search['language'] ?? null,
            previewLink: $search['previewLink'] ?? null,
            infoLink: $search['infoLink'] ?? null,
            canonicalVolumeLink: $search['canonicalVolumeLink'] ?? null,
        );

        $self->authors = $search['authors'] ?? [];
        $self->industryIdentifiers = GoogleBookIndustryIdentifier::toArray($search['industryIdentifiers'] ?? []);
        $self->categories = $search['categories'] ?? [];

        return $self;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getPublishedDate(): ?string
    {
        return $this->publishedDate;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getReadingModes(): ?GoogleBookReadingModes
    {
        return $this->readingModes;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function getPrintType(): ?string
    {
        return $this->printType;
    }

    public function isMaturityRating(): bool
    {
        return $this->maturityRating === 'MATURE';
    }

    public function isAllowAnonLogging(): ?bool
    {
        return $this->allowAnonLogging;
    }

    public function getContentVersion(): ?string
    {
        return $this->contentVersion;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getPreviewLink(): ?string
    {
        return $this->previewLink;
    }

    public function getInfoLink(): ?string
    {
        return $this->infoLink;
    }

    public function getCanonicalVolumeLink(): ?string
    {
        return $this->canonicalVolumeLink;
    }

    /**
     * @return array<string>
     */
    public function getAuthors(): array
    {
        return $this->authors;
    }

    /**
     * @return GoogleBookIndustryIdentifier[]
     */
    public function getIndustryIdentifiers(): array
    {
        return $this->industryIdentifiers;
    }

    /**
     * @return array<string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}

class GoogleBookIndustryIdentifier
{
    protected function __construct(
        protected ?string $type = null,
        protected ?string $identifier = null,
    ) {
    }

    /**
     * Convert GoogleBookIndustryIdentifier to Collection.
     *
     * @return GoogleBookIndustryIdentifier[]
     */
    public static function toArray(array $search): array
    {
        /** @var GoogleBookIndustryIdentifier[] */
        $collection = [];

        foreach ($search as $item) {
            $collection[] = new self(
                type: $item['type'] ?? null,
                identifier: $item['identifier'] ?? null,
            );
        }

        return $collection;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}

class GoogleBookReadingModes
{
    protected function __construct(
        protected bool $text = false,
        protected bool $image = false,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            text: $search['text'] ?? false,
            image: $search['image'] ?? false,
        );
    }

    public function isText(): bool
    {
        return $this->text;
    }

    public function isImage(): bool
    {
        return $this->image;
    }
}

class GoogleBookVolumeSaleInfo
{
    /** @var GoogleBookVolumeSaleInfoOffer[] */
    protected array $offers = [];

    protected function __construct(
        protected ?string $country = null,
        protected ?string $saleability = null,
        protected ?bool $isEbook = null,
        protected ?GoogleBookVolumeSaleInfoPrice $listPrice = null,
        protected ?GoogleBookVolumeSaleInfoPrice $retailPrice = null,
        protected ?string $buyLink = null,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            country: $search['country'] ?? null,
            saleability: $search['saleability'] ?? null,
            isEbook: $search['isEbook'] ?? false,
            listPrice: GoogleBookVolumeSaleInfoPrice::make($search['listPrice'] ?? null),
            retailPrice: GoogleBookVolumeSaleInfoPrice::make($search['retailPrice'] ?? null),
            buyLink: $search['buyLink'] ?? null,
        );
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getSaleability(): ?string
    {
        return $this->saleability;
    }

    public function isEbook(): bool
    {
        return $this->isEbook;
    }

    public function getListPrice(): ?GoogleBookVolumeSaleInfoPrice
    {
        return $this->listPrice;
    }

    public function getRetailPrice(): ?GoogleBookVolumeSaleInfoPrice
    {
        return $this->retailPrice;
    }

    public function getBuyLink(): ?string
    {
        return $this->buyLink;
    }

    /**
     * @return GoogleBookVolumeSaleInfoOffer[]
     */
    public function getOffers(): array
    {
        return $this->offers;
    }
}

class GoogleBookVolumeSaleInfoPrice
{
    protected function __construct(
        protected ?float $amount = null,
        protected ?string $currencyCode = null,
    ) {
    }

    public static function make(?array $search): self
    {
        if (is_null($search)) {
            return new self();
        }

        return new self(
            amount: $search['amount'] ?? null,
            currencyCode: $search['currencyCode'] ?? null,
        );
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }
}

class GoogleBookVolumeSaleInfoOffer
{
    protected function __construct(
        protected ?int $finskyOfferType = null,
        protected ?GoogleBookVolumeSaleInfoOfferPrice $listPrice = null,
        protected ?GoogleBookVolumeSaleInfoOfferPrice $retailPrice = null,
        protected ?bool $giftable = null,
    ) {
    }

    public static function make(?array $search): self
    {
        if (is_null($search)) {
            return new self();
        }

        return new self(
            finskyOfferType: $search['finskyOfferType'] ?? null,
            listPrice: GoogleBookVolumeSaleInfoOfferPrice::make($search['listPrice'] ?? null),
            retailPrice: GoogleBookVolumeSaleInfoOfferPrice::make($search['retailPrice'] ?? null),
            giftable: $search['giftable'] ?? false,
        );
    }

    public function getFinskyOfferType(): ?int
    {
        return $this->finskyOfferType;
    }

    public function getListPrice(): ?GoogleBookVolumeSaleInfoOfferPrice
    {
        return $this->listPrice;
    }

    public function getRetailPrice(): ?GoogleBookVolumeSaleInfoOfferPrice
    {
        return $this->retailPrice;
    }

    public function isGiftable(): ?bool
    {
        return $this->giftable;
    }
}

class GoogleBookVolumeSaleInfoOfferPrice
{
    protected function __construct(
        protected ?float $amount = null,
        protected ?string $currencyCode = null,
    ) {
    }

    public static function make(?array $search): self
    {
        if (is_null($search)) {
            return new self();
        }

        return new self(
            amount: $search['amount'] ?? null,
            currencyCode: $search['currencyCode'] ?? null,
        );
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }
}

class GoogleBookAccessInfo
{
    protected function __construct(
        protected ?string $country = null,
        protected ?string $viewability = null,
        protected bool $embeddable = false,
        protected bool $publicDomain = false,
        protected ?string $textToSpeechPermission = null,
        protected ?GoogleBookAccessInfoAvailable $epub = null,
        protected ?GoogleBookAccessInfoAvailable $pdf = null,
        protected ?string $webReaderLink = null,
        protected bool $accessViewStatus = false,
        protected bool $quoteSharingAllowed = false,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            country: $search['country'] ?? null,
            viewability: $search['viewability'] ?? null,
            embeddable: $search['embeddable'] ?? false,
            publicDomain: $search['publicDomain'] ?? false,
            textToSpeechPermission: $search['textToSpeechPermission'] ?? null,
            epub: GoogleBookAccessInfoAvailable::make($search['epub'] ?? null),
            pdf: GoogleBookAccessInfoAvailable::make($search['pdf'] ?? null),
            webReaderLink: $search['webReaderLink'] ?? null,
            accessViewStatus: $search['accessViewStatus'] ?? false,
            quoteSharingAllowed: $search['quoteSharingAllowed'] ?? false,
        );
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getViewability(): ?string
    {
        return $this->viewability;
    }

    public function isEmbeddable(): bool
    {
        return $this->embeddable;
    }

    public function isPublicDomain(): bool
    {
        return $this->publicDomain;
    }

    public function getTextToSpeechPermission(): ?string
    {
        return $this->textToSpeechPermission;
    }

    public function getEpub(): ?GoogleBookAccessInfoAvailable
    {
        return $this->epub;
    }

    public function getPdf(): ?GoogleBookAccessInfoAvailable
    {
        return $this->pdf;
    }

    public function getWebReaderLink(): ?string
    {
        return $this->webReaderLink;
    }

    public function isAccessViewStatus(): bool
    {
        return $this->accessViewStatus;
    }

    public function isAuoteSharingAllowed(): bool
    {
        return $this->quoteSharingAllowed;
    }
}

class GoogleBookAccessInfoAvailable
{
    protected function __construct(
        protected bool $isAvailable = false,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            isAvailable: $search['isAvailable'] ?? false,
        );
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }
}

class GoogleBookSearchInfo
{
    protected function __construct(
        protected ?string $textSnippet = null,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            textSnippet: $search['textSnippet'] ?? null,
        );
    }

    public function getTextSnippet(): ?string
    {
        return $this->textSnippet;
    }
}
