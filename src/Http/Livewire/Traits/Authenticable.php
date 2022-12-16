<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Support\Facades\Auth;

trait Authenticable
{
    public ?object $user;

    public function initializeAuthenticable()
    {
        $this->user();
    }

    public function user()
    {
        $this->user = Auth::user();
        if (property_exists($this, 'refresh')) {
            $this->user = $this->user->refresh();
        }
    }
}
