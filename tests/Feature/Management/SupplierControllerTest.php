<?php

use App\Enums\AddressTypeEnum;
use App\Enums\PartnerTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Partner;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

describe('tests for the "index" method of Management/SupplierController', function () {
    $componentName = 'erp/suppliers/index';

    test('users without supplier.index permission get a 403 response', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.suppliers.index'))
            ->assertForbidden();
    });

    test('authorized users can access the supplier listing', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('supplier listing supports searching by identification', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value, 'identification' => 'SEARCHME123']);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index', ['search' => $targetSupplier->identification]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('erp/suppliers/index'));
    });

    test('supplier listing supports sorting', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index', ['sort_by' => 'name', 'sort_dir' => 'desc']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('erp/suppliers/index'));
    });

    test('supplier listing supports pagination controls', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index', ['per_page' => 5]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('erp/suppliers/index'));
    });

    test('supplier listing gracefully falls back when sort field is invalid', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index', ['sort_by' => 'invalid_field']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('erp/suppliers/index'));
    });

    test('supplier listing supports created_at filtering', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value, 'created_at' => Carbon::now()->subDays(4), 'updated_at' => Carbon::now()->subDays(4)]);

        $start = Carbon::parse($targetSupplier->created_at)->startOfDay()->valueOf();
        $end = Carbon::parse($targetSupplier->created_at)->endOfDay()->valueOf();

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index', ['filters' => "created_at:{$start},{$end}"]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('erp/suppliers/index'));
    });

    test('supplier listing supports supplier_type filtering', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.index', ['filters' => 'supplier_type:supplier']));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('erp/suppliers/index'));
    });
});

describe('tests for the "show" method of Management/SupplierController', function () {
    $componentName = 'erp/suppliers/show';

    test('users without supplier.show permission cannot view supplier details', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetSupplier = Partner::with('addresses')->whereIn('type', [PartnerTypeEnum::SUPPLIER->value, PartnerTypeEnum::BOTH->value])->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.suppliers.show', $targetSupplier))
            ->assertForbidden();
    });

    test('authorized users can view supplier details including addresses', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::with('addresses')->whereIn('type', [PartnerTypeEnum::SUPPLIER->value, PartnerTypeEnum::BOTH->value])->firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.show', $targetSupplier));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('show redirects back when the partner is not a supplier', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $client = Partner::factory()->createOne(['type' => PartnerTypeEnum::CLIENT->value]);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.show', $client));

        $response->assertRedirect(route('erp.suppliers.index'));
        $response->assertSessionHas('error', 'The specified partner is not a supplier.');
    });
});

describe('tests for the "create" method of Management/SupplierController', function () {
    $componentName = 'erp/suppliers/create';

    test('users without supplier.create permission cannot access the create page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.suppliers.create'))
            ->assertForbidden();
    });

    test('authorized users can access the supplier creation page', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.create'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });
});

describe('tests for the "store" method of Management/SupplierController', function () {
    test('users without supplier.create permission cannot store suppliers', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::VENDOR->value);

        $this->actingAs($unauthorizedUser)
            ->post(route('erp.suppliers.store'), [])
            ->assertForbidden();
    });

    test('authorized users can store suppliers with addresses', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $payload = [
            'name' => 'Clarice e Giovana Eletrônica Ltda',
            'email' => 'seguranca@clariceegiovanaeletronicaltda.com.br',
            'identification' => '19.969.462/0001-40',
            'phone_number' => '+551136168946',
            'supplier_type' => 'supplier',

            'type' => AddressTypeEnum::BILLING->value,
            'label' => 'HQ',
            'street' => 'Praça Doutor Adib Zaidam Addad',
            'number' => '886',
            'complement' => 'Loja 1',
            'neighborhood' => 'Casa Verde',
            'city' => 'São Paulo',
            'state' => 'São Paulo',
            'postal_code' => '02514-080',
            'country' => 'Brasil',
            'is_primary' => true,
        ];

        $this->actingAs($authorizedUser)
            ->post(route('erp.suppliers.store'), $payload)
            ->assertRedirect(route('erp.suppliers.index'));

        $this->assertDatabaseHas('partners', [
            'name' => $payload['name'],
            'email' => $payload['email'],
            'identification' => $payload['identification'],
        ]);

        $this->assertDatabaseHas('addresses', [
            'street' => $payload['street'],
            'number' => $payload['number'],
        ]);
    });
});

describe('tests for the "edit" & "update" methods of Management/SupplierController', function () {
    $componentName = 'erp/suppliers/edit';

    test('users without supplier.edit permission cannot access edit page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::VENDOR->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.suppliers.edit', $targetSupplier))
            ->assertForbidden();
    });

    test('authorized users can access edit page', function () use ($componentName) {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);

        $response = $this->actingAs($authorizedUser)->get(route('erp.suppliers.edit', $targetSupplier));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('authorized users can update suppliers and addresses', function () {
        $authorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);
        $targetSupplier->addresses()->create([
            'type' => AddressTypeEnum::BILLING->value,
            'label' => 'Old',
            'street' => 'Old St',
            'number' => '1',
            'complement' => '',
            'neighborhood' => 'Oldhood',
            'city' => 'Oldcity',
            'state' => 'Oldstate',
            'postal_code' => '00000',
            'country' => 'Oldland',
            'is_primary' => true,
        ]);

        $payload = [
            'name' => 'Updated Name',
            'email' => 'updated+'.uniqid().'@example.com',
            'identification' => '15.062.341/0001-69',
            'phone_number' => '+15557654321',
            'supplier_type' => 'supplier',

            'type' => AddressTypeEnum::BILLING->value,
            'label' => 'New',
            'street' => 'New St',
            'number' => '99',
            'complement' => 'Apt 2',
            'neighborhood' => 'Newhood',
            'city' => 'Newcity',
            'state' => 'Newstate',
            'postal_code' => '99999',
            'country' => 'Newland',
            'is_primary' => true,
        ];

        $this->actingAs($authorizedUser)
            ->patch(route('erp.suppliers.update', $targetSupplier), $payload)
            ->assertRedirect(route('erp.suppliers.index'));

        $this->assertDatabaseHas('partners', [
            'id' => $targetSupplier->id,
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);

        $this->assertDatabaseHas('addresses', [
            'street' => $payload['street'],
            'number' => $payload['number'],
        ]);
    });
});

describe('tests for the "destroy" & "forceDestroy" methods of Management/SupplierController', function () {
    test('users without supplier.destroy permission cannot delete suppliers', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.suppliers.destroy', $targetSupplier))
            ->assertForbidden();
    });

    test('authorized users can soft delete suppliers', function () {
        $authorizedUser = getSuperAdmin();
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);

        $this->actingAs($authorizedUser)
            ->delete(route('erp.suppliers.destroy', $targetSupplier))
            ->assertRedirect(route('erp.suppliers.index'));

        $this->assertSoftDeleted('partners', ['id' => $targetSupplier->id]);
    });

    test('users without supplier.destroy permission cannot permanently delete suppliers', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $targetSupplier = Partner::withTrashed()->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.suppliers.force-destroy', $targetSupplier))
            ->assertForbidden();
    });

    test('authorized users can permanently delete suppliers', function () {
        $authorizedUser = getSuperAdmin();
        $targetSupplier = Partner::factory()->createOne(['type' => PartnerTypeEnum::SUPPLIER->value]);

        $this->actingAs($authorizedUser)
            ->delete(route('erp.suppliers.force-destroy', $targetSupplier))
            ->assertRedirect(route('erp.suppliers.index'));

        $this->assertDatabaseMissing('partners', ['id' => $targetSupplier->id]);
    });
});
