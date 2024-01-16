<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;

/**
 * Use your own Iframely instance
 *
 * @see https://iframely.com/docs/host
 */
class IframelyService
{
    protected function __construct(
        protected string $base_uri,
        protected Client $client,
        protected ?string $api_key = null,
        protected bool $omit_script = false,
    ) {
    }

    /**
     * @param  string  $api  Iframely instance to use, can be set from `steward.iframely.api`
     */
    public static function make(?string $api = null): self
    {
        if (! $api) {
            $api = \Kiwilan\Steward\StewardConfig::iframelyApi();
        }

        $api_key = \Kiwilan\Steward\StewardConfig::iframelyKey();

        $client = new Client([
            'base_uri' => $api,
            'http_errors' => false,
        ]);

        $iframely = new IframelyService($api, $client);
        $iframely->api_key = $api_key;

        return $iframely;
    }

    public function omitScript(bool $omit_script): self
    {
        $this->omit_script = $omit_script;

        return $this;
    }

    /**
     * @param  string  $media_url  The media url to get the embed code.
     * @param  string  $endpoint  Can be `oembed` or `iframely`
     */
    public function get(string $media_url, string $endpoint = 'oembed'): array
    {
        $query = [
            'url' => $media_url,
            'api_key' => $this->api_key,
        ];

        if ($this->omit_script) {
            $query['omit_script'] = 1;
        }
        $query_params = http_build_query($query);
        $api = "/{$endpoint}?{$query_params}";

        $response = $this->client->get($api);
        $body = $response->getBody()->getContents();

        return json_decode($body, true);
    }
}
