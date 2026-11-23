<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Str;

test('purchase and sale share a reference namespace and retain their parties', function () {
    $purchase = Order::factory()->purchase()->create();
    $sale = Order::factory()->create();

    expect(Str::isUlid($purchase->number))->toBeTrue()
        ->and($purchase->reference())->toBe('#'.$purchase->number)
        ->and($sale->number)->not->toBe($purchase->number)
        ->and($purchase->supplier)->not->toBeNull()
        ->and($sale->customer)->not->toBeNull();

    expect(fn () => $sale->update(['supplier_id' => $purchase->supplier_id]))->toThrow(DomainException::class);
});

test('line totals round fractional quantities and preserve historical product details', function () {
    $unit = UnitOfMeasure::factory()->fractional()->create(['code' => 'TEST-L']);
    $product = Product::factory()->for($unit)->create(['name' => 'Original product']);
    $sale = Order::factory()->create([
        'delivery_fee_type' => PaymentMethod::FEE_PERCENTAGE,
        'delivery_fee_rate' => 250,
    ]);
    $line = OrderItem::factory()->for($sale)->for($product)->create([
        'quantity_milliunits' => 1_500,
        'unit_price_cents' => 101,
    ]);
    $product->update(['name' => 'Renamed product', 'sale_price_cents' => 200]);

    expect($line->refresh()->product_name)->toBe('Original product')
        ->and($line->unit_code)->toBe('TEST-L')
        ->and($line->line_total_cents)->toBe(152)
        ->and($sale->subtotalCents())->toBe(152)
        ->and($sale->deliveryFeeCents())->toBe(4)
        ->and($sale->totalCents())->toBe(156);
});

test('an indivisible unit rejects a fractional order quantity', function () {
    expect(fn () => OrderItem::factory()->create(['quantity_milliunits' => 1_500]))
        ->toThrow(DomainException::class, 'The quantity exceeds the precision of the product unit.');
});
