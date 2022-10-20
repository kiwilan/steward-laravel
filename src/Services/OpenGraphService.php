<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraph;

class OpenGraphService
{
    protected function __construct(
        protected string $url,
        protected ?string $body = null,
        protected ?OpenGraph $openGraph = null,
    ) {
    }

    public static function make(string $url)
    {
        $service = new OpenGraphService($url);

        $client = new Client();
        $response = $client->get($url);
        $service->body = $response->getBody()->getContents();

        $openGraph = new OpenGraph();
        $openGraph->title = $service->extractMeta();

        return $service;
    }

    private function extractMeta(string $name = 'og:title'): ?string
    {
        $start = strpos($this->body, "property=\"{$name}\"");

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
