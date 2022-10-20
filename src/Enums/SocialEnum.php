<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum SocialEnum: string
{
    use LazyEnum;

    case dailymotion = 'dailymotion';
    case instagram = 'instagram';
    case facebook = 'facebook';
    case flickr = 'flickr';
    case giphy = 'giphy';
    case imgur = 'imgur';
    case kickstarter = 'kickstarter';
    case linkedin = 'linkedin';
    case pinterest = 'pinterest';
    case reddit = 'reddit';
    case snapchat = 'snapchat';
    case soundcloud = 'soundcloud';
    case spotify = 'spotify';
    case ted = 'ted';
    case tumblr = 'tumblr';
    case tiktok = 'tiktok';
    case twitch = 'twitch';
    case twitter = 'twitter';
    case vimeo = 'vimeo';
    case youtube = 'youtube';

    public static function find(string $url): ?SocialEnum
    {
        foreach (SocialEnum::cases() as $enum) {
            if (str_contains($url, $enum->value)) {
                return $enum;
            }
        }

        if (str_contains($url, 'youtu')) {
            return SocialEnum::youtube;
        }

        return null;
    }
}
