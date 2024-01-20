<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms;

class Display
{
    public static function make(): Forms\Components\Toggle
    {
        return Forms\Components\Toggle::make('display')
            ->helperText('Show this block on the page')
            ->label('Display')
            ->default(true)
            ->columnSpan(2);
    }
}
