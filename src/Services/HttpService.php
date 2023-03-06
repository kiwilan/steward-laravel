<?php

namespace Kiwilan\Steward\Services;

use Closure;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\HttpModelQuery;
use Kiwilan\Steward\Services\Http\HttpResponse;
use Kiwilan\Steward\Services\Http\HttpUtils;

class HttpService
{
    /** @var Collection<string,HttpResponse> */
    protected ?Collection $responses = null;

    protected function __construct(
        protected HttpUtils $utils,
    ) {
    }

    /**
     * Create HttpService instance.
     *
     * @param  Collection<int,HttpModelQuery>|Collection<int,object>|string[]  $requests
     */
    public static function make(iterable $requests): self
    {
        $self = new HttpService(
            HttpUtils::make($requests)
        );

        $self->responses = collect([]);

        return $self;
    }

    /**
     * Set max curl handles.
     */
    public function setMaxCurlHandles(int $maximum = 100): self
    {
        $this->utils->options->maxCurlHandles = $maximum;

        return $this;
    }

    public function setMaxRedirects(int $maximum): self
    {
        $this->utils->options->maxRedirects = $maximum;

        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->utils->options->timeout = $timeout;

        return $this;
    }

    public function setGuzzleConcurrency(int $maximum): self
    {
        $this->utils->options->guzzleConcurrency = $maximum;

        return $this;
    }

    public function setModelId(string $name = 'id'): self
    {
        $this->utils->modelId = $name;

        return $this;
    }

    public function setModelUrl(string $name = 'url'): self
    {
        $this->utils->modelUrl = $name;

        return $this;
    }

    public function setPoolable(bool $allowed = true): self
    {
        $this->utils->options->poolable = $allowed;

        return $this;
    }

    public function setPoolLimit(int $limit): self
    {
        $this->utils->options->poolLimit = $limit;

        return $this;
    }

    public function utils(): HttpUtils
    {
        return $this->utils;
    }

    /**
     * Transform Collection to URL array with Model `$model_id` as key and `$model_url` as value.
     * Make `GET` request on each url.
     */
    public function execute(): self
    {
        $responses = $this->utils->execute();
        $this->responses = $this->toHttpResponse($responses);

        return $this;
    }

    /**
     * Get responses.
     *
     * @return Collection<string,HttpResponse>
     */
    public function responses(): Collection
    {
        return $this->responses;
    }

    /**
     * Transform GuzzleHttp Response to HttpResponse.
     *
     * @param  Collection<int,?Response>  $responses
     * @return Collection<string,HttpResponse>
     */
    public function toHttpResponse(Collection $responses): Collection
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
     * Parse responses from HttpService.
     *
     * @param  Collection<int|string,HttpResponse>  $responses
     * @param  Collection<int,object>  $queries
     * @param  Closure  $closure   Closure to parse response
     * @return Collection<int|string,Collection<int|string,mixed>> Two Collections with `fullfilled` and `rejected` keys
     */
    public static function parseResponses(Collection $responses, Collection $queries, Closure $closure)
    {
        $fullfilled = collect([]);
        $rejected = collect([]);

        foreach ($responses as $id => $response) {
            $query = $queries->first(fn (HttpModelQuery $query) => $query->model_id === $id);

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
}
