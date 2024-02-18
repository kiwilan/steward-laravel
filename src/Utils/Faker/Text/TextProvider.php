<?php

namespace Kiwilan\Steward\Utils\Faker\Text;

use Kiwilan\Steward\Enums\FakerTextEnum;

class TextProvider
{
    protected function __construct(
        protected FakerTextEnum $type,
        protected TextProviderInterface $provider,
    ) {
    }

    public static function make(FakerTextEnum $type = FakerTextEnum::lorem): TextProvider
    {
        $provider = match ($type) {
            FakerTextEnum::lorem => new LoremProvider(),
            FakerTextEnum::sindarin => new SindarinProvider(),
            FakerTextEnum::klingon => new KlingonProvider(),
            FakerTextEnum::navi => new NaviProvider(),
        };

        return new self($type, $provider);
    }

    /**
     * @return string|string[]
     */
    public function words(int|false $limit = 3, bool $asText = false)
    {
        return $this->generate($limit, $asText);
    }

    /**
     * @return string|string[]
     */
    private function generate(int|false $limit = 3, bool $asText = false): string|array
    {
        if ($this->type === FakerTextEnum::lorem) {
            return LoremProvider::generate($limit, $asText);
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
