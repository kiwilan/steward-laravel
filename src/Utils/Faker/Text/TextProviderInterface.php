<?php

namespace Kiwilan\Steward\Utils\Faker\Text;

interface TextProviderInterface
{
    /**
     * Generate text.
     *
     * @return string[]
     */
    public static function words();
}
