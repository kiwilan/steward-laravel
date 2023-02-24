<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HttpPoolService
{
    /** @var string[] */
    protected array $urls = [];

    /**
     * @param  string[]  $urls
     * @return Collection<int,Response>
     */
    public static function make(array $urls)
    {
        $self = new self();
        $self->urls = $urls;

        $responses = $self->executePool();
        $responses = $self->parseResponses($responses);

        return collect($responses);
    }

    /**
     * @param  array<int,Response>  $responses
     * @return Collection<int,Response>
     */
    private function parseResponses(array $responses): Collection
    {
        $parsedResponses = collect();

        foreach ($responses as $key => $response) {
            if ($response instanceof Response) {
                $parsedResponses->put($key, $response);
            }
        }

        return $parsedResponses;
    }

    /**
     * @return array<int,Response>
     */
    private function executePool(): array
    {
        $limit = config('steward.http.pool_limit', 250);
        $chunks = array_chunk($this->urls, $limit);
        $responses = [];

        foreach ($chunks as $key => $chunk) {
            $res = Http::pool(function (Pool $pool) use ($chunk) {
                foreach ($chunk as $key => $url) {
                    $pool->as($key)->get($url);
                }
            });

            $responses = array_merge($responses, $res);
        }

        return $responses;
    }
}
