<?php

namespace Kiwilan\Steward\Services\GoogleBook;

/**
 * Manage GoogleBook API.
 */
interface GoogleBookable
{
    /**
     * Convert GoogleBook data into Model data.
     */
    public function googleBookConvert(GoogleBook $book): self;
}
