<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Concerns\Erp\ManagesErpPermissions;
use App\Enums\RoleEnum;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\UserRequest;
use App\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Inertia\Response;
use InertiaUI\Modal\Modal;
use Spatie\Permission\Models\Permission;

final class UserController extends Controller
{
    use ErpResourceResponses, ManagesErpPermissions;

    private const string PAGE = 'erp/settings/users';

    private const string INDEX_ROUTE = 'erp.settings.users.index';

    private const string INDEX_PERMISSION = 'user.index';

    public function index(DataTableRequest $request): Response
    {
        return $this->renderResourceIndex($request, self::PAGE, User::query()->toBase(), ['name' => 'text', 'email' => 'text']);
    }

    public function create(Request $request): Modal|Response
    {
        return $this->form($request, 'create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => $this->save($request->user(), new User, $request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('User created.'));
    }

    public function show(Request $request, User $user): Modal|Response
    {
        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $user->name,
            'details' => [
                'Name' => $user->name,
                'Email' => $user->email,
                'Roles' => $user->roles()->orderBy('name')->pluck('name')->implode(', '),
            ],
        ]);
    }

    public function edit(Request $request, User $user): Modal|Response
    {
        $this->authorizeChange($request->user(), $user);

        return $this->form($request, 'edit', $user);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->writeResource(function () use ($request, $user): void {
            $locked = User::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $this->authorizeChange($request->user(), $locked);
            $this->save($request->user(), $locked, $request->validated());
        });

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('User updated.'));
    }

    public function confirmDestroy(Request $request, User $user): Modal|Response
    {
        $this->authorizeChange($request->user(), $user);

        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Delete '.$user->name,
            'id' => $user->getKey(),
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->writeResource(function () use ($request, $user): void {
            $locked = User::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $actor = $request->user();
            $this->authorizeChange($actor, $locked);

            if ($locked->is($actor)) {
                throw new DomainException('This record is still in use and cannot be removed.');
            }

            $locked->delete();
        });

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('User deleted.'));
    }

    private function form(Request $request, string $view, ?User $user = null): Modal|Response
    {
        $actor = $request->user();
        $self = $user?->is($actor) ?? false;

        return $this->renderResource($request, self::PAGE.'/'.$view, self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $user === null ? 'New user' : 'Edit user',
            'id' => $user?->getKey(),
            'values' => $user === null ? [] : [...$user->only(['name', 'email']), 'role_ids' => $user->roles()->pluck('roles.id')],
            'choices' => $self ? [] : ['role_ids' => $this->assignableRoles($actor)->map->only(['id', 'name'])->values()],
            'self' => $self,
        ]);
    }

    /** @param array<string, mixed> $data */
    private function save(User $actor, User $user, array $data): void
    {
        $ids = $data['role_ids'] ?? [];
        $roles = $this->assignableRoles($actor)->whereIn('id', $ids);
        abort_unless($roles->count() === count($ids), 403);

        if ($user->exists && $user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->fill(Arr::except($data, ['role_ids']))->save();

        if (! $user->is($actor)) {
            $user->syncRoles($roles);
        }
    }

    private function authorizeChange(User $actor, User $user): void
    {
        abort_if($user->hasRole(RoleEnum::SuperAdmin->value), 403);
        abort_unless($user->getAllPermissions()->every(fn (Permission $permission): bool => $actor->can($permission->name)), 403);
    }
}
