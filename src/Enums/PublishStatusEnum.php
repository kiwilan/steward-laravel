<?php

namespace Kiwilan\Steward\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Kiwilan\Steward\Traits\LazyEnum;

enum PublishStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use LazyEnum;

    case draft = 'draft';

    case scheduled = 'scheduled';

    case published = 'published';

    public function getColor(): ?string
    {
        return match ($this) {
            self::draft => 'danger',
            self::scheduled => 'warning',
            self::published => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::draft => 'heroicon-o-document-text',
            self::scheduled => 'heroicon-o-clock',
            self::published => 'heroicon-o-check-circle',
        };
    }

    public function getLabel(): ?string
    {
        return $this->toArray()[$this->value];
    }
}
