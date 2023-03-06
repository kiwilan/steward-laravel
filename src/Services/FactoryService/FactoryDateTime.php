<?php

namespace Kiwilan\Steward\Services\FactoryService;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\FactoryService\Utils\FactoryTimestamps;

class FactoryDateTime
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
        $tz = new DateTimeZone('UTC');

        $createdAtStr = $this->generateDateTime($minimum);
        $createdAt = Carbon::createFromDate($createdAtStr, tz: $tz);

        $updatedAtStr = $this->generateDateTime($createdAt);
        $updatedAt = Carbon::createFromDate($updatedAtStr, tz: $tz);

        while (! $this->checkIfCreatedAtIsBeforeUpdatedAt($createdAt, $updatedAt)) {
            $updatedAtStr = $this->generateDateTime($createdAt);
            $updatedAt = Carbon::createFromDate($updatedAtStr, tz: $tz);
        }

        return new FactoryTimestamps(
            $createdAt,
            $updatedAt,
        );
    }

    /**
     * Set timestamps.
     *
     * @return array<string,string>
     */
    public function setTimestamps(string $minimum = '-20 years'): array
    {
        $timestamps = $this->timestamps($minimum);

        return [
            'created_at' => $timestamps->getCreatedAt(),
            'updated_at' => $timestamps->getUpdatedAt(),
        ];
    }

    private function checkIfCreatedAtIsBeforeUpdatedAt(DateTime $createdAt, DateTime $updatedAt): bool
    {
        return $createdAt < $updatedAt;
    }

    private function generateDateTime(string $between): DateTime
    {
        $date = $this->factory->faker()
            ->dateTimeBetween($between)
        ;

        while (! $this->validateDateTime($date)) {
            $date = $this->factory->faker()
                ->dateTimeBetween($between)
            ;
        }

        return $date;
    }

    private function validateDateTime(DateTime|string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        if ($date instanceof DateTime) {
            $date = $date->format($format);
        }

        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
}
