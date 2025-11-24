<?php

use App\Enums\RoleEnum;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;

describe('tests for the "index" method of Admin\UserController', function () {
    $componentName = 'administrative/users/index';

    test('authenticated users who are not admins get a 403 response', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('administrative.users.index'))->assertForbidden();
    });

    test('authenticated admin users can visit the user administration page', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $this->actingAs($admin)
            ->get(route('administrative.users.index'))
            ->assertOk();
    });

    test('user administration page displays users', function () use ($componentName) {
        $this->actingAs($user = User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.users.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('users.data', 10) // Default per_page is 10
            ->where('users.data.0.id', $user->id)
        );
    });

    test('user administration page supports search', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $searchUser = User::factory()->create(['name' => 'UniqueName123']);

        $response = $this->get(route('administrative.users.index', ['search' => 'UniqueName123']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('users.data', 1)
            ->where('users.data.0.id', $searchUser->id)
        );
    });

    test('user administration page supports sorting', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.users.index', ['sort_by' => 'name', 'sort_dir' => 'desc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('users.data', 10)
            ->where('users.data.0.name', User::orderBy('name', 'desc')->first()->name)
        );
    });

    test('user administration page supports pagination', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.users.index', ['per_page' => 5, 'page' => 2]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('users.data', 5)
            ->where('users.current_page', 2)
            ->where('users.per_page', 5)
        );
    });

    test('user administration page handles invalid sort parameters gracefully', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.users.index', ['sort_by' => 'invalid_column', 'sort_dir' => 'asc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->has('users.data', 10)
            ->where('users.data.0.id', User::orderBy('id', 'asc')->first()->id)
        );
    });

    test('user administration page supports date filtering', function () use ($componentName) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $specificUser = User::factory()->create(['created_at' => '2023-01-15 12:00:00']);

        $startMs = Carbon::parse('2023-01-01')->startOfDay()->valueOf();
        $endMs = Carbon::parse('2023-01-31')->endOfDay()->valueOf();

        $response = $this->get(route('administrative.users.index', [
            'filters' => "created_at:{$startMs},{$endMs}",
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->where('users.data.0.id', $specificUser->id)
        );
    });

    test('user administration page supports role filtering', function () use ($componentName) {
        $this->actingAs($admin = User::where('email', config('app.admin_email'))->first());

        $role = RoleEnum::SUPER_ADMIN->value;

        $response = $this->get(route('administrative.users.index', [
            'filters' => "roles:{$role}",
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->where('users.data.0.roles.0.name', $role)
            ->where('users.data.0.id', $admin->id)
        );
    });
});

describe('tests for the "create" and "store" methods of Admin\UserController', function () {
    $createComponent = 'administrative/users/create';

    test('authenticated users who are not admins get a 403 response when accessing the create user page', function () {
        $this->actingAs(User::factory()->create());

        $this->get(route('administrative.users.create'))->assertForbidden();
    });

    test('authenticated admin users can visit the create user page', function () use ($createComponent) {
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.users.create'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($createComponent));
    });

    test('admin users can create new user accounts', function () {
        $faker = app(Faker::class);
        $admin = User::where('email', config('app.admin_email'))->first();

        $this->actingAs($admin)
            ->post(route('administrative.users.store'), [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => $faker->password(8, 16),
                'role' => $faker->randomElement(Role::pluck('name')),
            ])->assertRedirect(route('administrative.users.index'))
            ->assertSessionHas('success');
    });
});

describe('tests for "show", "edit", "update", "destroy" and "forceDestroy" methods of Admin\UserController', function () {
    test('non-admin users cannot view a user (show) page', function () {
        $this->actingAs(User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first());

        $target = User::inRandomOrder()->first();

        $this->get(route('administrative.users.show', $target->id))->assertForbidden();
    });

    test('admin users can view a user (show) page with permissions and addresses loaded', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $this->actingAs($admin);

        $target = User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first();

        $response = $this->get(route('administrative.users.show', $target->id));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('administrative/users/show')
            ->has('user')
            ->where('user.id', $target->id)
            ->has('user.permissions')
            ->has('user.addresses')
        );
    });

    test('non-admin users cannot access edit page', function () {
        $this->actingAs(User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first());

        $target = User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first();

        $this->get(route('administrative.users.edit', $target->id))->assertForbidden();
    });

    test('admin users can access edit page', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $target = User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first();

        $this->actingAs($admin);

        $response = $this->get(route('administrative.users.edit', $target->id));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('administrative/users/edit')
            ->has('user')
            ->where('user.id', $target->id)
        );
    });

    test('admin users can update a user account', function () {
        $faker = app(Faker::class);
        $admin = User::where('email', config('app.admin_email'))->first();

        $target = User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first();

        $randomRole = [RoleEnum::BUYER->value, RoleEnum::VENDOR->value, RoleEnum::FINANCE->value];

        $this->actingAs($admin)
            ->patch(route('administrative.users.update', $target->id), [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'role' => $faker->randomElement($randomRole),
            ])->assertRedirect(route('administrative.users.show', $target->id))
            ->assertSessionHas('success');
    });

    test('admin cannot delete their own account (destroy)', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $this->actingAs($admin)
            ->delete(route('administrative.users.destroy', $admin->id))
            ->assertSessionHas('error');
    });

    test('admin can delete other users (destroy)', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $target = User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first();

        $this->actingAs($admin)
            ->delete(route('administrative.users.destroy', $target->id))
            ->assertRedirect(route('administrative.users.index'))
            ->assertSessionHas('success');
    });

    test('admin cannot force-delete their own account (forceDestroy)', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $this->actingAs($admin)
            ->delete(route('administrative.users.force-destroy', $admin->id))
            ->assertSessionHas('error');
    });

    test('admin can permanently delete other users (forceDestroy)', function () {
        $admin = User::where('email', config('app.admin_email'))->first();

        $target = User::inRandomOrder()->whereHas('roles', fn ($q) => $q->where('name', '!=', RoleEnum::SUPER_ADMIN->value))->first();

        $this->actingAs($admin)
            ->delete(route('administrative.users.force-destroy', $target->id))
            ->assertRedirect(route('administrative.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    });
});
