<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\UserRole;

trait HasRole
{
    protected $default_role_column = 'role';

    public function initializeHasRole()
    {
        $this->fillable[] = $this->getRoleColumn();

        $this->casts[] = $this->getRoleColumn();
        $this->casts[$this->getRoleColumn()] = UserRole::class;
    }

    public function getRoleColumn(): string
    {
        return $this->role_column ?? $this->default_role_column;
    }
}
