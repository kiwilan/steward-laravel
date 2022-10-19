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
        // https://open.spotify.com/track/3tlkmfnEvrEyL35tWnqHYl?si=96d4c52f62684f31 => https://open.spotify.com/embed/track/3tlkmfnEvrEyL35tWnqHYl?utm_source=generator

        $this->origin = SocialEnum::findMedia($this->url);

        $format = match ($this->origin) {
            SocialEnum::dailymotion => $this->dailymotion(),
            SocialEnum::youtube => $this->youtube(),
            default => null,
        };
    }

    private function dailymotion()
    {
        $video_id = '';
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $this->url, $m)) {
            if (isset($m[6])) {
                $video_id = $m[6];
            }
            if (isset($m[4])) {
                $video_id = $m[4];
            }
            $video_id = $m[2];
        }
        $this->video_id = $video_id;
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

    private function youtube()
    {
        // https://youtu.be/0c9aRUTSV6U
        // https://www.youtube.com/watch?v=0c9aRUTSV6U
        // https://www.youtube.com/embed/0c9aRUTSV6U

        preg_match("/^(?:http(?:s)?:\\/\\/)?(?:www\\.)?(?:m\\.)?(?:youtu\\.be\\/|youtube\\.com\\/(?:(?:watch)?\\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\\/))([^\\?&\"'>]+)/", $this->url, $matches);
        if (isset($matches[1])) {
            $this->video_id = $matches[1];
            $this->url = "https://www.youtube.com/embed/{$this->video_id}";
        }
    }
}
