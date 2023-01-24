<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum LanguageEnum: string
{
    use LazyEnum;

    case en = 'en';

    case fr = 'fr';
}
