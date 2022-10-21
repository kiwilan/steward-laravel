<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphItem;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphTwitter;

class OpenGraphService
{
    protected const OPEN_GRAPH_META = [
        'title' => [
            'name' => 'og:title',
            'type' => 'property',
        ],
        'description' => [
            'name' => 'og:description',
            'type' => 'property',
        ],
        'image' => [
            'name' => 'og:image',
            'type' => 'property',
        ],
        'url' => [
            'name' => 'og:url',
            'type' => 'property',
        ],
        'type' => [
            'name' => 'og:type',
            'type' => 'property',
        ],
        'site_name' => [
            'name' => 'og:site_name',
            'type' => 'property',
        ],
        'locale' => [
            'name' => 'og:locale',
            'type' => 'property',
        ],
        'theme_color' => [
            'name' => 'theme-color',
            'type' => 'name',
        ],
    ];

    protected function __construct(
        protected string $url,
        protected ?string $body = null,
        protected ?OpenGraphItem $openGraph = null,
        protected bool $is_twitter = false,
    ) {
    }

    public static function make(string $url): ?OpenGraphItem
    {
        $service = new OpenGraphService($url);

        if (str_contains($service->url, 'twitter')) {
            return $service->twitter()
                ->getOpenGraph();
        }

        $client = new Client();
        $response = $client->get($url);
        $service->body = $response->getBody()->getContents();
        $service->setMetadata();

        return $service->openGraph;
    }

    private function twitter(): OpenGraphTwitter
    {
        $this->is_twitter = true;

        return OpenGraphTwitter::make($this->url);
    }

    private function setMetadata(): self
    {
        $this->openGraph = new OpenGraphItem();

        foreach (self::OPEN_GRAPH_META as $property => $meta) {
            $this->openGraph->{$property} = $this->extract($meta['name'], $meta['type']);
        }

        return $this;
    }

    private function extract(string $name = 'og:title', string $type = 'property'): ?string
    {
        $start = strpos($this->body, "{$type}=\"{$name}\"");

        $end = strpos($this->body, '>', $start);
        if (! is_numeric($start) || ! is_numeric($end)) {
            return null;
        }

        $len = strlen("property=\"{$name}\"");
        $substr = substr($this->body, $start + $len, $end - $start + $len);

        $exp = explode('"', $substr);

        return $exp[1] ?? '';
    }
}
