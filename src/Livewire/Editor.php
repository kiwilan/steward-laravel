<?php

namespace Kiwilan\Steward\Livewire;

use Livewire\Component;

class Editor extends Component
{
    public string $content = ' ';

    public bool $preview = false;

    public function togglePreview()
    {
        $this->preview = ! $this->preview;
    }

    public function render()
    {
        return view('livewire.editor');
    }
}
