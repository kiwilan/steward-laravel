<?php

namespace Kiwilan\Steward\Services\FactoryService;

use DateTime;
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
        $createdAtStr = $this->generateDateTime($minimum);
        $createdAt = Carbon::createFromDate($createdAtStr);

        $updatedAtStr = $this->generateDateTime($createdAt);
        $updatedAt = Carbon::createFromDate($updatedAtStr);

        while (! $this->checkIfCreatedAtIsBeforeUpdatedAt($createdAt, $updatedAt)) {
            $updatedAtStr = $this->generateDateTime($createdAt);
            $updatedAt = Carbon::createFromDate($updatedAtStr);
        }

        return new FactoryTimestamps(
            $this->toSqlDate($createdAt->format('Y-m-d H:i:s')),
            $this->toSqlDate($updatedAt->format('Y-m-d H:i:s')),
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
            'created_at' => $timestamps->createdAt,
            'updated_at' => $timestamps->updatedAt,
        ];
    }

    private function checkIfCreatedAtIsBeforeUpdatedAt(DateTime $createdAt, DateTime $updatedAt): bool
    {
        return $createdAt < $updatedAt;
    }

    private function generateDateTime(string $between, string $format = 'Y-m-d H:i:s'): DateTime
    {
        $date = $this->factory->faker()
            ->dateTimeBetween($between)
        ;

        while (! $this->checkDateValidity($date)) {
            $date = $this->factory->faker()
                ->dateTimeBetween($between)
                ->format($format)
            ;
        }

        return $date;
    }

    private function checkDateValidity(DateTime $date): bool
    {
        try {
            Carbon::parse($date);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function toSqlDate(string $date): string
    {
        $date = strtotime($date);

        return date('Y-m-d h:i:s', $date);
    }
}
