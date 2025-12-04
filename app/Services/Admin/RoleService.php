<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\RoleRequest;
use App\Interfaces\Admin\RoleServiceInterface;
use Arr;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService implements RoleServiceInterface
{
    public function getPaginatedRoles(?int $perPage, ?string $search, ?string $sortBy, ?string $sortDir): LengthAwarePaginator
    {
        return Role::withCount(['users', 'permissions'])
            ->when($search, fn ($query) => $query->whereLike('name', "%$search%")->orWhereLike('guard_name', "%$search%"))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createRole(RoleRequest $request): Role
    {
        $guard = config('auth.defaults.guard', 'web');

        $validated = $request->validated();

        $roleData = Arr::only($validated, ['name']);
        $roleData['guard_name'] = $guard;

        $permissions = Arr::get($validated, 'permissions', []);

        return DB::transaction(fn () => Role::create($roleData)->syncPermissions($permissions));
    }
}
