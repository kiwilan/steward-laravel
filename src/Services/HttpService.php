<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\Http\FetchService;
use Kiwilan\Steward\Services\Http\HttpResponse;
use Kiwilan\Steward\Services\Http\PoolRequest;
use Kiwilan\Steward\Services\Http\PoolService;

class HttpService
{
    protected function __construct(
    ) {
    }

    public static function fetch(string $url): HttpResponse
    {
        return FetchService::make($url);
    }

    public static function pool(iterable $requests): PoolRequest
    {
        return PoolService::make($requests);
    }

    public static function buildURL(string $url, array $params = []): string
    {
        if (! $params) {
            return $url;
        }

        $query = http_build_query($params);

        return $url.'?'.$query;
    }
}
