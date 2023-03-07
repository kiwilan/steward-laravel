<?php

namespace Kiwilan\Steward\Services;

use Closure;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\HttpModelQuery;
use Kiwilan\Steward\Services\Http\HttpRequest;
use Kiwilan\Steward\Services\Http\HttpResponse;

class HttpService
{
    protected function __construct(
        protected HttpRequest $request,
    ) {
    }

    /**
     * Create HttpService instance.
     *
     * @param  Collection<int,HttpModelQuery>|Collection<int,object>|string[]  $requests
     */
    public static function make(iterable $requests): HttpRequest
    {
        $self = new HttpService(
            HttpRequest::make($requests)
        );

        return $self->request;
    }

    public function request(): HttpRequest
    {
        return $this->request;
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
