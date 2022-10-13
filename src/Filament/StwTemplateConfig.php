<?php

namespace Kiwilan\Steward\Filament;

use Filament\Forms;

class StwTemplateConfig
{
    public static function block(array $content = [], string $make = 'block', string $label = 'Block')
    {
        return Forms\Components\Repeater::make($make)
            ->schema([
                StwBuilderConfig::display(),
                ...$content,
            ])
            ->disableItemMovement()
            ->maxItems(1)
            ->columnSpan(2)
            ->label($label)
            ->createItemButtonLabel("Add {$label}");
    }
}
