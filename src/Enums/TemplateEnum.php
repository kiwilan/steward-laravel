<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum TemplateEnum: string
{
    use LazyEnum;

    case home = 'home';
    case about = 'about';
}
