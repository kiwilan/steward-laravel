<?php

namespace Kiwilan\Steward\Services\GoogleBookService;

use Kiwilan\Steward\Class\GoogleBook;

/**
 * Manage GoogleBook API.
 */
interface GoogleBookable
{
    /**
     * Convert GoogleBook data into Model data.
     */
    public function googleBookConvert(GoogleBook $google_book): self;
}
