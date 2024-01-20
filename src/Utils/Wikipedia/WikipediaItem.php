<?php

namespace Kiwilan\Steward\Utils\Wikipedia;

use DateTime;
use Kiwilan\Steward\Utils\Wikipedia\Models\WikipediaModelPage;

/**
 * Class to store `WikipediaItem` data.
 */
class WikipediaItem
{
    protected function __construct(
        protected string $requestUrl,
        protected string|int|null $identifier,
        protected ?string $title = null,
        protected string $language = 'en',
        protected ?int $pageId = null,
        protected ?string $pageUrl = null,
        protected ?string $fullUrl = null,
        protected ?int $wordCount = null,
        protected ?DateTime $timestamp = null,
        protected ?string $extract = null,
        protected ?string $fullText = null,
        protected ?string $pictureUrl = null,
        protected ?string $pictureBase64 = null,
    ) {
    }

    public static function make(
        WikipediaModelPage $model,
        mixed $identifier,
        bool $withImage = false,
    ): self {
        $self = new self($model->getRequestUrl(), $identifier);

        $self->title = $model->getTitle();
        $self->language = $model->getPagelanguage() ?? 'en';
        $self->pageId = $model->getPageId() ? intval($model->getPageId()) : null;
        $self->pageUrl = $model->getEditurl();
        $self->fullUrl = $model->getFullurl();
        $self->wordCount = $model->getLength() ? intval($model->getLength()) : null;
        $self->timestamp = new DateTime($model->getTouched());
        $self->extract = $model->getExtract() ? $self->convertExtract($model->getExtract(), 2000) : null;
        $self->fullText = $model->getExtract();
        $self->pictureUrl = $model->getThumbnail() ? $model->getThumbnail()->getSource() : null;

        if ($withImage) {
            $self->pictureBase64 = $self->fetchPicture($self->pictureUrl);
        }

        return $self;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getIdentifier(): string|int
    {
        return $this->identifier;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    public function getPageUrl(): ?string
    {
        return $this->pageUrl;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function getWordCount(): ?int
    {
        return $this->wordCount;
    }

    public function getTimestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    public function getExtract(): ?string
    {
        return $this->extract;
    }

    public function getFullText(): ?string
    {
        return $this->fullText;
    }

    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    public function getPictureBase64(): ?string
    {
        return $this->pictureBase64;
    }

    /**
     * Convert extract to text.
     */
    private function convertExtract(?string $text, ?int $limit = null): string
    {
        if (! $text) {
            return '';
        }

        $text = strip_tags($text); // remove html tags
        $text = preg_replace('/\[[^\]]*\]/', '', $text); // remove all text between brackets
        $text = preg_replace('/\([^\)]*\)/', '', $text); // remove all text between parenthesis
        $text = preg_replace('/\{[^\}]*\}/', '', $text); // remove all text between curly brackets
        $text = preg_replace('/\<[^\>]*\>/', '', $text); // remove all text between angle brackets
        $text = preg_replace('/\s\s+/', ' ', $text); // remove extra break lines

        if ($limit && strlen($text) > $limit) {
            $text = substr($text, 0, $limit);
            $text = "{$text}...";
        }

        $text = htmlspecialchars($text); // convert html special chars
        $text = html_entity_decode($text); // translate html entities

        return trim($text);
    }

    /**
     * Get picture from WikipediaService pictureUrl.
     */
    private function fetchPicture(?string $pictureUrl): ?string
    {
        if (! $pictureUrl) {
            return null;
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $pictureUrl);
        $picture = $response->getBody()->getContents();

        if (! $picture) {
            return null;
        }

        return base64_encode($picture);
    }
}
