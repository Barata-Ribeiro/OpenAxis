<?php

declare(strict_types=1);

use App\Models\CashEntry;
use App\Models\Category;
use App\Models\FinancialAccount;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 11, 23)->setTime(12, 0));
});

it('authorizes a custom role by its permissions and limits access to the corresponding cash data', function (): void {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'payables-reviewer']);
    $role->givePermissionTo('financial-account.index');
    $user->assignRole($role);

    $this->get(route('erp.inventory.index'))->assertRedirect(route('login'));
    $this->actingAs($user)->get(route('erp.inventory.index'))->assertForbidden();
    $this->get(route('erp.cash-flow.index', ['tab' => 'receipts']))->assertForbidden();
    $this->get(route('erp.cash-flow.index'))->assertInertia(fn (Assert $page) => $page
        ->component('erp/cash-flow/index')
        ->where('tab', 'payables')
        ->where('tabs', ['payables'])
        ->where('summary.receipts', null)
        ->where('summary.receipt_count', null));
});

it('returns stock, expiry metrics and valuation from active batch balances', function (): void {
    $user = User::factory()->create()->givePermissionTo('product.index');
    $this->actingAs($user);
    $before = $this->get(route('erp.inventory.index'))->inertiaProps('summary');
    $product = Product::factory()->create(['name' => 'Tea', 'minimum_stock_milliunits' => 2000]);
    $active = ProductBatch::factory()->for($product)->create(['expires_on' => today()->addDays(5)]);
    $empty = ProductBatch::factory()->for($product)->create(['expires_on' => today()->subDays(2)]);
    $location = StorageLocation::factory()->create();
    StockMovement::factory()->for($active)->for($location)->opening(400)->create(['quantity_milliunits' => 2000]);
    StockMovement::factory()->for($empty)->for($location)->opening(900)->create();
    StockMovement::factory()->for($empty)->for($location)->create(['type' => StockMovement::TYPE_ISSUE, 'quantity_milliunits' => -1000]);

    $this->get(route('erp.inventory.index', ['table' => ['search' => 'Tea']]))
        ->assertViewHas('page', fn (array $page): bool => data_get($page, 'liveProps.summary.listeners.0.channel.name') === 'erp.inventory'
            && data_get($page, 'liveProps.table.listeners.0.channel.type') === 'private')
        ->assertInertia(fn (Assert $page) => $page
            ->component('erp/inventory/index')
            ->where('summary.products', $before['products'] + 1)
            ->where('summary.critical', $before['critical'] + 1)
            ->where('summary.in_stock', $before['in_stock'] + 2)
            ->where('summary.expiring', $before['expiring'] + 1)
            ->where('summary.expired', $before['expired'])
            ->where('table.rows.0.quantity', 2)
            ->where('table.rows.0.days_remaining', 5)
            ->where('table.rows.0.average_cost', 2)
            ->where('table.rows.0.current_value', 4)
            ->where('table.rows.0.status', 'expiring'));
});

it('filters server rows with grouped conditions, range bounds and literal search characters before pagination', function (): void {
    $user = User::factory()->create()->givePermissionTo('product.index');
    $first = Product::factory()->create(['name' => 'Cocoa 25%']);
    Product::factory()->create(['name' => 'Cocoa 20%']);
    Product::factory()->create(['name' => 'Cocoa 25x']);
    Product::factory()->create(['name' => 'Coffee 25%']);
    $batch = ProductBatch::factory()->for($first)->create(['expires_on' => today()->addDays(2)]);
    StockMovement::factory()->for($batch)->create();
    $this->actingAs($user);

    $this->get(route('erp.inventory.index', ['table' => [
        'search' => '%',
        'perPage' => 1,
        'filters' => [
            ['id' => 'name', 'operator' => 'startsWith', 'value' => 'cocoa', 'joinOperator' => 'or'],
            ['id' => 'name', 'operator' => 'startsWith', 'value' => 'coffee'],
            ['id' => 'quantity', 'operator' => 'inRange', 'value' => [1 => '1']],
        ],
        'sorting' => [['id' => 'name', 'desc' => 1]],
    ]]))->assertInertia(fn (Assert $page) => $page
        ->where('table.total', 3)
        ->where('table.rows.0.name', 'Coffee 25%'));

    $this->get(route('erp.inventory.index', ['table' => [
        'filters' => [['id' => 'expires_on', 'operator' => 'isRelativeToToday', 'value' => [1, 3]]],
    ]]))->assertInertia(fn (Assert $page) => $page
        ->where('table.total', 1)
        ->where('table.rows.0.id', $first->id));

    $this->getJson(route('erp.inventory.index', ['table' => [
        'sorting' => [['id' => 'password', 'desc' => 1]],
    ]]))->assertUnprocessable()->assertJsonValidationErrors('table.sorting');

    $this->getJson(route('erp.inventory.index', ['table' => [
        'filters' => [['id' => 'quantity', 'operator' => 'equals', 'value' => ['unexpected']]],
    ]]))->assertUnprocessable()->assertJsonValidationErrors('table.filters');

    $this->getJson(route('erp.inventory.index', ['table' => [
        'sorting' => [['id' => 'name', 'desc' => 1, 'unexpected' => true]],
    ]]))->assertUnprocessable()->assertJsonValidationErrors('table.sorting.0');
});

