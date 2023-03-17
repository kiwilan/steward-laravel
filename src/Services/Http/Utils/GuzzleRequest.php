<?php

namespace Kiwilan\Steward\Services\Http\Utils;

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
    protected ?Collection $fullfilled = null;

    /** @var Collection<string,Response> */
    protected ?Collection $rejected = null;

    /** @var Collection<string,Response> */
    protected ?Collection $all = null;

    protected function __construct(
        protected GuzzleOptions $options,
        protected int $requestCount = 0,
        protected int $fullfilledCount = 0,
        protected int $rejectedCount = 0,
    ) {
        $this->fullfilled = collect([]);
        $this->rejected = collect([]);
        $this->all = collect([]);
    }

    /**
     * @param  Collection<string,string>  $requests
     */
    public static function make(iterable $requests, GuzzleOptions $options): GuzzleRequest
    {
        $self = new GuzzleRequest($options);

        $self->requests = $requests;
        $self->requestCount = count($requests);

        if ($self->options->poolable) {
            $res = $self->inParallels($requests);
        } else {
            $res = $self->inSeries($requests);
        }

        $self->fullfilled = $res->get('fullfilled');
        $self->rejected = $res->get('rejected');
        $self->all = $res->get('all');

        return $self;
    }

    /**
     * @return Collection<string,Response>
     */
    public function fullfilled(): Collection
    {
        return $this->fullfilled;
    }

    public function fullfilledCount(): int
    {
        return $this->fullfilledCount;
    }

    public function rejectedCount(): int
    {
        return $this->rejectedCount;
    }

    public function requestCount(): int
    {
        return $this->requestCount;
    }

    /**
     * @return Collection<string,Response>
     */
    public function rejected(): Collection
    {
        return $this->rejected;
    }

    /**
     * @return Collection<string,Response>
     */
    public function all(): Collection
    {
        return $this->all;
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
        $fullfilled = collect([]);

        /** @var Collection<int,Response> */
        $rejected = collect([]);

        foreach ($urls as $id => $url) {
            $client = new Client();
            $response = $client->get($url);
            $guzzle->put($id, $response);
        }

        foreach ($guzzle as $key => $value) {
            if ($value->getStatusCode() === 200) {
                $fullfilled->put($key, $value);
                $this->fullfilledCount++;
            } else {
                $rejected->put($key, $value);
                $this->rejectedCount++;
            }
        }

        return collect([
            'fullfilled' => $fullfilled,
            'rejected' => $rejected,
            'all' => $fullfilled->merge($rejected),
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

        $start_time = microtime(true);

        if ($urls_count > 0) {
            $firstUrl = $urls->first();
            $domain = parse_url($firstUrl, PHP_URL_HOST);
            $console->newLine();
            $console->print("  HttpService pool {$domain} with async requests...", 'bright-blue');

            if (! $this->options->poolable) {
                $console->print('  Pool is disabled!', 'red');
            }
            $console->print("  Pool is limited to {$this->options->poolLimit} from .env");
            $console->print("    - {$urls_count} requests", 'default');
            $console->print("    - Converted into {$chunks_size} chunks", 'default');
        }

        $fullfilled = collect([]);
        $rejected = collect([]);
        $all = collect([]);

        foreach ($chunks as $chunk_key => $chunk_urls) {
            $chunk_urls_count = count($chunk_urls);
            $current_chunk = $chunk_key + 1;
            $console->print("  Execute {$chunk_urls_count} requests from chunk {$current_chunk}...");

            $res = $this->pool($chunk_urls);

            $fullfilled = $fullfilled->merge($res->get('fullfilled'));
            $rejected = $rejected->merge($res->get('rejected'));
            $all = $all->merge($res->get('all'));
        }

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        $execution_time = number_format((float) $execution_time, 2, '.', '');
        $color = $this->rejectedCount > 0 ? 'bright-red' : 'bright-green';
        $console->print("  {$this->fullfilledCount} requests fullfilled, {$this->rejectedCount} requests rejected.", $color);
        $console->print("  Done in {$execution_time} seconds.");
        $console->newLine();

        return collect([
            'fullfilled' => $fullfilled,
            'rejected' => $rejected,
            'all' => $all,
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
        $fullfilled = collect([]);

        /** @var Collection<int,?mixed> */
        $rejected = collect([]);

        // Create GuzzleHttp pool.
        $pool = new Pool($client, $requests, [
            'concurrency' => $this->options->guzzleConcurrency,
            'fulfilled' => function (Response $response, $index) use ($fullfilled, $urls) {
                $response = $response->withHeader('Origin', $urls[$index] ?? null); // Add Origin header for URL
                $response = $response->withHeader('ID', $index ?? null);
                $fullfilled->put($index, $response);

                $this->fullfilledCount++;
            },
            'rejected' => function (mixed $reason, $index) use ($fullfilled, $rejected, $urls) {
                Log::warning('HttpService: one request rejected', [$reason, $index, $urls[$index]]);
                $response = new Response(
                    status: 500,
                    headers:[
                        'Origin' => $urls[$index] ?? null,
                        'ID' => $index ?? null,
                    ],
                    reason: $reason,
                );
                $fullfilled->put($index, $response);
                $rejected->put($index, $response);

                $this->rejectedCount++;
            },
        ]);

        // Execute pool.
        $pool->promise()->wait();

        $res = collect([]);
        $res->put('fullfilled', $fullfilled);
        $res->put('rejected', $rejected);
        $res->put('all', $fullfilled->merge($rejected));

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
