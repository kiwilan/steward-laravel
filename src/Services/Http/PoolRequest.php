<?php

namespace Kiwilan\Steward\Services\Http;

use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Services\Http\Utils\GuzzleOptions;
use Kiwilan\Steward\Services\Http\Utils\GuzzleRequest;
use Kiwilan\Steward\Services\Http\Utils\ObjectQuery;
use stdClass;

class PoolRequest
{
    /** @var Collection<int,ObjectQuery>|Collection<int,object>|string[] */
    protected mixed $requestsOrigin = null;

    /** @var Collection<string,mixed> */
    protected ?Collection $requests = null;

    /** @var Collection<string,HttpResponse> */
    protected ?Collection $fullfilled = null;

    /** @var Collection<string,HttpResponse> */
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
        public string $identifier = 'id',
        public string $url = 'url',
    ) {
    }

    /**
     * Create PoolRequest instance.
     *
     * @param  Collection<int,ObjectQuery>|Collection<int,object>|string[]  $requests
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
    public function setIdentifier(string $field = 'id'): self
    {
        $this->identifier = $field;

        return $this;
    }

    /**
     * Set model attribute with url, default is `url`.
     */
    public function setUrl(string $field = 'url'): self
    {
        $this->url = $field;

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
     * @return Collection<string,HttpResponse>
     */
    public function fullfilled(): Collection
    {
        return $this->fullfilled;
    }

    /**
     * Get rejected responses.
     *
     * @return Collection<string,HttpResponse>
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
            if (! $item) {
                continue;
            }

            $identifier = null;

            if (method_exists($item, 'get'.ucfirst($this->identifier))) {
                $identifier = $item->{'get'.ucfirst($this->identifier)}();
            } elseif (method_exists($item, $this->identifier)) {
                $identifier = $item->{$this->identifier}();
            } elseif (property_exists($item, $this->identifier)) {
                $identifier = $item->{$this->identifier};
            }

            $url = null;

            if (method_exists($item, 'get'.ucfirst($this->url))) {
                $url = $item->{'get'.ucfirst($this->url)}();
            } elseif (method_exists($item, $this->url)) {
                $url = $item->{$this->url}();
            } elseif (property_exists($item, $this->url)) {
                $url = $item->{$this->url};
            }

            if ($url) {
                $urls->put($identifier, $url);
            }
        }

        $pool = GuzzleRequest::make($urls, $this->options);

        $this->responses = $this->toHttpResponse($pool->all());

        $this->fullfilled = $this->responses->filter(fn (HttpResponse $response) => $response->isSuccess());
        $this->fullfilledCount = $this->fullfilled->count();
        $this->rejected = $this->responses->filter(fn (HttpResponse $response) => ! $response->isSuccess());
        $this->rejectedCount = $this->rejected->count();

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
            $id = $response->getHeader('ID')[0];
            $response = HttpResponse::make($id, $response);
            $list->put($id, $response);
        }

        return $list;
    }

    /**
     * @param  Collection<int,ObjectQuery>|Collection<int,object>|string[]  $requests
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
        $this->options->poolable = \Kiwilan\Steward\StewardConfig::httpAsyncAllow();
        $this->options->poolLimit = \Kiwilan\Steward\StewardConfig::httpPoolLimit();

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
            if (is_object($item)) {
                $requests->put($key, $item);

                continue;
            }
            $object = new stdClass();
            $object->id = $key;
            $object->url = $item;
            $requests->put($key, $object);
        }

        return $requests;
    }
}
