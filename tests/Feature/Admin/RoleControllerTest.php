<?php

use App\Enums\RoleEnum;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('tests for the "index" method of Admin\RoleController', function () {
    $componentName = 'administrative/roles/index';

    test('authenticated users who are not admins get a 403 response', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('administrative.roles.index'))->assertForbidden();
    });

    test('authenticated admin users can visit the role administration page', function () {
        $admin = getSuperAdmin();

        $this->actingAs($admin);

        $this->get(route('administrative.roles.index'))->assertOk();
    });

    test('role administration page displays roles', function () use ($componentName) {
        $this->actingAs($user = getSuperAdmin());

        $response = $this->get(route('administrative.roles.index'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
                ->has('roles.data', 5) // Default per_page is 10
                ->where('roles.data.0.id', $user->id)
            );
    });

    test('role administration page supports search', function () use ($componentName) {
        $this->actingAs(getSuperAdmin());

        $searchRole = Role::create(['name' => 'UniqueRole123']);

        $response = $this->get(route('administrative.roles.index', ['search' => 'UniqueRole123']));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
                ->has('roles.data', 1)
                ->where('roles.data.0.id', $searchRole->id)
            );
    });

    test('role administration page supports sorting', function () use ($componentName) {
        $this->actingAs(getSuperAdmin());

        $response = $this->get(route('administrative.roles.index', ['sort_by' => 'name', 'sort_dir' => 'desc']));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
                ->has('roles.data', 5)
                ->where('roles.data.0.name', Role::orderBy('name', 'desc')->first()->name)
            );
    });

    test('role administration page supports pagination', function () use ($componentName) {
        $this->actingAs(getSuperAdmin());

        $guard = config('auth.defaults.guard', 'web');

        for ($i = 1; $i <= 10; $i++) {
            Role::firstOrCreate(['name' => "TestRole{$i}", 'guard_name' => $guard]);
        }

        $response = $this->get(route('administrative.roles.index', ['per_page' => 5, 'page' => 2]));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
                ->has('roles.data', 5)
                ->where('roles.current_page', 2)
                ->where('roles.per_page', 5)
            );
    });

    test('role administration page handles invalid sort parameters gracefully', function () use ($componentName) {
        $this->actingAs(getSuperAdmin());

        $response = $this->get(route('administrative.roles.index', ['sort_by' => 'invalid_column', 'sort_dir' => 'asc']));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
                ->has('roles.data', 5)
                ->where('roles.data.0.id', Role::orderBy('id', 'asc')->first()->id)
            );
    });
});

describe('tests for the "create" method of Admin\RoleController', function () {
    $componentName = 'administrative/roles/create';

    test('authenticated users who are not admins get a 403 response', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('administrative.roles.create'))->assertForbidden();
    });

    test('authenticated admin users can access the role creation form', function () {
        $admin = getSuperAdmin();

        $this->actingAs($admin);

        $this->get(route('administrative.roles.create'))->assertOk();
    });

    test('role creation form loads permissions', function () use ($componentName) {
        $this->actingAs(getSuperAdmin());

        $response = $this->get(route('administrative.roles.create'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component($componentName)
                ->missing('permissions')
                ->loadDeferredProps(fn (AssertableInertia $reload) => $reload
                    ->has('permissions')
                    ->where('permissions.0.name', 'user.index')
                ));
    });
});

