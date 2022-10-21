<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphItem;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphTwitter;

class SocialService
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?SocialEnum $type = null,
        protected ?string $embed_url = null,
        protected ?string $embedded = null,
        protected string $width = '100%',
        protected string $height = '450',
        protected bool $rounded = false,
        protected string $title = '',
        protected bool $is_unknown = false,
        protected ?OpenGraphItem $openGraph = null,
    ) {
    }

    public static function make(string $url): SocialService
    {
        $service = new SocialService($url);
        $service->find();

        return $service;
    }

    public function getEmbedded(): ?string
    {
        return $this->embedded;
    }

    public function getIsUnknown(): bool
    {
        return $this->is_unknown;
    }

    public function getOpenGraph(): ?OpenGraphItem
    {
        return $this->openGraph;
    }

    private function find()
    {
        $this->type = SocialEnum::find($this->url);

        $this->embed_url = match ($this->type) {
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
            default => 'unknown',
        };

        if (SocialEnum::twitter !== $this->type) {
            $this->embedded = $this->setHtml();
        }

        if ('unknown' === $this->embed_url || ! $this->embed_url) {
            $this->is_unknown = true;
            $this->unknown();
        }
    }

    private function dailymotion(): ?string
    {
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $this->url, $m)) {
            if (isset($m[6])) {
                $this->media_id = $m[6];
            }
            if (isset($m[4])) {
                $this->media_id = $m[4];
            }
            $this->media_id = $m[2];

            return "https://www.dailymotion.com/embed/video/{$this->media_id}";
        }

        return null;
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

    private function spotify()
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
            $embed .= 'theme=1';

            return $embed;
        }
    }

    private function twitter(): string
    {
        $twitter = OpenGraphTwitter::make($this->url);

        $this->title = $twitter->getOpenGraph()->title;
        $this->embedded = $twitter->getHtml();

        return $this->url;
    }

    private function youtube(): ?string
    {
        $regex = "/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\\/))([^\\?&\"'>]+)/";
        preg_match($regex, $this->url, $matches);
        if (isset($matches[1])) {
            $this->media_id = $matches[1];

            return "https://www.youtube.com/embed/{$this->media_id}";
        }

        return null;
    }

    private function unknown(): ?OpenGraphItem
    {
        $this->openGraph = OpenGraphService::make($this->url);

        return $this->openGraph;
    }

    private function setHtml(): string
    {
        $src = $this->embed_url;
        $width = $this->width;
        $height = $this->height;
        $title = $this->title;

        $allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';

        $html = '<iframe ';
        $html .= "src=\"{$src}\" ";
        $html .= "width=\"{$width}\" ";
        $html .= "height=\"{$height}\" ";
        $html .= "title=\"{$title}\" ";
        $html .= "allow=\"{$allow}\" ";
        $html .= 'allowfullscreen ';
        $html .= 'frameborder="0" ';
        $html .= 'scrolling="no" ';
        $html .= 'loading="lazy" ';
        $html .= '></iframe>';

        return $html;
    }
}
