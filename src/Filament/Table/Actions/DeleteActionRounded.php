<?php

namespace Kiwilan\Steward\Filament\Table\Actions;

use Filament\Tables\Actions\DeleteAction;

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
