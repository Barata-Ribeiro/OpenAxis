<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Services\Admin\UserService;
use Inertia\Inertia;

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
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
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
            $startDate,
            $endDate,
            $filters
        );

        return Inertia::render('administrative/users/index', [
            'users' => $users,
        ]);
    }
}
