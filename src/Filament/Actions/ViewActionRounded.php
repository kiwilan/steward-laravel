<?php

namespace Kiwilan\Steward\Filament\Actions;

use Filament\Actions\ViewAction;

class ViewActionRounded
{
    public static function make()
    {
        return ViewAction::make()
            ->button()
            ->outlined()
            ->icon('')
        ;
    }
}
