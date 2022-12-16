<?php

namespace App\Http\Livewire\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * @template T
 */
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
        if (property_exists($this->user, 'refresh')) {
            $this->user = $this->user->refresh();
        }
    }

    /**
     * @return T
     */
    public function getUser()
    {
        return $this->user;
    }
}
