<?php

namespace Kiwilan\Steward\Services\Social\Modules;

use Kiwilan\Steward\Services\Social\SocialInterface;
use Kiwilan\Steward\Services\Social\SocialModule;

class SocialTed extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialTed($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex() {}
}
