<?php

namespace Kiwilan\Steward\Services;

use Closure;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kiwilan\Steward\Services\HttpService\HttpServiceQuery;
use Kiwilan\Steward\Services\HttpService\HttpServiceResponse;
use Kiwilan\Steward\Utils\Console;
use stdClass;

/**
 * Manage requests to external API.
 *
 * @property int                                 $max_curl_handles   Guzzle max curl handles
 * @property int                                 $max_redirects      Guzzle max redirects
 * @property int                                 $timeout            Guzzle timeout
 * @property int                                 $guzzle_concurrency Guzzle concurrency
 * @property Collection<int,object>              $requests           List of models to request
 * @property string                              $model_url          Field name of url into each model of `collection`
 * @property string                              $model_id           model_id, default is `model_id`
 * @property Collection<int,HttpServiceResponse> $responses          List of responses
 */
class HttpService
{
    public function __construct(
        public int $max_curl_handles = 100,
        public int $max_redirects = 10,
        public int $timeout = 30,
        public int $guzzle_concurrency = 5,
        public ?Collection $requests = null,
        public ?string $model_url = 'url',
        public string $model_id = 'id',
        public bool $poolable = true,
        public int $pool_limit = 250,
        public ?Collection $responses = null,
    ) {
        $this->requests = collect([]);
        $this->responses = collect([]);
    }

    /**
     * Create HttpService instance.
     *
     * @param  Collection<int,HttpServiceQuery>|Collection<int,object>|string[]  $requests
     */
    public static function make(mixed $requests): self
    {
        $service = new HttpService();
        if (0 === count($requests)) {
            throw new \Exception('Requests must be an array or a collection');
        }

        if ($requests instanceof Collection && is_object($requests->first())) {
            $service->requests = $requests;
        } else {
            $service->requests = $service->arrayToRequests($requests);
        }
        $service->setDefaultOptions();

        return $service;
    }

    /**
     * Parse responses from HttpService.
     *
     * @param  Collection<int|string,HttpServiceResponse>  $responses
     * @param  Collection<int,object>  $queries
     * @param  Closure  $closure   Closure to parse response
     * @return Collection<int|string,Collection<int|string,mixed>> Two Collections with `fullfilled` and `rejected` keys
     */
    public static function parseResponses(Collection $responses, Collection $queries, Closure $closure)
    {
        $fullfilled = collect([]);
        $rejected = collect([]);

        foreach ($responses as $id => $response) {
            $query = $queries->first(fn (HttpServiceQuery $query) => $query->model_id === $id);
            if (null !== $query) {
                $parsed = $closure($query, $response);
                $fullfilled->put($id, $parsed);
            } else {
                $rejected->put($id, $response);
            }
        }

        $responses = collect([]);
        $responses->put('fullfilled', $fullfilled);
        $responses->put('rejected', $rejected);

        return $responses;
    }

    /**
     * Set default options.
     *
     * - `poolable` from `steward.http.async_allow`
     * - `pool_limit` from `steward.http.pool_limit`
     */
    public function setDefaultOptions()
    {
        $this->poolable = config('steward.http.async_allow') ?? true;
        $this->pool_limit = config('steward.http.pool_limit') ?? 250;
    }

    /**
     * Set max curl handles.
     */
    public function setMaxCurlHandles(int $max_curl_handles = 100): self
    {
        $this->max_curl_handles = $max_curl_handles;

        return $this;
    }

