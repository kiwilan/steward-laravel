<?php

namespace Kiwilan\Steward\Filament\Table\Actions;

use Filament\Tables\Actions\ViewAction;

class ViewActionRounded
{
    public static function make()
    {
        return ViewAction::make()
            ->button()
            ->outlined()
            ->icon('');
    }
}
