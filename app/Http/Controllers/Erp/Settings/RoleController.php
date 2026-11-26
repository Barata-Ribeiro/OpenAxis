<?php

declare(strict_types=1);

namespace App\Http\Controllers\Erp\Settings;

use App\Concerns\Erp\ErpResourceResponses;
use App\Concerns\Erp\ManagesErpPermissions;
use App\Enums\RoleEnum;
use App\Http\Requests\Erp\DataTableRequest;
use App\Http\Requests\Erp\Settings\RoleRequest;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;
use InertiaUI\Modal\Modal;
use Spatie\Permission\Models\Role;

final class RoleController extends Controller
{
    use ErpResourceResponses, ManagesErpPermissions;

    private const string PAGE = 'erp/settings/roles';

    private const string INDEX_ROUTE = 'erp.settings.roles.index';

    private const string INDEX_PERMISSION = 'role.index';

    public function index(DataTableRequest $request): Response
    {
        return $this->renderResourceIndex($request, self::PAGE, $this->roles()->toBase(), ['name' => 'text']);
    }

    public function create(Request $request): Modal|Response
    {
        return $this->form($request, 'create');
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->writeResource(fn () => $this->save($request->user(), new Role, $request->validated()));

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Role created.'));
    }

    public function show(Request $request, Role $role): Modal|Response
    {
        $this->ensureGuard($role);

        return $this->renderResource($request, self::PAGE.'/show', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $role->name,
            'details' => [
                'Name' => $role->name,
                'Permissions' => $role->permissions()->orderBy('name')->pluck('name')->implode(', '),
            ],
        ]);
    }

    public function edit(Request $request, Role $role): Modal|Response
    {
        $this->ensureGuard($role);
        $this->authorizeChange($request->user(), $role);

        return $this->form($request, 'edit', $role);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->writeResource(function () use ($request, $role): void {
            $locked = $this->roles()->whereKey($role->getKey())->lockForUpdate()->firstOrFail();
            $this->authorizeChange($request->user(), $locked);
            $this->save($request->user(), $locked, $request->validated());
        });

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Role updated.'));
    }

    public function confirmDestroy(Request $request, Role $role): Modal|Response
    {
        $this->ensureGuard($role);
        $this->authorizeChange($request->user(), $role);

        return $this->renderResource($request, self::PAGE.'/confirm-destroy', self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => 'Delete '.$role->name,
            'id' => $role->getKey(),
        ]);
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->writeResource(function () use ($request, $role): void {
            $locked = $this->roles()->whereKey($role->getKey())->lockForUpdate()->firstOrFail();
            $actor = $request->user();
            $this->authorizeChange($actor, $locked);

            if ($locked->users()->exists()) {
                throw new DomainException('This record is still in use and cannot be removed.');
            }

            $locked->delete();
        });

        return $this->resourceRedirect($request, self::INDEX_ROUTE, self::INDEX_PERMISSION, __('Role deleted.'));
    }

    private function form(Request $request, string $view, ?Role $role = null): Modal|Response
    {
        $actor = $request->user();

        return $this->renderResource($request, self::PAGE.'/'.$view, self::INDEX_ROUTE, self::INDEX_PERMISSION, [
            'title' => $role === null ? 'New role' : 'Edit role',
            'id' => $role?->getKey(),
            'values' => $role === null ? [] : ['name' => $role->name, 'permission_ids' => $role->permissions()->pluck('permissions.id')],
            'choices' => ['permission_ids' => $this->assignablePermissions($actor)->map->only(['id', 'name'])->values()],
        ]);
    }

    /** @param array<string, mixed> $data */
    private function save(User $actor, Role $role, array $data): void
    {
        $ids = $data['permission_ids'] ?? [];
        $permissions = $this->assignablePermissions($actor)->whereIn('id', $ids);
        abort_unless($permissions->count() === count($ids), 403);

        abort_if($role->name === RoleEnum::SuperAdmin->value || $data['name'] === RoleEnum::SuperAdmin->value, 403);
        $role->forceFill(['name' => $data['name'], 'guard_name' => config('auth.defaults.guard', 'web')])->save();
        $role->syncPermissions($permissions);
    }

    private function authorizeChange(User $actor, Role $role): void
    {
        abort_if($role->name === RoleEnum::SuperAdmin->value, 403);
        abort_unless($role->permissions()->pluck('name')->every(fn (string $name): bool => $actor->can($name)), 403);
    }

    private function ensureGuard(Role $role): void
    {
        abort_unless($role->guard_name === config('auth.defaults.guard', 'web'), 404);
    }

    /** @return Builder<Role> */
    private function roles(): Builder
    {
        return Role::query()->where('guard_name', config('auth.defaults.guard', 'web'));
    }
}
