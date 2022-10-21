<?php

namespace Kiwilan\Steward\Services\OpenGraphService;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use GuzzleHttp\Client;

class OpenGraphItem
{
    public function __construct(
        protected string $original_url,
        protected ?string $html = null,
        protected array $meta_values = [],
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?string $type = null,
        public ?string $site_name = null,
        public ?string $locale = null,
        public ?string $theme_color = null,
    ) {
    }

    public static function make(string $url): OpenGraphItem
    {
        $og = new OpenGraphItem($url);

        $client = new Client();
        $response = $client->get($og->original_url);
        $og->html = $response->getBody()->getContents();

        $og->meta_values = $og->setMetaValues();
        $og->convertMetaValues();

        $og->url = $og->checkUrl();
        $og->image = $og->checkImage();

        return $og;
    }

    public function isEmpty()
    {
        return empty($this->title) && empty($this->description) && empty($this->image);
    }

    private function setMetaValues()
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadhtml($this->html);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        $meta_nodes = [
            'title' => [
                $xpath->query('//meta[@property="og:title"]/@content'),
                $xpath->query('//meta[@name="twitter:title"]/@content'),
                $xpath->query('//title'),
            ],
            'description' => [
                $xpath->query('//meta[@property="og:description"]/@content'),
                $xpath->query('//meta[@name="twitter:description"]/@content'),
                $xpath->query('//meta[@name="description"]/@content'),
            ],
            'image' => [
                $xpath->query('//meta[@property="og:image"]/@content'),
                $xpath->query('//meta[@name="twitter:image"]/@content'),
            ],
            'url' => [
                $xpath->query('//meta[@property="og:url"]/@content'),
                $xpath->query('//meta[@property="twitter:url"]/@content'),
            ],
            'type' => [
                $xpath->query('//meta[@property="og:type"]/@content'),
                $xpath->query('//meta[@name="twitter:card"]/@content'),
            ],
            'site_name' => [
                $xpath->query('//meta[@property="og:site_name"]/@content'),
                $xpath->query('//meta[@name="twitter:site"]/@content'),
                $xpath->query('//meta[@name="twitter:creator"]/@content'),
            ],
            'locale' => [
                $xpath->query('//meta[@property="og:locale"]/@content'),
                $xpath->query('//meta[@name="twitter:creator"]/@content'),
            ],
            'theme_color' => [
                $xpath->query('//meta[@name="theme-color"]/@content'),
            ],
        ];

        $meta_values = [];
        foreach ($meta_nodes as $property => $query) {
            $meta_values[$property] = $this->extractMeta($query);
        }

        return $meta_values;
    }

    private function convertMetaValues()
    {
        $this->title = $this->convertMetaValue('title');
        $this->description = $this->convertMetaValue('description');
        $this->image = $this->convertMetaValue('image');
        $this->url = $this->convertMetaValue('url');
        $this->type = $this->convertMetaValue('type');
        $this->site_name = $this->convertMetaValue('site_name');
        $this->locale = $this->convertMetaValue('locale');
        $this->theme_color = $this->convertMetaValue('theme_color');
    }

    private function convertMetaValue(string $key): ?string
    {
        $value = $this->meta_values[$key] ?? null;

        if (! $value) {
            return null;
        }
        $value = html_entity_decode($value);
        $value = iconv('utf-8', 'latin1', $value);

        return $value;
    }

    /**
     * @param  DOMNodeList[]  $nodes
     */
    private function extractMeta(array $nodes): ?string
    {
        foreach ($nodes as $node) {
            if ($node->item(0)) {
                return $node->item(0)->nodeValue;
            }
        }

        return null;
    }

    private function checkUrl(): string
    {
        return filter_var($this->url, FILTER_VALIDATE_URL)
            ? $this->url
            : rtrim($this->original_url, '/');
    }

    private function checkImage(): ?string
    {
        if (0 === strpos($this->image, '/')) {
            return "{$this->url}{$this->image}";
        }

        return $this->image;
    }
}
