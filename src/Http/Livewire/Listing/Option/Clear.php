<?php

namespace Kiwilan\Steward\Http\Livewire\Listing\Option;

use Livewire\Component;

class Clear extends Component
{
    public function clear()
    {
        $this->emitUp('clear', true);
    }

    public function render()
    {
        return view('steward::livewire.listing.option.clear');
    }
}
