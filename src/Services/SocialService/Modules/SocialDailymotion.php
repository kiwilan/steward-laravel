<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialInterface;
use Kiwilan\Steward\Services\SocialService\SocialModule;

class SocialDailymotion extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialDailymotion($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
        // TODO other URL formats
        $regex = '!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!';

        if (preg_match($regex, $this->url, $matches)) {
            if (isset($matches[6])) {
                $this->media_id = $matches[6];
            } elseif (isset($matches[4])) {
                $this->media_id = $matches[4];
            } else {
                $this->media_id = $matches[2];
            }

            $this->embed_url = "https://www.dailymotion.com/embed/video/{$this->media_id}";
        }
    }
}
