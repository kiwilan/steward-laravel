<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

/**
 * Find current authenticatable if exists.
 *
 * @property Authenticatable|User|null $auth
 *
 * @method Authenticatable|User|null auth()
 */
trait LiveAuth
{
    public Authenticatable|User|null $auth;

    public function initializeLiveAuth()
    {
        $this->liveAuth();
    }

    private function liveAuth()
    {
        if (! Auth::check()) {
            return redirect()->route(\Kiwilan\Steward\StewardConfig::authLoginRoute());
        }

        /** @var User */
        $auth = Auth::user();

        if (method_exists($auth, 'refresh')) {
            $auth = $auth->refresh();
        }

        $this->auth = $auth;
    }

    public function auth(): Authenticatable|User|null
    {
        return $this->auth;
    }
}
