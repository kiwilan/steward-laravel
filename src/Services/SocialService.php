<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Services\SocialService\Modules\SocialDailymotion;
use Kiwilan\Steward\Services\SocialService\Modules\SocialDefault;
use Kiwilan\Steward\Services\SocialService\Modules\SocialFacebook;
use Kiwilan\Steward\Services\SocialService\Modules\SocialFlickr;
use Kiwilan\Steward\Services\SocialService\Modules\SocialGiphy;
use Kiwilan\Steward\Services\SocialService\Modules\SocialImgur;
use Kiwilan\Steward\Services\SocialService\Modules\SocialInstagram;
use Kiwilan\Steward\Services\SocialService\Modules\SocialKickstarter;
use Kiwilan\Steward\Services\SocialService\Modules\SocialLinkedin;
use Kiwilan\Steward\Services\SocialService\Modules\SocialPinterest;
use Kiwilan\Steward\Services\SocialService\Modules\SocialReddit;
use Kiwilan\Steward\Services\SocialService\Modules\SocialSnapchat;
use Kiwilan\Steward\Services\SocialService\Modules\SocialSoundcloud;
use Kiwilan\Steward\Services\SocialService\Modules\SocialSpotify;
use Kiwilan\Steward\Services\SocialService\Modules\SocialTed;
use Kiwilan\Steward\Services\SocialService\Modules\SocialTiktok;
use Kiwilan\Steward\Services\SocialService\Modules\SocialTumblr;
use Kiwilan\Steward\Services\SocialService\Modules\SocialTwitch;
use Kiwilan\Steward\Services\SocialService\Modules\SocialTwitter;
use Kiwilan\Steward\Services\SocialService\Modules\SocialVimeo;
use Kiwilan\Steward\Services\SocialService\Modules\SocialYoutube;
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
