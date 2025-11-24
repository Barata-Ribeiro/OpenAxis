<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditUserRequest;
use App\Http\Requests\Admin\UserAccountRequest;
use App\Http\Requests\QueryRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected UserService $userService) {}

    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $filters = $validated['filters'] ?? [];

        $allowedSorts = ['id', 'name', 'email', 'roles', 'created_at', 'updated_at', 'deleted_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $users = $this->userService->getPaginatedUsers(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );

        return Inertia::render('administrative/users/index', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        $user->load(['roles.permissions:id,name,title', 'addresses']);

        $permissions = $user->roles
            ->flatMap(fn ($role) => $role->permissions ?? collect())
            ->unique('id')
            ->values();

        $user->setRelation('permissions', $permissions);

        return Inertia::render('administrative/users/show', [
            'user' => $user,
        ]);
    }

    public function create()
    {
        return Inertia::render('administrative/users/create');
    }

    public function store(UserAccountRequest $request)
    {
        $userId = Auth::id();
        Log::info('User: Creating New User', ['action_user_id' => $userId]);

        $validated = $request->validated();

        try {
            $user = $this->userService->createUser($validated);

            return to_route('administrative.users.index')->with('success', $user->name." account's created successfully.");
        } catch (Exception $e) {
            Log::error('User: A Creation Error Occurred', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'An unknown error occurred while creating the user.');
        }
    }

    public function edit(User $user)
    {
        $user->load(['roles:name']);

        return Inertia::render('administrative/users/edit', [
            'user' => $user,
        ]);
    }

    public function update(EditUserRequest $request, User $user)
    {
        $userId = Auth::id();
        Log::info('User: Updating User', ['action_user_id' => $userId, 'target_user_id' => $user->id]);

        try {
            $this->userService->updateUser($user, $request);

            return to_route('administrative.users.show', $user->id)->with('success', $user->name."'s account updated successfully.");
        } catch (Exception $e) {
            Log::error('User: An Update Error Occurred', ['action_user_id' => $userId, 'target_user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'An unknown error occurred while updating the user.');
        }
    }

    public function destroy(User $user)
    {
        $userId = Auth::id();

        if ($userId === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        Log::info('User: Deleting User', ['action_user_id' => $userId, 'target_user_id' => $user->id]);

        try {
            $user->delete();

            return to_route('administrative.users.index')->with('success', $user->name."'s account deleted successfully.");
        } catch (Exception $e) {
            Log::error('User: A Deletion Error Occurred', ['action_user_id' => $userId, 'target_user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'An unknown error occurred while deleting the user.');
        }
    }

    public function forceDestroy(User $user)
    {
        $userId = Auth::id();

        if ($userId === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        Log::info('User: Permanently Deleting User', ['action_user_id' => $userId, 'target_user_id' => $user->id]);

        try {
            $user->forceDelete();

            return to_route('administrative.users.index')->with('success', $user->name."'s account permanently deleted successfully.");
        } catch (Exception $e) {
            Log::error('User: A Permanent Deletion Error Occurred', ['action_user_id' => $userId, 'target_user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'An unknown error occurred while permanently deleting the user.');
        }
    }
}
