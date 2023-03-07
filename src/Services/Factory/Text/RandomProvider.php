<?php

namespace Kiwilan\Steward\Services\Factory\Text;

use Kiwilan\Steward\Enums\FactoryTextEnum;

/**
 * Generate Lorem text.
 */
class RandomProvider implements TextProviderInterface
{
    /**
     * Lorem words.
     *
     * @return string[]
     */
    public static function words()
    {
        return [
        ];
    }

    public static function select(): FactoryTextEnum
    {
        $enums = [
            FactoryTextEnum::lorem,
            FactoryTextEnum::sindarin,
            FactoryTextEnum::klingon,
            FactoryTextEnum::navi,
        ];

        return $enums[array_rand($enums)];
    }
}
