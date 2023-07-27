<?php

namespace Kiwilan\Steward\Livewire\Traits;

/**
 * `Livewire\Component` trait to live validate property.
 */
trait LiveAutoUpdatable
{
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}
