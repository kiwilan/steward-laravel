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

    public static function toggleButtons(string $field = 'status'): \Filament\Forms\Components\ToggleButtons
    {
        return \Filament\Forms\Components\ToggleButtons::make($field)
            ->options([
                PublishStatusEnum::draft->name => PublishStatusEnum::draft->getLocaleBaseName(),
                PublishStatusEnum::scheduled->name => PublishStatusEnum::scheduled->getLocaleBaseName(),
                PublishStatusEnum::published->name => PublishStatusEnum::published->getLocaleBaseName(),
            ])
            ->icons([
                PublishStatusEnum::draft->name => PublishStatusEnum::draft->getIcon(),
                PublishStatusEnum::scheduled->name => PublishStatusEnum::scheduled->getIcon(),
                PublishStatusEnum::published->name => PublishStatusEnum::published->getIcon(),
            ])
            ->colors([
                PublishStatusEnum::draft->name => PublishStatusEnum::draft->getColor(),
                PublishStatusEnum::scheduled->name => PublishStatusEnum::scheduled->getColor(),
                PublishStatusEnum::published->name => PublishStatusEnum::published->getColor(),
            ])
            ->default('draft')
            ->inline();
    }
}
