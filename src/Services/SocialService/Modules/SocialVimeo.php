<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialInterface;
use Kiwilan\Steward\Services\SocialService\SocialModule;

class SocialVimeo extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialVimeo($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
    }
}
