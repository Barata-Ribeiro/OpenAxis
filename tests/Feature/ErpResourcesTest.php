<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Inertia\Testing\AssertableInertia as Assert;
use InertiaUI\Modal\Modal;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->withoutVite();
});

it('lists archived records separately and restores them with the corresponding resource routes', function (
    string $resource,
    string $permission,
    string $page,
    string $title,
    string $model,
): void {
    $actor = User::factory()->create()->givePermissionTo([
        "$permission.index", "$permission.show", "$permission.edit", "$permission.destroy",
    ]);
    $name = 'Archive lifecycle fixture';
    /** @var Model $record */
    $record = $model::factory()->create(['name' => $name]);
    $id = $record->getKey();
    $filter = ['table' => ['search' => $name]];
    $activeUrl = route("$resource.index", $filter);
    $archivedUrl = route("$resource.index", ['trashed' => 'only', ...$filter]);

    $this->actingAs($actor)->get(route("$resource.archive", $id), [Modal::HEADER_BASE_URL => $activeUrl])
        ->assertInertia(fn (Assert $response) => $response
            ->component("$page/index")
            ->where('_inertiaui_modal.component', "$page/confirm-destroy")
            ->where('_inertiaui_modal.props.id', $id));

    $this->from($activeUrl)->delete(route("$resource.destroy", $id))
        ->assertRedirect($activeUrl)
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => "$title archived."]);
    $this->assertSoftDeleted($record);

    $this->get($activeUrl)->assertInertia(fn (Assert $response) => $response
        ->component("$page/index")
        ->where('archived', false)
        ->where('table.total', 0));
    $this->get($archivedUrl)->assertInertia(fn (Assert $response) => $response
        ->component("$page/index")
        ->where('archived', true)
        ->where('table.total', 1)
        ->where('table.rows.0.id', $id)
        ->whereNot('table.rows.0.deleted_at', null));

    $this->get(route("$resource.show", $id), [
        Modal::HEADER_BASE_URL => $archivedUrl,
    ])->assertInertia(fn (Assert $response) => $response
        ->component("$page/index")
        ->where('_inertiaui_modal.component', "$page/show")
        ->where('_inertiaui_modal.props.title', $name)
        ->where('_inertiaui_modal.props.modal', true));
    $this->get(route("$resource.edit", $id))->assertNotFound();

    $this->from($archivedUrl)->patch(route("$resource.restore", $id))
        ->assertRedirect($archivedUrl)
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => "$title restored."]);
    $this->assertNotSoftDeleted($record);
    $this->get($archivedUrl)->assertInertia(fn (Assert $response) => $response->where('table.total', 0));
    $this->patch(route("$resource.restore", $id))->assertNotFound();
})->with([
    'payment methods' => ['erp.settings.payment-methods', 'payment-method', 'erp/settings/payment-methods', 'Payment method', PaymentMethod::class],
    'expense categories' => ['erp.settings.expense-categories', 'expense-category', 'erp/settings/expense-categories', 'Expense category', ExpenseCategory::class],
    'product categories' => ['erp.settings.categories', 'category', 'erp/settings/categories', 'Product category', Category::class],
    'units of measure' => ['erp.settings.units', 'unit-of-measure', 'erp/settings/units', 'Unit of measure', UnitOfMeasure::class],
    'storage locations' => ['erp.settings.locations', 'storage-location', 'erp/settings/locations', 'Storage location', StorageLocation::class],
    'products' => ['erp.inventory.products', 'product', 'erp/inventory/products', 'Product', Product::class],
]);

it('requires the archive permission to restore an archived record', function (): void {
    $record = PaymentMethod::factory()->create();
    $record->delete();

    $actor = User::factory()->create()->givePermissionTo(['payment-method.index', 'payment-method.edit']);
    $this->actingAs($actor)
        ->patch(route('erp.settings.payment-methods.restore', $record))
        ->assertForbidden();

    $this->assertSoftDeleted($record);
});

it('uses the payment method pages and validates updates against the bound record', function (): void {
    $actor = User::factory()->create()->givePermissionTo(['payment-method.create', 'payment-method.edit']);
    $this->actingAs($actor)->get(route('erp.settings.payment-methods.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('erp/settings/payment-methods/create')
            ->missing('modal')
            ->where('values.fee_type', PaymentMethod::FEE_FIXED));

    $values = ['name' => 'Test terminal', 'fee_type' => PaymentMethod::FEE_PERCENTAGE, 'fee_rate' => 150, 'is_active' => true];
    $this->post(route('erp.settings.payment-methods.store'), $values)
        ->assertRedirect(route('dashboard'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Payment method created.']);
    $method = PaymentMethod::query()->where('name', $values['name'])->firstOrFail();
    $other = PaymentMethod::factory()->create();

    $this->get(route('erp.settings.payment-methods.edit', $method))->assertInertia(fn (Assert $page) => $page
        ->component('erp/settings/payment-methods/edit')
        ->where('id', $method->id)
        ->where('values.name', $method->name));

    $url = route('erp.settings.payment-methods.update', $method);
    $this->patchJson($url, [...$values, 'name' => $other->name])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->patch($url, [...$values, 'fee_rate' => 225])
        ->assertRedirect(route('dashboard'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Payment method updated.']);

    expect($method->refresh()->fee_rate)->toBe(225);
});

it('uses permanent deletion confirmations for users and roles while enforcing protected records', function (): void {
    $actor = User::factory()->create()->givePermissionTo(['user.index', 'user.destroy', 'role.index', 'role.destroy']);
    $role = Role::create(['name' => 'Temporary role']);
    $user = User::factory()->create()->assignRole($role);
    $this->actingAs($actor);

    $this->get(route('erp.settings.roles.confirm-destroy', $role))
        ->assertInertia(fn (Assert $page) => $page
            ->component('erp/settings/roles/index')
            ->where('_inertiaui_modal.component', 'erp/settings/roles/confirm-destroy'));
    $this->deleteJson(route('erp.settings.roles.destroy', $role))->assertUnprocessable()->assertJsonValidationErrors('record');
    $this->deleteJson(route('erp.settings.users.destroy', $actor))->assertUnprocessable()->assertJsonValidationErrors('record');
    $this->deleteJson(route('erp.settings.roles.destroy', Role::findByName('super-admin')))->assertForbidden();

    $this->delete(route('erp.settings.users.destroy', $user))
        ->assertRedirect(route('erp.settings.users.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'User deleted.']);
    $this->assertModelMissing($user);

    $this->delete(route('erp.settings.roles.destroy', $role))
        ->assertRedirect(route('erp.settings.roles.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Role deleted.']);
    $this->assertModelMissing($role);
});
