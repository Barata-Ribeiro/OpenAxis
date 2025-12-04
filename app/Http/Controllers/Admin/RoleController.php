<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Requests\QueryRequest;
use App\Services\Admin\RoleService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;
use Spatie\Permission\Models\Permission;

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
        if (! \in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $roles = $this->roleService->getPaginatedRoles($perPage, $search, $sortBy, $sortDir);

        return Inertia::render('administrative/roles/index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        Log::info('Role: Accessing role creation form', ['action_user_id' => Auth::id()]);

        $permissions = Permission::all(['id', 'title', 'name']);

        return Inertia::render('administrative/roles/create', [
            'permissions' => Inertia::defer(fn () => $permissions),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $userId = Auth::id();
        Log::info('Role: Creating new role', ['action_user_id' => $userId, 'role_name' => $request->input('name')]);

        try {
            $role = $this->roleService->createRole($request);

            return to_route('administrative.roles.index')->with('success', "Role '$role->name' created successfully.");
        } catch (Exception $e) {
            Log::error('Role: Error creating new role', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the role.');
        }
    }
}
