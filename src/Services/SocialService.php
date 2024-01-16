<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Services\Social\SocialModule;

class SocialService
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?SocialEnum $type = null,
        protected ?string $embed_url = null,
        protected string $title = '',
        protected bool $is_unknown = false,
        protected bool $is_frame = false,
        protected bool $is_custom = false,
        public ?SocialModule $module = null,
    ) {
    }

    public static function make(string $url): self
    {
        $social = new SocialService($url);
        $social->module = $social->setModule();

        return $social;
    }

    public function getIsUnknown(): bool
    {
        return $this->is_unknown;
    }

    public function getIsCustom(): bool
    {
        return $this->is_custom;
    }

    public function getIsFrame(): bool
    {
        return $this->is_frame;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getEmbedUrl(): ?string
    {
        return $this->embed_url;
    }

    public function getType(): ?SocialEnum
    {
        return $this->type;
    }

    private function setModule()
    {
        $this->type = SocialEnum::find($this->url);

        return match ($this->type) {
            SocialEnum::dailymotion => \Kiwilan\Steward\Services\Social\Modules\SocialDailymotion::make($this->url),
            SocialEnum::instagram => \Kiwilan\Steward\Services\Social\Modules\SocialInstagram::make($this->url),
            SocialEnum::facebook => \Kiwilan\Steward\Services\Social\Modules\SocialFacebook::make($this->url),
            SocialEnum::flickr => \Kiwilan\Steward\Services\Social\Modules\SocialFlickr::make($this->url),
            SocialEnum::giphy => \Kiwilan\Steward\Services\Social\Modules\SocialGiphy::make($this->url),
            SocialEnum::imgur => \Kiwilan\Steward\Services\Social\Modules\SocialImgur::make($this->url),
            SocialEnum::kickstarter => \Kiwilan\Steward\Services\Social\Modules\SocialKickstarter::make($this->url),
            SocialEnum::linkedin => \Kiwilan\Steward\Services\Social\Modules\SocialLinkedin::make($this->url),
            SocialEnum::pinterest => \Kiwilan\Steward\Services\Social\Modules\SocialPinterest::make($this->url),
            SocialEnum::reddit => \Kiwilan\Steward\Services\Social\Modules\SocialReddit::make($this->url),
            SocialEnum::snapchat => \Kiwilan\Steward\Services\Social\Modules\SocialSnapchat::make($this->url),
            SocialEnum::soundcloud => \Kiwilan\Steward\Services\Social\Modules\SocialSoundcloud::make($this->url),
            SocialEnum::spotify => \Kiwilan\Steward\Services\Social\Modules\SocialSpotify::make($this->url),
            SocialEnum::ted => \Kiwilan\Steward\Services\Social\Modules\SocialTed::make($this->url),
            SocialEnum::tumblr => \Kiwilan\Steward\Services\Social\Modules\SocialTumblr::make($this->url),
            SocialEnum::tiktok => \Kiwilan\Steward\Services\Social\Modules\SocialTiktok::make($this->url),
            SocialEnum::twitch => \Kiwilan\Steward\Services\Social\Modules\SocialTwitch::make($this->url),
            SocialEnum::twitter => \Kiwilan\Steward\Services\Social\Modules\SocialTwitter::make($this->url),
            SocialEnum::vimeo => \Kiwilan\Steward\Services\Social\Modules\SocialVimeo::make($this->url),
            SocialEnum::youtube => \Kiwilan\Steward\Services\Social\Modules\SocialYoutube::make($this->url),
            default => \Kiwilan\Steward\Services\Social\Modules\SocialDefault::make($this->url),
        };
    }
}
