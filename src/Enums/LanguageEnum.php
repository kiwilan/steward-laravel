<?php

namespace Kiwilan\Enums;

use Kiwilan\Traits\LazyEnum;

enum LanguageEnum: string
{
    use LazyEnum;

    case en = 'en';
    case fr = 'fr';
}