it('registers products and posts receipts and issues without bypassing ledger constraints', function (): void {
    $user = User::factory()->create()->givePermissionTo(['product.create', 'stock-movement.create', 'stock-movement.index']);
    $location = StorageLocation::factory()->create(['is_active' => true]);
    $unit = UnitOfMeasure::factory()->create(['quantity_decimals' => 0]);
    $category = Category::factory()->create();
    $this->actingAs($user);

    $this->get(route('erp.inventory.products.create'))->assertInertia(fn (Assert $page) => $page
        ->component('erp/inventory/products/create')
        ->has('units')
        ->has('locations')
        ->has('categories'));
    $this->post(route('erp.inventory.products.store'), [
        'sku' => 'ERP-001', 'name' => 'Widget', 'unit_of_measure_id' => $unit->id, 'category_ids' => [$category->id],
        'cost_price_cents' => 100, 'sale_price_cents' => 200, 'minimum_stock_milliunits' => 0, 'is_active' => true,
    ])->assertRedirect(route('dashboard'));
    $product = Product::query()->where('sku', 'ERP-001')->firstOrFail();
    expect($product->categories()->first()->id)->toBe($category->id);

    $when = now()->format('Y-m-d\TH:i');
    $receipt = [
        'product_id' => $product->id, 'storage_location_id' => $location->id,
        'quantity_milliunits' => 2000, 'occurred_at' => $when,
    ];
    $this->postJson(route('erp.inventory.movements.store-receipt'), [...$receipt, 'quantity_milliunits' => 500])
        ->assertUnprocessable()->assertJsonValidationErrors('quantity_milliunits');
    expect($product->batches()->count())->toBe(0);
    $this->post(route('erp.inventory.movements.store-receipt'), $receipt)
        ->assertRedirect(route('erp.inventory.movements.index'));
    $batch = $product->batches()->firstOrFail();

    $this->get(route('erp.inventory.movements.issue'))->assertInertia(fn (Assert $page) => $page
        ->component('erp/inventory/movements/create')
        ->where('direction', 'issue')
        ->where('options', fn ($options) => collect($options)->pluck('id')->contains($batch->id)));

    $issue = [
        'product_batch_id' => $batch->id, 'storage_location_id' => $location->id,
        'quantity_milliunits' => 1000, 'occurred_at' => $when,
    ];
    $this->postJson(route('erp.inventory.movements.store-issue'), [...$issue, 'quantity_milliunits' => 3000])
        ->assertUnprocessable()->assertJsonValidationErrors('quantity_milliunits');
    $this->post(route('erp.inventory.movements.store-issue'), $issue)
        ->assertRedirect(route('erp.inventory.movements.index'));
    expect($product->currentStockMilliunits())->toBe(1000);
});

it('stores processed product images and enforces the limit across edits', function (): void {
    $disk = Storage::fake('public');
    $user = User::factory()->create()->givePermissionTo(['product.create', 'product.edit']);
    $unit = UnitOfMeasure::factory()->create();
    $this->actingAs($user);

    $values = [
        'name' => 'Product with photos', 'cost_price_cents' => 100, 'sale_price_cents' => 200,
        'minimum_stock_milliunits' => 0, 'is_active' => '1',
    ];
    $create = [...$values, 'sku' => 'PHOTOS-1', 'unit_of_measure_id' => $unit->id];
    $store = route('erp.inventory.products.store');

    $this->post($store, [...$create, 'images' => [UploadedFile::fake()->create('notes.txt', 1, 'text/plain')]], ['Accept' => 'application/json'])
        ->assertUnprocessable()->assertJsonValidationErrors('images.0');
    $this->post($store, [...$create, 'images' => array_map(fn (int $index) => UploadedFile::fake()->image("image-$index.jpg"), range(1, 6))], ['Accept' => 'application/json'])
        ->assertUnprocessable()->assertJsonValidationErrors('images');

    $this->post($store, [...$create, 'images' => array_map(fn (int $index) => UploadedFile::fake()->image("image-$index.jpg"), range(1, 5))])
        ->assertRedirect(route('dashboard'));
    $product = Product::query()->where('sku', 'PHOTOS-1')->firstOrFail();
    $photos = $product->images()->get();
    expect($photos)->toHaveCount(5);
    expect($photos->first()->path)->toEndWith('.webp');
    $disk->assertExists($photos->pluck('path')->all());

    $update = route('erp.inventory.products.update', $product);
    $this->post($update, [...$values, '_method' => 'PATCH', 'images' => [UploadedFile::fake()->image('extra.png')]], ['Accept' => 'application/json'])
        ->assertUnprocessable()->assertJsonValidationErrors('images');
    $this->post($update, [
        ...$values, '_method' => 'PATCH', 'images' => [UploadedFile::fake()->image('replacement.png')],
        'remove_image_ids' => [$photos->first()->id],
    ])->assertRedirect(route('dashboard'));
    expect($product->images()->count())->toBe(5);
    expect($product->images()->whereKey($photos->first()->id)->exists())->toBeFalse();
    $disk->assertMissing($photos->first()->path);
});

