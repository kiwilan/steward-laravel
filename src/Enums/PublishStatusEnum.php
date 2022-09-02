<?php

namespace Kiwilan\Enums;

use Kiwilan\Traits\LazyEnum;

enum PublishStatusEnum: string
{
    use LazyEnum;

    case draft = 'draft';
    case scheduled = 'scheduled';
    case published = 'published';
}
