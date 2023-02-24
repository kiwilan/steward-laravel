<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Support\Carbon;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\FactoryService\Utils\FactoryTimestamps;

class FactoryDate
{
    public function __construct(
        public FactoryService $factory,
    ) {
    }

    /**
     * Generate timestamps.
     */
    public function timestamps(string $minimum = '-20 years'): FactoryTimestamps
    {
        $createdAtStr = $this->generateDateTime($minimum);
        $createdAt = Carbon::createFromTimeString($createdAtStr);

        $updatedAtStr = $this->generateDateTime($createdAt);
        $updatedAt = Carbon::createFromTimeString($updatedAtStr);

        return new FactoryTimestamps(
            $createdAt->format('Y-m-d H:i:s'),
            $updatedAt->format('Y-m-d H:i:s'),
            $createdAt,
            $updatedAt,
        );
    }

    private function generateDateTime(string $between, string $format = 'Y-m-d H:i:s'): string
    {
        $date = $this->factory->faker()
                ->dateTimeBetween($between)
                ->format($format);

        while (! $this->checkDateValidity($date)) {
            $date = $this->factory->faker()
                ->dateTimeBetween($between)
                ->format($format);
        }

        return $date;
    }

    private function checkDateValidity(string $date): bool
    {
        $date = Carbon::createFromTimeString($date);

        return $date->isValid();
    }
}
