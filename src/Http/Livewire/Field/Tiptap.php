<?php

namespace Kiwilan\Steward\Http\Livewire\Field;

use Livewire\Component;

class Tiptap extends Component
{
    public string $content = ' ';

    public bool $preview = false;

    public function togglePreview()
    {
        $this->preview = ! $this->preview;
    }

    public function render()
    {
        return view('steward::livewire.field.tiptap');
    }
}
