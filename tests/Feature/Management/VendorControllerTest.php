<?php

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Vendor;
use Faker\Generator as Faker;
use Inertia\Testing\AssertableInertia;

describe('tests for the "index" method of Management/VendorController', function () {
    $componentName = 'erp/vendors/index';

    test('users without vendor.index permission get a 403 response', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.vendors.index'))
            ->assertForbidden();
    });

    test('authorized users can access the vendor listing', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $expectedFirst = Vendor::withTrashed()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.vendors.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('vendors.data', 10)
            ->where('vendors.data.0.id', $expectedFirst->id)
        );
    });

    test('vendor listing supports searching by user email', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetVendor = Vendor::with('user')->inRandomOrder()->firstOrFail();
        $search = $targetVendor->user()->first()->email;

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.vendors.index', ['search' => $search]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('vendors.data')
        );

        $vendors = $response->inertiaProps('vendors.data');
        expect(collect($vendors)->pluck('id'))->toContain($targetVendor->id);
    });

    test('vendor listing supports sorting', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $expectedFirst = Vendor::withTrashed()->orderBy('first_name', 'desc')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.vendors.index', ['sort_by' => 'first_name', 'sort_dir' => 'desc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('vendors.data.0.id', $expectedFirst->id)
        );
    });

    test('vendor listing supports pagination controls', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.vendors.index', ['per_page' => 5, 'page' => 2]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('vendors.data', 5)
            ->where('vendors.current_page', 2)
            ->where('vendors.per_page', 5)
        );
    });

    test('vendor listing gracefully falls back when sort field is invalid', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $expectedFirst = Vendor::withTrashed()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.vendors.index', ['sort_by' => 'invalid_column', 'sort_dir' => 'asc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('vendors.data.0.id', $expectedFirst->id)
        );
    });
});

describe('tests for the "show" method of Management/VendorController', function () {
    $componentName = 'erp/vendors/show';

    test('users without vendor.show permission cannot view vendor details', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetVendor = Vendor::with('user')->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.vendors.show', $targetVendor))
            ->assertForbidden();
    });

    test('authorized users can view vendor details including user', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetVendor = Vendor::with('user')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.vendors.show', $targetVendor));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('vendor')
            ->where('vendor.id', $targetVendor->id)
            ->has('vendor.user')
        );
    });
});

describe('tests for the "create" method of Management/VendorController', function () {
    $componentName = 'erp/vendors/create';

    test('users without vendor.create permission cannot access the create page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.vendors.create'))
            ->assertForbidden();
    });

    test('authorized users can access the vendor creation page', function () use ($componentName) {
        $authorizedUser = getSuperAdmin();

        $response = $this->actingAs($authorizedUser)->get(route('erp.vendors.create'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });
});

describe('tests for the "store" method of Management/VendorController', function () {
    test('users without vendor.create permission cannot store vendors', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->post(route('erp.vendors.store'), [])
            ->assertForbidden();
    });

    test('authorized users can store vendors', function () {
        $faker = app(Faker::class);

        $userToAssign = User::role(RoleEnum::VENDOR->value)
            ->whereNotIn('id', Vendor::pluck('user_id')->toArray())
            ->firstOrFail();

        $authorizedUser = getSuperAdmin();

        $payload = [
            'user_id' => $userToAssign->id,
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'phone_number' => $faker->phoneNumber(),
            'commission_rate' => $faker->numberBetween(0, 20),
            'is_active' => true,
        ];

        $this->actingAs($authorizedUser)
            ->post(route('erp.vendors.store'), $payload)
            ->assertRedirect(route('erp.vendors.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vendors', [
            'user_id' => $payload['user_id'],
            'first_name' => $payload['first_name'],
        ]);
    });
});

describe('tests for the "edit" method of Management/VendorController', function () {
    $componentName = 'erp/vendors/edit';

    test('users without vendor.edit permission cannot access edit page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetVendor = Vendor::firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.vendors.edit', $targetVendor))
            ->assertForbidden();
    });

    test('authorized users can access edit page', function () use ($componentName) {
        $authorizedUser = getSuperAdmin();
        $targetVendor = Vendor::firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.vendors.edit', $targetVendor));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('vendor')
            ->where('vendor.id', $targetVendor->id)
        );
    });
});

describe('tests for the "update" method of Management/VendorController', function () {
    test('users without vendor.edit permission cannot update vendors', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetVendor = Vendor::inRandomOrder()->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->patch(route('erp.vendors.update', $targetVendor), [])
            ->assertForbidden();
    });

    test('authorized users can update vendors', function () {
        $faker = app(Faker::class);
        $authorizedUser = getSuperAdmin();
        $targetVendor = Vendor::inRandomOrder()->firstOrFail();

        $payload = [
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'commission_rate' => $faker->numberBetween(0, 20),
            'phone_number' => $faker->phoneNumber(),
            'is_active' => false,
        ];

        $this->actingAs($authorizedUser)
            ->patch(route('erp.vendors.update', $targetVendor), $payload)
            ->assertRedirect(route('erp.vendors.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vendors', [
            'id' => $targetVendor->id,
            'first_name' => $payload['first_name'],
            'commission_rate' => $payload['commission_rate'],
        ]);
    });
});

describe('tests for the "destroy" method of Management/VendorController', function () {
    test('users without vendor.destroy permission cannot delete vendors', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetVendor = Vendor::whereNull('deleted_at')->inRandomOrder()->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.vendors.destroy', $targetVendor))
            ->assertForbidden();
    });

    test('authorized users can soft delete vendors', function () {
        $admin = getSuperAdmin();
        $targetVendor = Vendor::whereNull('deleted_at')->inRandomOrder()->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('erp.vendors.destroy', $targetVendor))
            ->assertRedirect(route('erp.vendors.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('vendors', ['id' => $targetVendor->id]);
    });
});

describe('tests for the "forceDestroy" method of Management/VendorController', function () {
    test('users without vendor.destroy permission cannot permanently delete vendors', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetVendor = Vendor::inRandomOrder()->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.vendors.force-destroy', $targetVendor))
            ->assertForbidden();
    });

    test('authorized users can permanently delete vendors', function () {
        $admin = getSuperAdmin();
        $targetVendor = Vendor::inRandomOrder()->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('erp.vendors.force-destroy', $targetVendor))
            ->assertRedirect(route('erp.vendors.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('vendors', ['id' => $targetVendor->id]);
    });
});
