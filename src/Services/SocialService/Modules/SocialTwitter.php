<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

use Kiwilan\Steward\Services\SocialService\SocialServiceTwitter;

class SocialTwitter extends SocialModule implements SocialInterface
{
    protected function __construct(
        $url,
        protected ?SocialServiceTwitter $social = null,
    ) {
        parent::__construct($url);
    }

    public static function make(string $url): self
    {
        $module = new SocialTwitter($url);
        $module->regex();

        return $module;
    }

    public function regex()
    {
        $this->social = SocialServiceTwitter::make($this->url);
        $this->media_id = $this->social->getMediaId();
        $this->embed_url = $this->social->getEmbedUrl();
        $this->is_valid = $this->social->getIsValid();

        $html = $this->social->getHtml();
        $this->html = $this->html($html);
        $this->html_is_custom = true;
    }

    private function html(?string $html = null)
    {
        return <<<HTML
            <div>
                {!! $html !!}
                <script
                    async
                    src="https://platform.twitter.com/widgets.js"
                    charset="utf-8"
                ></script>
            </div>
        HTML;
    }
}
