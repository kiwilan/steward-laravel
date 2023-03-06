<?php

namespace Kiwilan\Steward\Services\Social;

class SocialServiceHtml
{
    protected function __construct(
        protected SocialModule $social,
        protected ?string $src = null,
        protected int $width = 400,
        protected int $height = 500,
        protected string $title = '',
        protected string $allow = '',
    ) {
    }

    public static function make(SocialModule $social): ?string
    {
        $html = new SocialServiceHtml($social);
        $html->src = $social->getEmbedUrl();

        return $html->getHtml();
    }

    private function getHtml(): string
    {
        if ($this->social->getHtmlIsCustom()) {
            return $this->social->getHtml();
        }

        return $this->iframe();
    }

    private function iframe(): string
    {
        return <<<HTML
            <div align="center">
                <iframe
                    src="{$this->src}"
                    width="{$this->width}"
                    height="{$this->height}"
                    title="{$this->title}"
                    style="border:none"
                    scrolling="no"
                    frameborder="0"
                    allowfullscreen
                    allow="{$this->allow}"
                    loading="lazy"
                ></iframe>
            </div>
        HTML;
    }
}
