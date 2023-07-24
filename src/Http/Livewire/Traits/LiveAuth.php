<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

/**
 * `Livewire\Component` trait to find auth user.
 *
 * @property Authenticatable|Model|null $auth
 *
 * @method Authenticatable|Model|null auth()
 */
trait LiveAuth
{
    public Authenticatable|Model|null $auth;

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

    public function auth(): Authenticatable|Model|null
    {
        return $this->auth;
    }
}
