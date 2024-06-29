<?php

namespace Kiwilan\Steward\Services\Social\Modules;

use Kiwilan\Steward\Services\Social\SocialInterface;
use Kiwilan\Steward\Services\Social\SocialModule;

class SocialSoundcloud extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialSoundcloud($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex() {}
}
