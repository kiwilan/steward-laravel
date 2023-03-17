<?php

namespace Kiwilan\Steward\Services\Http;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\Utils\HttpQuery;

class PoolService
{
    protected function __construct(
        protected PoolRequest $request,
    ) {
    }

    /**
     * Create HttpService instance.
     *
     * @param  Collection<int,HttpQuery>|Collection<int,object>|string[]  $requests
     */
    public static function make(iterable $requests): PoolRequest
    {
        $self = new PoolService(
            PoolRequest::make($requests)
        );

        return $self->request;
    }

    public function request(): PoolRequest
    {
        return $this->request;
    }
}
