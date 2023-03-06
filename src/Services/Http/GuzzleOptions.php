<?php

namespace Kiwilan\Steward\Services\Http;

class GuzzleOptions
{
    public function __construct(
        public bool $poolable = false,
        public int $poolLimit = 250,
        public int $maxCurlHandles = 100,
        public int $maxRedirects = 10,
        public int $timeout = 30,
        public int $guzzleConcurrency = 5,
    ) {
    }
}