describe('tests for the "store" method of Admin\RoleController', function () {
    test('authenticated users who are not admins get a 403 response', function () {
        $this->actingAs(User::factory()->create());

        $this->post(route('administrative.roles.store'), ['name' => 'Test Role'])->assertForbidden();
    });

    test('authenticated admin can create a role', function () {
        $admin = getSuperAdmin();

        $this->actingAs($admin);

        $permission = Permission::create(['name' => 'test.permission', 'title' => 'Test Permission', 'guard_name' => 'web']);

        $this->post(route('administrative.roles.store'), [
            'name' => 'Test Role',
            'permissions' => [$permission->name],
        ])
            ->assertRedirect(route('administrative.roles.index'))
            ->assertSessionHas('success', "Role 'Test Role' created successfully.");

        $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
        $role = Role::where('name', 'Test Role')->first();
        $this->assertTrue($role->hasPermissionTo('test.permission'));
    });

    test('role creation fails with invalid data', function () {
        $this->actingAs(getSuperAdmin());

        $response = $this->post(route('administrative.roles.store'), ['name' => '']);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
    });

    test('role creation handles exceptions gracefully', function () {
        $this->actingAs(getSuperAdmin());

        $response = $this->post(route('administrative.roles.store'), ['name' => str_repeat('a', 256)]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
    });
});

describe('tests for the "edit" method of Admin\RoleController', function () {
    $componentName = 'administrative/roles/edit';

    test('authenticated users who are not admins get a 403 response', function () {
        $role = Role::create(['name' => 'Test Role']);

        $this->actingAs(User::factory()->create());

        $this->get(route('administrative.roles.edit', $role))->assertForbidden();
    });

    test('authenticated admin can access the role edit form', function () {
        $admin = getSuperAdmin();

        $this->actingAs($admin);

        $role = Role::whereNot('name', RoleEnum::SUPER_ADMIN->value)->first();

        $this->get(route('administrative.roles.edit', $role))->assertOk();
    });

    test('super admin role cannot be edited', function () {
        $this->actingAs(getSuperAdmin());

        $superAdminRole = Role::where('name', RoleEnum::SUPER_ADMIN->value)->first();

        $response = $this->get(route('administrative.roles.edit', $superAdminRole));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'The Super Admin role cannot be edited through this interface.');
    });

    test('role edit form loads role and permissions', function () use ($componentName) {
        $this->actingAs(getSuperAdmin());

        $role = Role::create(['name' => 'Test Role']);
        $permission = Permission::create(['name' => 'test.permission', 'title' => 'Test Permission', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $response = $this->get(route('administrative.roles.edit', $role));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('role')->where('role.name', $role->name)->has('role.permissions', 1)
            ->missing('permissions')
            ->loadDeferredProps(fn (AssertableInertia $reload) => $reload
                ->has('permissions')
                ->where('permissions', fn ($permissions) => collect($permissions)->pluck('name')->contains($permission->name))
            )
        );
    });
});

describe('tests for the "update" method of Admin\RoleController', function () {
    test('authenticated users who are not admins get a 403 response', function () {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(User::factory()->create());

        $this->patch(route('administrative.roles.update', $role), ['name' => 'Updated Role'])->assertForbidden();
    });

    test('authenticated admin can update a role', function () {
        $admin = getSuperAdmin();

        $this->actingAs($admin);

        $role = Role::create(['name' => 'Test Role']);
        $permission = Permission::create(['name' => 'test.permission', 'title' => 'Test Permission', 'guard_name' => 'web']);

        $this->patch(route('administrative.roles.update', $role), [
            'name' => 'Updated Role',
            'permissions' => [$permission->name],
        ])->assertRedirect(route('administrative.roles.index'))
            ->assertSessionHas('success', "Role 'Updated Role' updated successfully.");

        $role->refresh();
        $this->assertEquals('Updated Role', $role->name);
        $this->assertTrue($role->hasPermissionTo('test.permission'));
    });

    test('role update fails with invalid data', function () {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(getSuperAdmin());

        $response = $this->patch(route('administrative.roles.update', $role), ['name' => '']);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
    });

    test('role update handles exceptions gracefully', function () {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(getSuperAdmin());

        $response = $this->patch(route('administrative.roles.update', $role), ['name' => str_repeat('a', 256)]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
    });
});

describe('tests for the "destroy" method of Admin\RoleController', function () {
    test('authenticated users who are not admins get a 403 response', function () {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(User::factory()->create());

        $this->delete(route('administrative.roles.destroy', $role))->assertForbidden();
    });

    test('authenticated admin can delete a role', function () {
        $admin = getSuperAdmin();

        $this->actingAs($admin);

        $role = Role::create(['name' => 'Test Role']);

        $this->delete(route('administrative.roles.destroy', $role))
            ->assertRedirect(route('administrative.roles.index'))
            ->assertSessionHas('success', "Role 'Test Role' deleted successfully.");

        $this->assertDatabaseMissing('roles', ['name' => 'Test Role']);
    });

    test('protected roles cannot be deleted', function () {
        $this->actingAs(getSuperAdmin());

        $protectedRole = Role::where('name', RoleEnum::SUPER_ADMIN->value)->first();

        $response = $this->delete(route('administrative.roles.destroy', $protectedRole));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cannot delete a protected role.');

        $this->assertDatabaseHas('roles', ['name' => RoleEnum::SUPER_ADMIN->value]);
    });

    test('role deletion handles exceptions gracefully', function () {
        $role = Role::create(['name' => 'Test Role']);
        $this->actingAs(getSuperAdmin());

        $this->delete(route('administrative.roles.destroy', $role))
            ->assertRedirect(route('administrative.roles.index'));
    });
});
