<?php

namespace Kiwilan\Steward\Utils\Factory\Text;

interface TextProviderInterface
{
    /**
     * Generate text.
     *
     * @return string[]
     */
    public static function words();
}
