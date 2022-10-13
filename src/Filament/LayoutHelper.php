<?php

namespace Kiwilan\Steward\Filament;

use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Illuminate\Support\Str;

class LayoutHelper
{
    public static function container(mixed $columns = null, Form $form, int $width = 3)
    {
        return $form
            ->schema($columns)
            ->columns([
                'sm' => $width,
                'lg' => null,
            ]);
    }

    public static function column(array|Closure $firstPart = [], array|Closure $secondPart = [], array|Closure $thirdPart = [], int $width = 2)
    {
        return Forms\Components\Group::make()
            ->schema([
                ! empty($firstPart)
                    ? Forms\Components\Card::make()
                        ->schema($firstPart)
                        ->columns([
                            'sm' => $width,
                        ])
                    : Forms\Components\Group::make(),
                ! empty($secondPart)
                    ? Forms\Components\Card::make()
                        ->schema($secondPart)
                        ->columns([
                            'sm' => $width,
                        ])
                    : Forms\Components\Group::make(),
                ! empty($thirdPart)
                ? Forms\Components\Card::make()
                    ->schema($thirdPart)
                    ->columns([
                        'sm' => $width,
                    ])
                : Forms\Components\Group::make(),
            ])
            ->columnSpan([
                'sm' => $width,
            ]);
    }

    public static function card(array|Closure $card = [], int $columns = 2, string $title = '')
    {
        return Forms\Components\Card::make()
            ->schema([
                Forms\Components\Placeholder::make(Str::slug($title))
                    ->label($title)
                    ->columnSpan(2),
                ...$card,
            ])
            ->columns($columns);
    }
}
