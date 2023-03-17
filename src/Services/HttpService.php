<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\FetchService;
use Kiwilan\Steward\Services\Http\HttpResponse;
use Kiwilan\Steward\Services\Http\PoolRequest;
use Kiwilan\Steward\Services\Http\PoolService;
use Kiwilan\Steward\Services\Http\Utils\HttpQuery;

class HttpService
{
    /**
     * Fetch an URL.
     */
    public static function fetch(string $url): HttpResponse
    {
        return FetchService::make($url);
    }

    /**
     * Create a pool of requests.
     *
     * @param  Collection<int,HttpQuery>|Collection<int,object>|string[]  $requests
     */
    public static function pool(iterable $requests): PoolRequest
    {
        return PoolService::make($requests);
    }

    /**
     * Build an URL
     *
     * @param  string[]  $params
     * @param  string[]  $query
     */
    public static function buildURL(string $url, array $params = [], array $query = []): string
    {
        if (! empty($params)) {
            $paramsStr = implode('/', $params);

            $url .= "/{$paramsStr}";
        }

        if (! empty($query)) {
            $queryStr = http_build_query($query);

            $url .= "?{$queryStr}";
        }

        $url = str_replace(' ', '%20', $url);

        return str_replace('//', '/', $url);
    }
}
