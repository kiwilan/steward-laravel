<?php

namespace Kiwilan\Steward\Services\Social\Modules;

use Kiwilan\Steward\Services\Social\SocialInterface;
use Kiwilan\Steward\Services\Social\SocialModule;

class SocialInstagram extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialInstagram($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
        $regex = '/(?:https?:\/\/www\.)?instagram\.com\S*?\/p\/(\w{11})\/?/';

        if (preg_match($regex, $this->url, $matches)) {
            $this->media_id = $matches[1] ?? null;
            $this->embed_url = "https://www.instagram.com/p/{$this->media_id}/embed";
        }
    }
}
