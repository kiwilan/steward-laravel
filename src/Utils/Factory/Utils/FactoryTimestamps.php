<?php

namespace Kiwilan\Steward\Utils\Factory\Utils;

use Illuminate\Support\Carbon;

class FactoryTimestamps
{
    public function __construct(
        public Carbon $createdAtCarbon,
        public Carbon $updatedAtCarbon,
    ) {
    }

    public function getCreatedAt(): string
    {
        return $this->createdAtCarbon->toDateTimeString();
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAtCarbon->toDateTimeString();
    }
}
