<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;

class IframelyService
{
    protected function __construct(
        protected string $base_uri,
        protected Client $client,
        protected ?string $media_url = null,
    ) {
    }

    public static function make(): self
    {
        $api = config('steward.iframely.api');
        $client = new Client([
            'base_uri' => $api,
            'http_errors' => false,
        ]);

        $iframely = new IframelyService($api, $client);

        return $iframely;
    }

    /**
     * @param  string  $media_url The media url to get the embed code.
     * @param  string  $endpoint Can be `oembed` or `iframely`
     */
    public function get(string $media_url, string $endpoint = 'oembed'): array
    {
        $query = http_build_query([
            'url' => $media_url,
        ]);
        $api = "/{$endpoint}?{$query}";

        $response = $this->client->get($api);
        $body = $response->getBody()->getContents();

        return json_decode($body, true);
    }
}
