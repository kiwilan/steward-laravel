<?php

namespace Kiwilan\Steward\Filament;

use Filament\Forms;

class BuilderHelper
{
    public static function container(array $content, string $field = 'content')
    {
        return Forms\Components\Builder::make($field)
            ->blocks([
                ...$content,
            ])
            ->columnSpan(2);
    }

    public static function block(array $content, string $name = 'content')
    {
        return Forms\Components\Builder\Block::make($name)
            ->schema([
                ...$content,
            ])
            ->columns(2);
    }

    public static function display()
    {
        return Forms\Components\Toggle::make('display')
            ->helperText('Show this block on the page')
            ->label('Display')
            ->default(true)
            ->columnSpan(2);
    }

    public static function basic()
    {
        return BuilderHelper::container([
            BuilderHelper::block([
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('slug'),
                Forms\Components\Textarea::make('summary'),
                LayoutHelper::card('SEO', [
                    Forms\Components\TextInput::make('meta_title'),
                    Forms\Components\Textarea::make('meta_description'),
                ]),
                Forms\Components\RichEditor::make('content')
                    ->columnSpan(2),
            ]),
        ]);
    }
}
