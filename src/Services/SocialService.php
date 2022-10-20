<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Enums\SocialEnum;

class SocialService
{
    private function __construct(
        protected string $url,
        protected ?string $video_id = null,
        protected ?SocialEnum $origin = null,
    ) {
    }

    public static function make(string $url): SocialService
    {
        $service = new SocialService($url);
        $service->findMedia();

        return $service;
    }

    private function findMedia()
    {
        // https://www.dailymotion.com/video/x8elgz7 => https://www.dailymotion.com/embed/video/x8elgz7
        // https://www.youtube.com/watch?v=0c9aRUTSV6U => https://www.youtube.com/embed/0c9aRUTSV6U
        // https://vimeo.com/161110645 => https://player.vimeo.com/video/161110645?h=e46badf906
        // https://open.spotify.com/track/3tlkmfnEvrEyL35tWnqHYl?si=96d4c52f62684f31 =>

        // spotify: les quatre saisons
        // 'url' => 'https://open.spotify.com/track/6xMpUNOfaSkyywPiFFXZFh'
        // 'share_url' => 'https://open.spotify.com/track/6xMpUNOfaSkyywPiFFXZFh?si=0ac6060423d540ed'
        // 'embed' => 'https://open.spotify.com/embed/track/6xMpUNOfaSkyywPiFFXZFh?utm_source=generator'

        // youtube: Les Quatre Saisons de Antonio Vivaldi
        // 'url' => 'https://www.youtube.com/watch?v=C243DQBfjho'
        // 'embed' => 'https://www.youtube.com/embed/C243DQBfjho'
        // 'short_url' => 'https://youtu.be/C243DQBfjho'

        // instagram: operadeparis
        // 'profile' => 'https://www.instagram.com/operadeparis'
        // 'post' => 'https://www.instagram.com/reel/CjcnkEXMmSI'
        // 'post_link' => 'https://www.instagram.com/reel/CjcnkEXMmSI/?utm_source=ig_web_copy_link'
        // 'post_embed' => 'https://www.instagram.com/reel/CjcnkEXMmSI/?utm_source=ig_embed&amp;utm_campaign=loading'

        // dailymotion: Vivaldi - Les 4 Saisons
        // 'url' => 'https://www.dailymotion.com/video/x1t5li3'
        // 'short_url' => 'https://dai.ly/x1t5li3'
        // 'embed' => 'https://www.dailymotion.com/embed/video/x1t5li3'

        // twitter: Ballet Opéra Paris
        // 'profile' => 'https://twitter.com/balletoparis'
        // 'profile_link' => 'https://twitter.com/BalletOParis?s=20&t=kZTtgbvNXVIVkUR5zwNeIw'
        // 'tweet' => 'https://twitter.com/BalletOParis/status/1580947790250721283'
        // 'embed' => 'https://twitter.com/BalletOParis/status/1580947790250721283?ref_src=twsrc%5Etfw'

        $this->origin = SocialEnum::findMedia($this->url);

        $this->url = match ($this->origin) {
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
    }

    private function dailymotion(): ?string
    {
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $this->url, $m)) {
            if (isset($m[6])) {
                $this->video_id = $m[6];
            }
            if (isset($m[4])) {
                $this->video_id = $m[4];
            }
            $this->video_id = $m[2];

            return "https://www.dailymotion.com/embed/video/{$this->video_id}";
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
    }

    private function youtube(): ?string
    {
        // https://youtu.be/0c9aRUTSV6U
        // https://www.youtube.com/watch?v=0c9aRUTSV6U
        // https://www.youtube.com/embed/0c9aRUTSV6U

        preg_match("/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\\/))([^\\?&\"'>]+)/", $this->url, $matches);
        if (isset($matches[1])) {
            $this->video_id = $matches[1];

            return "https://www.youtube.com/embed/{$this->video_id}";
        }

        return null;
    }
}
