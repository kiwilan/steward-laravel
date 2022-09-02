<?php

namespace Kiwilan\Enums;

use Kiwilan\Traits\LazyEnum;

enum TemplateEnum: string
{
    use LazyEnum;

    case basic = 'basic';
    case home = 'home';
    case about = 'about';
}
