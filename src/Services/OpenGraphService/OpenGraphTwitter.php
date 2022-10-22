<?php

namespace Kiwilan\Steward\Services\OpenGraphService;

use GuzzleHttp\Client;
use Kiwilan\Steward\Services\SocialService\SocialServiceTwitter;

class OpenGraphTwitter
{
    protected function __construct(
        protected string $origin_url,
        protected SocialServiceTwitter $social,
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
        $social = SocialServiceTwitter::make($url);

        $twitter = new OpenGraphTwitter($url, $social);
        $twitter->open_graph = $twitter->setOpenGraph();

        return $twitter;
    }

    public function getOpenGraph(): ?OpenGraphItem
    {
        return $this->open_graph;
    }

    private function setOpenGraph(): OpenGraphItem
    {
        $og = new OpenGraphItem($this->origin_url);
        $response = $this->social->getResponse();

        $og->site_name = $response['provider_name'] ?? null;
        $og->title = $response['author_name'] ?? null;
        $og->site_url = $response['url'] ?? null;
        $og->description = html_entity_decode(strip_tags($response['html']));
        $og->theme_color = '#1DA1F2';

        return $og;
    }
}
