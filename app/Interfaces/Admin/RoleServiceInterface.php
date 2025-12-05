<?php

namespace App\Interfaces\Admin;

use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

interface RoleServiceInterface
{
    /**
     * Retrieve a paginated list of roles with optional sorting and searching.
     *
     * @param  int|null  $perPage  Number of items per page. If null, the default pagination size is applied.
     * @param  string|null  $search  Search term to perform full- or partial-text matching against relevant role fields (name, guard_name, etc.). If null or empty, no search filtering is applied.
     * @param  string|null  $sortBy  Column or attribute name to sort by (e.g. 'name', 'guard_name', 'created_at'). If null, a default sort column is used.
     * @param  string|null  $sortDir  Sort direction: 'asc' or 'desc'. If null, a default direction (usually 'asc') is used.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Paginated collection of roles matching the provided criteria.
     */
    public function getPaginatedRoles(?int $perPage, ?string $search, ?string $sortBy, ?string $sortDir): LengthAwarePaginator;

    /**
     * Create a new role.
     *
     * Creates and persists a new Role using the validated data from the provided RoleRequest.
     *
     * @param  RoleRequest  $request  Validated request containing attributes required to create the role.
     * @return Role The newly created Role instance.
     *
     * @throws \Throwable If the role could not be created (database or validation errors).
     */
    public function createRole(RoleRequest $request): Role;

    /**
     * Update an existing role.
     *
     * Updates the given Role with validated data from the provided RoleRequest and returns the updated instance.
     *
     * @param  RoleRequest  $request  Validated request containing attributes to update.
     * @param  Role  $role  The Role instance to update.
     * @return Role The updated Role instance.
     *
     * @throws \Throwable If the role could not be updated (database or validation errors).
     */
    public function updateRole(RoleRequest $request, Role $role): Role;
}
