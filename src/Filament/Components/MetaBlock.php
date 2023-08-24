<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutCard;

class MetaBlock
{
    public static function make(bool $card = false): Forms\Components\Component
    {
        $timestamps = [
            Forms\Components\Placeholder::make('id')
                ->label('ID')
                ->content(fn ($record): ?string => $record?->id),
            Forms\Components\Placeholder::make('created_at')
                ->label(__('steward::filament.form_label.created_at'))
                ->content(fn ($record): ?string => $record?->created_at?->diffForHumans()),
            Forms\Components\Placeholder::make('updated_at')
                ->label(__('steward::filament.form_label.updated_at'))
                ->content(fn ($record): ?string => $record?->updated_at?->diffForHumans()),
        ];

        return $card
            ? FilamentLayoutCard::make($timestamps, 'Timestamps')
            : Forms\Components\Group::make($timestamps);
    }
}
