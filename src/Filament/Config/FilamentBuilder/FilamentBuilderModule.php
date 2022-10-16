<?php

namespace Kiwilan\Steward\Filament\Config\FilamentBuilder;

use Filament\Forms\Components\Builder\Block;

interface FilamentBuilderModule
{
    /**
     * @return array<Block>
     */
    public static function make(): array;
}
