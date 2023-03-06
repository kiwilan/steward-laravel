<?php

namespace Kiwilan\Steward\Services\Social\Modules;

use Kiwilan\Steward\Services\Social\SocialInterface;
use Kiwilan\Steward\Services\Social\SocialModule;
use Kiwilan\Steward\Services\Social\SocialServiceTwitter;

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

        $html = $module->social->getHtml();
        $module->html = $module->html($html);
        $module->html_is_custom = true;

        return $module;
    }

    public function regex()
    {
        $this->social = SocialServiceTwitter::make($this->url);
        $this->media_id = $this->social->getMediaId();
        $this->embed_url = $this->social->getEmbedUrl();
        $this->is_valid = $this->social->getIsValid();
    }

    private function html(?string $html = null)
    {
        return <<<HTML
            <div align="center">
                {$html}
            </div>
        HTML;
    }
}
