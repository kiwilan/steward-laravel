<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;
use Kiwilan\Steward\Enums\SocialEnum;

class SocialService
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?SocialEnum $type = null,
        protected ?string $embed_url = null,
        protected ?string $embedded = null,
        protected string $width = '100%',
        protected string $height = '500',
        protected bool $rounded = false,
        protected string $title = '',
    ) {
    }

    public static function make(string $url): SocialService
    {
        $service = new SocialService($url);
        $service->find();

        return $service;
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
            SocialEnum::spotify => null,
            SocialEnum::ted => null,
            SocialEnum::tumblr => null,
            SocialEnum::tiktok => null,
            SocialEnum::twitch => null,
            SocialEnum::twitter => null,
            SocialEnum::vimeo => null,
            SocialEnum::youtube => $this->youtube(),
            default => null,
        };

        if ($this->type !== SocialEnum::twitter) {
            $this->embedded = $this->setHtml();
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

    // @phpstan-ignore-next-line
    private function spotify()
    {
        // https://open.spotify.com/track/3tlkmfnEvrEyL35tWnqHYl?si=f24863fe8f2f49d3
        // https://open.spotify.com/embed/track/3tlkmfnEvrEyL35tWnqHYl?utm_source=generator
    }

    // @phpstan-ignore-next-line
    private function twitter()
    {
        // https://publish.twitter.com
        // https://developer.twitter.com/en/docs/twitter-for-websites/embedded-tweets/overview
        $api = 'https://publish.twitter.com/oembed?url=';

        $client = new Client();
        $res = $client->get("{$api}{$this->url}");
        $body = $res->getBody()->getContents();

        // $this->embedded = $body['html'];
    }

    private function youtube(): ?string
    {
        // https://youtu.be/0c9aRUTSV6U
        // https://www.youtube.com/watch?v=0c9aRUTSV6U
        // https://www.youtube.com/embed/0c9aRUTSV6U

        preg_match("/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\\/))([^\\?&\"'>]+)/", $this->url, $matches);
        if (isset($matches[1])) {
            $this->media_id = $matches[1];

            return "https://www.youtube.com/embed/{$this->media_id}";
        }

        return null;
    }

    private function setHtml()
    {
        $this->embedded = <<<HTML
            <iframe
                src="{ $this->embed_url }"
                width="{ $this->width }"
                height="{ this->height }"
                src="{ $this->url }"
                title="{ $this->title }"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy"
            ></iframe>
        HTML;
    }
}