it('reports period totals and settles only the remaining balance once', function (): void {
    $user = User::factory()->create()->givePermissionTo(['cash-entry.index', 'cash-entry.create', 'financial-account.index', 'financial-account.show']);
    $this->actingAs($user);
    $before = $this->get(route('erp.cash-flow.index'))->inertiaProps('summary');
    $method = PaymentMethod::factory()->create(['fee_type' => PaymentMethod::FEE_FIXED, 'fee_rate' => 25, 'is_active' => true]);
    $payable = FinancialAccount::factory()->payable()->create(['amount_cents' => 500, 'due_on' => today()]);
    CashEntry::factory()->for($method)->create(['amount_cents' => 1000]);
    CashEntry::factory()->for($method)->create(['amount_cents' => 700, 'occurred_at' => today()->subDay()]);
    CashEntry::factory()->forAccount($payable)->for($method)->create(['amount_cents' => 200]);

    $this->actingAs($user)->get(route('erp.cash-flow.index'))->assertInertia(fn (Assert $page) => $page
        ->where('period.preset', 'today')
        ->where('summary.receipts', $before['receipts'] + 1000)
        ->where('summary.expenses', $before['expenses'] + 200)
        ->where('summary.balance', $before['balance'] + 800)
        ->where('summary.net_profit', $before['net_profit'] + 750)
        ->where('summary.payables', $before['payables'] + 300)
        ->where('summary.receipt_count', $before['receipt_count'] + 1)
        ->where('summary.expense_count', $before['expense_count'] + 1));

    $this->get(route('erp.cash-flow.index', [
        'period' => 'custom', 'start' => today()->subDay()->toDateString(), 'end' => today()->toDateString(),
    ]))->assertInertia(fn (Assert $page) => $page->where('summary.receipt_count', $before['receipt_count'] + 2));

    $this->get(route('erp.cash-flow.index', ['tab' => 'payables']))->assertInertia(fn (Assert $page) => $page
        ->where('table.rows.0.status', 'partially_paid'));
    $payment = ['payment_method_id' => $method->id, 'occurred_at' => now()->format('Y-m-d\TH:i'), 'expected_amount_cents' => 300];
    $url = route('erp.cash-flow.payables.pay', $payable);
    $this->postJson($url, [...$payment, 'expected_amount_cents' => 500])->assertUnprocessable()->assertJsonValidationErrors('record');
    $cashFlow = route('erp.cash-flow.index', ['tab' => 'payables', 'period' => 'month']);
    $this->post($url, $payment, ['X-InertiaUI-Modal-Base-Url' => $cashFlow])->assertRedirect($cashFlow);
    $this->postJson($url, $payment)->assertUnprocessable()->assertJsonValidationErrors('record');

    expect($payable->fresh()->status)->toBe(FinancialAccount::STATUS_PAID);
    expect($payable->cashEntries()->count())->toBe(2);
    expect($payable->cashEntries()->latest('id')->first()->amount_cents)->toBe(300);

    $cancel = route('erp.cash-flow.payables.destroy', $payable);
    $this->deleteJson($cancel)->assertForbidden();
    $user->givePermissionTo('financial-account.cancel');
    $this->deleteJson($cancel)->assertUnprocessable()->assertJsonValidationErrors('record');
});

it('permits administrators to delegate only permissions they hold and assigns custom roles to users', function (): void {
    $administrator = User::factory()->create()->givePermissionTo(['role.create', 'user.create', 'product.index']);
    $allowed = Permission::findByName('product.index');
    $forbidden = Permission::findByName('cash-entry.index');
    $this->actingAs($administrator);

    $this->post(route('erp.settings.roles.store'), ['name' => 'inventory-reader', 'permission_ids' => [$forbidden->id]])
        ->assertForbidden();
    $this->post(route('erp.settings.roles.store'), ['name' => 'inventory-reader', 'permission_ids' => [$allowed->id]])
        ->assertRedirect(route('dashboard'));
    $role = Role::findByName('inventory-reader');

    $this->post(route('erp.settings.users.store'), [
        'name' => 'Warehouse manager', 'email' => 'warehouse@example.test', 'role_ids' => [$role->id],
        'password' => 'testPass123!', 'password_confirmation' => 'testPass123!',
    ])->assertRedirect(route('dashboard'));

    expect(User::query()->where('email', 'warehouse@example.test')->firstOrFail()->can('product.index'))->toBeTrue();
});
