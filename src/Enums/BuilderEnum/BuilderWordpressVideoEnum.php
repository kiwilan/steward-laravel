<?php

namespace Kiwilan\Steward\Enums\BuilderEnum;

use Kiwilan\Steward\Traits\LazyEnum;

enum BuilderWordpressVideoEnum: string
{
    use LazyEnum;

    case youtube = 'youtube';
    case dailymotion = 'dailymotion';
    case vimeo = 'vimeo';
}
