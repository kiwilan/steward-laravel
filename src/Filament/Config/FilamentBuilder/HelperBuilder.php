<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder;

use Filament\Forms;

class HelperBuilder
{
    public static function container(array $content, string $field = 'content', ?int $minItems = null, ?int $maxItems = null)
    {
        $container = Forms\Components\Builder::make($field)
            ->blocks([
                ...$content,
            ])
            ->collapsed()
            ->collapsible()
            ->columnSpan(2)
        ;

        if ($minItems) {
            $container->minItems($minItems);
        }
        if ($maxItems) {
            $container->maxItems($maxItems);
        }

        return $container;
    }

    public static function block(array $fields): FilamentBuilderBlock
    {
        // return Forms\Components\Builder\Block::make($name)
        //     ->schema([
        //         ...$content,
        //     ])
        //     ->icon('heroicon-o-bookmark')
        //     ->columns(2)
        // ;

        return FilamentBuilderBlock::make($fields);
    }
}
