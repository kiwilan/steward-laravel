<?php

namespace Kiwilan\Steward\Services\Factory\Text;

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

    public static function select(): TextProviderInterface
    {
        $providers = [
            LoremProvider::class,
            SindarinProvider::class,
            KlingonProvider::class,
            NaviProvider::class,
        ];

        $provider = $providers[array_rand($providers)];

        return new $provider();
    }
}
