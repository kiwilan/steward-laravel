<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Services\SocialService\Modules\SocialTwitter;
use Kiwilan\Steward\Services\SocialService\Modules\SocialYoutube;

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
    ) {
    }

    public static function make(string $url): self
    {
        $social = new SocialService($url);
        $social->find();

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

    private function find()
    {
        $this->type = SocialEnum::find($this->url);

        $social = match ($this->type) {
            SocialEnum::dailymotion => $this->dailymotion(),
            SocialEnum::instagram => $this->instagram(),
            SocialEnum::facebook => $this->facebook(),
            SocialEnum::flickr => null,
            SocialEnum::giphy => null,
            SocialEnum::imgur => null,
            SocialEnum::kickstarter => null,
            SocialEnum::linkedin => null,
            SocialEnum::pinterest => null,
            SocialEnum::reddit => null,
            SocialEnum::snapchat => null,
            SocialEnum::soundcloud => null,
            SocialEnum::spotify => $this->spotify(),
            SocialEnum::ted => null,
            SocialEnum::tumblr => null,
            SocialEnum::tiktok => null,
            SocialEnum::twitch => null,
            SocialEnum::twitter => SocialTwitter::make($this->url),
            SocialEnum::vimeo => null,
            SocialEnum::youtube => SocialYoutube::make($this->url),
            default => false,
        };

        if (! $social) {
            $this->is_unknown = true;
        }
    }

    private function dailymotion(): bool
    {
        // TODO other URL formats
        $regex = '!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!';
        if (preg_match($regex, $this->url, $matches)) {
            if (isset($matches[6])) {
                $this->media_id = $matches[6];
            } elseif (isset($matches[4])) {
                $this->media_id = $matches[4];
            } else {
                $this->media_id = $matches[2];
            }

            $this->embed_url = "https://www.dailymotion.com/embed/video/{$this->media_id}";
            $this->is_frame = true;

            return true;
        }

        return false;
    }

    private function instagram(): bool
    {
        // https://www.instagram.com/p/BC2_hmZh4K7
        $regex = '/(?:https?:\/\/www\.)?instagram\.com\S*?\/p\/(\w{11})\/?/';
        if (preg_match($regex, $this->url, $matches)) {
            $this->media_id = $matches[1] ?? null;
            $this->embed_url = "https://www.instagram.com/p/{$this->media_id}/embed";
            $this->is_custom = true;

            return true;
        }

        return false;
    }

    private function facebook(): bool
    {
        $regex = '/(?:https?:\/\/(?:www|m|mbasic|business)\.(?:facebook|fb)\.com\/)(?:photo(?:\.php|s)|permalink\.php|video\.php|media|watch\/|questions|notes|[^\/]+\/(?:activity|posts|videos|photos))[\/?](?:fbid=|story_fbid=|id=|b=|v=|)(?|([0-9]+)|[^\/]+\/(\d+))/';
        if (preg_match($regex, $this->url, $matches)) {
            $this->media_id = $matches[1] ?? null;
            $this->embed_url = "https://www.facebook.com/plugins/post.php?href={$this->url}&show_text=true&width=500";
            $this->is_frame = true;

            return true;
        }

        return false;
    }

    private function spotify(): bool
    {
        // https://open.spotify.com/track/3tlkmfnEvrEyL35tWnqHYl?si=f24863fe8f2f49d3
        // https://open.spotify.com/embed/track/3tlkmfnEvrEyL35tWnqHYl?utm_source=generator
        $regex = '/^(https:\/\/open.spotify.com\/|user:track:album:artist:playlist:)([a-zA-Z0-9]+)(.*)$/m';
        if (preg_match($regex, $this->url, $matches)) {
            $type = $matches[2] ?? 'track';
            $this->media_id = $matches[3]
                ? str_replace('/', '', $matches[3])
                : null;

            $embed = "https://open.spotify.com/embed/{$type}/{$this->media_id}?";
            $embed .= 'utm_source=generator';
            $embed .= '&theme=1';

            $this->embed_url = $embed;
            $this->is_frame = true;

            return true;
        }

        return false;
    }
}
