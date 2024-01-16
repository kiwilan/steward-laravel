<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum SpatieMediaMethodEnum: string
{
    use LazyEnum;

    case addMedia = 'addMedia';

    case addMediaFromBase64 = 'addMediaFromBase64';

    case addMediaFromDisk = 'addMediaFromDisk';

    case addMediaFromRequest = 'addMediaFromRequest';

    case addMediaFromStream = 'addMediaFromStream';

    case addMediaFromString = 'addMediaFromString';

    case addMediaFromUrl = 'addMediaFromUrl';
}
