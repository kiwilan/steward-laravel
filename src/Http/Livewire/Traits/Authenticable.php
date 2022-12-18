<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * @template T
 */
trait Authenticable
{
    public ?object $auth;

    public function initializeAuthenticable()
    {
        $this->auth();
    }

    public function auth()
    {
        $this->auth = Auth::user();
        if (property_exists($this->auth, 'refresh')) {
            $this->auth = $this->auth->refresh();
        }
    }

    /**
     * @return T
     */
    public function getAuth()
    {
        return $this->auth;
    }
}
