<?php

declare(strict_types=1);

namespace App\Actions\Erp;

use App\Enums\RoleEnum;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class SettingsRecords
{
    public const array MODULES = [
        'payment-methods' => 'payment-method',
        'expense-categories' => 'expense-category',
        'categories' => 'category',
        'units' => 'unit-of-measure',
        'locations' => 'storage-location',
        'users' => 'user',
        'roles' => 'role',
    ];

    private const array MODELS = [
        'payment-methods' => PaymentMethod::class,
        'expense-categories' => ExpenseCategory::class,
        'categories' => Category::class,
        'units' => UnitOfMeasure::class,
        'locations' => StorageLocation::class,
        'users' => User::class,
        'roles' => Role::class,
    ];

    /** @param key-of<self::MODULES> $section */
    public function __construct(public readonly string $section) {}

    /** @return Builder<covariant Model> */
    public function query(): Builder
    {
        $query = self::MODELS[$this->section]::query();

        return $this->section === 'roles' ? $query->where('guard_name', $this->guard()) : $query;
    }

    /** @return array<literal-string, 'text'|'number'> */
    public function fields(): array
    {
        return ['name' => 'text'] + match ($this->section) {
            'payment-methods' => ['fee_type' => 'text', 'fee_rate' => 'number', 'is_active' => 'number'],
            'expense-categories' => ['is_system' => 'number'],
            'units' => ['code' => 'text', 'quantity_decimals' => 'number'],
            'locations' => ['code' => 'text', 'is_active' => 'number'],
            'users' => ['email' => 'text'],
            default => [],
        };
    }

    public function title(): string
    {
        return match ($this->section) {
            'payment-methods' => 'Payment method',
            'expense-categories' => 'Expense category',
            'categories' => 'Product category',
            'units' => 'Unit of measure',
            'locations' => 'Storage location',
            'users' => 'User',
            'roles' => 'Role',
        };
    }

    /** @return array<string, mixed> */
    public function choices(User $actor, ?Model $record): array
    {
        return match ($this->section) {
            'categories' => ['parent_id' => Category::query()->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                ->orderBy('name')->get(['id', 'name'])],
            'users' => ['role_ids' => $this->roles($actor)->map->only(['id', 'name'])->values()],
            'roles' => ['permission_ids' => $this->permissions($actor)->map->only(['id', 'name'])->values()],
            default => [],
        };
    }

    /** @return array<string, mixed> */
    public function values(?Model $record): array
    {
        if ($record === null) {
            return match ($this->section) {
                'payment-methods' => ['fee_type' => PaymentMethod::FEE_FIXED, 'fee_rate' => 0, 'is_active' => true],
                'locations' => ['is_active' => true],
                'units' => ['quantity_decimals' => 0],
                default => [],
            };
        }

        return match (true) {
            $record instanceof Category => $record->only(['name', 'parent_id']),
            $record instanceof User => [...$record->only(['name', 'email']), 'role_ids' => $record->roles()->pluck('roles.id')],
            $record instanceof Role => ['name' => $record->name, 'permission_ids' => $record->permissions()->pluck('permissions.id')],
            default => $record->only(array_keys($this->fields())),
        };
    }

    /** @return array<string, mixed> */
    public function details(Model $record): array
    {
        $details = ['Name' => $record->getAttribute('name')];

        return $details + match (true) {
            $record instanceof Category => ['Parent category' => $record->parent()->value('name')],
            $record instanceof PaymentMethod => [
                'Fee type' => ucfirst($record->fee_type),
                'Fee rate' => ['feeRate' => $record->fee_rate, 'feeType' => $record->fee_type],
                'Status' => $record->is_active ? 'Active' : 'Inactive',
            ],
            $record instanceof ExpenseCategory => ['System category' => $record->is_system ? 'Yes' : 'No'],
            $record instanceof UnitOfMeasure => ['Code' => $record->code, 'Quantity decimals' => $record->quantity_decimals],
            $record instanceof StorageLocation => ['Code' => $record->code, 'Status' => $record->is_active ? 'Active' : 'Inactive'],
            $record instanceof User => ['Email' => $record->email, 'Roles' => $record->roles()->orderBy('name')->pluck('name')->implode(', ')],
            $record instanceof Role => ['Permissions' => $record->permissions()->orderBy('name')->pluck('name')->implode(', ')],
            default => [],
        };
    }

    /** @param array<string, mixed> $data */
    public function save(User $actor, Model $record, array $data): void
    {
        if ($record instanceof User) {
            $roles = $this->roles($actor)->whereIn('id', $data['role_ids'] ?? []);
            abort_unless($roles->count() === count($data['role_ids'] ?? []), 403);

            if ($record->exists && $record->email !== $data['email']) {
                $record->email_verified_at = null;
            }

            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }

            $record->fill(collect($data)->except('role_ids')->all())->save();

            if (! $record->is($actor)) {
                $record->syncRoles($roles);
            }

            return;
        }

        if ($record instanceof Role) {
            abort_if($data['name'] === RoleEnum::SuperAdmin->value, 403);
            $permissions = $this->permissions($actor)->whereIn('id', $data['permission_ids'] ?? []);
            abort_unless($permissions->count() === count($data['permission_ids'] ?? []), 403);
            $record->forceFill(['name' => $data['name'], 'guard_name' => $this->guard()])->save();
            $record->syncPermissions($permissions);

            return;
        }

        $record->fill($data)->save();
    }

    public function authorizeChange(User $actor, Model $record): void
    {
        if ($record instanceof User) {
            abort_if($record->hasRole(RoleEnum::SuperAdmin->value), 403);
            abort_unless($record->getAllPermissions()->every(fn (Permission $permission): bool => $actor->can($permission->name)), 403);
        }

        if ($record instanceof Role) {
            abort_if($record->name === RoleEnum::SuperAdmin->value, 403);
            abort_unless($record->permissions()->pluck('name')->every(fn (string $name): bool => $actor->can($name)), 403);
        }
    }

    public function guard(): string
    {
        return config('auth.defaults.guard', 'web');
    }

    /** @return Collection<int, Role> */
    private function roles(User $actor): Collection
    {
        return Role::query()->where('guard_name', $this->guard())->where('name', '!=', RoleEnum::SuperAdmin->value)
            ->with('permissions')->orderBy('name')->get()
            ->filter(fn (Role $role): bool => $role->permissions->pluck('name')->every(fn (string $name): bool => $actor->can($name)));
    }

    /** @return Collection<int, Permission> */
    private function permissions(User $actor): Collection
    {
        return Permission::query()->where('guard_name', $this->guard())->orderBy('name')->get()
            ->filter(fn (Permission $permission): bool => $actor->can($permission->name));
    }
}
