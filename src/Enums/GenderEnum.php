<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum GenderEnum: string
{
    use LazyEnum;

    case other = 'other';
    case female = 'female';
    case male = 'male';
    case nonbinary = 'nonbinary';
    case notsay = 'notsay';
}
