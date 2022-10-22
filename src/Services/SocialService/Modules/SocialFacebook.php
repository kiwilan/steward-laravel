<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialInterface;
use Kiwilan\Steward\Services\SocialService\SocialModule;

class SocialFacebook extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialFacebook($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
        $regex = '/(?:https?:\/\/(?:www|m|mbasic|business)\.(?:facebook|fb)\.com\/)(?:photo(?:\.php|s)|permalink\.php|video\.php|media|watch\/|questions|notes|[^\/]+\/(?:activity|posts|videos|photos))[\/?](?:fbid=|story_fbid=|id=|b=|v=|)(?|([0-9]+)|[^\/]+\/(\d+))/';
        if (preg_match($regex, $this->url, $matches)) {
            $this->media_id = $matches[1] ?? null;
            $this->embed_url = "https://www.facebook.com/plugins/post.php?href={$this->url}&show_text=true&width=500";
        }
    }
}
