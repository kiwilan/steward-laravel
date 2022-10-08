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
    public function wikipediaConvert(WikipediaItem $wikipediaItem, bool $with_media = true): self;
}
