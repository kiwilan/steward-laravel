<?php

namespace Kiwilan\Steward\Services\Social\Modules;

use Kiwilan\Steward\Services\Social\SocialInterface;
use Kiwilan\Steward\Services\Social\SocialModule;

class SocialTiktok extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialTiktok($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex() {}
}
