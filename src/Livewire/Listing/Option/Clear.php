<?php

namespace Kiwilan\Steward\Livewire\Listing\Option;

use Livewire\Component;

class Clear extends Component
{
    public function clear()
    {
        $this->dispatch('clear', true);
    }

    public function render()
    {
        return view('steward::livewire.listing.option.clear');
    }
}
