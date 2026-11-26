<?php

declare(strict_types=1);

namespace App\Concerns\Erp;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait ManagesErpPermissions
{
    /** @return Collection<int, Role> */
    private function assignableRoles(User $actor): Collection
    {
        return Role::query()->where('guard_name', config('auth.defaults.guard', 'web'))
            ->where('name', '!=', RoleEnum::SuperAdmin->value)
            ->with('permissions')->orderBy('name')->get()
            ->filter(fn (Role $role): bool => $role->permissions->every(fn (Permission $permission): bool => $actor->can($permission->name)));
    }

    /** @return Collection<int, Permission> */
    private function assignablePermissions(User $actor): Collection
    {
        return Permission::query()->where('guard_name', config('auth.defaults.guard', 'web'))->orderBy('name')->get()
            ->filter(fn (Permission $permission): bool => $actor->can($permission->name));
    }
}
