<?php

namespace Kiwilan\Steward\Services\Factory\Text;

use Kiwilan\Steward\Enums\FactoryTextEnum;

class TextProvider
{
    protected function __construct(
        protected FactoryTextEnum $type,
        protected TextProviderInterface $provider,
    ) {
    }

    public static function make(FactoryTextEnum $type = FactoryTextEnum::lorem): TextProvider
    {
        $provider = match ($type) {
            FactoryTextEnum::sindarin => new SindarinProvider(),
            FactoryTextEnum::klingon => new KlingonProvider(),
            FactoryTextEnum::navi => new NaviProvider(),
            default => new LoremProvider(),
        };

        return new self($type, $provider);
    }

    /**
     * @return string|string[]
     */
    public function words(int|false $limit = 3, bool $asText = false)
    {
        if ($this->type === FactoryTextEnum::lorem) {
            $faker = \Faker\Factory::create();

            return $faker->words($limit, $asText);
        }

        $words = $this->provider->words();
        shuffle($words);

        if ($limit) {
            $words = array_slice($words, 0, $limit);
        }

        return $asText ? implode(' ', $words) : $words;
    }

    public static function capitalizeFirst(string $string): string
    {
        return mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
    }
}
