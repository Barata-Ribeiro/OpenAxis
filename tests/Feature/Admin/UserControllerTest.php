<?php

use App\Models\User;
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
        $this->actingAs(User::where('email', config('app.admin_email'))->first());

        $this->get(route('administrative.users.index'))->assertOk();
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

        $response = $this->get(route('administrative.users.index', [
            'start_date' => '2023-01-01',
            'end_date' => '2023-01-31',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->where('users.data.0.id', $specificUser->id)
        );
    });

    test('user administration page supports role filtering', function () use ($componentName) {
        $this->actingAs($admin = User::where('email', config('app.admin_email'))->first());

        $response = $this->get(route('administrative.users.index', [
            'filters' => 'roles:super-admin',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName)
            ->where('users.data.0.roles.0.name', 'super-admin')
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
