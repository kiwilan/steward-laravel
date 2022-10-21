<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphTwitter;

class SocialService
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?SocialEnum $type = null,
        protected ?string $embed_url = null,
        protected ?string $embedded = null,
        protected string $title = '',
        protected bool $is_unknown = false,
        protected bool $is_embedded = false,
        protected bool $is_frame = false,
    ) {
    }

    public static function make(string $url): self
    {
        $social = new SocialService($url);
        $social->find();

        return $social;
    }

    public function getEmbedded(): ?string
    {
        return $this->embedded;
    }

    public function getIsUnknown(): bool
    {
        return $this->is_unknown;
    }

    public function getIsEmbedded(): bool
    {
        return $this->is_embedded;
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
            SocialEnum::instagram => null,
            SocialEnum::facebook => null,
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
            SocialEnum::twitter => $this->twitter(),
            SocialEnum::vimeo => null,
            SocialEnum::youtube => $this->youtube(),
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

    // @phpstan-ignore-next-line
    private function instagram()
    {
        // https://www.instagram.com/p/BC2_hmZh4K7
    }

    // @phpstan-ignore-next-line
    private function facebook()
    {
        // <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Falicia.carasco%2Fposts%2Fpfbid0qQVtgkX2vt6JQPgv1EsTXCg7WBTKufQB1QaKgjyhq1EMhHjcaxEvzS5kHnUqUwxTl&show_text=true&width=500" width="500" height="736" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        // https://www.facebook.com/alicia.carasco/posts/pfbid0qQVtgkX2vt6JQPgv1EsTXCg7WBTKufQB1QaKgjyhq1EMhHjcaxEvzS5kHnUqUwxTl
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

    private function twitter(): bool
    {
        $twitter = OpenGraphTwitter::make($this->url);

        $this->embedded = $twitter->getHtml();
        $this->title = $twitter->getOpenGraph()->title;
        $this->is_embedded = true;

        return true;
    }

    private function youtube(): bool
    {
        $regex = "/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\\/))([^\\?&\"'>]+)/";
        if (preg_match($regex, $this->url, $matches)) {
            if (isset($matches[1])) {
                $this->media_id = $matches[1];
                $this->embed_url = "https://www.youtube.com/embed/{$this->media_id}";
                $this->is_frame = true;

                return true;
            }
        }

        return false;
    }
}
