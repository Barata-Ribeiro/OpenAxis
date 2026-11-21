<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\StorageLocation;
use App\Models\UnitOfMeasure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('batches are numbered per SKU and retain their own expiry dates', function () {
    $product = Product::factory()->create(['sku' => 'RI435']);
    $first = ProductBatch::factory()->for($product)->create(['expires_on' => '2027-05-10']);
    $second = ProductBatch::factory()->for($product)->create();

    expect($first->code)->toBe('RI435-001')
        ->and($first->expires_on->toDateString())->toBe('2027-05-10')
        ->and($second->code)->toBe('RI435-002')
        ->and($second->expires_on)->toBeNull();
});

test('opening costs stay exact while receipts, issues, and returns change the stock balance', function () {
    $unit = UnitOfMeasure::factory()->fractional()->create();
    $product = Product::factory()->for($unit)->create();
    $batch = ProductBatch::factory()->for($product)->create();
    $location = StorageLocation::factory()->create();
    $movement = StockMovement::factory()->for($batch)->for($location);
    $movement->opening(4_599)->create(['quantity_milliunits' => 7_125]);
    $receipt = $movement->create(['quantity_milliunits' => 2_500]);

    $issue = $movement->create(['type' => StockMovement::TYPE_ISSUE, 'quantity_milliunits' => -2_125]);
    $movement->create(['type' => StockMovement::TYPE_RETURN, 'quantity_milliunits' => 1_125, 'return_of_id' => $issue->id]);
    $movement->create(['type' => StockMovement::TYPE_RETURN, 'quantity_milliunits' => -500, 'return_of_id' => $receipt->id]);

    expect($product->currentStockMilliunits())->toBe(8_125)
        ->and($product->openingStockValueCents())->toBe(4_599);
    expect(fn () => $issue->delete())->toThrow(DomainException::class);
    expect(fn () => $movement->create([
        'type' => StockMovement::TYPE_RETURN,
        'quantity_milliunits' => 1_001,
        'return_of_id' => $issue->id,
    ]))->toThrow(DomainException::class, 'The return exceeds the quantity remaining on the original movement.');
});

test('a stock issue cannot draw stock from another location or exceed the order line', function () {
    $product = Product::factory()->create();
    $batch = ProductBatch::factory()->for($product)->create();
    $location = StorageLocation::factory()->create();
    $elsewhere = StorageLocation::factory()->create();
    $movement = StockMovement::factory()->for($batch)->for($location);
    $movement->opening(4_000)->create(['quantity_milliunits' => 4_000]);

    $sale = Order::factory()->confirmed()->create();
    $line = OrderItem::factory()->for($sale)->for($product)->create(['quantity_milliunits' => 2_000]);
    $issue = $movement->for($line)->state(['type' => StockMovement::TYPE_ISSUE]);

    expect(fn () => $issue->for($elsewhere)->create(['quantity_milliunits' => -1_000]))
        ->toThrow(DomainException::class, 'This batch has insufficient stock at the selected location.');

    $issue->create(['quantity_milliunits' => -2_000]);

    expect(fn () => $issue->create(['quantity_milliunits' => -1_000]))
        ->toThrow(DomainException::class, 'The stock movement would exceed the ordered quantity.');
    expect(fn () => $line->update(['quantity_milliunits' => 3_000]))
        ->toThrow(DomainException::class, 'A posted order item retains its quantity and price.');
});
