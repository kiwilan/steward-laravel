<?php

namespace Kiwilan\Steward\Enums\BuilderEnum;

use Kiwilan\Steward\Traits\LazyEnum;

enum BuilderSocialEnum: string
{
    use LazyEnum;

    case facebook = 'facebook';
    case twitter = 'twitter';
    case youtube = 'youtube';
    case instagram = 'instagram';
    case pinterest = 'pinterest';
    case linkedin = 'linkedin';
    case tiktok = 'tiktok';
    case twitch = 'twitch';
    case snapchat = 'snapchat';
}
