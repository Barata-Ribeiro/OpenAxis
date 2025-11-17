<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(QueryRequest $request)
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'asc';
        $search = trim($validated['search'] ?? '');
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $allowedSorts = ['id', 'name', 'email', 'roles', 'created_at', 'updated_at', 'deleted_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $users = User::query()
            ->select(['id', 'name', 'email', 'created_at', 'updated_at', 'deleted_at'])
            ->when($search, fn ($query, $search) => $query->whereLike('name', "%$search%")->orWhereLike('email', "%$search%"))
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]))
            ->when($startDate && ! $endDate, fn ($q) => $q->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($startDate)->endOfDay()]))
            ->when($endDate && ! $startDate, fn ($q) => $q->whereBetween('created_at', [Carbon::parse($endDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]))
            ->with('roles:id,name');

        if ($sortBy === 'roles') {
            $rolesSub = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('model_has_roles.model_id', DB::raw("GROUP_CONCAT(roles.name ORDER BY roles.name SEPARATOR ', ') as roles_names"))
                ->where('model_has_roles.model_type', User::class)
                ->groupBy('model_has_roles.model_id');

            $users->leftJoinSub($rolesSub, 'r', fn ($join) => $join->on('users.id', '=', 'r.model_id'))
                ->orderBy(DB::raw('COALESCE(r.roles_names, "")'), $sortDir);
        } else {
            $users->orderBy("users.$sortBy", $sortDir);
        }

        $users = $users->paginate($perPage)
            ->withQueryString();

        return Inertia::render('administrative/users/index', [
            'users' => $users,
        ]);
    }
}
