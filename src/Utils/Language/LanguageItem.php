<?php

namespace Kiwilan\Steward\Utils\Language;

class LanguageItem
{
    /**
     * @param  string  $name  English name, e.g. `French`
     * @param  array  $countryNames  Country name, e.g. [`France`, `Belgium`, `Canada`, `Switzerland`, `France`]
     * @param  array  $countryCodes  Country code, e.g. [`fr`, `fr_BE`, `fr_CA`, `fr_CH`, `fr_FR`]
     * @param  string  $codeIso639_1  ISO 639-1, e.g. `fr`
     * @param  array  $codeIso639_2  ISO 639-2, e.g. `['fre', 'fra']`
     */
    public function __construct(
        public string $name,
        public array $countryNames,
        public array $countryCodes,
        public string $codeIso639_1,
        public array $codeIso639_2,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['countryNames'],
            $data['countryCodes'],
            $data['codeIso639_1'],
            $data['codeIso639_2'],
        );
    }
}
