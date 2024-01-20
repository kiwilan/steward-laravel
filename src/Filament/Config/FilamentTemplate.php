<?php

namespace Kiwilan\Steward\Filament\Config;

use Filament\Forms;
use Kiwilan\Steward\Filament\Components\Display;

class FilamentTemplate
{
    public static function block(array $content = [], string $make = 'block', string $label = 'Block')
    {
        return Forms\Components\Repeater::make($make)
            ->schema([
                Display::make(),
                ...$content,
            ])
            ->reorderable(false)
            ->maxItems(1)
            ->columnSpan(2)
            ->label($label)
            ->addActionLabel("Add {$label}");
    }
}
