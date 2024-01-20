<?php

namespace Kiwilan\Steward\Filament\Table\Actions;

use Filament\Tables\Actions\EditAction;

class EditActionRounded
{
    public static function make()
    {
        return EditAction::make()
            ->button()
            ->outlined()
            ->icon('');
    }
}
