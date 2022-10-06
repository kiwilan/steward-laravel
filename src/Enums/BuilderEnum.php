<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum BuilderEnum: string
{
    use LazyEnum;

    case basic = 'basic';
    case home = 'home';
    case about = 'about';
}
