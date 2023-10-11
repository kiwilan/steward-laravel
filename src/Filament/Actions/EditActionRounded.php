<?php

namespace App\Filament\Actions;

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
