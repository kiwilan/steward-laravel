<?php

namespace Kiwilan\Steward\Services\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlFactory;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kiwilan\Steward\Utils\Console;

class GuzzleRequest
{
    /** @var Collection<string,string> */
    protected ?Collection $requests = null;

    /** @var Collection<string,Response> */
    protected ?Collection $responses = null;

    /** @var Collection<string,mixed> */
    protected ?Collection $responsesFailed = null;

    protected function __construct(
        protected GuzzleOptions $options,
        protected int $requestCount = 0,
        protected int $successCount = 0,
        protected int $failedCount = 0,
    ) {
    }

    /**
     * @param  Collection<string,string>  $requests
     */
    public static function make(iterable $requests, GuzzleOptions $options): GuzzleRequest
    {
        $self = new GuzzleRequest($options);

        $self->requests = $requests;
        $self->requestCount = count($requests);
        $self->responses = collect([]);
        $self->responsesFailed = collect([]);

        if ($self->options->poolable) {
            $res = $self->inParallels($requests);
        } else {
            $res = $self->inSeries($requests);
        }

        $self->responses = $res->get('fullfilled');
        $self->responsesFailed = $res->get('rejected');

        return $self;
    }

    /**
     * @return Collection<string,Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    /**
     * @return Collection<string,mixed>
     */
    public function getResponsesFailed(): Collection
    {
        return $this->responsesFailed;
    }

    /**
     * Execute requests (no pools).
     *
     * @param  Collection<int,string>  $urls
     * @return Collection<string,mixed>
     */
    private function inSeries(Collection $urls): Collection
    {
        /** @var Collection<int,Response> */
        $guzzle = collect([]);

        /** @var Collection<int,Response> */
        $responses = collect([]);

        /** @var Collection<int,Response> */
        $failed = collect([]);

        foreach ($urls as $id => $url) {
            $client = new Client();
            $response = $client->get($url);
            $guzzle->put($id, $response);
        }

        foreach ($guzzle as $key => $value) {
            if ($value->getStatusCode() === 200) {
                $responses->put($key, $value);
                $this->successCount++;
            } else {
                $failed->put($key, $value);
                $this->failedCount++;
            }
        }

        return collect([
            'fullfilled' => $responses,
            'rejected' => $failed,
        ]);
    }

    /**
     * Execute requests with Guzzle Pool.
     *
     * @param  Collection<int,string>  $urls
     */
    private function inParallels(Collection $urls)
    {
        $console = Console::make();

        /**
         * Chunk by limit into arrays.
         */
        $urls_count = count($urls);

        /**
         * @var Collection<int,Collection<int,string>> $chunks
         */
        $chunks = $urls->chunk($this->options->poolLimit);

        $chunks_size = count($chunks);

        if ($urls_count > 0) {
            $console->print('HttpService will setup async requests...');
            $console->print("Pool is limited to {$this->options->poolLimit} from .env, {$urls_count} requests will become {$chunks_size} chunks.");
            $console->newLine();
        }

        $responses = collect([]);
        $responsesFailed = collect([]);

        // async query on each chunk.
        foreach ($chunks as $chunk_key => $chunk_urls) {
            $chunk_urls_count = count($chunk_urls);
            $current_chunk = $chunk_key + 1;
            $console->print("Execute {$chunk_urls_count} requests from chunk {$current_chunk}...");

            $res = $this->pool($chunk_urls);

            $responses = $responses->merge($res->get('fullfilled'));
            $responsesFailed = $responsesFailed->merge($res->get('rejected'));
        }

        return collect([
            'fullfilled' => $responses,
            'rejected' => $responsesFailed,
        ]);
    }

