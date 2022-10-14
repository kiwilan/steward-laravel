<?php

namespace Kiwilan\Steward\Filament\Config\FilamentLayout;

use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Field;
use Illuminate\Support\Str;

class FilamentLayoutCard
{
    /**
     * @param  Field[]  $fields
     * @param  ?string  $title
     */
    public static function make(array $fields = [], ?string $title = null): Card
    {
        $list = [];
        if ($title) {
            $list[] = Forms\Components\Placeholder::make(Str::slug($title))
            ->label($title)
            ->columnSpan(2);
        }
        $list = array_merge($list, $fields);

        return Forms\Components\Card::make()->schema($list);
    }
}
