<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialInterface;
use Kiwilan\Steward\Services\SocialService\SocialModule;

class SocialTiktok extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialTiktok($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
    }
}