    /**
     * Create and make `GET` requests from `$urls`.
     *
     * @param  Collection<int,string>  $urls
     * @return Collection<string,mixed>
     */
    private function pool(Collection $urls): Collection
    {
        // Need to have curl extension.
        if (extension_loaded('curl')) {
            $handler = HandlerStack::create(
                new CurlMultiHandler([
                    'handle_factory' => new CurlFactory($this->options->maxCurlHandles),
                    'select_timeout' => $this->options->timeout,
                ])
            );
        } else {
            $handler = HandlerStack::create();
        }

        // Create the client and turn off Exception throwing.
        $client = new Client([
            // No exceptions of 404, 500 etc.
            'http_errors' => false,
            'handler' => $handler,
            // Curl options, any CURLOPT_* option is available
            'curl' => [
                // CURLOPT_BINARYTRANSFER => true,
            ],
            RequestOptions::CONNECT_TIMEOUT => $this->options->timeout,
            // Allow redirects?
            // Set this to RequestOptions::ALLOW_REDIRECTS => false, to turn off.
            RequestOptions::ALLOW_REDIRECTS => [
                'max' => $this->options->maxRedirects,        // allow at most 10 redirects.
                'strict' => true,      // use "strict" RFC compliant redirects.
                'track_redirects' => false,
            ],
        ]);

        // Prepare requests with `id` and `url`.
        $requests = [];

        foreach ($urls as $id => $url) {
            if ($url) {
                $requests[$id] = new Request('GET', $url);
            }
        }

        /** @var Collection<int,?Response> */
        $responses = collect([]);

        /** @var Collection<int,?mixed> */
        $responsesFailed = collect([]);

        // Create GuzzleHttp pool.
        $pool = new Pool($client, $requests, [
            'concurrency' => $this->options->guzzleConcurrency,
            'fulfilled' => function (Response $response, $index) use ($responses, $urls) {
                // dump($response->getBody()->getContents());
                $response = $response->withHeader('Origin', $urls[$index] ?? null); // Add Origin header for URL
                // $responses[$index] = $response;
                $responses->put($index, $response);

                $this->successCount++;
            },
            'rejected' => function (mixed $reason, $index) use ($responses, $responsesFailed, $urls) {
                Log::warning('HttpService: one request rejected', [$reason, $index, $urls[$index]]);
                $responses->put($index, null);
                $responsesFailed->put($index, $reason);

                $this->failedCount++;
            },
        ]);

        // Execute pool.
        $pool->promise()->wait();

        $res = collect([]);
        $res->put('fullfilled', $responses);
        $res->put('rejected', $responsesFailed);

        return $res;
    }

    /**
     * GuzzleHttp pool.
     *
     * From: https://nunomaduro.com/speed_up_your_php_http_guzzle_requests_with_concurrency
     */
    // @phpstan-ignore-next-line
    private function poolAsyncSettle(array $urls)
    {
        if (extension_loaded('curl')) {
            $handler = HandlerStack::create(
                new CurlMultiHandler([
                    'handle_factory' => new CurlFactory($this->options->maxCurlHandles),
                    'select_timeout' => $this->options->timeout,
                ])
            );
        } else {
            $handler = HandlerStack::create();
        }

        $client = new Client([
            // No exceptions of 404, 500 etc.
            'http_errors' => false,
            'handler' => $handler,
            // Curl options, any CURLOPT_* option is available
            'curl' => [
                // CURLOPT_BINARYTRANSFER => true,
            ],
            RequestOptions::CONNECT_TIMEOUT => $this->options->timeout,
            // Allow redirects?
            // Set this to RequestOptions::ALLOW_REDIRECTS => false, to turn off.
            RequestOptions::ALLOW_REDIRECTS => [
                'max' => $this->options->maxRedirects,        // allow at most 10 redirects.
                'strict' => true,      // use "strict" RFC compliant redirects.
                'track_redirects' => false,
            ],
        ]);

        $promises = [];

        foreach ($urls as $id => $url) {
            $promises[$id] = $client->getAsync($url);
        }

        $responses = Utils::settle(
            Utils::unwrap($promises),
        )->wait();

        $responses_list = [];

        foreach ($responses as $id => $response) {
            /** @var string */
            $state = $response['state']; // "fulfilled"

            /** @var \GuzzleHttp\Psr7\Response */
            $value = $response['value']; // "fulfilled"

            $body = json_decode($value->getBody()->getContents(), true);
            $responses_list[$id] = $body;
        }

        return $responses_list;
    }
}
