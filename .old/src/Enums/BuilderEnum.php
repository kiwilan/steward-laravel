<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum BuilderEnum: string
{
    use LazyEnum;

    case wordpress = 'wordpress';
}
