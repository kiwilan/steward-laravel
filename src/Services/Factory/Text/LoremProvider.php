<?php

namespace Kiwilan\Steward\Services\Factory\Text;

/**
 * Generate Lorem text.
 */
class LoremProvider implements TextProviderInterface
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

    /**
     * @return string|string[]
     */
    public static function generate(int|false $limit = 3, bool $asText = false): mixed
    {
        $faker = \Faker\Factory::create();

        return $faker->words($limit, $asText);
    }
}
