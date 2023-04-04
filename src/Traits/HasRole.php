<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeHaveDashboardAccess(Builder $builder): Builder
    {
        return $builder->where($this->getRoleColumn(), '!=', UserRoleEnum::user->value)
            ->where('is_blocked', '!=', true)
        ;
    }

    public function isDashboardAllowed(): bool
    {
        return $this->isEditor() || $this->isAdmin() || $this->isSuperAdmin() && ! $this->isBlocked();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->value === UserRoleEnum::super_admin->value;
    }

    public function isAdmin(): bool
    {
        return $this->role->value === UserRoleEnum::admin->value;
    }

    public function isEditor(): bool
    {
        return $this->role->value === UserRoleEnum::editor->value;
    }

    public function isUser(): bool
    {
        return $this->role->value === UserRoleEnum::user->value;
    }

    public function isBlocked()
    {
        return $this->is_blocked;
    }

    protected function getIsSuperAdminAttribute(): bool
    {
        return $this->isSuperAdmin();
    }

    protected function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    protected function getIsEditorAttribute(): bool
    {
        return $this->isEditor();
    }

    protected function getIsUserAttribute(): bool
    {
        return $this->isUser();
    }

    public function scopeWhereIsUser(Builder $query)
    {
        return $query->where('role', UserRoleEnum::user);
    }
}
