<?php

namespace Kiwilan\Steward\Http\Livewire;

use Livewire\Component;

class FieldEditor extends Component
{
    public string $content = ' ';

    public bool $preview = false;

    public function togglePreview()
    {
        $this->preview = ! $this->preview;
    }

    public function render()
    {
        return view('steward::livewire.field.editor');
    }
}
