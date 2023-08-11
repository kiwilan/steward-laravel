<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DateFilter
{
    public static function make(string $field = 'created_at')
    {
        return Filter::make('created_at')
            ->form([
                Forms\Components\DatePicker::make('created_from')
                    ->label('Publié depuis le')
                    ->placeholder(fn ($state): string => now()->subYear()->format('M d, Y')),
                Forms\Components\DatePicker::make('created_until')
                    ->label("Publié jusqu'au")
                    ->placeholder(fn ($state): string => now()->format('M d, Y')),
            ])
            ->query(
                fn (Builder $query, array $data): Builder => $query
                    ->when(
                        $data['created_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate($field, '>=', $date),
                    )
                    ->when(
                        $data['created_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate($field, '<=', $date),
                    )
            )
        ;
    }
}
