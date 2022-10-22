<?php

namespace Kiwilan\Steward\Services\SocialService\Modules;

abstract class SocialModule
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?string $embed_url = null,
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
}
