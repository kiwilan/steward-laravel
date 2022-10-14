<?php

namespace Kiwilan\Steward\Filament\Config;

use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Illuminate\Support\Str;

class FilamentLayout
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

    public static function column(array|Closure $content = [], int $width = 2)
    {
        $parts = [];
        foreach ($content as $part) {
            if (! empty($part)) {
                $parts[] = Forms\Components\Card::make()
                    ->schema($part)
                    ->columns([
                        'sm' => $width,
                    ]);
            } else {
                $parts[] = Forms\Components\Group::make();
            }
        }

        return Forms\Components\Group::make()
            ->schema($parts)
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
