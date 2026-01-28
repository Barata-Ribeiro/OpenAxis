<?php

namespace App\Services\Admin;

use App\Common\Helpers;
use App\Enums\RoleEnum;
use App\Http\Requests\Admin\EditUserRequest;
use App\Http\Requests\Admin\UserAccountRequest;
use App\Interfaces\Admin\UserServiceInterface;
use App\Mail\NewUserMail;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserService implements UserServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function getPaginatedUsers(?int $perPage, ?string $sortBy, ?string $sortDir, ?string $search, $filters): LengthAwarePaginator
    {
        $roles = $filters['roles'] ?? [];
        $createdAtRange = $filters['created_at'] ?? [];

        [$start, $end] = Helpers::getDateRange($createdAtRange);

        $users = User::query()
            ->select(['id', 'name', 'email', 'created_at', 'updated_at', 'deleted_at'])
            ->when($search, fn ($query, $search) => $query->whereLike('name', "%$search%")->orWhereLike('email', "%$search%"))
            ->when($createdAtRange, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($roles, fn ($q) => $q->whereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', $roles)))
            ->with('roles:id,name', 'media')
            ->withTrashed();

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

    /**
     * {@inheritDoc}
     */
    public function createUser(UserAccountRequest $request): User
    {
        $validated = $request->validated();

        $data = collect($validated)
            ->except('role')
            ->toArray();

        $user = User::create($data)->assignRole($validated['role']);

        Mail::to($user->email)->send(new NewUserMail($user->name, $user->email, $data['password']));

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser(User $user, EditUserRequest $data): User
    {

        $validated = $data->validated();

        if (! $data->filled('password')) {
            unset($validated['password']);
            unset($validated['password_confirmation']);
        }

        $user->updateOrFail($validated);
        $user->syncRoles($validated['role']);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function generateCsvExport(LengthAwarePaginator $users): BinaryFileResponse
    {
        $finalFilename = Carbon::now()->format('Y_m_d_H_i_s').'_users_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$finalFilename\"",
        ];

        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_'.Str::ulid()).'.csv';
        $openFile = fopen($csvFileName, 'w');

        fwrite($openFile, "\xEF\xBB\xBF");

        $delimiter = ';';
        $header = ['ID', 'Name', 'Email', 'Roles', 'Created At', 'Updated At', 'Deleted At'];
        fputcsv($openFile, $header, $delimiter);

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->map(fn (string $name): string => RoleEnum::tryFrom($name)?->label() ?? $name)->join(', ');

            $row = [
                $user->id,
                $user->name,
                $user->email,
                $roles,
                $user->created_at,
                $user->updated_at,
                $user->deleted_at,
            ];

            fputcsv($openFile, $row, $delimiter);
        }

        fclose($openFile);

        Log::info('User: CSV Export Generated', ['action_user_id' => Auth::id()]);

        return response()->download($csvFileName, $finalFilename, $headers)->deleteFileAfterSend(true);
    }
}
