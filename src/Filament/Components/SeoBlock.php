<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms;
use Kiwilan\Steward\Filament\Config\FilamentLayout\FilamentLayoutCard;

class SeoBlock
{
    public static function make(bool $card = false): Forms\Components\Component
    {
        $seo = [
            Forms\Components\Placeholder::make('seo')
                ->label('SEO'),
            Forms\Components\TextInput::make('slug')
                ->label(__('steward::filament.form_helper.metalink'))
                ->required()
                ->unique(column: 'slug', ignoreRecord: true)
                ->maxLength(256),
            Forms\Components\TextInput::make('meta_title')
                ->label(__('steward::filament.form_helper.meta_title'))
                ->maxLength(256),
            Forms\Components\Textarea::make('meta_description')
                ->label(__('steward::filament.form_helper.meta_description'))
                ->maxLength(256),
        ];

        return $card
            ? FilamentLayoutCard::make($seo, 'SEO')
            : Forms\Components\Group::make($seo);
    }
}
