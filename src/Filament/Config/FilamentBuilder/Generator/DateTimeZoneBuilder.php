<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout\Generator;

use DateTime;
use DateTimeZone;

class DateTimeZoneBuilder
{
    protected function __construct(
        protected array $regions = [],
        protected array $timezones = [],
        protected array $timezone_offsets = [],
        protected array $timezone_list = [],
    ) {
    }

    public static function make(): self
    {
        $dtz = new DateTimeZoneBuilder();
        $dtz->regions = $dtz->setRegions();
        $dtz->timezones = $dtz->setTimezones();
        $dtz->timezone_offsets = $dtz->setTimezoneOffsets();
        $dtz->timezone_list = $dtz->setTimezoneList();

        return $dtz;
    }

    /**
     * @return array<string>
     */
    public function getTimezones(): array
    {
        return $this->timezones;
    }

    private function setTimezoneList(): array
    {
        $timezone_list = [];
        foreach ($this->timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }

    private function setTimezoneOffsets(): array
    {
        $timezone_offsets = [];
        foreach ($this->timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by offset
        asort($timezone_offsets);

        return $timezone_offsets;
    }

    private function setTimezones(): array
    {
        $timezones = [];
        foreach ($this->regions as $region) {
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
