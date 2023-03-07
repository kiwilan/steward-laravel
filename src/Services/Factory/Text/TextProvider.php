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

    public static function make(FactoryTextEnum $type = FactoryTextEnum::random): TextProvider
    {
        $provider = match ($type) {
            FactoryTextEnum::lorem => new LoremProvider(),
            FactoryTextEnum::sindarin => new SindarinProvider(),
            FactoryTextEnum::klingon => new KlingonProvider(),
            FactoryTextEnum::navi => new NaviProvider(),
            default => new RandomProvider(),
        };

        return new self($type, $provider);
    }

    /**
     * @return string|string[]
     */
    public function words(int|false $limit = 3, bool $asText = false)
    {
        if ($this->type === FactoryTextEnum::random) {
            $this->type = RandomProvider::select();

            $self = self::make($this->type);

            return $self->generate($limit, $asText);
        }

        return $this->generate($limit, $asText);
    }

    /**
     * @return string|string[]
     */
    private function generate(int|false $limit = 3, bool $asText = false): string|array
    {
        if ($this->type === FactoryTextEnum::lorem) {
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
