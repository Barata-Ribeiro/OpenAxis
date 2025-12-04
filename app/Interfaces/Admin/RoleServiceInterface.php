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

    public function createRole(RoleRequest $request): Role;
}
