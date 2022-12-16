<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder\Generator;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;

class DateTimeZoneBuilder
{
    protected function __construct(
        protected ?string $region = null,
        protected ?string $city = null,
        protected ?string $slug = null,
        protected ?int $offset = null,
        protected ?string $pretty_offset = null,
        protected ?string $label = null,
    ) {
    }

    public static function make(bool $sort_utc = false): array
    {
        $timezones = self::getTimezones();
        if ($sort_utc) {
            usort($timezones, fn (DateTimeZoneBuilder $a, DateTimeZoneBuilder $b) => strcmp($a->pretty_offset, $b->pretty_offset));
        }

        $list = [];

        foreach ($timezones as $timezone) {
            $list[$timezone->slug] = $timezone->label;
        }

        return $list;
    }

    /**
     * @return array<DateTimeZoneBuilder>
     */
    private static function getTimezones(): array
    {
        $list = [];

        $date_time_zones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        foreach ($date_time_zones as $timezone) {
            $dtz = new DateTimeZoneBuilder();

            $dtz->region = explode('/', $timezone)[0];
            $dtz->city = explode('/', $timezone)[1] ?? '';
            $dtz->city = str_replace('_', ' ', $dtz->city);
            $dtz->slug = Str::slug("{$dtz->region} {$dtz->city}");

            $tz = new DateTimeZone($timezone);
            $dtz->offset = $tz->getOffset(new DateTime());

            $offset_prefix = $dtz->offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($dtz->offset));
            $dtz->slug = "{$offset_prefix}{$offset_formatted}_{$dtz->slug}";

            $dtz->pretty_offset = "{$offset_prefix}{$offset_formatted}";
            $dtz->label = "{$dtz->region} {$dtz->city} ({$dtz->pretty_offset})";

            $list[$dtz->slug] = $dtz;
        }

        return $list;
    }
}
