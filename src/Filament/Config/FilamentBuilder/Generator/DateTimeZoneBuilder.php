<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder\Generator;

use DateTime;
use DateTimeZone;

class DateTimeZoneBuilder
{
    protected function __construct(
        protected array $timezones = [],
    ) {
    }

    public static function make(bool $full = false): self
    {
        $dtz = new DateTimeZoneBuilder();

        $dtz->timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        if ($full) {
            $regions = $dtz->setRegions();
            $timezones = $dtz->setTimezones($regions);
            $timezone_offsets = $dtz->setTimezoneOffsets($timezones);
            $dtz->timezones = $dtz->setTimezoneList($timezone_offsets);
        }

        return $dtz;
    }

    /**
     * @return array<string>
     */
    public function getTimezones(): array
    {
        return $this->timezones;
    }

    private function setTimezoneList(array $timezone_offsets): array
    {
        $timezone_list = [];
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }

    private function setTimezoneOffsets(array $tz): array
    {
        $timezone_offsets = [];
        foreach ($tz as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by offset
        asort($timezone_offsets);

        return $timezone_offsets;
    }

    private function setTimezones(array $regions): array
    {
        $timezones = [];
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        return $timezones;
    }

    private function setRegions(): array
    {
        return [
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        ];
    }
}
