<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

interface SocialInterface
{
    public static function make(string $url): self;

    public function regex();
}
