<?php

namespace Kiwilan\Steward\Services\Http;

use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Utils\Console;
use stdClass;

class HttpUtils
{
    /** @var Collection<string,mixed> */
    public ?Collection $requests = null;

    /** @var Collection<string,Response> */
    public ?Collection $responses = null;

    /** @var Collection<string,Response> */
    public ?Collection $responsesFailed = null;

    protected function __construct(
        public GuzzleOptions $options,
        //
        public int $requestCount = 0,
        public int $successCount = 0,
        public int $failedCount = 0,
        //
        public string $modelId = 'id',
        public string $modelUrl = 'url',
    ) {
    }

    /**
     * Create HttpUtils instance.
     *
     * @param  Collection<int,HttpModelQuery>|Collection<int,object>|string[]  $requests
     */
    public static function make(iterable $requests): self
    {
        $self = new self(new GuzzleOptions());

        $self->requestCount = count($requests);

        $self->requests = collect([]);
        $self->responses = collect([]);
        $self->responsesFailed = collect([]);

        $self->requests = $self->transformRequests($requests);
        $self->setDefaultOptions();

        return $self;
    }

    /**
     * Transform Collection to URL array with Model `$model_id` as key and `$model_url` as value.
     * Make `GET` request on each url.
     *
     * @return Collection<string,Response>
     */
    public function execute(): Collection
    {
        Artisan::call('cache:clear');

        /** @var Collection<string,string> */
        $urls = collect([]);

        // Prepare requests
        foreach ($this->requests as $item) {
            $urls->put($item->{$this->modelId}, $item->{$this->modelUrl});
        }

        $pool = GuzzleRequest::make($urls, $this->options);

        $this->responses = $pool->getResponses();
        $this->successCount = $pool->getSuccessCount();
        $this->failedCount = $pool->getFailedCount();
        $this->responsesFailed = $pool->getResponsesFailed();

        $console = Console::make();
        $console->print('Done.');
        $console->print("{$this->successCount} requests succeeded, {$this->failedCount} requests failed.");

        return $this->responses;
    }

    /**
     * @param  Collection<int,HttpModelQuery>|Collection<int,object>|string[]  $requests
     */
    private function transformRequests(mixed $requests): mixed
    {
        if (! is_iterable($requests)) {
            throw new \Exception('`$requests` must be an iterable');
        }

        $parsed = null;

        if ($requests instanceof Collection && is_object($requests->first())) {
            $parsed = $requests;
        } else {
            $parsed = $this->basicArrayToRequests($requests);
        }

        return $parsed;
    }

    /**
     * Set default options.
     *
     * - `poolable` from `steward.http.async_allow`
     * - `pool_limit` from `steward.http.pool_limit`
     */
    private function setDefaultOptions(): self
    {
        $this->options->poolable = config('steward.http.async_allow') ?? true;
        $this->options->poolLimit = config('steward.http.pool_limit') ?? 250;

        return $this;
    }

    /**
     * Transform Collection input to Collection of objects with `model_id` and `url` properties.
     *
     * @param  Collection<int,string>|string[]  $iterable
     * @return Collection<int,object>
     */
    private function basicArrayToRequests(mixed $iterable): Collection
    {
        /** @var Collection<int,object> */
        $requests = collect([]);

        foreach ($iterable as $key => $item) {
            $object = new stdClass();
            $object->id = $key;
            $object->url = $item;
            $requests->put($key, $object);
        }

        return $requests;
    }
}
