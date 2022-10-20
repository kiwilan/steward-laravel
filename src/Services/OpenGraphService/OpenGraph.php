<?php

namespace Kiwilan\Steward\Services\OpenGraphService;

class OpenGraph
{
    public function __construct(
        public ?string $title = null
    ) {
    }

    public static function make()
    {
    }
}
