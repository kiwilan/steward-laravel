<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Authenticable
{
    public ?Model $user;

    public function initializeAuthenticable()
    {
        $this->user();
    }

    public function user()
    {
        /** @var Model */
        $this->user = Auth::user();
        $this->user = $this->user->refresh();
    }
}
