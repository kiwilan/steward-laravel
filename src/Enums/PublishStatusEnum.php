<?php

namespace Kiwilan\Steward\Enums;

use Kiwilan\Steward\Traits\LazyEnum;

enum PublishStatusEnum: string
{
    use LazyEnum;

    case draft = 'draft';

    case scheduled = 'scheduled';

    case published = 'published';
}
