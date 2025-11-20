<?php

namespace App\Services\Admin;

use App\Interfaces\Admin\RoleServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService implements RoleServiceInterface
{
    public function getPaginatedRoles(?int $perPage, ?string $search, ?string $sortBy, ?string $sortDir): LengthAwarePaginator
    {
        return Role::withCount('users')
            ->when($search, fn ($query) => $query->whereLike('name', "%$search%")->orWhereLike('guard_name', "%$search%"))
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }
}
