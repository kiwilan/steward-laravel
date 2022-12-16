<?php

namespace Kiwilan\Steward\Services\WikipediaService;

use Kiwilan\Steward\Class\WikipediaItem;

/**
 * Manage Wikipedia API.
 */
interface Wikipediable
{
    /**
     * Convert WikipediaItem data into Model data.
     */
    public function wikipediaConvert(WikipediaItem $wikipedia_item, bool $default = false): self;
}
