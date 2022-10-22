<?php

namespace Kiwilan\Steward\Services\SocialService;

use Kiwilan\Steward\Services\SocialService\SocialServiceHtml;

abstract class SocialModule
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?string $embed_url = null,
        protected ?string $src = null,
        protected bool $is_valid = false,
        //
        protected ?string $html = null,
        protected bool $html_is_custom = false,
    ) {
    }

    public function getMediaId(): ?string
    {
        return $this->media_id;
    }

    public function getEmbedUrl(): ?string
    {
        return $this->embed_url;
    }

    public function getIsValid(): bool
    {
        return $this->is_valid;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getHtmlIsCustom(): bool
    {
        return $this->html_is_custom;
    }

    public function setHtml()
    {
        $this->html = SocialServiceHtml::make($this);
    }
}
