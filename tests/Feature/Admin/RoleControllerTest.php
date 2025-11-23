<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;

describe('tests for the "index" method of Admin\RoleController', function () {
    $componentName = 'administrative/roles/index';

    test('authenticated users who are not admins get a 403 response', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('administrative.roles.index'))->assertForbidden();
    });

    test('authenticated admin users can visit the role administration page', function () {
        $admin = User::where('email', config('app.admin_email'))->first();
        $admin->givePermissionTo('role.edit');
        $this->actingAs($admin);

        $this->get(route('administrative.roles.index'))->assertOk();
    });

    test('role administration page displays roles', function () use ($componentName) {
        $this->actingAs($user = User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.roles.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('roles.data', 4) // Default per_page is 10
            ->where('roles.data.0.id', $user->id)
        );
    });

    test('role administration page supports search', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $searchRole = Role::create(['name' => 'UniqueRole123']);

        $response = $this->get(route('administrative.roles.index', ['search' => 'UniqueRole123']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('roles.data', 1)
            ->where('roles.data.0.id', $searchRole->id)
        );
    });

    test('role administration page supports sorting', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.roles.index', ['sort_by' => 'name', 'sort_dir' => 'desc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('roles.data', 4)
            ->where('roles.data.0.name', Role::orderBy('name', 'desc')->first()->name)
        );
    });

    test('role administration page supports pagination', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $guard = config('auth.defaults.guard', 'web');

        for ($i = 1; $i <= 10; $i++) {
            Role::firstOrCreate(['name' => "TestRole{$i}", 'guard_name' => $guard]);
        }

        $response = $this->get(route('administrative.roles.index', ['per_page' => 5, 'page' => 2]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('roles.data', 5)
            ->where('roles.current_page', 2)
            ->where('roles.per_page', 5)
        );
    });

    test('role administration page handles invalid sort parameters gracefully', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.roles.index', ['sort_by' => 'invalid_column', 'sort_dir' => 'asc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('roles.data', 4)
            ->where('roles.data.0.id', Role::orderBy('id', 'asc')->first()->id)
        );
    });
});