    public function setMaxRedirects(int $max_redirects): self
    {
        $this->max_redirects = $max_redirects;

        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function setGuzzleConcurrency(int $guzzle_concurrency): self
    {
        $this->guzzle_concurrency = $guzzle_concurrency;

        return $this;
    }

    public function setModelId(string $model_id = 'id'): self
    {
        $this->model_id = $model_id;

        return $this;
    }

    public function setModelUrl(string $model_url = 'url'): self
    {
        $this->model_url = $model_url;

        return $this;
    }

    public function setPoolable(bool $poolable): self
    {
        $this->poolable = $poolable;

        return $this;
    }

    public function setPoolLimit(int $pool_limit): self
    {
        $this->pool_limit = $pool_limit;

        return $this;
    }

    /**
     * Transform Collection to URL array with Model `$model_id` as key and `$model_url` as value.
     * Make `GET` request on each url.
     *
     * @return Collection<int,HttpServiceResponse>
     */
    public function execute()
    {
        Artisan::call('cache:clear');

        $urls = collect([]);

        // Prepare requests
        foreach ($this->requests as $item) {
            $urls->put($item->{$this->model_id}, $item->{$this->model_url});
        }

        if ($this->poolable) {
            $this->executeRequestsPool($urls);
        } else {
            $this->executeRequests($urls);
        }

        return $this->responses;
    }

    /**
     * Transform GuzzleHttp Response to HttpServiceResponse.
     *
     * @param  Collection<int,?Response>  $responses
     * @return Collection<int,HttpServiceResponse>
     */
    public function setResponses(Collection $responses)
    {
        /** @var Collection<int,HttpServiceResponse> */
        $list = collect([]);
        foreach ($responses as $id => $response) {
            $response = HttpServiceResponse::make($id, $response);
            $list->put($id, $response);
        }

        return $list;
    }

    /**
     * Execute requests with Guzzle Pool.
     *
     * @param  Collection<int,string>  $urls
     */
    private function executeRequestsPool(Collection $urls)
    {
        $console = Console::make();

        /**
         * Chunk by limit into arrays.
         */
        $urls_count = count($urls);
        /**
         * @var Collection<int,Collection<int,string>> $chunks
         */
        $chunks = $urls->chunk($this->pool_limit);

        $chunks_size = count($chunks);

        if ($urls_count > 0) {
            $console->print('HttpService will setup async requests...');
            $console->print("Pool is limited to {$this->pool_limit} from .env, {$urls_count} requests will become {$chunks_size} chunks.");
            $console->newLine();
        }

        /**
         * async query on each chunk.
         */
        foreach ($chunks as $chunk_key => $chunk_urls) {
            $chunk_urls_count = count($chunk_urls);
            $current_chunk = $chunk_key + 1;
            $console->print("Execute {$chunk_urls_count} requests from chunk {$current_chunk}...");

            $this->usePool($chunk_urls);
        }
    }

    /**
     * Execute requests.
     *
     * @param  Collection<int,string>  $urls
     */
    private function executeRequests(Collection $urls)
    {
        foreach ($urls as $id => $url) {
            $client = new Client();
            $guzzle = $client->get($url);

            $response = HttpServiceResponse::make($id, $guzzle);
            $this->responses->put($id, $response);
        }
    }

    /**
     * Transform Collection input to Collection of objects with `model_id` and `url` properties.
     *
     * @param  Collection<int,string>|string[]  $array
     * @return Collection<int,object>
     */
    private function arrayToRequests(mixed $array)
    {
        /** @var Collection<int,object> */
        $requests = collect([]);
        foreach ($array as $key => $item) {
            $object = new stdClass();
            $object->id = $key;
            $object->url = $item;
            $requests->put($key, $object);
        }

        return $requests;
    }

    /**
     * GuzzleHttp pool.
     *
     * From: https://nunomaduro.com/speed_up_your_php_http_guzzle_requests_with_concurrency
     */
    // @phpstan-ignore-next-line
    private function useAsyncSettle(array $urls)
    {
        if (extension_loaded('curl')) {
            $handler = HandlerStack::create(
                new CurlMultiHandler([
                    'handle_factory' => new CurlFactory($this->max_curl_handles),
                    'select_timeout' => $this->timeout,
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
            RequestOptions::CONNECT_TIMEOUT => $this->timeout,
            // Allow redirects?
            // Set this to RequestOptions::ALLOW_REDIRECTS => false, to turn off.
            RequestOptions::ALLOW_REDIRECTS => [
                'max' => $this->max_redirects,        // allow at most 10 redirects.
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

    /**
     * Create and make `GET` requests from `$urls`.
     *
     * @param  Collection<int,string>  $urls
     * @return Collection<int,HttpServiceResponse>
     */
    private function usePool(Collection $urls)
    {
        // Need to have curl extension.
        if (extension_loaded('curl')) {
            $handler = HandlerStack::create(
                new CurlMultiHandler([
                    'handle_factory' => new CurlFactory($this->max_curl_handles),
                    'select_timeout' => $this->timeout,
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
            RequestOptions::CONNECT_TIMEOUT => $this->timeout,
            // Allow redirects?
            // Set this to RequestOptions::ALLOW_REDIRECTS => false, to turn off.
            RequestOptions::ALLOW_REDIRECTS => [
                'max' => $this->max_redirects,        // allow at most 10 redirects.
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

        // Create GuzzleHttp pool.
        $pool = new Pool($client, $requests, [
            'concurrency' => $this->guzzle_concurrency,
            'fulfilled' => function (Response $response, $index) use ($responses, $urls) {
                $response = $response->withHeader('Origin', $urls[$index] ?? null); // Add Origin header for URL
                $responses[$index] = $response;
            },
            'rejected' => function (mixed $reason, $index) use ($responses, $urls) {
                $url = $urls[$index];
                Log::warning('HttpService: one request rejected', [$reason, $index, $url]);
                $responses[$index] = null;
            },
        ]);

        // Execute pool.
        $pool->promise()->wait();
        // Transform GuzzleHttp Response to HttpServiceResponse.
        $this->responses = $this->setResponses($responses);

        return $this->responses;
    }
}
