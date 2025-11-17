<?php

namespace App\Services\Admin;

use App\Interfaces\Admin\UserServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    public function getPaginatedUsers(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, ?string $startDate, ?string $endDate): LengthAwarePaginator
    {
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

        return $users->paginate($perPage)->withQueryString();
    }
}
