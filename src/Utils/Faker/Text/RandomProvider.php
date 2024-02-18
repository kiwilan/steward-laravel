<?php

namespace Kiwilan\Steward\Utils\Faker\Text;

use Kiwilan\Steward\Enums\FakerTextEnum;

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

    public static function select(): FakerTextEnum
    {
        $enums = [
            FakerTextEnum::lorem,
            FakerTextEnum::sindarin,
            FakerTextEnum::klingon,
            FakerTextEnum::navi,
        ];

        return $enums[array_rand($enums)];
    }
}
