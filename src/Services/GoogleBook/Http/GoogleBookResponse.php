<?php

namespace Kiwilan\Steward\Services\GoogleBook\Http;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\HttpResponse;

class GoogleBookResponse
{
    protected function __construct(
        protected string $requestUrl,
        protected string $originalIsbn,
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
    public static function toCollection(HttpResponse $response): Collection
    {
        /** @var Collection<int,GoogleBookResponse> */
        $collection = collect([]);

        $body = $response->toArray();

        if (! array_key_exists('items', $body)) {
            return $collection;
        }

        $searchs = $body['items'];

        foreach ($searchs as $search) {
            $collection->push(
                self::make($search, $response->metadata()->origin()),
            );
        }

        return $collection;
    }

    public function requestUrl(): string
    {
        return $this->requestUrl;
    }

    public function originalIsbn(): string
    {
        return $this->originalIsbn;
    }

    public function kind(): ?string
    {
        return $this->kind;
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function etag(): ?string
    {
        return $this->etag;
    }

    public function volumeInfo(): ?GoogleBookVolumeInfo
    {
        return $this->volumeInfo;
    }

    public function saleInfo(): ?GoogleBookVolumeSaleInfo
    {
        return $this->saleInfo;
    }

    public function accessInfo(): ?GoogleBookAccessInfo
    {
        return $this->accessInfo;
    }

    public function searchInfo(): ?GoogleBookSearchInfo
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

    public function title(): ?string
    {
        return $this->title;
    }

    public function publishedDate(): ?string
    {
        return $this->publishedDate;
    }

    public function publisher(): ?string
    {
        return $this->publisher;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function readingModes(): ?GoogleBookReadingModes
    {
        return $this->readingModes;
    }

    public function pageCount(): ?int
    {
        return $this->pageCount;
    }

    public function printType(): ?string
    {
        return $this->printType;
    }

    public function maturityRating(): ?string
    {
        return $this->maturityRating;
    }

    public function allowAnonLogging(): ?bool
    {
        return $this->allowAnonLogging;
    }

    public function contentVersion(): ?string
    {
        return $this->contentVersion;
    }

    public function language(): ?string
    {
        return $this->language;
    }

    public function previewLink(): ?string
    {
        return $this->previewLink;
    }

    public function infoLink(): ?string
    {
        return $this->infoLink;
    }

    public function canonicalVolumeLink(): ?string
    {
        return $this->canonicalVolumeLink;
    }

    /**
     * @return array<string>
     */
    public function authors(): array
    {
        return $this->authors;
    }

    /**
     * @return GoogleBookIndustryIdentifier[]
     */
    public function industryIdentifiers(): array
    {
        return $this->industryIdentifiers;
    }

    /**
     * @return array<string>
     */
    public function categories(): array
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

    public function type(): ?string
    {
        return $this->type;
    }

    public function identifier(): ?string
    {
        return $this->identifier;
    }
}

class GoogleBookReadingModes
{
    protected function __construct(
        protected ?bool $text = null,
        protected ?bool $image = null,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            text: $search['text'] ?? null,
            image: $search['image'] ?? null,
        );
    }

    public function text(): ?bool
    {
        return $this->text;
    }

    public function image(): ?bool
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
            isEbook: $search['isEbook'] ?? null,
            listPrice: GoogleBookVolumeSaleInfoPrice::make($search['listPrice'] ?? null),
            retailPrice: GoogleBookVolumeSaleInfoPrice::make($search['retailPrice'] ?? null),
            buyLink: $search['buyLink'] ?? null,
        );
    }

    public function country(): ?string
    {
        return $this->country;
    }

    public function saleability(): ?string
    {
        return $this->saleability;
    }

    public function isEbook(): ?bool
    {
        return $this->isEbook;
    }

    public function listPrice(): ?GoogleBookVolumeSaleInfoPrice
    {
        return $this->listPrice;
    }

    public function retailPrice(): ?GoogleBookVolumeSaleInfoPrice
    {
        return $this->retailPrice;
    }

    public function buyLink(): ?string
    {
        return $this->buyLink;
    }

    /**
     * @return GoogleBookVolumeSaleInfoOffer[]
     */
    public function offers(): array
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

    public function amount(): ?float
    {
        return $this->amount;
    }

    public function currencyCode(): ?string
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
            giftable: $search['giftable'] ?? null,
        );
    }

    public function finskyOfferType(): ?int
    {
        return $this->finskyOfferType;
    }

    public function listPrice(): ?GoogleBookVolumeSaleInfoOfferPrice
    {
        return $this->listPrice;
    }

    public function retailPrice(): ?GoogleBookVolumeSaleInfoOfferPrice
    {
        return $this->retailPrice;
    }

    public function giftable(): ?bool
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

    public function amount(): ?float
    {
        return $this->amount;
    }

    public function currencyCode(): ?string
    {
        return $this->currencyCode;
    }
}

class GoogleBookAccessInfo
{
    protected function __construct(
        protected ?string $country = null,
        protected ?string $viewability = null,
        protected ?bool $embeddable = null,
        protected ?bool $publicDomain = null,
        protected ?string $textToSpeechPermission = null,
        protected ?GoogleBookAccessInfoAvailable $epub = null,
        protected ?GoogleBookAccessInfoAvailable $pdf = null,
        protected ?string $webReaderLink = null,
        protected ?bool $accessViewStatus = null,
        protected ?bool $quoteSharingAllowed = null,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            country: $search['country'] ?? null,
            viewability: $search['viewability'] ?? null,
            embeddable: $search['embeddable'] ?? null,
            publicDomain: $search['publicDomain'] ?? null,
            textToSpeechPermission: $search['textToSpeechPermission'] ?? null,
            epub: GoogleBookAccessInfoAvailable::make($search['epub'] ?? null),
            pdf: GoogleBookAccessInfoAvailable::make($search['pdf'] ?? null),
            webReaderLink: $search['webReaderLink'] ?? null,
            accessViewStatus: $search['accessViewStatus'] ?? null,
            quoteSharingAllowed: $search['quoteSharingAllowed'] ?? null,
        );
    }

    public function country(): ?string
    {
        return $this->country;
    }

    public function viewability(): ?string
    {
        return $this->viewability;
    }

    public function embeddable(): ?bool
    {
        return $this->embeddable;
    }

    public function publicDomain(): ?bool
    {
        return $this->publicDomain;
    }

    public function textToSpeechPermission(): ?string
    {
        return $this->textToSpeechPermission;
    }

    public function epub(): ?GoogleBookAccessInfoAvailable
    {
        return $this->epub;
    }

    public function pdf(): ?GoogleBookAccessInfoAvailable
    {
        return $this->pdf;
    }

    public function webReaderLink(): ?string
    {
        return $this->webReaderLink;
    }

    public function accessViewStatus(): ?bool
    {
        return $this->accessViewStatus;
    }

    public function quoteSharingAllowed(): ?bool
    {
        return $this->quoteSharingAllowed;
    }
}

class GoogleBookAccessInfoAvailable
{
    protected function __construct(
        protected ?bool $isAvailable = null,
    ) {
    }

    public static function make(array $search): self
    {
        return new self(
            isAvailable: $search['isAvailable'] ?? null,
        );
    }

    public function isAvailable(): ?bool
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

    public function textSnippet(): ?string
    {
        return $this->textSnippet;
    }
}
