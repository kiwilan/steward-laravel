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
        if (! Auth::check()) {
            return redirect()->route(\Kiwilan\Steward\StewardConfig::authLoginRoute());
        }

        $this->auth = Auth::user();

        if (method_exists($this->auth, 'refresh')) {
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
