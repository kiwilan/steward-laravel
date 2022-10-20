<?php

namespace Kiwilan\Steward\Services\OpenGraphService;

use GuzzleHttp\Client;

class OpenGraphTwitter
{
    protected function __construct(
        protected ?string $media_id = null,
        protected array $api = [],
    ) {
    }

    /**
     * @see https://publish.twitter.com
     * @see https://developer.twitter.com/en/docs/twitter-for-websites/embedded-tweets/overview
     * @see https://developer.twitter.com/en/docs/twitter-for-websites/webpage-properties
     */
    public static function make(string $url): self
    {
        $og = new OpenGraphTwitter();

        $regex = '/\\/(\\d+)\\/?$/is';
        if (preg_match($regex, $url, $matches)) {
            $og->media_id = $matches[1]
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
        if (! $body) {
            return $og;
        }

        $og->media_id = json_decode($body, true);

        return $og;
    }

    public function getMediaId(): ?string
    {
        return $this->media_id;
    }

    public function getApi(): array
    {
        return $this->api;
    }
}
