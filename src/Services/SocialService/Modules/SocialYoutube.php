<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialInterface;
use Kiwilan\Steward\Services\SocialService\SocialModule;

class SocialYoutube extends SocialModule implements SocialInterface
{
    public static function make(string $url): self
    {
        $module = new SocialYoutube($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
        $regex = "/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\\/))([^\\?&\"'>]+)/";

        if (preg_match($regex, $this->url, $matches)) {
            if (isset($matches[1])) {
                $this->media_id = $matches[1];
                $this->embed_url = "https://www.youtube.com/embed/{$this->media_id}";
                // $this->is_frame = true;
                $this->is_valid = true;
            }
        }
    }
}
