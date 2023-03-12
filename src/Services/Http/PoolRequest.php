<?php

namespace Kiwilan\Steward\Services\Http;

use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Services\Http\Utils\GuzzleOptions;
use Kiwilan\Steward\Services\Http\Utils\GuzzleRequest;
use Kiwilan\Steward\Services\Http\Utils\HttpModelQuery;
use stdClass;

class PoolRequest
{
    /** @var Collection<int,HttpModelQuery>|Collection<int,object>|string[] */
    protected mixed $requestsOrigin = null;

    /** @var Collection<string,mixed> */
    protected ?Collection $requests = null;

    /** @var Collection<string,Response> */
    protected ?Collection $fullfilled = null;

    /** @var Collection<string,mixed> */
    protected ?Collection $rejected = null;

    /** @var Collection<string,HttpResponse> */
    protected ?Collection $responses = null;

    protected function __construct(
        protected GuzzleOptions $options,
        //
        protected int $requestCount = 0,
        protected int $fullfilledCount = 0,
        protected int $rejectedCount = 0,
        //
        protected string $modelId = 'id',
        protected string $modelUrl = 'url',
    ) {
    }

    /**
     * Create PoolRequest instance.
     *
     * @param  Collection<int,HttpModelQuery>|Collection<int,object>|string[]  $requests
     */
    public static function make(iterable $requests): self
    {
        $self = new self(new GuzzleOptions());

        $self->requestsOrigin = $requests;
        $self->requestCount = count($requests);

        $self->requests = collect([]);
        $self->fullfilled = collect([]);
        $self->rejected = collect([]);
        $self->responses = collect([]);

        $self->requests = $self->transformRequests($requests);
        $self->setDefaultOptions();

        return $self;
    }

    /**
     * Set max curl handles, default is `100`.
     */
    public function setMaxCurlHandles(int $maximum = 100): self
    {
        $this->options->maxCurlHandles = $maximum;

        return $this;
    }

    /**
     * Set max redirects, default is `10`.
     */
    public function setMaxRedirects(int $maximum): self
    {
        $this->options->maxRedirects = $maximum;

        return $this;
    }

    /**
     * Set Guzzle timeout, default is `30`.
     */
    public function setTimeout(int $timeout): self
    {
        $this->options->timeout = $timeout;

        return $this;
    }

    /**
     * Set Guzzle concurrency, default is `5`.
     */
    public function setGuzzleConcurrency(int $maximum): self
    {
        $this->options->guzzleConcurrency = $maximum;

        return $this;
    }

    /**
     * Set model attribute with id, default is `id`.
     */
    public function setModelId(string $name = 'id'): self
    {
        $this->modelId = $name;

        return $this;
    }

    /**
     * Set model attribute with url, default is `url`.
     */
    public function setModelUrl(string $name = 'url'): self
    {
        $this->modelUrl = $name;

        return $this;
    }

    /**
     * Disable Guzzle Pool, default is `true`.
     */
    public function setNoPoolable(): self
    {
        $this->options->poolable = false;

        return $this;
    }

    /**
     * Set Guzzle Pool limit, default is `250` but you can update it into your config file.
     */
    public function setPoolLimit(int $limit): self
    {
        $this->options->poolLimit = $limit;

        return $this;
    }

    /**
     * @return Collection<string,mixed>
     */
    public function requests(): Collection
    {
        return $this->requests;
    }

    /**
     * @return Collection<string,Response>
     */
    public function fullfilled(): Collection
    {
        return $this->fullfilled;
    }

    /**
     * Get rejected responses.
     *
     * @return Collection<string,mixed>
     */
    public function rejected(): Collection
    {
        return $this->rejected;
    }

    /**
     * @return Collection<string,HttpResponse>
     */
    public function responses(): Collection
    {
        return $this->responses;
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

    public function modelId(): string
    {
        return $this->modelId;
    }

    public function modelUrl(): string
    {
        return $this->modelUrl;
    }

    public function options(): GuzzleOptions
    {
        return $this->options;
    }

    /**
     * Execute requests.
     */
    public function execute(): self
    {
        Artisan::call('cache:clear');

        /** @var Collection<string,string> */
        $urls = collect([]);

        // Prepare requests
        foreach ($this->requests as $item) {
            $urls->put($item->{$this->modelId}, $item->{$this->modelUrl});
        }

        $pool = GuzzleRequest::make($urls, $this->options);

        $this->fullfilled = $pool->fullfilled();
        $this->fullfilledCount = $pool->fullfilledCount();
        $this->rejectedCount = $pool->rejectedCount();
        $this->rejected = $pool->rejected();
        $this->responses = $this->toHttpResponse($this->fullfilled);

        return $this;
    }

    /**
     * Transform GuzzleHttp Response to HttpResponse.
     *
     * @param  Collection<int,?Response>  $responses
     * @return Collection<string,HttpResponse>
     */
    private function toHttpResponse(Collection $responses): Collection
    {
        /** @var Collection<string,HttpResponse> */
        $list = collect([]);

        foreach ($responses as $id => $response) {
            $response = HttpResponse::make($id, $response);
            $list->put($id, $response);
        }

        return $list;
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
