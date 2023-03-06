<?php

namespace Kiwilan\Steward\Services\Social;

interface SocialInterface
{
    public static function make(string $url): self;

    public function regex();
}
