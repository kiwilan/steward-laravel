<?php

namespace Kiwilan\Steward\Filament\Actions;

use Filament\Actions\EditAction;

class EditActionRounded
{
    public static function make()
    {
        return EditAction::make()
            ->button()
            ->outlined()
            ->icon('')
        ;
    }
}
