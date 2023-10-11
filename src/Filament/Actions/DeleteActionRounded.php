<?php

namespace App\Filament\Actions;

use Filament\Actions\DeleteAction;

class DeleteActionRounded
{
    public static function make()
    {
        return DeleteAction::make()
            ->button()
            ->outlined()
            ->icon('')
        ;
    }
}
