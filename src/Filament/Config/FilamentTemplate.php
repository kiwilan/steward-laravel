<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Forms;

class FilamentTemplate
{
    public static function block(array $content = [], string $make = 'block', string $label = 'Block')
    {
        return Forms\Components\Repeater::make($make)
            ->schema([
                FilamentForm::display(),
                ...$content,
            ])
            ->disableItemMovement()
            ->maxItems(1)
            ->columnSpan(2)
            ->label($label)
            ->createItemButtonLabel("Add {$label}");
    }
}
