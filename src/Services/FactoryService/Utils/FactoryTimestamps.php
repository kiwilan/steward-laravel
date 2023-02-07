<?php

namespace Kiwilan\Steward\Services\FactoryService\Utils;

use Illuminate\Support\Carbon;

class FactoryTimestamps
{
    public function __construct(
        public string $createdAt,
        public string $updatedAt,
        public Carbon $createdAtCarbon,
        public Carbon $updatedAtCarbon,
    ) {
    }
}
