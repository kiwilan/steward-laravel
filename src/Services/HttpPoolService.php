<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Kiwilan\Steward\Utils\Console;

class HttpPoolService
{
    /** @var array<string,string> */
    protected array $urls = [];

    /** @var array<string,string> */
    protected array $headers = [];

    /** @var Collection<string,Response> */
    protected mixed $responses = null;

    /** @var Collection<string,mixed> */
    protected mixed $responsesFailed = null;

    protected function __construct(
        protected int $limit = 250,
        protected int $timeout = 100,
        protected int $retry = 3,
        protected int $retryDelay = 100,
        protected int $requestCount = 0,
        protected int $successCount = 0,
        protected int $failedCount = 0,
    ) {
    }

    /**
     * @param  array<string,string>  $urls
     * @param  string[]  $headers
     */
    public static function make(array $urls, array $headers = []): self
    {
        $self = new self();
        $self->urls = $urls;
        $self->headers = $headers;
        $self->limit = config('steward.http.pool_limit', 200);
        // $self->timeout = config('steward.http.pool_timeout', 10);
        $self->requestCount = count($urls);

        Console::make()->print('Execute Guzzle pool requests');
        Console::make()->print("Requests: $self->requestCount");
        Console::make()->print("Limit: $self->limit");

        $responses = $self->executePool();
        $responses = $self->parseResponses($responses);

        if ($self->failedCount > 0) {
            Console::make()->print("Failed requests: $self->failedCount");
        }

        $self->responses = collect($responses);

        Console::make()->print("Done.\n");

        return $self;
    }

    /**
     * @return Collection<string,Response>
     */
    public function responses(): Collection
    {
        return $this->responses;
    }

    public function responsesFailed(): Collection
    {
        return $this->responsesFailed;
    }

    public function requestCount(): int
    {
        return $this->requestCount;
    }

    public function successCount(): int
    {
        return $this->successCount;
    }

    public function failedCount(): int
    {
        return $this->failedCount;
    }

    /**
     * @param  array<string,mixed>  $responses
     * @return Collection<string,Response>
     */
    private function parseResponses(array $responses): Collection
    {
        $parsedResponses = collect();
        $parsedResponsesFailed = collect();

        foreach ($responses as $key => $response) {
            if ($response instanceof Response) {
                $parsedResponses->put($key, $response);
                $this->successCount++;
            } else {
                $this->failedCount++;
                $parsedResponsesFailed->put($key, $response);
            }
        }

        Console::make()->print('Count '.count($responses).' responses, '.$this->failedCount.' failed requests');

        $this->responsesFailed = $parsedResponsesFailed;

        return $parsedResponses;
    }

    /**
     * @return array<string,Response>
     */
    private function executePool(): array
    {
        $chunks = array_chunk($this->urls, $this->limit, true);
        $responses = [];

        foreach ($chunks as $key => $chunk) {
            Console::make()->print("Chunk: $key, ".count($chunk).' requests');
            $res = Http::pool(function (Pool $pool) use ($chunk) {
                foreach ($chunk as $key => $url) {
                    $pool->as($key)
                        ->withHeaders($this->headers)
                        ->timeout($this->timeout)
                        ->retry($this->retry, $this->retryDelay)
                        ->get($url)
                    ;
                }
            });

            $responses = array_merge($responses, $res);
        }

        return $responses;
    }
}
