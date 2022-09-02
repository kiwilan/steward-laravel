<?php

namespace Kiwilan\Enums;

use Kiwilan\Traits\LazyEnum;

enum SpatieMediaMethodEnum: string
{
    use LazyEnum;

    case addMediaFromString = 'addMediaFromString';
    case addMediaFromBase64 = 'addMediaFromBase64';
    case addMediaFromDisk = 'addMediaFromDisk';
}
