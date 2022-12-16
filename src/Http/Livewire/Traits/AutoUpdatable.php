<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

trait AutoUpdatable
{
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}
