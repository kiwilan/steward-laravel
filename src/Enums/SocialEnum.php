<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum SocialEnum: string
{
    use LazyEnum;

    case dailymotion = 'dailymotion';
    case instagram = 'instagram';
    case facebook = 'facebook';
    case linkedin = 'linkedin';
    case pinterest = 'pinterest';
    case snapchat = 'snapchat';
    case spotify = 'spotify';
    case tiktok = 'tiktok';
    case twitch = 'twitch';
    case twitter = 'twitter';
    case vimeo = 'vimeo';
    case youtube = 'youtube';

    public static function findMedia(string $url): ?SocialEnum
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
