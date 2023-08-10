<?php

namespace Kiwilan\Steward\Services\Wikipedia;

use DateTime;
use Illuminate\Support\Facades\Http;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\Steward\Services\Wikipedia\Http\WikipediaPageIdResponse;
use Kiwilan\Steward\Services\Wikipedia\Http\WikipediaSearchResponse;

/**
 * Class to store `WikipediaItem` data.
 */
class WikipediaItem
{
    protected function __construct(
        protected string $requestUrl,
        protected string|int|null $identifier,
        protected ?string $title = null,
        protected ?string $language = null,
        protected ?string $pageId = null,
        protected ?string $pageUrl = null,
        protected ?string $fullUrl = null,
        protected ?string $wordCount = null,
        protected ?DateTime $timestamp = null,
        protected ?string $extract = null,
        protected ?string $pictureUrl = null,
    ) {
    }

    public static function make(HttpPoolResponse $response): ?self
    {
        if (! $response->isSuccess()) {
            return null;
        }

        $options = WikipediaSearchResponse::toCollection($response);

        if ($options->isEmpty()) {
            return null;
        }

        $relevants = $options->slice(0, 5);
        $current = $relevants->first();

        foreach ($relevants as $key => $option) {
            if (0 === $key) {
                $current = $option;

                break;
            }

            // if (str_contains($option->title, '(writer)')) {
            //     $pageId = $option->pageid();

            //     break;
            // }

            // if (str_contains($option->title, '(author)')) {
            //     $pageId = $option->pageid();

            //     break;
            // }
        }

        $self = new WikipediaItem(
            requestUrl: $current->requestUrl(),
            identifier: $response->getId(),
        );

        return $self->create($current);
    }

    public static function makePageId(HttpPoolResponse $response, ?WikipediaItem $item): ?self
    {
        if (! $response->isSuccess() || ! $item) {
            return null;
        }

        $options = WikipediaPageIdResponse::toCollection($response);

        if ($options->isEmpty()) {
            return null;
        }

        $first = $options->first();

        return $item->fromPageId($first);
    }

    /**
     * Get picture from WikipediaService pictureUrl.
     */
    public static function fetchPicture(?string $pictureUrl): ?string
    {
        $picture = null;

        if ($pictureUrl) {
            $picture = Http::timeout(120)->get($pictureUrl)->body();
        }

        return base64_encode($picture);
    }

    private function fromPageId(WikipediaPageIdResponse $response): self
    {
        $this->language = $response->pagelanguage();
        $this->pageId = $response->pageid();
        $this->fullUrl = $response->fullurl();
        $this->extract = $this->convertExtract($response->extract(), 2000);
        $this->pictureUrl = $response->thumbnail()?->source();

        return $this;
    }

    public function requestUrl(): string
    {
        return $this->requestUrl;
    }

    public function identifier(): string|int
    {
        return $this->identifier;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function language(): ?string
    {
        return $this->language;
    }

    public function pageId(): ?string
    {
        return $this->pageId;
    }

    public function pageUrl(): ?string
    {
        return $this->pageUrl;
    }

    public function fullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function wordCount(): ?string
    {
        return $this->wordCount;
    }

    public function timestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    public function extract(): ?string
    {
        return $this->extract;
    }

    public function pictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    private function create(WikipediaSearchResponse $response): self
    {
        $regex = '/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i';
        preg_match($regex, $response->requestUrl(), $matches);

        $this->language = 'en';

        if (array_key_exists(1, $matches)) {
            $this->language = $matches[1];
        }

        $this->title = $response->title();
        $this->pageId = $response->pageid();
        $this->wordCount = $response->wordcount();
        $this->timestamp = new DateTime($response->timestamp());
        $this->pageUrl = WikipediaQuery::buildPageIdUrl($this->pageId, $this->language);

        return $this;
    }

    private function convertExtract(?string $text, int $limit): string
    {
        if (! $text) {
            return '';
        }

        $text = trim($text);
        $text = strip_tags($text);
        $text = str_replace('<<', '"', $text);
        $text = str_replace('>>', '"', $text);

        if ($limit && strlen($text) > $limit) {
            $text = substr($text, 0, $limit);
        }

        $text = trim($text);
        $text = preg_replace('/\s\s+/', ' ', $text); // remove extra break lines

        $text = htmlspecialchars($text); // convert html special chars
        $text = html_entity_decode($text); // translate html entities

        return "{$text}...";
    }
}
