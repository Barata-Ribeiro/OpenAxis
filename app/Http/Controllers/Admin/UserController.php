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
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Log;

use function in_array;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected UserService $userService) {}

    public function index(QueryRequest $request)
    {
        $users = $this->getPaginatedUsersFromRequest($request);

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

        try {
            $user = $this->userService->createUser($request);

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

    public function generateCsv(QueryRequest $request)
    {
        $userId = Auth::id();

        try {
            $users = $this->getPaginatedUsersFromRequest($request);

            if ($users->isEmpty()) {
                return back()->with('error', 'No users found to export.');
            }

            return $this->userService->generateCsvExport($users);
        } catch (Exception $e) {
            Log::error('User: A CSV Generation Error Occurred', ['action_user_id' => $userId, 'error' => $e->getMessage()]);

            return back()->with('error', 'An unknown error occurred while generating the CSV export.');
        }
    }

    /**
     * Build and return a LengthAwarePaginator of users based on the given request.
     *
     * Applies filtering, searching, sorting and eager-loading options provided by the
     * validated QueryRequest, then paginates the resulting query.
     *
     * Expected request inputs (handled/validated by QueryRequest):
     *  - page / per_page: pagination parameters
     *  - sort: sorting column/direction
     *  - filters: associative array of field => value
     *  - with: relations to eager-load
     *
     * @param  QueryRequest  $request  Validated query parameters for filtering, sorting and pagination.
     * @return \Illuminate\Pagination\LengthAwarePaginator Paginated collection of User models.
     */
    private function getPaginatedUsersFromRequest(QueryRequest $request): LengthAwarePaginator
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

        return $this->userService->getPaginatedUsers(
            $perPage,
            $sortBy,
            $sortDir,
            $search,
            $filters
        );
    }
}
