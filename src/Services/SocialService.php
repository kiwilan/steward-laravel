<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Services\SocialService\Modules\{SocialDailymotion,SocialDefault,SocialFacebook,SocialFlickr,SocialGiphy,SocialImgur,SocialInstagram,SocialKickstarter,SocialLinkedin,SocialPinterest,SocialReddit,SocialSnapchat,SocialSoundcloud,SocialSpotify,SocialTed,SocialTiktok,SocialTumblr,SocialTwitch,SocialTwitter,SocialVimeo,SocialYoutube};
use Kiwilan\Steward\Services\SocialService\SocialModule;

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
            SocialEnum::dailymotion => SocialDailymotion::make($this->url),
            SocialEnum::instagram => SocialInstagram::make($this->url),
            SocialEnum::facebook => SocialFacebook::make($this->url),
            SocialEnum::flickr => SocialFlickr::make($this->url),
            SocialEnum::giphy => SocialGiphy::make($this->url),
            SocialEnum::imgur => SocialImgur::make($this->url),
            SocialEnum::kickstarter => SocialKickstarter::make($this->url),
            SocialEnum::linkedin => SocialLinkedin::make($this->url),
            SocialEnum::pinterest => SocialPinterest::make($this->url),
            SocialEnum::reddit => SocialReddit::make($this->url),
            SocialEnum::snapchat => SocialSnapchat::make($this->url),
            SocialEnum::soundcloud => SocialSoundcloud::make($this->url),
            SocialEnum::spotify => SocialSpotify::make($this->url),
            SocialEnum::ted => SocialTed::make($this->url),
            SocialEnum::tumblr => SocialTumblr::make($this->url),
            SocialEnum::tiktok => SocialTiktok::make($this->url),
            SocialEnum::twitch => SocialTwitch::make($this->url),
            SocialEnum::twitter => SocialTwitter::make($this->url),
            SocialEnum::vimeo => SocialVimeo::make($this->url),
            SocialEnum::youtube => SocialYoutube::make($this->url),
            default => SocialDefault::make($this->url),
        };
    }
}
