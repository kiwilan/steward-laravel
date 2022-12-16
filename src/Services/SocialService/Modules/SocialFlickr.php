<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialInterface;
use Kiwilan\Steward\Services\SocialService\SocialModule;

class SocialFlickr extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialFlickr($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
        $regex = '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(?:user\/)?(\d+)/';
        // dump(preg_match($regex, $this->url, $matches));
        if (preg_match($regex, $this->url, $matches)) {
            dump($matches);
            // $this->media_id = $matches[1] ?? null;
            // $this->embed_url = "";
        }
    }
}
