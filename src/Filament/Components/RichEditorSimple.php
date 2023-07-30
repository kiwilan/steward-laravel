<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms;

class RichEditorSimple
{
    public static function make(
        string $field,
        array $toolbar = [
            'bold',
            'italic',
            'link',
        ],
    ): Forms\Components\RichEditor {
        return Forms\Components\RichEditor::make($field)
            ->toolbarButtons($toolbar)
        ;
    }
}
