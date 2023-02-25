<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Kiwilan\Steward\Utils\Console;

class HttpPoolService
{
    /** @var string[] */
    protected array $urls = [];

    /** @var string[] */
    protected array $headers = [];

    /** @var Collection<string,Response> */
    protected mixed $responses = null;

    protected function __construct(
        protected int $limit = 250,
        protected int $failedRequests = 0,
    ) {
    }

    /**
     * @param  string[]  $urls
     * @param  string[]  $headers
     */
    public static function make(array $urls, array $headers = []): self
    {
        $self = new self();
        $self->urls = $urls;
        $self->headers = $headers;
        $self->limit = config('steward.http.pool_limit', 250);

        Console::make()->print("Limit: $self->limit");

        $responses = $self->executePool();
        $responses = $self->parseResponses($responses);

        if ($self->failedRequests > 0) {
            Console::make()->print("Failed requests: $self->failedRequests");
        }

        $self->responses = collect($responses);

        return $self;
    }

    /**
     * @return Collection<string,Response>
     */
    public function responses(): Collection
    {
        return $this->responses;
    }

    public function failedRequests(): int
    {
        return $this->failedRequests;
    }

    /**
     * @param  array<string,mixed>  $responses
     * @return Collection<string,Response>
     */
    private function parseResponses(array $responses): Collection
    {
        $parsedResponses = collect();

        foreach ($responses as $key => $response) {
            if ($response instanceof Response) {
                $parsedResponses->put($key, $response);
            } else {
                $this->failedRequests++;
            }
        }

        Console::make()->print('Count '.count($responses).' responses, '.$this->failedRequests.' failed requests');

        return $parsedResponses;
    }

    /**
     * @return array<string,Response>
     */
    private function executePool(): array
    {
        $chunks = array_chunk($this->urls, $this->limit);
        $responses = [];

        foreach ($chunks as $key => $chunk) {
            Console::make()->print("Chunk: $key, ".count($chunk).' requests');
            $res = Http::pool(function (Pool $pool) use ($chunk) {
                foreach ($chunk as $key => $url) {
                    $pool->as($key)
                        ->withHeaders($this->headers)
                        ->get($url)
                    ;
                }
            });

            $responses = array_merge($responses, $res);
        }

        return $responses;
    }
}
