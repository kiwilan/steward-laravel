<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class FilamentLayoutCard
{
    /**
     * @param  Field[]  $fields
     * @param  ?string  $title
     */
    public static function make(array $fields = [], ?string $title = null, int $width = 2): Section
    {
        $list = [];

        if ($title) {
            $list[] = Forms\Components\Placeholder::make(Str::slug($title))
                ->label($title)
                ->columnSpan([
                    'sm' => 1,
                    'lg' => $width,
                ]);
        }
        $list = array_merge($list, $fields);

        return Section::make()
            ->schema($list)
            ->columns([
                'sm' => 1,
                'lg' => $width,
            ]);
    }
}
