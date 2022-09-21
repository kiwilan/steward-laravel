<?php

namespace Kiwilan\Steward\Traits;

use Kiwilan\Steward\Enums\UserRoleEnum;

trait HasRole
{
    protected $default_role_column = 'role';

    public function initializeHasRole()
    {
        $this->fillable[] = $this->getRoleColumn();
        $this->fillable[] = 'is_blocked';

        $this->casts[$this->getRoleColumn()] = UserRoleEnum::class;
        $this->casts['is_blocked'] = 'boolean';
    }

    public function getRoleColumn(): string
    {
        return $this->role_column ?? $this->default_role_column;
    }

    public function haveDashboardAccess(): bool
    {
        return $this->is_editor || $this->is_admin || $this->is_super_admin && ! $this->is_blocked;
    }

    protected function getIsSuperAdminAttribute(): bool
    {
        return $this->role->value === UserRoleEnum::super_admin->value;
    }

    protected function getIsAdminAttribute(): bool
    {
        return $this->role->value === UserRoleEnum::admin->value;
    }

    protected function getIsEditorAttribute(): bool
    {
        return $this->role->value === UserRoleEnum::editor->value;
    }

    protected function getIsUserAttribute(): bool
    {
        return $this->role->value === UserRoleEnum::user->value;
    }
}
