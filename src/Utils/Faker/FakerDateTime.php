<?php

namespace Kiwilan\Steward\Utils\Faker;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon;
use Kiwilan\Steward\Utils\Faker;
use Kiwilan\Steward\Utils\Faker\Utils\FakerTimestamps;

class FakerDateTime
{
    public function __construct(
        public Faker $faker,
    ) {}

    /**
     * Generate timestamps.
     */
    public function timestamps(string $minimum = '-20 years'): FakerTimestamps
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

        return new FakerTimestamps(
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
        $date = $this->faker->generator()->dateTimeBetween($between);

        while (! $this->validateDateTime($date)) {
            $date = $this->faker->generator()->dateTimeBetween($between);
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
