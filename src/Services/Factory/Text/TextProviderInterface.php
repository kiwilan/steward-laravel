<?php

namespace Kiwilan\Steward\Services\Factory\Text;

interface TextProviderInterface
{
    /**
     * Generate text.
     *
     * @return string[]
     */
    public static function words();
}
