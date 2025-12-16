<?php

use App\Enums\RoleEnum;
use App\Models\PaymentCondition;
use Faker\Generator as Faker;
use Inertia\Testing\AssertableInertia;

describe('tests for the "index" method of Management/PaymentConditionController', function () {
    $componentName = 'erp/payment-conditions/index';

    test('users without payment condition index permission get a 403 response', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.payment-conditions.index'))
            ->assertForbidden();
    });

    test('authorized users can access the payment condition listing', function () use ($componentName) {
        $authorizedUser = getSuperAdmin();

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });

    test('payment condition listing supports searching by code', function () {
        $authorizedUser = getSuperAdmin();
        $target = PaymentCondition::inRandomOrder()->firstOrFail();
        $search = $target->code;

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.index', ['search' => $search]));

        $response->assertOk();
        $paymentConditions = $response->inertiaProps('paymentConditions.data');
        expect(collect($paymentConditions)->pluck('id'))->toContain($target->id);
    });

    test('payment condition listing supports sorting by code desc', function () {
        $authorizedUser = getSuperAdmin();
        $expectedFirst = PaymentCondition::orderBy('code', 'desc')->firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.index', ['sort_by' => 'code', 'sort_dir' => 'desc']));

        $response->assertOk();
        $paymentConditions = $response->inertiaProps('paymentConditions.data');
        expect(collect($paymentConditions)->first()['id'])->toBe($expectedFirst->id);
    });

    test('payment condition listing supports pagination controls', function () {
        $authorizedUser = getSuperAdmin();

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.index', ['per_page' => 1]));

        $response->assertOk();
        $paymentConditions = $response->inertiaProps('paymentConditions.data');
        expect(count($paymentConditions))->toBeLessThanOrEqual(1);
    });

    test('payment condition listing filters by is_active', function () {
        $authorizedUser = getSuperAdmin();
        $target = PaymentCondition::where('is_active', true)->first();

        if ($target === null) {
            $this->markTestSkipped('No active payment condition found to test is_active filter.');
        }

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.index', ['filters' => ['is_active' => ['true']]]));

        $response->assertOk();
        $paymentConditions = $response->inertiaProps('paymentConditions.data');
        expect(collect($paymentConditions)->pluck('id'))->toContain($target->id);
    });
});

describe('tests for the "create" method of Management/PaymentConditionController', function () {
    $componentName = 'erp/payment-conditions/create';

    test('users without payment condition create permission cannot access the create page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.payment-conditions.create'))
            ->assertForbidden();
    });

    test('authorized users can access the payment condition creation page', function () use ($componentName) {
        $authorizedUser = getSuperAdmin();

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.create'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
    });
});

describe('tests for the "edit" method of Management/PaymentConditionController', function () {
    $componentName = 'erp/payment-conditions/edit';

    test('users without payment condition edit permission cannot access edit page', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $target = PaymentCondition::firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->get(route('erp.payment-conditions.edit', $target))
            ->assertForbidden();
    });

    test('authorized users can access edit page and see the payment condition', function () use ($componentName) {
        $authorizedUser = getSuperAdmin();
        $target = PaymentCondition::firstOrFail();

        $response = $this->actingAs($authorizedUser)->get(route('erp.payment-conditions.edit', $target));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component($componentName));
        expect($response->inertiaProps('paymentCondition.id'))->toBe($target->id);
    });
});

describe('tests for the "update" method of Management/PaymentConditionController', function () {
    test('users without payment condition edit permission cannot update payment conditions', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $target = PaymentCondition::inRandomOrder()->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->patch(route('erp.payment-conditions.update', $target), ['name' => 'Should Not Update'])
            ->assertForbidden();
    });

    test('authorized users can update payment conditions', function () {
        $faker = app(Faker::class);

        $authorizedUser = getSuperAdmin();
        $target = PaymentCondition::inRandomOrder()->firstOrFail();

        $payload = [
            'name' => 'Updated Name '.$faker->unique()->word(),
            'code' => 'UPD'.$faker->unique()->numerify('###'),
            'days_until_due' => 30,
            'installments' => 2,
            'is_active' => true,
        ];

        $this->actingAs($authorizedUser)
            ->patch(route('erp.payment-conditions.update', $target), $payload)
            ->assertRedirect(route('erp.payment-conditions.index'));

        $this->assertDatabaseHas('payment_conditions', [
            'id' => $target->id,
            'name' => $payload['name'],
        ]);
    });

    test('updating with a duplicate code fails validation and does not modify the record', function () {
        $faker = app(Faker::class);

        $authorizedUser = getSuperAdmin();
        $target = PaymentCondition::inRandomOrder()->firstOrFail();
        $other = PaymentCondition::where('id', '!=', $target->id)->first();

        if ($other === null) {
            $this->markTestSkipped('Need at least two payment conditions to test unique code validation.');
        }

        $payload = [
            'name' => 'Updated Name '.$faker->unique()->word(),
            'code' => $other->code,
            'days_until_due' => 15,
            'installments' => 1,
            'is_active' => true,
        ];

        $response = $this->actingAs($authorizedUser)
            ->patch(route('erp.payment-conditions.update', $target), $payload);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseMissing('payment_conditions', [
            'id' => $target->id,
            'name' => $payload['name'],
        ]);
    });
});

describe('tests for the "destroy" method of Management/PaymentConditionController', function () {
    test('users without payment condition destroy permission cannot delete payment conditions', function () {
        $unauthorizedUser = getUserWithRole(RoleEnum::BUYER->value);
        $target = PaymentCondition::inRandomOrder()->firstOrFail();

        $this->actingAs($unauthorizedUser)
            ->delete(route('erp.payment-conditions.destroy', $target))
            ->assertForbidden();
    });

    test('authorized users can permanently delete payment conditions', function () {
        $authorizedUser = getSuperAdmin();
        $target = PaymentCondition::inRandomOrder()->firstOrFail();

        $this->actingAs($authorizedUser)
            ->delete(route('erp.payment-conditions.destroy', $target))
            ->assertRedirect(route('erp.payment-conditions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('payment_conditions', [
            'id' => $target->id,
        ]);
    });
});
