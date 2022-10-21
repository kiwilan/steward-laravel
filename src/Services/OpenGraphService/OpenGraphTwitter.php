<?php

namespace Kiwilan\Steward\Services\OpenGraphService;

use GuzzleHttp\Client;

class OpenGraphTwitter
{
    protected function __construct(
        protected string $origin_url,
        protected ?string $media_id = null,
        protected array $api = [],
        protected ?OpenGraphItem $open_graph = null,
    ) {
    }

    /**
     * @see https://publish.twitter.com
     * @see https://developer.twitter.com/en/docs/twitter-for-websites/embedded-tweets/overview
     * @see https://developer.twitter.com/en/docs/twitter-for-websites/webpage-properties
     */
    public static function make(string $url): self
    {
        $twitter = new OpenGraphTwitter($url);

        $regex = '/\\/(\\d+)\\/?$/is';
        if (preg_match($regex, $url, $matches)) {
            $twitter->media_id = $matches[1]
                ? $matches[1]
                : ($matches[0] ?? null);
        }

        $client = new Client();

        $api = 'https://publish.twitter.com/oembed?url=';
        $endpoint = "{$api}{$url}";
        $endpoint .= '&align=center';
        $endpoint .= '&conversation=none';
        $endpoint .= '&hide_media=true';
        $endpoint .= '&lang=fr';
        $endpoint .= '&theme=dark';

        $res = $client->get($endpoint);
        $body = $res->getBody()->getContents();

        if ($body) {
            $twitter->api = json_decode($body, true);
            $twitter->open_graph = $twitter->setOpenGraph();
        }

        return $twitter;
    }

    public function getOpenGraph(): ?OpenGraphItem
    {
        return $this->open_graph;
    }

    public function getHtml(): ?string
    {
        return $this->api['html'] ?? null;
    }

    private function setOpenGraph(): OpenGraphItem
    {
        $og = new OpenGraphItem($this->origin_url);

        $og->site_name = $this->api['provider_name'] ?? null;
        $og->title = $this->api['author_name'] ?? null;
        $og->site_url = $this->api['url'] ?? null;
        $og->description = $this->setDescription();
        $og->theme_color = '#1DA1F2';

        return $og;
    }

    private function setDescription(): string
    {
        return html_entity_decode(strip_tags($this->api['html']));
    }
}
