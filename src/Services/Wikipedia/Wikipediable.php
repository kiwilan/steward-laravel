<?php

namespace Kiwilan\Steward\Services\Wikipedia;

/**
 * Manage Wikipedia API.
 */
interface Wikipediable
{
    /**
     * Convert WikipediaItem data into Model data.
     */
    public function wikipediaConvert(WikipediaItem $item, bool $default = false): self;
}
