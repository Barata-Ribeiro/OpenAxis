<?php

use App\Enums\RoleEnum;
use App\Models\Client;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;

describe('tests for the "index" method of Management/ClientController', function () {
    $componentName = 'erp/clients/index';

    test('users without client.index permission get a 403 response', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.clients.index'))
            ->assertForbidden();
    });

    test('authorized users can access the client listing', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $expectedFirst = Client::withTrashed()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.clients.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('clients.data', 10)
            ->where('clients.data.0.id', $expectedFirst->id)
        );
    });

    test('client listing supports searching by identification', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetClient = Client::withTrashed()->inRandomOrder()->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.index', ['search' => $targetClient->identification]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('clients.data', 1)
            ->where('clients.data.0.id', $targetClient->id)
        );
    });

    test('client listing supports sorting', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $expectedFirst = Client::withTrashed()->orderBy('name', 'desc')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.index', ['sort_by' => 'name', 'sort_dir' => 'desc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('clients.data.0.id', $expectedFirst->id)
        );
    });

    test('client listing supports pagination controls', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.index', ['per_page' => 5, 'page' => 2]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('clients.data', 5)
            ->where('clients.current_page', 2)
            ->where('clients.per_page', 5)
        );
    });

    test('client listing gracefully falls back when sort field is invalid', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $expectedFirst = Client::withTrashed()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.index', ['sort_by' => 'invalid_column', 'sort_dir' => 'asc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('clients.data.0.id', $expectedFirst->id)
        );
    });

    test('client listing supports created_at filtering', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetClient = Client::factory()->createOne(['created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)]);

        $start = Carbon::parse($targetClient->created_at)->startOfDay()->valueOf();
        $end = Carbon::parse($targetClient->created_at)->endOfDay()->valueOf();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.index', [
                'filters' => "created_at:{$start},{$end}",
            ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('clients.data.0.id', $targetClient->id)
        );
    });

    test('client listing supports client_type filtering', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetClient = Client::withTrashed()->whereNotNull('client_type')->inRandomOrder()->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.index', [
                'filters' => "client_type:{$targetClient->client_type}",
            ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->where('clients.data.0.client_type', $targetClient->client_type)
        );
    });
});

describe('tests for the "show" method of Management/ClientController', function () {
    $componentName = 'erp/clients/show';

    test('users without client.show permission cannot view client details', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetClient = Client::with('addresses')->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.clients.show', $targetClient))
            ->assertForbidden();
    });

    test('authorized users can view client details including addresses', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetClient = Client::with('addresses')->firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.show', $targetClient));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('client')
            ->where('client.id', $targetClient->id)
            ->has('client.addresses')
        );
    });
});

describe('tests for the "create" method of Management/ClientController', function () {
    $componentName = 'erp/clients/create';

    test('users without client.create permission cannot access the create page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.clients.create'))
            ->assertForbidden();
    });

    test('authorized users can access the client creation page', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.clients.create'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });
});

describe('tests for the "store" method of Management/ClientController', function () {
    test('users without client.create permission cannot store clients', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->post(route('erp.clients.store'), [])
            ->assertForbidden();
    });

    test('authorized users can store clients with addresses', function () {
        $faker = app(Faker::class);
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $payload = [
            'name' => $faker->name(),
            'email' => Str::uuid().'@clients.test',
            'identification' => generateValidIdentification(),
            'phone_number' => '+1555550'.random_int(1000, 9999),
            'client_type' => 'individual',
            'type' => 'billing',
            'label' => 'HQ',
            'street' => $faker->streetName(),
            'number' => (string) $faker->buildingNumber(),
            'complement' => 'Suite '.$faker->randomNumber(2),
            'neighborhood' => $faker->citySuffix(),
            'city' => $faker->city(),
            'state' => $faker->state(),
            'postal_code' => $faker->postcode(),
            'country' => 'USA',
            'is_primary' => true,
        ];

        $this->actingAs($authorizedUser)
            ->post(route('erp.clients.store'), $payload)
            ->assertRedirect(route('erp.clients.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'email' => $payload['email'],
            'name' => $payload['name'],
        ]);

        $this->assertDatabaseHas('addresses', [
            'street' => $payload['street'],
            'addressable_type' => Client::class,
        ]);
    });
});

describe('tests for the "edit" method of Management/ClientController', function () {
    $componentName = 'erp/clients/edit';

    test('users without client.edit permission cannot access edit page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetClient = Client::firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.clients.edit', $targetClient))
            ->assertForbidden();
    });

    test('authorized users can access edit page', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetClient = Client::firstOrFail();

        $response = $this->actingAs($authorizedUser)
            ->get(route('erp.clients.edit', $targetClient));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component($componentName)
            ->has('client')
            ->where('client.id', $targetClient->id)
        );
    });
});

describe('tests for the "update" method of Management/ClientController', function () {
    test('users without client.edit permission cannot update clients', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetClient = Client::firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->patch(route('erp.clients.update', $targetClient), [])
            ->assertForbidden();
    });

    test('authorized users can update clients', function () {
        $faker = app(Faker::class);
        $authorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetClient = Client::firstOrFail();

        $payload = [
            'name' => 'Updated '.$faker->lastName(),
            'email' => Str::uuid().'@clients.test',
            'identification' => generateValidIdentification(),
            'phone_number' => '+1666660'.random_int(1000, 9999),
            'client_type' => 'company',
        ];

        $this->actingAs($authorizedUser)
            ->patch(route('erp.clients.update', $targetClient), $payload)
            ->assertRedirect(route('erp.clients.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'id' => $targetClient->id,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'client_type' => $payload['client_type'],
        ]);
    });
});

describe('tests for the "destroy" method of Management/ClientController', function () {
    test('users without client.destroy permission cannot delete clients', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetClient = Client::factory()->create();

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.clients.destroy', $targetClient))
            ->assertForbidden();
    });

    test('authorized users can soft delete clients', function () {
        $admin = getSuperAdmin();
        $targetClient = Client::factory()->create();

        $this->actingAs($admin)
            ->delete(route('erp.clients.destroy', $targetClient))
            ->assertRedirect(route('erp.clients.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('clients', ['id' => $targetClient->id]);
    });
});

describe('tests for the "forceDestroy" method of Management/ClientController', function () {
    test('users without client.destroy permission cannot permanently delete clients', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetClient = Client::factory()->create();

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.clients.force-destroy', $targetClient))
            ->assertForbidden();
    });

    test('authorized users can permanently delete clients', function () {
        $admin = getSuperAdmin();
        $targetClient = Client::factory()->create();

        $this->actingAs($admin)
            ->delete(route('erp.clients.force-destroy', $targetClient))
            ->assertRedirect(route('erp.clients.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('clients', ['id' => $targetClient->id]);
    });
});
