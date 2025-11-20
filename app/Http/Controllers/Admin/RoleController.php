<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Admin\RoleService;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $search = $validated['search'] ?? null;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';

        $allowedSorts = ['id', 'name', 'guard_name', 'created_at', 'updated_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $roles = $this->roleService->getPaginatedRoles($perPage, $search, $sortBy, $sortDir);

        return Inertia::render('administrative/roles/index', [
            'roles' => $roles,
        ]);
    }
}
