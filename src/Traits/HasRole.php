<?php

namespace Kiwilan\Steward\Traits;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Enum;
use Kiwilan\Steward\Enums\UserRoleEnum;
use UnitEnum;

/**
 * Trait HasRole
 *
 * - Default role column is `role`, can be override by setting `$role_column` property
 * - Default enum with is `\Kiwilan\Steward\Enums\UserRoleEnum`, can be override by setting `$role_enum` property
 *
 * ```php
 * class Post extends Model
 * {
 *    use HasRole;
 *
 *   protected $role_column = 'role_column'; // default is `role`
 *   protected $role_enum = 'role_enum'; // default is `\Kiwilan\Steward\Enums\UserRoleEnum`
 * }
 * ```
 */
trait HasRole
{
    protected $default_role_column = 'role';

    protected $default_role_enum = UserRoleEnum::class;

    public function initializeHasRole()
    {
        $this->fillable[] = $this->getRoleColumn();
        $this->fillable[] = 'is_blocked';

        $this->casts[$this->getRoleColumn()] = $this->getEnumClass();
        $this->casts['is_blocked'] = 'boolean';
    }

    private function getEnumClass(): mixed
    {
        return $this->role_enum ?? $this->default_role_enum;
    }

    public function getRoleColumn(): string
    {
        return $this->role_column ?? $this->default_role_column;
    }

    public function scopeHaveDashboardAccess(Builder $builder): Builder
    {
        return $builder->where($this->getRoleColumn(), '!=', $this->getEnumClass()::user->value)
            ->where('is_blocked', '!=', true)
        ;
    }

    public function isDashboardAllowed(): bool
    {
        return $this->isEditor() || $this->isAdmin() || $this->isSuperAdmin() && ! $this->isBlocked();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role->value === $this->getEnumClass()::super_admin->value;
    }

    public function isAdmin(): bool
    {
        return $this->role->value === $this->getEnumClass()::admin->value;
    }

    public function isEditor(): bool
    {
        return $this->role->value === $this->getEnumClass()::editor->value;
    }

    public function isUser(): bool
    {
        return $this->role->value === $this->getEnumClass()::user->value;
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
        return $query->where('role', $this->getEnumClass()::user);
    }

    public function scopeWhereIsNotSuperAdmin(Builder $query)
    {
        return $query->where('role', '!=', $this->getEnumClass()::super_admin);
    }
}
